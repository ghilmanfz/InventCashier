<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PriceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'priceHistories';
    protected static ?string $title = 'Price History';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('old_purchase_price')->money('IDR')->label('Old Cost'),
                Tables\Columns\TextColumn::make('old_selling_price')->money('IDR')->label('Old Price'),
                Tables\Columns\TextColumn::make('new_purchase_price')->money('IDR')->label('New Cost'),
                Tables\Columns\TextColumn::make('new_selling_price')->money('IDR')->label('New Price'),
            ]);
    }
}
