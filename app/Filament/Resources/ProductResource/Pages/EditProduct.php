<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (Product $product) {
                    try {
                        $product->delete();
                        Notification::make()
                            ->success()
                            ->title('Product Deleted')
                            ->body('The product has been successfully deleted.')
                            ->send();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Failed to delete product')
                            ->body('This product is still being used.')
                            ->send();
                    }
                }),
        ];
    }
}
