<?php

namespace App\Providers;

use App\S\Staff\CusSerManageService;
use Illuminate\Support\ServiceProvider;

class CusSerManageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('CusSerManagerService', function () {
            return new CusSerManageService();
        });
    }
}
