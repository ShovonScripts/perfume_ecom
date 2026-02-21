<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('ml_value', 8, 2);
            $table->string('ml_unit', 10)->default('ml');
            $table->string('type')->nullable();

            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();

            $table->integer('stock')->default(0);
            $table->string('sku')->unique();

            $table->integer('sort_order')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
