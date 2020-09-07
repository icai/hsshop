<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/10
 * Time: 19:15
 */

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Lib\Redis\WXXCXCacheRedis;

class WXXCXCacheProvider extends ServiceProvider
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
        $this->app->bind('wXXCXCacheRedis', function () {
            return new WXXCXCacheRedis();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['wXXCXCacheRedis'];
    }
}