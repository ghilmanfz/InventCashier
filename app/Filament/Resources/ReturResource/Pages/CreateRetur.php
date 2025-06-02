<?php

namespace App\Filament\Resources\ReturResource\Pages;

use App\Filament\Resources\ReturResource;
use App\Models\Product;
use App\Models\Retur;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateRetur extends CreateRecord
{
    protected static string $resource = ReturResource::class;

    /**
     * Dipanggil setelah Retur berhasil dibuat (setelah data Retur tersimpan di DB).
     * Karena Filament v3 memanggil afterCreate() tanpa argumen, kita ambil
     * instance Retur lewat $this->record.
     */
    protected function afterCreate(): void
    {
        /** @var Retur $retur */
        $retur = $this->record;

        // Ambil data form (kalau butuh validasi stok, dsb.)
        $data = $this->form->getState();

        // Ambil model Product yang diretur
        $product = Product::find($retur->product_id);

        // Validasi: jika quantity retur > stok saat ini, buang error
        if ($data['quantity'] > $product->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Stok tidak mencukupi. Stok saat ini: {$product->stock_quantity}.",
            ]);
        }

        // Kurangi stok di model Product
        $product->stock_quantity -= $data['quantity'];
        $product->save();

        // Tampilkan notifikasi berhasil
        Notification::make()
            ->title('Retur berhasil disimpan dan stok diperbarui.')
            ->success()
            ->send();
    }

    /**
     * Jika Anda ingin menambahkan tombol atau action selain default “Create”:
     */
    protected function getRedirectUrl(): string
    {
        // Setelah create, redirect ke daftar Retur
        return $this->getResource()::getUrl('index');
    }

    public function getActions(): array
    {
        return [
            Actions\Action::make('Cancel')
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index')),
            Actions\CreateAction::make(),
        ];
    }
}
