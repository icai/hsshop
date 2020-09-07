<?php

namespace App\Http\Middleware;

use App\Lib\Redis\NewUserFlagRedis;
use Closure;

class ShopAfterMiddleware
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
        $response = $next($request);
        $newUserRedis = new NewUserFlagRedis();
        $mid = $request->session()->get('mid',0);
        $newUserRedis->get($mid) && $newUserRedis->delete($mid);
        return $response;
    }
}
