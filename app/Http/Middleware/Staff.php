<?php

namespace App\Http\Middleware;

use App\Module\PermissionModule;
use Closure;
use Route;

/**
 * 总后台中间件
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月10日 20:41:24
 */
class Staff {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        /*判断用户是否登陆zhangyh*/
        $userData = $request->session()->get('userData');
        if (empty($userData['is_login'])){
            return redirect('/staff/login');
        }
        //获取用户权限
        $menu = (new PermissionModule())->getStaffPermission(session('userData')['id']);
        view()->share('__menu__',$menu);

        return $next($request);
    }
}
