<?php

namespace App\Http\Middleware;

use App\Model\Weixin;
use App\S\Weixin\ShopService;
use Carbon\Carbon;
use Closure;
use InfoRecommendService;
use PermissionService;
use Route;
use App\Services\Permission\WeixinRoleService;

class Merchants
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @update 何书哲 2019年08月14日 快过期天数处理
     */
    public function handle($request, Closure $next)
    {
        /**
         * 获取客服信息
         */
        $CusSerInfo = \CusSerManageService::getAll();
        view()->share('CusSerInfo', $CusSerInfo);

        // 获取用户信息
        $userInfo = $request->session()->get('userInfo');

        // 用户未登录，则跳到登录页面
        if (!$userInfo['id']) {
            return redirect('/auth/login');
        }

        // 店铺id为空，则跳转到店铺管理页面
        $rwid = Route::input('wid');
        if (empty(session('wid')) && empty($rwid)) {
            return redirect('/merchants/team');
        }

        // 第一次进店铺 或者 店铺切换
        if (($rwid != session('wid')) && !empty($rwid)) {
            /* 存wid */
            $request->session()->put('wid', $rwid);
            // modify zhangyh 当切换店铺时重新写入权限
            PermissionService::addPermissionToRedis();
            // 查询店铺信息
            // $weixinInfo = D('Weixin', 'uid', $userInfo['id'])->getInfo($rwid);
            $weixinInfo = (new ShopService())->getRowById($rwid);
            if (empty($weixinInfo)) {
                return redirect('/merchants/team');
            }
            // 存店铺logo
            $request->session()->put('logo', $weixinInfo['logo']);
            // 存店铺名称
            $request->session()->put('shop_name', $weixinInfo['shop_name']);
            // 存店铺名称
            $request->session()->put('shop_created_at', $weixinInfo['created_at'] ?? 0);
            // 手动保存session
            $request->session()->save();
        }

        // 判断用户权限
        if (!PermissionService::checkPermission()) {
            echo PermissionService::getNoPermissionInfo();
            exit();
        }

        // 店铺vip过期提示
        $checkUrl = substr($request->path(), 0, 15);
        // add fuguowei 20171221 店铺打烊截取之删除按钮发送验证码
        $deleteSendCode = substr($request->path(), 0, 23);
        // end
        $is_overdue = 0; // 定义是否为过期字段
        $weixinRoleData = (new WeixinRoleService())->init()->where(['wid' => session('wid')])->getList(false)[0]['data'];
        if ($weixinRoleData[0]) {
            if (strtotime($weixinRoleData[0]['end_time']) < time()) {
                $is_overdue = 1;
                if ($checkUrl != 'merchants/index' && $deleteSendCode != 'merchants/team/sendcode') {
                    abort('505');
                }
                view()->share('is_overdue', $is_overdue);
                $request->attributes->add(['is_overdue' => $is_overdue]); // 添加参数传到控制器

            } else {  // 小于30天时提示即将到期
                if (strtotime($weixinRoleData[0]['end_time']) < strtotime('+30 days')) {
                    // update 何书哲 2019年08月14日 快过期天数处理
                    $days = (new Carbon)->diffInDays(Carbon::parse($weixinRoleData[0]['end_time']));
                    view()->share([
                        'soonOverdue' => 1,
                        'days' => $days + 1
                    ]);
                }
            }
        }

        // 记录最近一次访问时间
        if ($weiexin = Weixin::select(['id'])->find($rwid)) {
            $weiexin->recordLog();
        }
        
        // 获取资讯信息
        $inforData = InfoRecommendService::getInfor();
        view()->share('_information', $inforData);
        $isOpen = 0; // 广告是否显示
        view()->share('__isOpen__', $isOpen);
        return $next($request);
    }
}
