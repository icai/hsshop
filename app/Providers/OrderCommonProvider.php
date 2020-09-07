<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/7
 * Time: 9:25
 */

namespace App\Providers;
use App\Lib\Order\OrderCommon;
use Illuminate\Support\ServiceProvider;

class OrderCommonProvider extends ServiceProvider
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
        $this->app->bind('orderCommon', function () {
            return new OrderCommon();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['orderCommon'];
    }
}