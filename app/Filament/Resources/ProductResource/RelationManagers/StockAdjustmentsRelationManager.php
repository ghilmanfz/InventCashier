<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Filament\Resources\StockAdjustmentResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class StockAdjustmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockAdjustments';

    public function form(Form $form): Form
    {
        return StockAdjustmentResource::form($form);
    }

    public function table(Table $table): Table
    {
        return StockAdjustmentResource::table($table);
    }
}
