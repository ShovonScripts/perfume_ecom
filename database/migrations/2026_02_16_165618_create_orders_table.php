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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('city');
            $table->string('area');
            $table->string('address_line');

            $table->enum('shipping_zone', ['inside_dhaka', 'outside_dhaka']);
            $table->decimal('shipping_fee', 10, 2);

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            $table->enum('payment_method', ['cod'])->default('cod');
            $table->enum('payment_status', ['pending','paid'])->default('pending');

            $table->boolean('delivery_charge_prepaid')->default(false);
            $table->string('delivery_trx_id')->nullable();

            $table->enum('status', [
                'pending','processing','packed',
                'shipped','delivered','cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
