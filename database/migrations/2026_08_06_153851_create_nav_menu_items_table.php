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
        Schema::create('nav_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('nav_menu_items')->nullOnDelete();
            $table->string('label');
            $table->string('url');
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedTinyInteger('depth')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nav_menu_items');
    }
};
