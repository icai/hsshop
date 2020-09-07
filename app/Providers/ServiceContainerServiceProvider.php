<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceContainerServiceProvider extends ServiceProvider {
    /**
     * 服务提供者加是否延迟加载
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 在容器中注册绑定
     *
     * @return void
     */
    public function register() {
        $this->app->bind('ServiceContainer', function () {
            return new \App\Services\Lib\ServiceContainer();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['ServiceContainer'];
    }
}
