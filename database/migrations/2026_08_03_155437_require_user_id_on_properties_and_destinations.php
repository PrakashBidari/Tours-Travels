<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Every property/destination must belong to a real vendor now, so the
        // old admin-owned/seeded rows with no owner can no longer exist.
        DB::table('properties')->whereNull('user_id')->delete();
        DB::table('destinations')->whereNull('user_id')->delete();

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE properties MODIFY user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE destinations MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('properties', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE properties MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE destinations MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('properties', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
