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
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class ReturResource extends Resource
{
    protected static ?string $model = Retur::class;

    // Ganti ikon ini ke heroicon yang valid (contoh: arrow-left)
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left';

    protected static ?string $navigationLabel = 'Retur Produk';
    protected static ?string $navigationGroup = 'Stock';
    protected static ?int $navigationSort = 35;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Informasi Retur')
                    ->schema([
                        // Pilih Product
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Quantity (harus >= 1)
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()       // pastikan hanya angka
                            ->required()
                            ->minValue(1)     // minimal 1
                            ->helperText('Masukkan jumlah barang yang diretur (minimal 1).'),

                        // Reason (textarea)
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(3)
                            ->helperText('Jelaskan alasan retur.'),

                        // Pilih Tipe Retur
                        Select::make('type')
                            ->label('Tipe Retur')
                            ->options([
                                'customer' => 'Customer',
                                'supplier' => 'Supplier',
                            ])
                            ->required()
                            ->reactive()
                            ->helperText('Pilih apakah retur dari Customer atau ke Supplier.'),

                        // Jika type == 'customer', munculkan dropdown Customer
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

                        // Jika type == 'supplier', munculkan dropdown Supplier
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
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1) Nama Produk lewat relasi product()
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                // 2) Quantity
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),

                // 3) Tipe (customer/supplier)
                TextColumn::make('type')
                    ->label('Tipe')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                        default    => '-',
                    }),

                // 4) Customer/Supplier (pakai related_id + formatStateUsing)
                TextColumn::make('related_id')
                    ->label('Customer/Supplier')
                    ->formatStateUsing(fn ($state, $record) => $record->related?->name ?? '-')
                    ->sortable(false)
                    ->searchable(false),

                // 5) Waktu Retur
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
}
