<?php

namespace App\Providers;

use App\Listeners\NotifySuperAdminOfNewVendor;
use App\Models\FooterColumn;
use App\Models\HeaderButton;
use App\Models\HeaderSetting;
use App\Models\NavMenuItem;
use App\Models\PageSetting;
use App\Models\SocialLink;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Verified::class, NotifySuperAdminOfNewVendor::class);

        // Uploaded images are served from whatever host/port the visitor is on, not APP_URL —
        // otherwise they 404 whenever the two differ (e.g. `artisan serve` on :8000, LAN IP).
        if (! $this->app->runningInConsole()) {
            config(['filesystems.disks.public.url' => $this->app['request']->root().'/storage']);
        }

        // Page views (e.g. the home hero) read the same settings row as the layout.
        View::composer('frontend.*', function ($view): void {
            $view->with('pageSettings', once(fn () => PageSetting::current()));
        });

        View::composer('frontend.layouts.site', function ($view): void {
            $settings = once(fn () => PageSetting::current());

            $allNavFlat = NavMenuItem::where('is_active', true)->orderBy('sort_order')->get();
            $attachChildren = function ($parentId) use (&$attachChildren, $allNavFlat) {
                return $allNavFlat->filter(fn ($item) => $item->parent_id == $parentId)->values()
                    ->each(fn ($item) => $item->setRelation('children', $attachChildren($item->id)));
            };

            $view->with([
                'pageSettings' => $settings,
                'footerColumns' => FooterColumn::ordered()->with('links')->get(),
                'footerSocialLinks' => SocialLink::ordered()->get(),
                'footerTagline' => $settings->footer_tagline,
                'footerIconUrl' => $settings->footer_icon_url,
                'footerCopyright' => $settings->rendered_copyright,
                'headerSettings' => HeaderSetting::current(),
                'navItems' => $attachChildren(null),
                'headerButtons' => HeaderButton::ordered()->where('is_active', true)->get(),
            ]);
        });
    }
}
