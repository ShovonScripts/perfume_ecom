<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Indexes for USER/ADMIN queries:
     * - is_admin: admin panel access checks
     * - role: role-based filtering (super_admin/manager/staff)
     *
     * Already indexed: email (unique)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_admin');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_admin']);
            $table->dropIndex(['role']);
        });
    }
};
