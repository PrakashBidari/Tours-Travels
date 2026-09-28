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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'vendor', 'user'])->default('user')->after('email');
            $table->enum('vendor_status', ['pending', 'approved', 'rejected'])->nullable()->after('role');
            $table->string('company_name')->nullable()->after('vendor_status');
            $table->string('company_phone')->nullable()->after('company_name');
            $table->text('company_description')->nullable()->after('company_phone');
            $table->decimal('commission_rate', 5, 2)->default(15.00)->after('company_description');

            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn([
                'role',
                'vendor_status',
                'company_name',
                'company_phone',
                'company_description',
                'commission_rate',
            ]);
        });
    }
};
