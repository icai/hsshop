<?php

namespace App\Http;


use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'userlogin'  => \App\Http\Middleware\UserLogin::class,
        'merchants'  => \App\Http\Middleware\Merchants::class,
        'wechat'     => \App\Http\Middleware\Wechat::class,
        'staff'      => \App\Http\Middleware\Staff::class,
        'shop'       => \App\Http\Middleware\Shop::class,
        'home'       => \App\Http\Middleware\Home::class,
        'microforum.serv' => \App\Http\Middleware\MicroForumServer::class,
		'microforum.client' => \App\Http\Middleware\MicroForumClient::class,
		'bindmobile' => \App\Http\Middleware\BindMobile::class,
        'xcx'       => \App\Http\Middleware\XCX::class,
        'sellerapp'       => \App\Http\Middleware\SellerApp::class,
        'stationing'        => \App\Http\Middleware\Stationing::class,
        'xcxAfter'  =>\App\Http\Middleware\XcxAfterMiddleware::class,
        'shopAfter'  =>\App\Http\Middleware\ShopAfterMiddleware::class,
        'aliapp'        => \App\Http\Middleware\AliApp::class,
        'baiduapp'        => \App\Http\Middleware\BaiduApp::class,
        'java'        => \App\Http\Middleware\Java::class,
    ];
}
