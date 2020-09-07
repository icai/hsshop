<?php

namespace App\Http\Middleware;

use Closure;
use Route;

class UserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /* 获取用户信息 */
        $userInfo = $request->session()->get('userInfo');

        /* 用户未登录，则跳到登录页面 */
        if ( !$userInfo['id'] ) {
            return redirect('/auth/login');
        }
        return $next($request);
    }
}
