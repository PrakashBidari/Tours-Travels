<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\ServiceBooking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Admin home: Ram Tours booking KPIs, charts and work queues for super admins and staff. */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $services = ServiceBooking::query();

        $stats = [
            'total_bookings' => (clone $services)->count() + Booking::count(),
            'today_bookings' => (clone $services)->whereDate('created_at', today())->count() + Booking::whereDate('created_at', today())->count(),
            'revenue' => (float) (clone $services)->where('payment_status', 'paid')->sum('total'),
            'revenue_month' => (float) (clone $services)->where('payment_status', 'paid')->where('paid_at', '>=', now()->startOfMonth())->sum('total'),
            'pending_payments' => (clone $services)->whereIn('payment_status', ['unpaid', 'pending'])->whereNotIn('status', ['cancelled', 'refunded'])->where('total', '>', 0)->count(),
            'pending_amount' => (float) (clone $services)->whereIn('payment_status', ['unpaid', 'pending'])->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
            'confirmed' => (clone $services)->whereIn('status', ['confirmed', 'ticketed', 'completed'])->count(),
            'tickets_issued' => (clone $services)->where('status', 'ticketed')->count(),
            'cancelled' => (clone $services)->whereIn('status', ['cancelled', 'refunded'])->count() + Booking::where('status', 'cancelled')->count(),
            'customers' => (clone $services)->distinct('email')->count('email'),
            'packages' => TourPackage::active()->count(),
            'new_inquiries' => Inquiry::where('status', 'new')->count(),
            'visitors_today' => (int) DB::table('site_visits')->where('date', today()->toDateString())->value('visitors'),
            'visitors_month' => (int) DB::table('site_visits')->where('date', '>=', now()->startOfMonth()->toDateString())->sum('visitors'),
            'page_views_month' => (int) DB::table('site_visits')->where('date', '>=', now()->startOfMonth()->toDateString())->sum('page_views'),
        ];

        $dailyCounts = ServiceBooking::where('created_at', '>=', today()->subDays(13))
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $trend = collect(range(13, 0))->map(function ($daysAgo) use ($dailyCounts) {
            $date = Carbon::today()->subDays($daysAgo);

            return ['label' => $date->format('M j'), 'short' => $date->format('j'), 'value' => (int) ($dailyCounts[$date->toDateString()] ?? 0)];
        });

        $byService = ServiceBooking::selectRaw("service_type, COUNT(*) as bookings, SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as revenue")
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        $serviceRows = collect(config('travel.service_types'))->except('hotel')->map(fn ($label, $key) => [
            'label' => $label,
            'bookings' => (int) ($byService[$key]->bookings ?? 0),
            'revenue' => (float) ($byService[$key]->revenue ?? 0),
            'url' => route('dashboard.service-bookings.index', ['service' => $key]),
        ])->put('hotel', ['label' => 'Hotel', 'bookings' => Booking::count(), 'revenue' => null, 'url' => $user->isSuperAdmin() ? route('dashboard.bookings.index') : null])
            ->sortByDesc('bookings')
            ->values();

        return view('backend.dashboard', [
            'stats' => $stats,
            'trend' => $trend,
            'serviceRows' => $serviceRows,
            'recent' => $user->canAccessAdmin('bookings') ? ServiceBooking::latest()->take(8)->get() : collect(),
            'upcoming' => $user->canAccessAdmin('bookings')
                ? ServiceBooking::whereNotIn('status', ['cancelled', 'refunded'])->whereBetween('travel_date', [today(), today()->addDays(7)])->orderBy('travel_date')->take(8)->get()
                : collect(),
            'pendingVendors' => $user->isSuperAdmin() ? User::where('role', 'vendor')->where('vendor_status', 'pending')->count() : 0,
        ]);
    }
}
