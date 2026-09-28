<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Counts public page views and unique daily visitors (one per session per day) for the
 * admin dashboard. Skips non-GET, AJAX, admin/account pages and obvious bots.
 */
class TrackSiteVisit
{
    protected const SKIP = ['dashboard*', 'vendor*', 'rental-partner*', 'account*', 'user/*', 'livewire*', 'up', 'sitemap.xml', 'booking/*'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || $request->ajax() || $request->is(...self::SKIP)
            || preg_match('/bot|crawl|spider|slurp|headless/i', (string) $request->userAgent())
            || $response->getStatusCode() !== 200) {
            return $response;
        }

        rescue(function () use ($request) {
            $today = today()->toDateString();
            $isNewVisitor = $request->session()->get('visit_day') !== $today;
            $request->session()->put('visit_day', $today);

            DB::table('site_visits')->upsert(
                ['date' => $today, 'visitors' => (int) $isNewVisitor, 'page_views' => 1],
                ['date'],
                ['visitors' => DB::raw('visitors + '.(int) $isNewVisitor), 'page_views' => DB::raw('page_views + 1')],
            );
        }, report: false);

        return $response;
    }
}
