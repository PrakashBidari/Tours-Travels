<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->setRoles(['super_admin', 'vendor', 'user', 'rental_partner', 'staff']);

        Schema::table('users', function (Blueprint $table) {
            // manager / ticketing / consultant / editor / finance — see config('travel.staff_roles').
            $table->string('staff_role')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('staff_role');
        });

        DB::table('users')->where('role', 'staff')->update(['role' => 'user']);
        $this->setRoles(['super_admin', 'vendor', 'user', 'rental_partner']);
    }

    /** MODIFY COLUMN is MySQL-only; other drivers (the SQLite test database) use ->change(). */
    protected function setRoles(array $roles): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('".implode("', '", $roles)."') NOT NULL DEFAULT 'user'");

            return;
        }

        Schema::table('users', fn (Blueprint $table) => $table->enum('role', $roles)->default('user')->change());
    }
};
