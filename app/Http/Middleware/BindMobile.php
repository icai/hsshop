<?php

namespace App\Http\Middleware;

use App\Services\WeixinService;
use Closure;
use App\S\Weixin\ShopService;

class BindMobile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月16日 百度小程序来源处理
     */
    public function handle($request, Closure $next)
    {
        $shopService = new ShopService();
        $mobile = session('mobile')??'';
        $wid = session('wid');
        //$shopData = (new WeixinService())->init()->model->select(['id','is_sms'])->find($wid);
        $shopData = $shopService->getRowById($wid);
        if ($shopData && $shopData['is_sms'] == 1 && !$mobile && $request->input('reqFrom') != 'aliapp' && $request->input('reqFrom') != 'baiduapp'){
            $url = '/shop/bindmobile/index/'.session('wid').'?url='.$request->fullUrl();
            return redirect($url);
        }
        return $next($request);
    }
}
