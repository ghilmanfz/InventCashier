<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Customer;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RetursRelationManager extends RelationManager
{
    protected static string $relationship = 'returns';   // ⇐ nama method di model

    /* ------------- FORM CRUD ------------- */
    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('quantity')
                ->numeric()->required()->minValue(1),

            Forms\Components\Textarea::make('reason')
                ->rows(3)->required(),

            Forms\Components\Select::make('type')
                ->options(['customer' => 'Customer', 'supplier' => 'Supplier'])
                ->reactive()->required(),

            Forms\Components\Select::make('related_id')
                ->label(fn ($get) => $get('type') === 'supplier' ? 'Supplier' : 'Customer')
                ->options(fn ($get) => $get('type') === 'supplier'
                    ? Supplier::pluck('name', 'id')
                    : Customer::pluck('name', 'id'))
                ->searchable()->required(),
        ])->columns(2);
    }

    /* ------------- TABEL LIST ------------- */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                /* gunakan accessor */
                Tables\Columns\TextColumn::make('related_name')
                    ->label('Customer / Supplier'),

                Tables\Columns\TextColumn::make('reason')->limit(30),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Date'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

}
