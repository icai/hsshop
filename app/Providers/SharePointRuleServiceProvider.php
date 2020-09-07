<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/31
 * Time: 18:43
 */

namespace App\Providers;
use App\S\Customer\SharePointRuleService;
use Illuminate\Support\ServiceProvider;

class SharePointRuleServiceProvider  extends ServiceProvider
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
        $this->app->bind('sharePointRuleService', function () {
            return new SharePointRuleService();
        });
    }

    /**
     * 获取由提供者提供的服务
     *
     * @return array
     */
    public function provides() {
        return ['sharePointRuleService'];
    }
}