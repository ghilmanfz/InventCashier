<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetails';

    /* ------------------------------------------------------------------ */
    /*                              FORM CRUD                             */
    /* ------------------------------------------------------------------ */
    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('price', Product::find($state)?->price ?? 0)
                ),

            TextInput::make('quantity')
                ->numeric()
                ->minValue(1)
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                    $set('subtotal', $state * ($get('price') ?? 0))
                ),

            TextInput::make('price')
                ->numeric()
                ->disabled()
                ->prefix('Rp'),

            TextInput::make('subtotal')
                ->numeric()
                ->disabled()
                ->prefix('Rp'),
        ])->columns(4);
    }

    /* ------------------------------------------------------------------ */
    /*                            TABEL DETAIL                            */
    /* ------------------------------------------------------------------ */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product')->searchable(),
                Tables\Columns\TextColumn::make('quantity')->alignCenter(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->label('Harga Satuan')->alignEnd(),
                Tables\Columns\TextColumn::make('subtotal')->money('IDR')->alignEnd(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),   // ➕ tambah item
            ])
            ->actions([
                Tables\Actions\EditAction::make(),     // ✏️ edit item
                Tables\Actions\DeleteAction::make(),   // 🗑️ hapus item
            ]);
    }
}
