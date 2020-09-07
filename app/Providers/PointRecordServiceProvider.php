<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/26
 * Time: 9:43
 */

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\S\Customer\PointRecordService;

class PointRecordServiceProvider extends ServiceProvider
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
        $this->app->bind('pointRecordService', function () {
            return new PointRecordService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['pointRecordService'];
    }
}