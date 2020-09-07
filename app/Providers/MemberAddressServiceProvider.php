<?php

namespace App\Providers;

use App\Services\Shop\MemberAddressService;
use Illuminate\Support\ServiceProvider;

class MemberAddressServiceProvider extends ServiceProvider
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
        $this->app->bind('MemberAddressService', function () {
            return new MemberAddressService();
        });
    }
}
