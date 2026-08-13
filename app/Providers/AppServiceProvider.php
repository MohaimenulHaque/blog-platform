<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, fn () => new SettingsService);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', fn (User $user) => $user->isAdmin());
        Gate::define('manage-content', fn (User $user) => $user->hasAnyRole(['admin', 'editor']));
        Gate::define('author-content', fn (User $user) => $user->hasAnyRole(['admin', 'editor', 'author']));
        Gate::define('publish-posts', fn (User $user) => $user->hasAnyRole(['admin', 'editor']));
        Gate::define('access-dashboard', fn (User $user) => true);
        Gate::define('manage-profile', fn (User $user, User $profile) => $user->id === $profile->id);
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
        Gate::define('manage-media', fn (User $user) => $user->isAdmin());
        Gate::define('manage-settings', fn (User $user) => $user->isAdmin());

        $this->applyDynamicSettings();
    }

    /**
     * Overlay persisted site settings on top of the application configuration.
     */
    protected function applyDynamicSettings(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }

            $settings = app(SettingsService::class)->all();

            if ($name = $settings['general.site_name'] ?? null) {
                config(['app.name' => $name]);
            }
        } catch (\Throwable $e) {
            // The database may not be available during early boot or migrations.
        }
    }
}
