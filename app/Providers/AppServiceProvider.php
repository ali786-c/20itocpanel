<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Migration\SourceAdapterInterface::class,
            \App\Services\Migration\Adapters\TwentyISourceAdapter::class
        );
        
        $this->app->bind(
            \App\Contracts\Migration\DestinationAdapterInterface::class,
            \App\Services\Migration\Adapters\CPanelDestinationAdapter::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
