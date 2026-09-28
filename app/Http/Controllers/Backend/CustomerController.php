<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ServiceBooking;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Customers are grouped by email so guests (who book without an account) and
 * registered travelers appear in one list with their full booking history.
 */
class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = ServiceBooking::query()
            ->select('email', DB::raw('MAX(full_name) as name'), DB::raw('MAX(phone) as phone'), DB::raw('MAX(nationality) as nationality'),
                DB::raw('COUNT(*) as bookings'), DB::raw("SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid"),
                DB::raw('MAX(created_at) as last_booking'), DB::raw('MAX(user_id) as user_id'))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('email', 'like', '%'.$request->string('q').'%')
                ->orWhere('full_name', 'like', '%'.$request->string('q').'%')
                ->orWhere('phone', 'like', '%'.$request->string('q').'%')))
            ->groupBy('email')
            ->orderByDesc('last_booking')
            ->paginate(30)
            ->withQueryString();

        return view('backend.customers.index', [
            'customers' => $customers,
            'registered' => User::where('role', 'user')->count(),
        ]);
    }

    public function show(string $email): View
    {
        $bookings = ServiceBooking::where('email', $email)->latest()->get();
        $user = User::where('email', $email)->first();

        abort_if($bookings->isEmpty() && ! $user, 404);

        return view('backend.customers.show', [
            'email' => $email,
            'user' => $user,
            'bookings' => $bookings,
            'hotelBookings' => $user ? Booking::with('property')->where('user_id', $user->id)->latest()->get() : collect(),
            'passports' => $bookings->pluck('passport_number')->filter()->unique()->values(),
            'preferences' => $bookings->countBy('service_label')->sortDesc(),
            'subscribed' => Subscriber::where('email', $email)->exists(),
        ]);
    }

    public function subscribers(): View
    {
        return view('backend.customers.subscribers', ['subscribers' => Subscriber::latest()->get()]);
    }

    public function exportSubscribers(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Subscribed at']);
            Subscriber::orderBy('id')->each(fn ($s) => fputcsv($out, [$s->email, $s->created_at]));
            fclose($out);
        }, 'ram-tours-subscribers.csv', ['Content-Type' => 'text/csv']);
    }

    public function destroySubscriber(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('status', "{$subscriber->email} unsubscribed.");
    }
}
