<?php

namespace App\Http\Middleware;

use App\Module\AliApp\AliAppModule;
use Closure;
use App\S\Member\MemberService;
use App\S\Store\StoreService;
use Route;
use WeixinService;
use App\Services\Permission\WeixinRoleService;
use App\S\Weixin\ShopService;

class AliApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function handle($request, Closure $next)
    {
        $shopService = new ShopService();
        $token = $request->input('token','');
        if (!$token){
            error('token不能为空');
        }
        $request->offsetSet('reqFrom','aliapp');
        if ($token !=session('token') || session('mid') || session('wid')){
            $tokenData = (new AliAppModule())->getTokenData($token);
            if (!$tokenData['wid'] || $tokenData['mid']){

            }
            $request->session()->put('wid', $tokenData['wid']);
            $request->session()->put('mid', $tokenData['mid']);
            $request->session()->put('token',$token);
            $request->session()->put('reqFrom','aliapp');
            $request->session()->save();
        }
        $wid = session('wid');
        //店铺vip过期提示 add by wuxiaoping 2017.10.18
        $checkUrl = substr($request->path(), 0, 9);
        $is_overdue = 0;  //定义一个是否过期字段（默认0为没过期）

        $weixinRoleData = (new WeixinRoleService())->init()->where(['wid'=>session('wid')])->getList(false)[0]['data'];
        if ($weixinRoleData[0]) {
            if (strtotime($weixinRoleData[0]['end_time']) < time()){
                $is_overdue = 1; // 表示过期
                if (isset($weixinInfo) && $weixinInfo)  {
                    $shopName = $weixinInfo['shop_name'];
                }else{
                    //$weixinInfo = WeixinService::getStageShop(session('wid'));
                    $weixinInfo = $shopService->getRowById(session('wid'));
                    $shopName = $weixinInfo['shop_name'];
                }
                /**会员主页，订单列表、详情，团购订单详情可正常访问，其他页面提示店铺打烊**/
                if ($checkUrl != 'shop/memb' && $checkUrl != 'shop/orde' && $checkUrl != 'shop/grou') {
                    view()->share('shopName',$shopName);
                    abort(506);
                }else{
                    $url = $request->url();
                    //我的会员卡，优惠券也显示506页面（过期提示）
                    if (stripos($url,'mycards') || stripos($url,'coupons') || stripos($url,'grouppurchase/detail')) {
                        view()->share('shopName',$shopName);
                        abort(506);
                    }
                }
                view()->share('is_overdue',$is_overdue);
            }
        }

        //获取店铺信息
        //$__weixin = WeixinService::init()->getInfo($wid);
        $__weixin = $shopService->getRowById($wid);
        view()->share('__weixin', $__weixin);
        //分销绑定上下级
        $pid = $request->input('_pid_')??'';
        if (!empty($pid) && $pid != session('mid') && is_numeric($pid)){
            (new MemberService())->bindParent($pid,session('mid'));
        }
        //end
        //显现店铺是否展示
        $storeService = new StoreService();
        $__storeNumber__ = $storeService->getStoreNum();
        view()->share('__storeNumber__', $__storeNumber__);

        if ($__weixin['is_sms'] == 1 && !session('mobile')){
            $__isBind__=1;
        }else{
            $__isBind__=0;
        }
        //绑定手机号默认全部已绑定
        view()->share('__isBind__',$__isBind__);

        return $next($request);
    }
}
