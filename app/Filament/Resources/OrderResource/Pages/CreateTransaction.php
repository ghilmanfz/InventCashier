<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\TemperedGlassPricing;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class CreateTransaction extends Page implements HasForms
{
    protected static string $resource = OrderResource::class;
    protected static string $view     = 'filament.resources.order-resource.pages.create-transaction';

    /* PUBLIC PROPERTY AKAN DI-BIND LIVEWIRE */
    public Order $record;
    public ?int $selectedProduct = null;
    public int  $quantityValue   = 1;
    public int  $discount        = 0;

    /* untuk tempered glass */
    public array $dimensions = []; // [product_id => ['l'=>..,'w'=>..]]

    /* ----------------------------- INIT ----------------------------- */
    public function mount(Order $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Order: {$this->record->order_number}";
    }

    /* ---------------------- TOMBOL PRODUK TABLE --------------------- */
    public function removeProduct(OrderDetail $orderDetail): void
    {
        $orderDetail->delete();
        $this->dispatch('productRemoved');
    }

    public function updateQuantity(OrderDetail $orderDetail, $quantity): void
    {
        if ($quantity > 0) {
            $orderDetail->update([
                'quantity' => $quantity,
                'subtotal' => $orderDetail->price * $quantity,
            ]);
        }
    }

    /* --------------------------- UPDATE TOTAL ----------------------- */
    protected function updateOrder(): void
    {
        $subtotal = $this->record->orderDetails->sum('subtotal');
        $this->record->update([
            'discount' => $this->discount,
            'total'    => max($subtotal - $this->discount, 0),
        ]);
    }

    /* ------------------------- FINALISASI --------------------------- */
    public function finalizeOrder(): void
    {
        $this->updateOrder();
        $this->record->update(['status' => OrderStatus::COMPLETED]);
        $this->redirect('/orders');
    }

    public function saveAsDraft(): void
    {
        $this->updateOrder();
        $this->redirect('/orders');
    }

    /* ---------- AUTOCOMPLETE SELECT (SUDAH ADA) --------------------- */
    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedProduct')
                ->label('Select Product')
                ->searchable()
                ->preload()
                ->options(Product::pluck('name', 'id')->toArray())
                ->live()
                ->afterStateUpdated(function (?int $state) {
                    if (!$state) {
                        return;
                    }
                    $this->addProductById($state);
                    $this->selectedProduct = null;
                }),
        ];
    }

    /* ------------------ FUNGSI TAMBAH PRODUK ------------------------ */
    public function addProductById(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        $this->record->orderDetails()->updateOrCreate(
            ['product_id' => $productId],
            [
                'quantity' => DB::raw('quantity + 1'),
                'price'    => $product->price,
                'subtotal' => DB::raw('(quantity + 1) * '.$product->price),
            ]
        );
    }

    /* untuk input barcode */
    public function addProductByCode(string $code): void
    {
        $product = Product::query()
            ->where('barcode', $code)
            ->orWhere('sku', $code)
            ->first();

        if (!$product) {
            Notification::make()->danger()->title('Product not found')->send();
            return;
        }

        $this->addProductById($product->id);
    }

    /* ------------------- TEMPERED GLASS LOGIC ----------------------- */
    public function updateDimension(int $productId, string $field, $value): void
    {
        $this->dimensions[$productId][$field] = (float) $value;

        $detail  = $this->record->orderDetails()->firstWhere('product_id', $productId);
        $product = Product::find($productId);

        if (!$detail || !$product || !$product->is_tempered_glass) {
            return;
        }

        $calc = TemperedGlassPricing::calculate(
            $this->dimensions[$productId]['l'] ?? 0,
            $this->dimensions[$productId]['w'] ?? 0,
            $product->price
        );

        $detail->update([
            'length_cm'         => $this->dimensions[$productId]['l'] ?? null,
            'width_cm'          => $this->dimensions[$productId]['w'] ?? null,
            'effective_area_m2' => $calc['effective_area'],
            'subtotal'          => $calc['total_price'],
        ]);

        $this->dispatch('subtotalUpdated');
    }
}
