<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Livewire\Backend\Api\ApiTokenManager;
use App\Livewire\Backend\Profile\DeleteUserForm;
use App\Livewire\Backend\Profile\LogoutOtherBrowserSessionsForm;
use App\Livewire\Backend\Profile\TwoFactorAuthenticationForm;
use App\Livewire\Backend\Profile\UpdatePasswordForm;
use App\Livewire\Backend\Profile\UpdateProfileInformationForm;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
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
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);
        Jetstream::ignoreRoutes();

        Vite::prefetch(concurrency: 3);

        // Re-point Jetstream's built-in Livewire components at their new
        // views under resources/views/backend, once every provider (including
        // Jetstream's own) has finished registering its default components.
        $this->app->booted(function () {
            Livewire::component('profile.update-profile-information-form', UpdateProfileInformationForm::class);
            Livewire::component('profile.update-password-form', UpdatePasswordForm::class);
            Livewire::component('profile.two-factor-authentication-form', TwoFactorAuthenticationForm::class);
            Livewire::component('profile.logout-other-browser-sessions-form', LogoutOtherBrowserSessionsForm::class);
            Livewire::component('profile.delete-user-form', DeleteUserForm::class);
            Livewire::component('api.api-token-manager', ApiTokenManager::class);
        });
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
