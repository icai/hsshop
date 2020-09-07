<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/25
 * Time: 16:18
 */

namespace App\Providers;
use App\S\Store\StoreNavService;
use Illuminate\Support\ServiceProvider;

class StoreNavServiceProvider  extends ServiceProvider
{
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
        $this->app->bind('storeNavService', function () {
            return new StoreNavService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['storeNavService'];
    }
}