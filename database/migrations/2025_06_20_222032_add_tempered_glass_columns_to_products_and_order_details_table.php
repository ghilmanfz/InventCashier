<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_tempered_glass')->default(false)->after('price');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('length_cm', 8, 2)->nullable()->after('quantity');
            $table->decimal('width_cm', 8, 2)->nullable()->after('length_cm');
            $table->decimal('effective_area_m2', 8, 3)->nullable()->after('width_cm');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['length_cm','width_cm','effective_area_m2']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_tempered_glass');
        });
    }
};
