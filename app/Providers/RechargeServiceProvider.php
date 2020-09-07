<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RechargeServiceProvider extends ServiceProvider {
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
    public function register()
    {
        // 订单服务类
        $this->app->bind('RechargeService', function () {
            return new \App\Services\Foundation\RechargeService();
        });

        // 订单操作记录服务类
        $this->app->bind('RechargeLogService', function () {
            return new \App\Services\Foundation\RechargeLogService();
        });

    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['RechargeService', 'RechargeLogService'];
    }
}
