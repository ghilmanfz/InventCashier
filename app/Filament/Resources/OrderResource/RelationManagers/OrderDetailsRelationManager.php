<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\OrderDetail;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderDetails';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix(fn (OrderDetail $record) => $record->quantity . ' x ')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->alignEnd(),
            ]);
    }
}
