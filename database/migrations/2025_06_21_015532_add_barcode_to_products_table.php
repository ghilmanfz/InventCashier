<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void            // ← jalankan saat migrate
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')
                  ->nullable()
                  ->after('sku');         // tambahkan kolom
        });
    }

    public function down(): void          // ← jalankan saat rollback
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('barcode'); // hapus kolom
        });
    }
};
