<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['nepal', 'international', 'trekking', 'adventure'])->default('nepal')->index();
            $table->string('destination')->index();
            $table->string('country')->default('Nepal');
            $table->unsignedSmallInteger('duration_days')->default(1);
            $table->unsignedSmallInteger('duration_nights')->default(0);
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('trip_style')->nullable();
            $table->string('season')->default('all');
            $table->string('difficulty')->nullable();
            $table->string('max_altitude')->nullable();
            $table->string('group_size')->nullable();
            $table->string('hotel')->nullable();
            $table->string('meals')->nullable();
            $table->string('transport')->nullable();
            $table->string('summary', 500)->nullable();
            $table->longText('overview')->nullable();
            $table->json('highlights')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('includes')->nullable();
            $table->json('excludes')->nullable();
            $table->text('visa_info')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->json('images')->nullable();
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
