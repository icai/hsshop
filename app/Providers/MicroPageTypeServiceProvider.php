<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/26
 * Time: 9:16
 */

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\S\Store\MicroPageTypeService;

class MicroPageTypeServiceProvider extends ServiceProvider
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
        $this->app->bind('microPageTypeService', function () {
            return new MicroPageTypeService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['microPageTypeService'];
    }
}