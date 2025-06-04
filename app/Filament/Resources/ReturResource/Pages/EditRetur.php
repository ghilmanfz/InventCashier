<?php

namespace App\Filament\Resources\ReturResource\Pages;

use App\Filament\Resources\ReturResource;
use App\Models\Product;
use App\Models\Retur;

// Untuk tombol “Cancel” (jika ingin menambahkan tombol Cancel yang kembali ke list)
use Filament\Pages\Actions\Action;

// **Perhatikan:** DeleteAction & SaveAction DIAMBIL dari namespace Filament\Pages\Actions
use Filament\Pages\Actions\DeleteAction as PageDeleteAction;
use Filament\Pages\Actions\SaveAction;

use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class EditRetur extends EditRecord
{
    protected static string $resource = ReturResource::class;

    // Properti untuk menyimpan nilai “lama” sebelum di‐edit
    protected ?int    $oldProductId = null;
    protected ?int    $oldQuantity  = null;
    protected ?string $oldType      = null;
    protected ?int    $oldRelatedId = null;

    /**
     * 1) Sebelum form di‐fill, simpan dulu nilai lama ke properti di atas.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Retur $record */
        $record = $this->record;

        $this->oldProductId = $record->product_id;
        $this->oldQuantity  = $record->quantity;
        $this->oldType      = $record->type;
        $this->oldRelatedId = $record->related_id;

        return $data;
    }

    /**
     * 2) Hook beforeSave() akan dipanggil saat user menekan tombol “Save”,
     *    sebelum record Retur di‐update di database.
     *    Gunakan untuk mengembalikan stok lama & validasi stok baru.
     */
    protected function beforeSave(): void
    {
        // Ambil inputan form (state terbaru)
        $data = $this->form->getState();

        // 2a) Ambil produk lama, lalu kembalikan stok lama (karena saat pembuatan awal, stok sudah dikurangi)
        if ($this->oldProductId !== null && $this->oldQuantity !== null) {
            $oldProduct = Product::find($this->oldProductId);

            if (! $oldProduct) {
                throw ValidationException::withMessages([
                    'product_id' => 'Produk lama tidak ditemukan.',
                ]);
            }

            // Kembalikan stok sesuai quantity lama
            $oldProduct->stock_quantity += $this->oldQuantity;
            $oldProduct->save();
        }

        // 2b) Validasi stok produk baru (hasil edit)
        $newProduct = Product::find($data['product_id'] ?? null);
        if (! $newProduct) {
            throw ValidationException::withMessages([
                'product_id' => 'Produk baru tidak valid.',
            ]);
        }

        // Pastikan quantity baru tidak melebihi stok produk
        if (($data['quantity'] ?? 0) > $newProduct->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Stok produk “{$newProduct->name}” tidak mencukupi. Stok saat ini: {$newProduct->stock_quantity}.",
            ]);
        }

        // Jika validasi lolos, Laravel akan lanjut melakukan update Retur di DB.
        // Pengurangan stok baru akan kita lakukan di afterSave().
    }

    /**
     * 3) Hook afterSave() dipanggil setelah record Retur di‐update di DB.
     *    Kita kurangi stok produk baru sesuai quantity terbaru.
     */
    protected function afterSave(): void
    {
        $updatedRetur = $this->record; // instance Retur yang sudah di‐update

        // Ambil produk baru (setelah update)
        $newProduct = Product::find($updatedRetur->product_id);
        if (! $newProduct) {
            // Jarang sekali terjadi, tapi jika produk tidak ada, kita skip
            return;
        }

        // Kurangi stok sesuai quantity terbaru
        $newProduct->stock_quantity -= $updatedRetur->quantity;
        $newProduct->save();

        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Retur berhasil diperbarui dan stok telah diubah.')
            ->success()
            ->send();
    }

    /**
     * 4) Jika Anda hanya ingin menampilkan tombol → “Delete” & “Save” (default Filament),
     *    Anda **tidak perlu** meng‐override getActions(). Filament otomatis akan menampilkan
     *    kedua tombol tersebut. Jika Anda ingin menambahkan tombol “Cancel” (link kembalikan ke index),
     *    Anda bisa uncomment block di bawah ini:
     */

//    protected function getActions(): array
//    {
//        return [
//            // Tombol “Cancel” (jika ingin kembali ke daftar Retur tanpa menyimpan perubahan)
//            Action::make('Cancel')
//                ->label('Cancel')
//                ->url($this->getResource()::getUrl('index')),
//            
//            // Tombol “Delete” (pojak kanan atas)
//            PageDeleteAction::make(),
//
//            // Tombol “Save” (pojok kanan atas)
//            SaveAction::make(),
//        ];
//    }
}
