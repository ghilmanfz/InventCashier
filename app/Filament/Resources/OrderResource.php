<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use function generateSequentialNumber;

class OrderResource extends Resource
{
    use \App\Traits\HasNavigationBadge;

    protected static ?string $model           = Order::class;
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                // Order Information
                //
                Section::make('Order Information')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order number')
                            ->required()
                            ->default(generateSequentialNumber(Order::class))
                            ->disabledOn('edit')
                            ->readOnly(),

                        TextInput::make('order_name')
                            ->label('Order name')
                            ->maxLength(255)
                            ->placeholder('Tulis nama pesanan'),

                        Select::make('customer_id')
                            ->label('Customer (optional)')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Customer'),

                        Group::make([
                            Select::make('payment_method')
                                ->label('Payment method')
                                ->options([
                                    PaymentMethod::CASH->value           => PaymentMethod::CASH->getLabel(),
                                    PaymentMethod::BANK_TRANSFER->value => PaymentMethod::BANK_TRANSFER->getLabel(),
                                ])
                                ->default(PaymentMethod::CASH->value)
                                ->required(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    OrderStatus::PENDING->value   => OrderStatus::PENDING->getLabel(),
                                    OrderStatus::COMPLETED->value => OrderStatus::COMPLETED->getLabel(),
                                    OrderStatus::CANCELLED->value => OrderStatus::CANCELLED->getLabel(),
                                ])
                                ->default(OrderStatus::PENDING->value)
                                ->required(),
                        ])->columns(2),
                    ])
                    ->columns(2),

                //
                // Order Details
                //
                Section::make('Order Details')
                    ->schema([
                        Repeater::make('orderDetails')
                            ->relationship()
                            ->reactive()
                            ->afterStateUpdated(function (mixed $state, Get $get, Set $set) {
                                $details = collect($get('orderDetails'))->sum(fn($d) => $d['subtotal'] ?? 0);
                                $set('total', $details - ($get('discount') ?? 0));
                            })
                            ->createItemButtonLabel('Tambah Produk')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function (mixed $state, Get $get, Set $set) {
                                        $set('price', Product::find($state)?->price ?? 0);
                                    })
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (mixed $state, Get $get, Set $set) {
                                        $sub = $state * ($get('price') ?? 0);
                                        $set('subtotal', $sub);
                                        $details = collect($get('orderDetails'))->sum(fn($d) => $d['subtotal'] ?? 0);
                                        $set('total', $details - ($get('discount') ?? 0));
                                    }),

                                TextInput::make('price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->readOnly()
                                    ->reactive()
                                    ->default(fn (Get $get) => Product::find($get('product_id'))?->price ?? 0)
                                    ->afterStateUpdated(function (mixed $state, Get $get, Set $set) {
                                        $sub = ($get('quantity') ?? 0) * $state;
                                        $set('subtotal', $sub);
                                        $details = collect($get('orderDetails'))->sum(fn($d) => $d['subtotal'] ?? 0);
                                        $set('total', $details - ($get('discount') ?? 0));
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->readOnly()
                                    ->reactive()
                                    ->default(fn (Get $get) => ($get('quantity') ?? 0) * ($get('price') ?? 0)),
                            ]),

                        TextInput::make('discount')
                            ->label('Discount')
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (mixed $state, Get $get, Set $set) {
                                $details = collect($get('orderDetails'))->sum(fn($d) => $d['subtotal'] ?? 0);
                                $set('total', $details - ($state ?? 0));
                            }),

                        TextInput::make('total')
                            ->label('Total After Discount')
                            ->numeric()
                            ->readOnly()
                            ->reactive()
                            ->default(fn (Get $get) =>
                                collect($get('orderDetails'))->sum(fn($d) => $d['subtotal'] ?? 0)
                                - ($get('discount') ?? 0)
                            ),
                    ])
                    ->columns(3),
            ]);
    }

    /** @throws Exception */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(self::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(OrderStatus::class),
                Tables\Filters\SelectFilter::make('payment_method')->multiple()->options(PaymentMethod::class),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->maxDate(fn (Forms\Get $get) => $get('created_until') ?: now())
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->query(fn (Builder $q, array $data) =>
                        $q
                            ->when($data['created_from'], fn ($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn ($q) => $q->whereDate('created_at', '<=', $data['created_until']))
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->action(fn (Order $record) =>
                        response()->streamDownload(
                            fn () => print(Pdf::loadView('pdf.print-order', ['order' => $record])->stream()),
                            'receipt-' . $record->order_number . '.pdf'
                        )
                    ),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('gray'),
                    Tables\Actions\EditAction::make()->color('gray'),
                    Tables\Actions\Action::make('edit-transaction')
                        ->visible(fn (Order $r) => $r->status === OrderStatus::PENDING)
                        ->label('Edit Transaction')
                        ->icon('heroicon-o-pencil')
                        ->url(fn (Order $r) => "/orders/{$r->getKey()}/transaction"),
                    Tables\Actions\Action::make('mark-as-complete')
                        ->visible(fn (Order $r) => $r->status === OrderStatus::PENDING)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Order $r) => $r->markAsComplete())
                        ->label('Mark as Complete'),
                    Tables\Actions\Action::make('divider')->label('')->disabled(),
                    Tables\Actions\DeleteAction::make()->before(fn (Order $o) => $o->orderDetails()->delete()),
                ])->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(fn ($records) => $records->each(fn (Order $o) => $o->orderDetails()->delete())),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export Excel')
                    ->fileDisk('public')
                    ->color('success')
                    ->icon('heroicon-o-document-text')
                    ->exporter(\App\Filament\Exports\OrderExporter::class),
            ]);
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('order_number')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('order_name')->searchable(),
            Tables\Columns\TextColumn::make('discount')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('total')
                ->numeric()->alignEnd()->sortable()
                ->summarize(Tables\Columns\Summarizers\Sum::make('total')->money('IDR')),
            Tables\Columns\TextColumn::make('profit')
                ->numeric()->alignEnd()->sortable()->toggleable()
                ->summarize(Tables\Columns\Summarizers\Sum::make('profit')->money('IDR')),
            Tables\Columns\TextColumn::make('payment_method')->badge()->color('gray'),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (OrderStatus $state): string => $state->getColor()),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()->sortable()
                ->formatStateUsing(fn (\Illuminate\Support\Carbon $dt) => $dt->format('d M Y H:i')),
        ];
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\OrderResource\RelationManagers\OrderDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'              => Pages\ListOrders::route('/'),
            'create'             => Pages\CreateOrder::route('/create'),
            'edit'               => Pages\EditOrder::route('/{record}/edit'),
            'view'               => Pages\ViewOrder::route('/{record}/details'),
            'create-transaction' => Pages\CreateTransaction::route('/{record}/transaction'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }
}
