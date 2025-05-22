<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = OrderResource::class;

    public function getTabs(): array
    {
        $statuses = collect([
            'all' => ['label' => 'All', 'badgeColor' => 'primary', 'status' => null],
            OrderStatus::PENDING->name => ['label' => 'Pending', 'badgeColor' => 'warning', 'status' => OrderStatus::PENDING],
            OrderStatus::COMPLETED->name => ['label' => 'Completed', 'badgeColor' => 'success', 'status' => OrderStatus::COMPLETED],
            OrderStatus::CANCELLED->name => ['label' => 'Cancelled', 'badgeColor' => 'danger', 'status' => OrderStatus::CANCELLED],
        ]);

        return $statuses->mapWithKeys(function ($data, $key) {
            $badgeCount = is_null($data['status'])
                ? Order::count()
                : Order::where('status', $data['status'])->count();

            return [$key => Tab::make($data['label'])
                ->badge($badgeCount)
                ->modifyQueryUsing(fn ($query) => is_null($data['status']) ? $query : $query->where('status', $data['status']))
                ->badgeColor($data['badgeColor'])];
        })->toArray();
    }

    public function getHeaderWidgets(): array
    {
        return [
            OrderResource\Widgets\OrderStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
