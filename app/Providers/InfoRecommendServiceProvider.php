<?php

namespace App\Providers;

use App\Services\Staff\InfoRecommendService;
use Illuminate\Support\ServiceProvider;

class InfoRecommendServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('InfoRecommendService', function () {
            return new InfoRecommendService();
        });
    }
}
