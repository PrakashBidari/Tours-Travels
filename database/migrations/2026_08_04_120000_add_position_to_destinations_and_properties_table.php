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
        Schema::table('destinations', function (Blueprint $table) {
            $table->unsignedInteger('position')->nullable()->after('is_featured')->index();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedInteger('position')->nullable()->after('is_featured')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
