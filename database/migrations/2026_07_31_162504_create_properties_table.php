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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('property_type');
            $table->string('city');
            $table->string('country');
            $table->string('address')->nullable();
            $table->text('description');
            $table->decimal('price_per_night', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('rating', 3, 1);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedTinyInteger('stars')->nullable();
            $table->json('images');
            $table->json('amenities');
            $table->unsignedTinyInteger('max_guests')->default(2);
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['city']);
            $table->index(['property_type']);
            $table->index(['price_per_night']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
