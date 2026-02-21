<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Indexes for VARIANT filtering/inventory queries:
     * - stock: inventory low-stock alerts + filtering
     * - price: price range filtering + sorting
     * - is_active: active variant filtering
     *
     * Already indexed: product_id (FK), sku (unique)
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('stock');
            $table->index('price');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['stock']);
            $table->dropIndex(['price']);
            $table->dropIndex(['is_active']);
        });
    }
};
