<?php

return [




    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => 'hsshop',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Shanghai',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => env('APP_LOG', 'daily'),

    'log_max_files' => env('APP_LOG_MAX', 300),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Mews\Captcha\CaptchaServiceProvider::class,//验证码插件
        SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,//二维码
        //Barryvdh\Debugbar\ServiceProvider::class,//调试插件
        // Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,//ide插件

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        App\Providers\ServiceContainerServiceProvider::class,
        App\Providers\RedisServiceProvider::class,//redis操作类
        App\Providers\OrderServiceProvider::class,//订单
        App\Providers\WeixinServiceProvider::class,//店铺
        App\Providers\MemberServiceProvider::class,//客户
        App\Providers\WeixinConfigServiceProvider::class,//通用设置
        App\Providers\PermissionServiceProvider::class,//权限
        App\Providers\MicroPageNoticeServiceProvider::class, //公共广告
        App\Providers\StaffOperLogServiceProvider::class, //总后台日志
        App\Providers\MicroPageServiceProvider::class, //微页面
        App\Providers\MicroPageTemplateServiceProvider::class, //自定义模板
        App\Providers\MemberHomeServiceProvider::class, //用户主页
        App\Providers\ProductServiceProvider::class, //商品
        App\Providers\ProductEvaluateServiceProvider::class, //商品评价
        App\Providers\ProductEvaluateDetailServiceProvider::class, //商品评价详情
        App\Providers\LinkToServiceProvider::class, //链接到
        App\Providers\MemberAddressServiceProvider::class, //收货地址
        App\Providers\InfoRecommendServiceProvider::class, //信息推荐
        App\Providers\PaymentServiceProvider::class, //支付
        App\Providers\QrCodeServiceProvider::class,//二维码服务类
        App\Providers\RechargeServiceProvider::class,//充值
        App\Providers\MicroPageTypeRelationServiceProvider::class,//微页面与微页面分类关系类 add by jonzhang
        Overtrue\LaravelWechat\ServiceProvider::class,
		App\Providers\StoreNavServiceProvider::class,//店铺导航类 add by jonzhang
        App\Providers\ProductGroupServiceProvider::class,//商品分组类 add by jonzhang
        App\Providers\FileInfoServiceProvider::class,//上传文件类 add by jonzhang
        App\Providers\MallModuleProvider::class,//移动端页面处理类 add by jonzhang
        App\Providers\PointRecordServiceProvider::class,//积分记录  add by jonzhang
        App\Providers\SignRecordServiceProvider::class,//签到记录  add by jonzhang
        App\Providers\OrderPointExtraRuleServiceProvider::class,//订单积分额外规则  add by jonzhang
        App\Providers\OrderPointRuleServiceProvider::class,//订单积分规则  add by jonzhang
        App\Providers\SharePointRuleServiceProvider::class,//分享积分  add by jonzhang
        App\Providers\PointApplyRuleServiceProvider::class,//积分使用  add by jonzhang
        App\Providers\SignServiceProvider::class,//签到使用  add by jonzhang
        App\Providers\OrderCommonProvider::class,//生成订单号使用  add by jonzhang
		App\Providers\CusSerManageServiceProvider::class, //后台客服
        App\Providers\MicroPageTypeServiceProvider::class, //微页面分类  add by jonzhang
        App\Providers\CurlBuilderProvider::class, //curl类  add by jonzhang
        App\Providers\WXXCXUserServiceProvider::class, //小程序用户信息类  add by jonzhang
        App\Providers\WXXCXCacheProvider::class, //小程序缓存类  add by jonzhang
        App\Providers\XCXPaymentModuleProvider::class, //小程序支付类  add by jonzhang
        App\Providers\CommonModuleProvider::class, //通用工具类  add by jonzhang
        App\Providers\BiServiceProvider::class,
        App\Providers\WXXCXMicroPageServiceProvider::class,//小程序微页面
        Maatwebsite\Excel\ExcelServiceProvider::class, //phpExcel
        App\Providers\MicPageServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [
        'App'                          => Illuminate\Support\Facades\App::class,
        'Artisan'                      => Illuminate\Support\Facades\Artisan::class,
        'Auth'                         => Illuminate\Support\Facades\Auth::class,
        'Blade'                        => Illuminate\Support\Facades\Blade::class,
        'Bus'                          => Illuminate\Support\Facades\Bus::class,
        'Cache'                        => Illuminate\Support\Facades\Cache::class,
        'Config'                       => Illuminate\Support\Facades\Config::class,
        'Cookie'                       => Illuminate\Support\Facades\Cookie::class,
        'Crypt'                        => Illuminate\Support\Facades\Crypt::class,
        'DB'                           => Illuminate\Support\Facades\DB::class,
        'Eloquent'                     => Illuminate\Database\Eloquent\Model::class,
        'Event'                        => Illuminate\Support\Facades\Event::class,
        'File'                         => Illuminate\Support\Facades\File::class,
        'Gate'                         => Illuminate\Support\Facades\Gate::class,
        'Hash'                         => Illuminate\Support\Facades\Hash::class,
        'Lang'                         => Illuminate\Support\Facades\Lang::class,
        'Log'                          => Illuminate\Support\Facades\Log::class,
        'Mail'                         => Illuminate\Support\Facades\Mail::class,
        'Notification'                 => Illuminate\Support\Facades\Notification::class,
        'Password'                     => Illuminate\Support\Facades\Password::class,
        'Queue'                        => Illuminate\Support\Facades\Queue::class,
        'Redirect'                     => Illuminate\Support\Facades\Redirect::class,
        'Redisx'                       => Illuminate\Support\Facades\Redis::class,
        'Request'                      => Illuminate\Support\Facades\Request::class,
        'Response'                     => Illuminate\Support\Facades\Response::class,
        'Route'                        => Illuminate\Support\Facades\Route::class,
        'Schema'                       => Illuminate\Support\Facades\Schema::class,
        'Session'                      => Illuminate\Support\Facades\Session::class,
        'Storage'                      => Illuminate\Support\Facades\Storage::class,
        'URL'                          => Illuminate\Support\Facades\URL::class,
        'Validator'                    => Illuminate\Support\Facades\Validator::class,
        'View'                         => Illuminate\Support\Facades\View::class,
        'Captcha'                      => Mews\Captcha\Facades\Captcha::class,//验证码插件
        'Debugbar'                     => Barryvdh\Debugbar\Facade::class,//调试插件
        'SC'                           => App\Facades\ServiceContainerFacade::class,
        'RedisService'                 => App\Facades\RedisServiceFacade::class,//redis操作类
        'OrderService'                 => App\Facades\OrderServiceFacade::class,//订单
        'OrderDetailService'           => App\Facades\OrderDetailServiceFacade::class,//订单详情
        'OrderLogService'              => App\Facades\OrderLogServiceFacade::class,//订单操作记录
        'MemberService'                => App\Facades\MemberServiceFacade::class,//会员管理
        'MemberCardService'            => App\Facades\MemberCardServiceFacade::class,//会员卡管理
        'MemberImportService'          => App\Facades\MemberImportServiceFacade::class,//会员导入
        'MemberLabelService'           => App\Facades\MemberLabelServiceFacade::class,//标签管理
        'MemberCardRecordService'      => App\Facades\MemberCardRecordServiceFacade::class,//会员卡领取
        'MemberFansService'            => App\Facades\MemberFansServiceFacade::class,//粉丝管理
        'WeixinService'                => App\Facades\WeixinServiceFacade::class,
        'WeixinConfigService'          => App\Facades\WeixinConfigServiceFacade::class,//通用设置
        'PermissionService'            => App\Facades\PermissionServiceFacade::class,//权限管理
        'MicroPageNoticeService'       => App\Facades\MicroPageNoticeServiceFacade::class,//公共广告
        'StaffOperLogService'          => App\Facades\StaffOperLogServiceFacade::class,//总后台日志
        'MicroPageService'             => App\Facades\MicroPageServiceFacade::class,//微页面
        'MicroPageTemplateService'     => App\Facades\MicroPageTemplateServiceFacade::class,//自定义模板
        'MemberHomeService'            => App\Facades\MemberHomeServiceFacade::class,//用户主页
        'ProductService'               => App\Facades\ProductServiceFacade::class,//商品
        'ProductEvaluateService'       => App\Facades\ProductEvaluateServiceFacade::class,//商品评价
        'ProductEvaluateDetailService' => App\Facades\ProductEvaluateDetailServiceFacade::class,//商品评价详情
        'LinkToService'                => App\Facades\LinkToServiceFacade::class,//链接到
        'MemberAddressService'         => App\Facades\MemberAddressServiceFacade::class,//收货地址
        'QrCode'                       => SimpleSoftwareIO\QrCode\Facades\QrCode::class, //二维码
        'QrCodeService'                => App\Facades\QrCodeServiceFacade::class, //二维码服务类
        'InfoRecommendService'         => App\Facades\InfoRecommendServiceFacade::class, //推荐信息
        'PaymentService'               => App\Facades\PaymentServiceFacade::class, //支付
        'RechargeService'              => App\Facades\RechargeServiceFacade::class,//充值
        'RechargeLogService'           => App\Facades\RechargeLogServiceFacade::class,//充值日志
        'MicroPageTypeRelationService' => App\Facades\MicroPageTypeRelationServiceFacade::class,//微页面与微页面分类关系类 add by jonzhang
        'EasyWeChat'                   => Overtrue\LaravelWechat\Facade::class,
		'StoreNavService'              => App\Facades\StoreNavServiceFacade::class, //店铺导航类 add by jonzhang
        'ProductGroupService'          => App\Facades\ProductGroupServiceFacade::class,//商品分组类 add by jonzhang
        'FileInfoService'              => App\Facades\FileInfoServiceFacade::class,//上传文件类 add by jonzhang
        'MallModule'                   => App\Facades\MallModuleFacade::class,//微页面数据处理类 add by jonzhang
		'PointRecordService'           => App\Facades\PointRecordServiceFacade::class,//积分记录 add by jonzhang
        'SignRecordService'            => App\Facades\SignRecordServiceFacade::class,//签到记录 add by jonzhang
        'OrderPointRuleService'        => App\Facades\OrderPointRuleServiceFacade::class,//订单积分记录 add by jonzhang
        'OrderPointExtraRuleService'   => App\Facades\OrderPointExtraRuleServiceFacade::class,//订单积分额外规则 add by jonzhang
        'SharePointRuleService'        => App\Facades\SharePointRuleServiceFacade::class,//分享积分记录 add by jonzhang
        'PointApplyRuleService'        => App\Facades\PointApplyRuleServiceFacade::class,//积分使用 add by jonzhang
        'SignService'                  => App\Facades\SignServiceFacade::class,//签到使用 add by jonzhang
        'OrderCommon'                  => App\Facades\OrderCommonFacade::class,//生成订单号使用 add by jonzhang
		'CusSerManageService'          => App\Facades\CusSerManageServiceFacade::class, //后台客服
        'MicroPageTypeService'         => App\Facades\MicroPageTypeServiceFacade::class, //微页面类型  add by jonzhang
        'CurlBuilder'                  => App\Facades\CurlBuilderFacade::class, //curl  add by jonzhang
        'WXXCXUserService'             => App\Facades\WXXCXUserServiceFacade::class, //小程序用户信息类  add by jonzhang
        'WXXCXCache'                   => App\Facades\WXXCXCacheFacade::class, //小程序缓存类  add by jonzhang
        'XCXPaymentModule'             => App\Facades\XCXPaymentModuleFacade::class, //小程序支付类  add by jonzhang
        'CommonModule'                 => App\Facades\CommonModuleFacade::class, //通用工具类  add by jonzhang
        'Bi'                           => App\Facades\BiServiceFacade::class, //
        'WXXCXMicroPageService'        => App\Facades\WXXCXMicroPageServiceFacade::class, //小程序微页面  add by jonzhang
        'Excel'                        => Maatwebsite\Excel\Facades\Excel::class,
        'MicPage'                      => App\Facades\MicPageFacade::class, //curl  add by jonzhang
    ],

    /*
    |--------------------------------------------------------------------------
    | 订单自动确认收货天数
    |--------------------------------------------------------------------------
    */

    'auto_confirm_receive_days' => 15,
    'source_url'    =>  env('SOURCE_URL'),
    'source_img_url'    =>  env('SOURCE_IMG_URL'),
    'cdn_img_url'    =>  env('CDN_IMG_URL','https://hsshop.cdn.huisou.cn/'),
    'source_video_url'    =>  env('SOURCE_VIDEO_URL'),
    'cdn_bucket'        => env('CDN_BUCKET','hsshop'),

    'auth_appid'   =>  env('AUTH_APPID'),
    'auth_secret'   =>  env('AUTH_SECRET'),

    'ali_app_id'    => env('ALI_APP_ID','2018071960652473'),

    'public_auth_appid'   =>  env('PUBLIC_APP_ID'),
    'public_auth_secret'  =>  env('PUBLIC_SECRET'),

    'sms_account_sid'   =>  '8a48b5514f4fc588014f67a8f5182ea2',
    'sms_account_token' =>  '9e4008a1f862450fa9bb4b09a7693465',
    'sms_appid'         =>  '8aaf07085c346c5a015c5c763fac0aeb',

    'kuaidi_customer'         => '84E98FFB0F5A3AE68378568A796B1001',
    'kuaidi_key'              =>  'XSfbDRNw9721',



    'bi'  =>  env('BI',0),

    //微信小程序 第三方平台配置信息
    'third_appId'=> env('third_appId'),
    'third_appSecret' => env('third_appSecret'),
    'third_token' => env('third_token'),
    'third_encodingAesKey' => env('third_encodingAesKey'),

	/**
	 * websocket
	 */
	'websocket_port' => env('WEBSOCKET_PORT', 9502),

	/**
	 * 控制微页面验证是否启用
	 */
	'enable_diy_component_validate' => true,

    'chat_url'              => env('chatUrl', ''), //聊天接口
    'del_wid'               => env('delWid', 0), //聊天接口
    'li_pid'                => [env('liPid', 0)], //li  商品id 订单显示会员信息
    'li_wid'                => [env('liWid', 0),env('liWid2', 0),env('liWid3', 0),env('liWid4', 0),env('liWid5', 0)], //li  店铺id 允许存在li活动
    'open_share'            => [env('openShareWid1', 0),env('openShareWid2', 0),env('openShareWid3', 0),env('openShareWid4', 0),env('openShareWid5', 0)],
    'seller_app_url'        => env('SELLER_APP_HOST', 'http://www.huisou.cn/'), //商家APP接口访问域名
    'app_login_time_out'    => 259200, //APP登陆超时时间
    'data_center_url'       => env('DATA_CENTER_URL',''),
    'dc_url'                => env('dc_Url','https://bdsa-user-test.huisou.cn'),
    'is_open_send_log'      => env('IS_OPEN_SEND_LOG',false),
    'log_center_url'        => env('LOG_CENTER_URL','192.168.0.240'),
	'app_jpush_key'         => env('app_jpush_key','d22650905c6e0445fcaa6ad6'),
	'master_jpush_secret'   => env('master_jpush_secret','530c629744d598cd80bed604'),
	'single_page_url'       => env('SINGLE_PAGE_URL','sellerapp-test.huisou.cn'),
    'fuzzy_search_url'      => env('FUZZY_SEARCH_URL', ''),

    'receive_company'       =>env('receive_company','杭州盈搜科技有限公司'),
    'receive_bank'          =>env('receive_bank','中国工商银行股份有限公司杭州九堡支行'),
    'receive_account'       =>env('receive_account','1202 0047 0990 0045 173'),

    'logo_page_type'        => env('logoPageType', 1),//底部logo类型
    'logo_page_id'          => env('logoMicroPageId',275),//底部logo小程序微页面id
    'request_domain'        => env('requestDomain', 'www.huisou.cn'),//小程序授权域名设置

	'RESEARCH_ID_SHOP'      => env('RESEARCH_ID_SHOP', 0), // 官网微商城预约活动id
	'RESEARCH_SHOP_RULE_ID_1' => env('RESEARCH_SHOP_RULE_ID_1', 0), // 官网微商城预约活动称呼输入框规则id
	'RESEARCH_SHOP_RULE_ID_2' => env('RESEARCH_SHOP_RULE_ID_2', 0), // 官网微商城预约活动手机号输入框规则id
	'RESEARCH_SHOP_RULE_ID_3' => env('RESEARCH_SHOP_RULE_ID_3', 0), // 官网微商城预约活动行业输入框规则id
	'RESEARCH_ID_XCX'         => env('RESEARCH_ID_XCX', 0), // 官网小程序预约活动id
	'RESEARCH_XCX_RULE_ID_1'  => env('RESEARCH_XCX_RULE_ID_1', 0), // 官网小程序预约活动称呼输入框规则id
	'RESEARCH_XCX_RULE_ID_2'  => env('RESEARCH_XCX_RULE_ID_2', 0), // 官网小程序预约活动手机号输入框规则id
	'RESEARCH_XCX_RULE_ID_3'  => env('RESEARCH_XCX_RULE_ID_3', 0), // 官网小程序预约活动行业输入框规则id
	'RESEARCH_MID'         => env('RESEARCH_MID', 0), // 官网预约活动用户id

    'baidu_app_id'     => env('BAIDU_APPID','14305231'),
    'baidu_app_key'    => env('BAIDU_KEY','gxCkGTGU0puQ4holTo9cLyIGr1bDH64c'),
    'baidu_app_secret' => env('BAIDU_SECRET','BTgsaXipgahvU6EyU0UKWrzDDXdXV5Ll'),

    'corp_id' => env('CORP_ID', 'ww2dd06e5b979a47d9'),//企业微信id
    'corp_secret' => env('CORP_SECRET', 'qsPttEqBkvw4zx2MbwETF_YT8TSiEqxt1TwJ020deDo'),//企业微信应用secret
    'agent_id' => env('AGENT_ID', '1000002'),//企业微信应用agentId
    'corp_uids' => env('CORP_UIDS', 'ReXinWangMinChenXianSheng|HeShuZhe|loveutopia|wQiao'),//企业微信发送对象

    'thumbnail_400' =>  env('THUMBNAIL_400',""),
    'thumbnail_300' =>  env('THUMBNAIL_300',""),
    'thumbnail_200' =>  env('THUMBNAIL_200',""),

    'wework_error_log_monitor_interval' => env('WEWORK_ERROR_LOG_MONITOR_INTERVAL', 1800), // 错误日志有效时间

    /*
    |--------------------------------------------------------------------------
    | 小程序聊天的socket域名
    |--------------------------------------------------------------------------
    |
    */
    'xcx_socket_url' => env('XCX_WEB_URL', 'wss://kf.huisou.cn'),

];
