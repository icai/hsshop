<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/2
 * Time: 14:40
 */

namespace App\Providers;
use App\S\Store\MicroPageTypeRelationService;
use Illuminate\Support\ServiceProvider;


class MicroPageTypeRelationServiceProvider extends ServiceProvider
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
        $this->app->bind('microPageTypeRelationService', function () {
            return new MicroPageTypeRelationService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['microPageTypeRelationService'];
    }
}