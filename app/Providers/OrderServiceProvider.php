<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider {
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
        $this->app->bind('OrderService', function () {
            return new \App\Services\Order\OrderService();
        });

        // 订单操作记录服务类
        $this->app->bind('OrderLogService', function () {
            return new \App\Services\Order\OrderLogService();
        });
		$this->app->bind('OrderDetailService', function () {
			return new \App\Services\Order\OrderDetailService();
		});
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['OrderService', 'OrderLogService','OrderDetailService'];
    }
}
