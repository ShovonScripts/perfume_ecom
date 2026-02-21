<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Indexes for frequent ORDER filtering/sorting queries:
     * - status: admin order filtering (pending/processing/shipped/delivered)
     * - payment_status: payment-based filtering
     * - created_at: date range filters + dashboard analytics
     * - phone: order lookup by phone number
     *
     * Already indexed: user_id (FK), order_number (unique)
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['phone']);
        });
    }
};
