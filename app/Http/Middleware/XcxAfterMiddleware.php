<?php

namespace App\Http\Middleware;

use App\Lib\Redis\NewUserFlagRedis;
use Closure;

class xcxAfterMiddleware
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
        $mid = $request->input('mid',0);
        $requestUrl = explode('?',$request->getRequestUri());
        if ($requestUrl[0] != '/xcx/bar/barList') {
            $newUserRedis->get($mid) && $newUserRedis->delete($mid);
        }
        return $response;
    }
}
