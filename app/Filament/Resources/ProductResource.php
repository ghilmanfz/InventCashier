<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductResource extends Resource
{
    use \App\Traits\HasNavigationBadge;

    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Stock';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->maxSize(1024)
                        ->imageCropAspectRatio('1:1')
                        ->directory('images/products'),
                ])->columns(2)
                    ->columnSpan([
                        'lg' => 2,
                    ]),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(500)
                    ->maxLength(255)
                    ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('sku', str($state . '-' . str()->random())->slug())),
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255)
                    ->suffixAction(function (Forms\Set $set, Forms\Get $get) {
                        return Forms\Components\Actions\Action::make('generateSku')
                            ->icon('heroicon-o-arrow-path')
                            ->hidden(! $get('name'))
                            ->action(fn () => $set('sku', str($get('name') . '-' . str()->random())->slug()));
                    }),
                    Forms\Components\TextInput::make('barcode')
                    ->readOnly()
                    ->helperText('Otomatis sama dengan SKU, boleh diedit manual jika perlu'),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\MarkdownEditor::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('stock_quantity')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('cost_price')
                        ->mask(\Filament\Support\RawJs::make('$money($input)'))
                        ->required()
                        ->prefix('Rp'),
                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->mask(\Filament\Support\RawJs::make('$money($input)'))
                        ->prefix('Rp')
                        ->live(500),
                    Forms\Components\Toggle::make('is_tempered_glass')
                    ->label('Tempered Glass')
                    ->helperText('Centang jika produk berupa kaca yang dihitung per m²')
                    ->inline(false),

                ])->columns(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ViewColumn::make('barcode_svg')
                    ->label('Barcode')
                    ->view('filament.components.barcode-image')   // view kecil <img>
                    ->viewData([
                        'src' => fn ($record) => $record->barcode_svg, // kirim $src
                    ])
                    ->toggleable(),
                
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->sortable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\IconColumn::make('is_tempered_glass')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-minus-circle')
                ->label('TG')
                ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(500)
                    ->color('gray')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('print-labels')
                    ->label('Print Labels')
                    ->icon('heroicon-o-printer')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $chunks = $records->chunk(8)->map->values();

                        /** @var Barryvdh\DomPDF\PDF $pdf */
                        $pdf = Pdf::loadView('pdf.label-107', ['chunks' => $chunks]);

                        return response()->streamDownload(
                            fn () => print($pdf->stream()),
                            'labels-' . now()->format('Ymd_His') . '.pdf'
                    );
                })
                ]),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StockAdjustmentsRelationManager::class,
            RelationManagers\PriceHistoriesRelationManager::class,  
            RelationManagers\RetursRelationManager::class,     
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
