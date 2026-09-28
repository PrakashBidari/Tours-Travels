<?php

use App\Http\Middleware\EnsureAdminPermission;
use App\Http\Middleware\EnsureRentalPartnerIsApproved;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureVendorIsApproved;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackSiteVisit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'vendor.approved' => EnsureVendorIsApproved::class,
            'rental-partner.approved' => EnsureRentalPartnerIsApproved::class,
            'admin.can' => EnsureAdminPermission::class,
        ]);

        $middleware->web(append: [SetLocale::class, TrackSiteVisit::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
