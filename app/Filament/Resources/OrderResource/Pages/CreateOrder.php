<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(
            'create-transaction',
            [
                // pakai route key (order_number), bukan primary key
                'record' => $this->record->getRouteKey(),
            ]
        );
    }
}
