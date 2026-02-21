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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_sender_number')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_transaction_id')->nullable();

            // We need to modify the existing column. 
            // Since enum changes are tricky, we can drop and re-add or just use string if we want flexibility.
            // But let's try modifying it if possible.
            // If Doctrine DBAL is not installed, simple modification might fail.
            // Let's assume we can just change it.
            // If it fails, we will use raw SQL.
            $table->enum('payment_status', ['pending', 'pending_verification', 'paid', 'failed'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_sender_number', 'payment_amount', 'payment_transaction_id']);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->change();
        });
    }
};
