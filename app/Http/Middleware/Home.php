<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use App\S\Foundation\Mobile_Detect;

/**
 * Class Home
 * @package App\Http\Middleware
 * @desc 官网中间件
 */
class Home {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        /**
         * 获取客服信息
         */
        $CusSerInfo = \CusSerManageService::getAll();
        view()->share('CusSerInfo', $CusSerInfo);
        
        $is_mobile = (new Mobile_Detect())->isMobile();
        $request->attributes->add(compact('is_mobile'));

        return $next($request);
    }
}
