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
        Schema::create('header_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->enum('style', ['solid', 'outline'])->default('solid');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_buttons');
    }
};
