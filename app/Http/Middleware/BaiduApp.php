<?php

namespace App\Http\Middleware;

use Closure;
use Route;

/**
 * 百度小程序中间件
 * 
 * @author 吴晓平 2018.10.15
 */
class BaiduApp {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $response = $next($request);
        // 执行动作
        if(!$request->session()->has('baiduToken')){
            return redirect("https://baiduapp.huisou.cn/baidu/login");
        }
        return $response;
    }
}
