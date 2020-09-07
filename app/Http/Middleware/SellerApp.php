<?php

namespace App\Http\Middleware;

use App\Module\BaseModule;
use App\Module\RSAModule;
use Closure;
use Route;
use PermissionService;

class SellerApp
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
        $RSAModule = new RSAModule();
        $uri = Route::current()->getUri();
        if(in_array($uri,config('sellerapp.exceptionUri'))){
            $parameter = json_decode($request->input('parameter'),true);
        }else{
            $parameter = $RSAModule->decrypt($request->input('parameter'));
        }
        $request->offsetSet('parameter',$parameter);
        if (!isset($parameter['token']) || !$parameter['token']){
            apperror('令牌不能为空',-40004);
        }
        $baseModule = new BaseModule();
        $tokenData = $baseModule->getTokenData($parameter['token']);
        if (!$tokenData || !$tokenData['is_login']){
            apperror('未登录或登录超时',-40001);
        }
        $request->offsetSet('_tokenData',$tokenData);
        if (isset($tokenData['wid']) && !PermissionService::checkPermission($tokenData['userInfo']['id'],$tokenData['wid'])){
            apperror('您无权限访问',-40006);
        }
        return $next($request);
    }
}
