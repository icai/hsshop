<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/1
 * Time: 11:47
 */

namespace App\Providers;
use App\S\Customer\PointApplyRuleService;
use Illuminate\Support\ServiceProvider;


class PointApplyRuleServiceProvider extends ServiceProvider
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
        $this->app->bind('pointApplyRuleService', function () {
            return new PointApplyRuleService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['pointApplyRuleService'];
    }
}