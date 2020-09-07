<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use View;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        /* 将底部栏文字 共享到多个视图文件 */
        View::share('siteFooterCopyRight', 'china.huisou.com');

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAuthRoutes();

        $this->mapMerchantsRoutes();

        $this->mapWechatRoutes();

        $this->mapStaffRoutes();

        $this->mapShopRoutes();

        $this->mapHomeRoutes();


        $this->mapXCXRoutes();

        $this->mapAppletRoutes();

        $this->mapSellerAppRoutes();

        $this->mapAliAppRoutes();

        $this->mapBaiduAppRoutes();

        $this->mapByteRoutes();

        $this->mapJavaRoutes();

    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * 用户登录注册路由
     * @return [type] [description]
     */
    protected function mapAuthRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/auth.php');
        });
    }

    /**
     * 商家管理后台路由
     * @return [type] [description]
     */
    protected function mapMerchantsRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/merchants.php');
        });
    }

    /**
     * 微信接口路由
     * @return [type] [description]
     */
    protected function mapWechatRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/wechat.php');
        });
    }

    /**
     * 总后台路由
     * @return [type] [description]
     */
    protected function mapStaffRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/staff.php');
        });
    }

    /**
     * 前台商城路由
     * @return [type] [description]
     */
    protected function mapShopRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/shop.php');
        });
    }

    /**
     * 官网路由
     * @return [type] [description]
     */
    protected function mapHomeRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/home.php');
        });
    }

    /**
     * 微信小程序路由
     * @return [type] [description]
     */
    protected function mapXCXRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {

            require base_path('routes/xcx.php');
        });
    }

    /* 小程序报名
     * @return [type] [description]
     */
    protected function mapAppletRoutes(){

        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {

            require base_path('routes/applet.php');

        });
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180123
     * @desc 商家后台App路由
     */
    protected function mapSellerAppRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/sellerapp.php');
        });
    }


    /**
     * 支付宝小程序路由
     * @author 张永辉 2018年7月19日
     */
    protected function mapAliAppRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/aliapp.php');
        });
    }


    /**
     * 百度小程序路由
     * @author 许立 2018年10月10日
     */
    protected function mapBaiduAppRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/baidu.php');
        });
    }

    /**
     * @desc 字节跳动小程序路由
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 23 日
     */
    protected function mapByteRoutes(){
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/bytedance.php');
        });
    }

    /**
     * @desc java接口路由
     * @author 陈文豪  229634630@qq.com 2020年07月06日14:27:32
     */
    protected function mapJavaRoutes(){
        Route::group([
            'middleware' => 'java',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/java.php');
        });
    }
}
