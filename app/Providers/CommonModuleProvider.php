<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/17
 * Time: 14:53
 */

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Module\CommonModule;

class CommonModuleProvider extends ServiceProvider
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
        $this->app->bind('commonModule', function () {
            return new CommonModule();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['commonModule'];
    }
}