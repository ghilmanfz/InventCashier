<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturResource\Pages;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Retur;
use App\Models\Supplier;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class ReturResource extends Resource
{
    protected static ?string $model = Retur::class;

    protected static ?string $navigationIcon  = 'heroicon-o-arrow-left';
    protected static ?string $navigationLabel = 'Retur Produk';
    protected static ?string $navigationGroup = 'Stock';
    protected static ?int    $navigationSort  = 35;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Informasi Retur')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Masukkan jumlah barang yang diretur (minimal 1).'),

                        Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(3)
                            ->helperText('Jelaskan alasan retur.'),

                        Select::make('type')
                            ->label('Tipe Retur')
                            ->options([
                                'customer' => 'Customer',
                                'supplier' => 'Supplier',
                            ])
                            ->required()
                            ->reactive()
                            ->helperText('Pilih apakah retur dari Customer atau ke Supplier.'),

                        Select::make('related_id')
                            ->label('Pilih Customer')
                            ->options(Customer::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('type') === 'customer')
                            ->required(fn (callable $get) => $get('type') === 'customer'),

                        Select::make('related_id')
                            ->label('Pilih Supplier')
                            ->options(Supplier::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('type') === 'supplier')
                            ->required(fn (callable $get) => $get('type') === 'supplier'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                        default    => '-',
                    }),

                TextColumn::make('related_id')
                    ->label('Customer/Supplier')
                    ->formatStateUsing(fn ($state, $record) => $record->related?->name ?? '-')
                    ->sortable(false)
                    ->searchable(false),

                TextColumn::make('created_at')
                    ->label('Waktu Retur')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReturs::route('/'),
            'create' => Pages\CreateRetur::route('/create'),
            'edit'   => Pages\EditRetur::route('/{record}/edit'),
        ];
    }

    /**
     * 1) Sembunyikan menu “Retur Produk” di sidebar jika user
     *    tidak punya permission "view retur".
     */
    public static function canViewNavigation(): bool
    {
        return auth()->user()?->can('view retur') ?? false;
    }

    /**
     * 2) Cek apakah user boleh melihat daftar Retur (viewAny).
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view retur') ?? false;
    }

    /**
     * 3) Cek apakah user boleh membuat Retur baru (create).
     */
    public static function canCreate(): bool
    {
        return auth()->user()?->can('create retur') ?? false;
    }

    /**
     * 4) Cek apakah user boleh meng‐edit Retur (update).
     */
    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update retur') ?? false;
    }

    /**
     * 5) Cek apakah user boleh meng‐hapus Retur (delete).
     */
    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete retur') ?? false;
    }
}
