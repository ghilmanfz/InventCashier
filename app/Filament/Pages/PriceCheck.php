<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PriceCheck extends Page implements HasTable
{
    use InteractsWithTable;

    /* Navigasi sidebar */
    protected static ?string $navigationGroup = 'Tools';
    protected static ?string $navigationIcon  = 'heroicon-o-magnifying-glass-circle';
    protected static ?string $navigationLabel = 'Price Check';
    protected static string  $view           = 'filament.pages.price-check';

    /* di-bind ke input */
    public string $query = '';

    /* ---------------- SEARCH BUTTON ---------------- */
    public function search(): void
    {
        $this->resetTable();                       // refresh tabel
        $this->dispatchBrowserEvent('pc-start');   // jalankan timer JS
    }

    /* Datasource */
    protected function baseQuery(): Builder
    {
        return Product::query()
            ->when(trim($this->query) !== '', function (Builder $q) {
                $t = '%'.$this->query.'%';
                $q->where(fn ($sub) => $sub
                    ->where('name',    'like', $t)
                    ->orWhere('sku',   'like', $t)
                    ->orWhere('barcode','like', $t));
            });
    }

    /* ---------------- KONFIGURASI TABEL ---------------- */
    protected function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->baseQuery())
            ->columns([
                Tables\Columns\TextColumn::make('barcode')->label('Barcode'),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('category.name')->label('Category'),
                Tables\Columns\TextColumn::make('stock_quantity')->label('Qty')->alignCenter(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('IDR')->alignEnd(),
            ])
            ->paginated(false);           // biasanya hanya 1-3 baris
    }
}
