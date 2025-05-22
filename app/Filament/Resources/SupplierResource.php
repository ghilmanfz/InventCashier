<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;  // ← tambah ini

class SupplierResource extends Resource
{
    use \App\Traits\HasNavigationBadge;

    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Suppliers';
    protected static ?string $navigationGroup = 'Stock';
    protected static ?int    $navigationSort  = 3;

    /**
     * Filament akan memanggil ini untuk menampilkan menu + index page
     */
    public static function canViewAny(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Gating: periksa permission "view any App\Models\Supplier"
        return Auth::user()->can("view any " . self::$model);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('address')->required()->maxLength(255),
                Forms\Components\TextInput::make('phone')->tel()->required()->maxLength(255),
                Forms\Components\Textarea::make('notes')->maxLength(65535)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('address')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit'   => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
