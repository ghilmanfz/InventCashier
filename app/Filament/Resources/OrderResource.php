<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;       // ← import widget di sini
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
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
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->default(generateSequentialNumber(Order::class))
                            ->readOnly(),
                        Forms\Components\TextInput::make('order_name')
                            ->maxLength(255)
                            ->placeholder('Tulis nama pesanan'),
                        Forms\Components\TextInput::make('total')
                            ->readOnlyOn('create')
                            ->default(0)
                            ->numeric(),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Customer (optional)')
                            ->placeholder('Pilih Customer'),
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('payment_method')
                                ->enum(\App\Enums\PaymentMethod::class)
                                ->options(\App\Enums\PaymentMethod::class)
                                ->default(\App\Enums\PaymentMethod::CASH)
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->required()
                                ->enum(\App\Enums\OrderStatus::class)
                                ->options(\App\Enums\OrderStatus::class)
                                ->default(\App\Enums\OrderStatus::PENDING),
                        ])->columnSpan(2)->columns(2),
                    ])->columns(2),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(self::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(\App\Enums\OrderStatus::class),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->multiple()
                    ->options(\App\Enums\PaymentMethod::class),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->maxDate(fn (Forms\Get $get) => $get('created_until') ?: now())
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['created_until'], fn (Builder $q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->action(function (Order $record) {
                        $pdf = Pdf::loadView('pdf.print-order', ['order' => $record]);
                        return response()->streamDownload(fn() => print($pdf->stream()), 'receipt-'.$record->order_number.'.pdf');
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('gray'),
                    Tables\Actions\EditAction::make()->color('gray'),
                    Tables\Actions\Action::make('edit-transaction')
                        ->visible(fn (Order $record) => $record->status === \App\Enums\OrderStatus::PENDING)
                        ->label('Edit Transaction')
                        ->icon('heroicon-o-pencil')
                        ->url(fn (Order $record) => "/orders/{$record->id}/transaction"),
                    Tables\Actions\Action::make('mark-as-complete')
                        ->visible(fn (Order $record) => $record->status === \App\Enums\OrderStatus::PENDING)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Order $record) => $record->markAsComplete())
                        ->label('Mark as Complete'),
                    Tables\Actions\Action::make('divider')->label('')->disabled(),
                    Tables\Actions\DeleteAction::make()
                        ->before(fn (Order $order) => $order->orderDetails()->delete()),
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
                ->numeric()->alignEnd()->sortable()->toggleable(isToggledHiddenByDefault: true)
                ->summarize(Tables\Columns\Summarizers\Sum::make('profit')->money('IDR')),
            Tables\Columns\TextColumn::make('payment_method')->badge()->color('gray'),
            Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => $state->getColor()),
            Tables\Columns\TextColumn::make('user.name')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('customer.name')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()->sortable()
                // ⚠️ pastikan parameter bernama $state, bukan $s atau lainnya:
                ->formatStateUsing(fn ($state) => $state->format('d M Y H:i'))
                ->toggleable(),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()->sortable()
                ->formatStateUsing(fn ($state) => $state->format('d M Y H:i'))
                ->toggleable(isToggledHiddenByDefault: true),
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
            // → pastikan pakai {record}/transaction
            'create-transaction' => Pages\CreateTransaction::route('/{record}/transaction'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderStats::class,    // ← pastikan ini merujuk ke class yang di-import di atas
        ];
    }
}