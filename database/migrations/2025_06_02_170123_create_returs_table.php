<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returs', function (Blueprint $table) {
            $table->id();

            // 1) Kolom foreign key ke produk
            $table->foreignId('product_id')
                  ->constrained()        // otomatis ke tabel 'products'
                  ->onDelete('cascade'); // jika produk dihapus, retur ikut terhapus

            // 2) Quantity (harus integer)
            $table->integer('quantity')
                  ->comment('Jumlah barang yang di-retur');

            // 3) Reason (boleh null, tapi di Form kita akan required)
            $table->text('reason')->nullable();

            // 4) Tipe retur: 'customer' atau 'supplier'
            $table->enum('type', ['customer', 'supplier']);

            // 5) related_id: akan menampung ID customer atau ID supplier
            $table->unsignedBigInteger('related_id')->nullable()
                  ->comment('ID Customer atau Supplier, sesuai tipe');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returs');
    }
};
