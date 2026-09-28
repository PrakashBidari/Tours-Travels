<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->unsignedTinyInteger('seats')->default(4);
            $table->unsignedTinyInteger('luggage')->default(2);
            $table->string('transmission')->default('Manual');
            $table->string('fuel')->default('Diesel');
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('driver_charge_per_day', 10, 2)->default(0);
            $table->boolean('self_drive')->default(false);
            $table->boolean('with_driver')->default(true);
            $table->json('services')->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bus_routes', function (Blueprint $table) {
            $table->id();
            $table->string('operator');
            $table->string('bus_name');
            $table->string('bus_type')->default('tourist');
            $table->string('from_city')->index();
            $table->string('to_city')->index();
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->string('boarding_point')->nullable();
            $table->string('dropping_point')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedTinyInteger('total_seats')->default(35);
            $table->json('amenities')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visa_services', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('flag', 16)->nullable();
            $table->string('visa_type')->default('tourist')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('processing_time')->nullable();
            $table->string('validity')->nullable();
            $table->string('stay_duration')->nullable();
            $table->decimal('embassy_fee', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->json('requirements')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_services');
        Schema::dropIfExists('bus_routes');
        Schema::dropIfExists('vehicles');
    }
};
