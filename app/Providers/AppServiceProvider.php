<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Schema;
use App\Services\SyncService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SyncService::class, function ($app) {
            return new SyncService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
         /*ADD THIS LINES*/
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);

        // Auto-sync when app starts (only in console mode for PHPDesktop)
        if ($this->app->runningInConsole()) {
            $syncService = app(SyncService::class);
            $syncResult = $syncService->sync();
            
            // Optional: Log sync result
            if ($syncResult) {
                \Log::info('Initial sync pull successful');
            }
        }
    }
}
