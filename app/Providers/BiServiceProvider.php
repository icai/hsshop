<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('BiService', function () {
            return new \App\S\Foundation\Bi();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['BiService'];
    }

}