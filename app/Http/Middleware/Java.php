<?php

namespace App\Http\Middleware;

use App\Module\RSAModule;
use Closure;
use Route;
use PermissionService;
use Carbon\Carbon;

/**
 * 中间健类
 * Class Java
 * @package App\Http\Middleware
 * @author 陈文豪 229634630@qq.com 2020年07月03日20:49:28
 */
class Java
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     * @author 陈文豪 229634630@qq.com 2020年07月03日20:49:28
     */
    public function handle($request, Closure $next)
    {
        $RSAModule = new RSAModule();
        $input = $RSAModule->decrypt($request->input('param'));
        if (empty($input)) {
            return xcxerror('param is empty', 40200);
        }

        // 接口失效验证
        $nowTime = Carbon::now()->timestamp;
        if (!isset($input['time']) || $input['time'] < $nowTime - 10 || $input['time'] > $nowTime + 10) {
            // return xcxerror('param is invalid', 40200);
        }

        if (!isset($input['token']) || empty($input['token'])) {
            return xcxerror('token is lose', 40200);
        }

        // 和java定义一个统一的token
        if ($input['token'] != config('app.key')) {
            return xcxerror('token is error', 40200);
        }

        foreach ($input as $key => $val) {
            $request->offsetSet($key, $val);
        }

        return $next($request);
    }
}
