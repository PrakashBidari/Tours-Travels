<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One table for every non-hotel booking (tour, flight, bus, car, visa). Service-specific
        // fields (flight route, bus seats, car pickup, uploaded visa documents…) live in `details`.
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_type')->index();
            $table->nullableMorphs('bookable');
            $table->string('title');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('travel_date')->nullable();
            $table->date('return_date')->nullable();
            $table->unsignedSmallInteger('adults')->default(1);
            $table->unsignedSmallInteger('children')->default(0);
            $table->text('special_request')->nullable();
            $table->json('details')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 3)->default('NPR');
            $table->string('coupon_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('unpaid')->index();
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('pnr')->nullable();
            $table->string('ticket_number')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bus_seat_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_booking_id')->constrained()->cascadeOnDelete();
            $table->date('travel_date');
            $table->string('seat_number', 8);
            $table->timestamps();

            // The database itself guarantees a seat can't be sold twice for the same trip.
            $table->unique(['bus_route_id', 'travel_date', 'seat_number'], 'bus_seat_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_seat_reservations');
        Schema::dropIfExists('service_bookings');
    }
};
