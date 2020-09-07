<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use WeixinService;
use App\Model\WeixinConfigSub;

/**
 * 店铺后台微信中间件
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年4月10日 13:39:11
 */
class Wechat {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $wid = session('wid');
        $weixinConfigSub = new WeixinConfigSub();
        $data = [];
        $obj = $weixinConfigSub->wheres(['wid' => $wid])->first();
        if($obj){
            $data = $obj->toArray();
        }
        if (empty($data)) {
             return redirect('/merchants/wechat/wxsettled')->with('errorMsg', '请先绑定公众号');
        }

        return $next($request);
    }
}
