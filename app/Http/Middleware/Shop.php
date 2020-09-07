<?php

namespace App\Http\Middleware;

use App\Jobs\LoginStatistics;
use App\Lib\Redis\ShopRedis;
use App\Module\AliApp\AliAppModule;
use App\Module\BindMobileModule;
use App\Module\WeChatAuthModule;
use App\S\Member\MemberService;
use App\S\Store\StoreService;
use Closure;
use Route;
use WeixinService;
use App\Services\Permission\WeixinRoleService;
use App\S\Weixin\ShopService;
use App\Module\BaiduApp\BaiduClientModule;

/**
 * 前台商城中间件件
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年3月10日 20:41:24
 */
class Shop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @update 2018年08月06日11:11:23 陈文豪 修改底部LOGO链接
     * @update 何书哲 2018年9月19日 登录日志发送数据中心
     * @update 许立 2018年10月16日 百度小程序来源处理
     * @update 何书哲 2019年06月25日 如果不存在uid，则是数据有问题，删除现有redis数据，否则其他字段数据可能有问题
     */
    public function handle($request, Closure $next)
    {
        $shopService = new ShopService();
        $token = $request->input('aliToken');
        if (empty($token)) {
            $token = $request->header('aliToken');
        }
        if (empty($token)) {
            $token = session('token');
        }

        $baiduToken = $request->input('baiduToken') ?? '';
        if (empty($baiduToken)) {
            $baiduToken = $request->header('baiduToken');
        }
        if (empty($baiduToken)) {
            $baiduToken = session('baiduToken');
        }

        if (!empty($token)) {
            $aliAppModule = new AliAppModule();
            $aliAppModule->updateTokenTime($token);
            $reqFrom = 'aliapp';
            if ($token != session('token') || !session('mid') || !session('wid')) {
                $tokenData = $aliAppModule->getTokenData($token);
                if (empty($tokenData['wid']) || empty($tokenData['mid'])) {
                    if ($request->expectsJson()) {
                        xcxerror('登陆超时', 40004);
                    } else {
                        $url = $request->url() . '?';
                        foreach ($request->input() as $key => $val) {
                            if (!in_array($key, ['aliToken', 'jumpUrl'])) {
                                $url .= $key . '=' . $val . '&';
                            }
                        }
                        $url = trim($url, '?');
                        $url = trim($url, '&');
                        return redirect('/aliapp/authorization/login?fromUrl=' . $url);
                    }
                }
                $request->session()->put('wid', $tokenData['wid']);
                $request->session()->put('mid', $tokenData['mid']);
                $request->session()->put('umid', '0');
                $request->session()->put('aliappConfigId', $tokenData['aliappConfigId']);
                $request->session()->put('token', $token);
                $request->session()->save();
            }
            if (!empty($request->input('jumpUrl'))) {
                $url = $request->input('jumpUrl');
                if ($request->input('aliToken')) {
                    strpos($url, '?') === false ? $url .= '?aliToken=' . $request->input('aliToken') : $url .= '&aliToken=' . $request->input('aliToken');
                }

                return redirect($url);
            }

            $wid = session('wid');
        } else if (!empty($baiduToken)) {
            $baiduClientModule = new BaiduClientModule();
            $reqFrom = 'baiduapp';
            if ($baiduToken != session('baiduToken') || !session('mid') || !session('wid')) {
                $tokenData = $baiduClientModule->getTokenData($baiduToken);
                if (empty($tokenData['wid']) || empty($tokenData['mid'])) {
                    if ($request->expectsJson()) {
                        xcxerror('登陆超时', 40004);
                    } else {
                        $url = $request->url() . '?';
                        foreach ($request->input() as $key => $val) {
                            if (!in_array($key, ['baiduToken', 'jumpUrl'])) {
                                $url .= $key . '=' . $val . '&';
                            }
                        }
                        $url = trim($url, '?');
                        $url = trim($url, '&');
                        return redirect('/baidu/login?fromUrl=' . $url);
                    }
                }
                $request->session()->put('wid', $tokenData['wid']);
                $request->session()->put('mid', $tokenData['mid']);
                $request->session()->put('umid', '0');
                $request->session()->put('baiduToken', $baiduToken);
                $request->session()->save();
            }
            if (!empty($request->input('jumpUrl'))) {
                $url = $request->input('jumpUrl');
                if ($request->input('baiduToken')) {
                    strpos($url, '?') === false ? $url .= '?baiduToken=' . $request->input('baiduToken') : $url .= '&baiduToken=' . $request->input('baiduToken');
                }

                return redirect($url);
            }

            $wid = session('wid');
        } else {
            $reqFrom = 'wechat';
            // 获取店铺id
            $wid = Route::input('wid');
            $swid = session('wid');

            if (empty($wid)) {
                $wid = $request->input('wid');
                if (empty($wid)) {
                    $wid = $swid;
                    if (empty($wid)) {
                        error('店铺已被外星人拆毁');
                    }
                }
            }

            // 测试帐号
            if ($request->input('debug') == 123321 && (config('app.env') == 'dev' || config('app.env') == 'local')) {
                // 保存会员信息至session
                $request->session()->put($wid . '_mid', '3');
                $request->session()->put('mid', '3');
                $request->session()->put('umid', '187');
                $request->session()->put('wid', $wid);
                $request->session()->put('mobile', '13323232323');
                $swid = $wid;
                $request->session()->save();
            }
            //验证用户是否登陆
            if (!session('umid')) {
                $wechatAuth = new WeChatAuthModule();
                $auth = $wechatAuth->auth();
                if ($auth) {
                    return $auth;
                }
            }
            // 将店铺id存入session
            if ($swid != $wid) {
                //用户公众号好认证
                $wechatAuthModule = new WeChatAuthModule();
                $result = $wechatAuthModule->isAuth($wid, session('umid'));
                if ($result['success']) {
                    $shopAuth = $wechatAuthModule->shopAuth($result['data'], $wid);
                    if ($shopAuth) {
                        return $shopAuth;
                    }
                } else {
                    $wechatAuthModule->authShopMember(session('umid'), $wid);
                }
                //$weixinInfo = WeixinService::getStageShop($wid);
                $weixinInfo = $shopService->getRowById($wid);
                // update 何书哲 2019年06月25日 如果不存在uid，则是数据有问题，删除现有redis数据，否则其他字段数据可能有问题
                if (empty($weixinInfo['uid'])) {
                    (new ShopRedis())->del($wid);
                    error('店铺所属用户不存在');
                }
                $request->session()->put('wid', $wid);
                $request->session()->put('weixin_uid', $weixinInfo['uid']);
                $request->session()->save();
                //绑定手机号码修改
                (new BindMobileModule())->dealMobile(session('mid'), session('mobile'));
                //何书哲 2018年9月19日 登录日志发送数据中心
                dispatch((new LoginStatistics(session('mid'), getIP(), 3))->onQueue('LoginStatistics'));
            }
        }
        //访问来源
        $request->session()->put('reqFrom', $reqFrom);
        $request->session()->save();
        $request->offsetSet('reqFrom', $reqFrom);
        view()->share('reqFrom', $reqFrom);
        //店铺vip过期提示 add by wuxiaoping 2017.10.18
        $checkUrl = substr($request->path(), 0, 9);
        $is_overdue = 0;  //定义一个是否过期字段（默认0为没过期）

        $weixinRoleData = (new WeixinRoleService())->init()->where(['wid' => session('wid')])->getList(false)[0]['data'];
        if ($weixinRoleData[0]) {
            if (strtotime($weixinRoleData[0]['end_time']) < time()) {
                $is_overdue = 1; // 表示过期
                if (isset($weixinInfo) && $weixinInfo) {
                    $shopName = $weixinInfo['shop_name'];
                } else {
                    //$weixinInfo = WeixinService::getStageShop(session('wid'));
                    $weixinInfo = $shopService->getRowById($wid);
                    $shopName = $weixinInfo['shop_name'];
                }
                /**会员主页，订单列表、详情可正常访问，其他页面提示店铺打烊**/
                if ($checkUrl != 'shop/memb' && $checkUrl != 'shop/orde') {
                    view()->share('shopName', $shopName);
                    abort(506);
                } else {
                    $url = $request->url();
                    //我的会员卡，优惠券也显示506页面（过期提示）
                    if (stripos($url, 'mycards') || stripos($url, 'coupons') || stripos($url, 'grouppurchase/detail')) {
                        view()->share('shopName', $shopName);
                        abort(506);
                    }
                }
                view()->share('is_overdue', $is_overdue);
            }
        }

        //获取店铺信息
        //$__weixin = WeixinService::init()->getInfo($wid);
        $__weixin = $shopService->getRowById($wid);
//        $__weixin['link'] = 'https://www.huisou.cn/shop/activity/freeApply/823/2981/9?_pid_=245319';
        $__weixin['link'] = 'https://www.huisou.cn/shop/index/3714';
        view()->share('__weixin', $__weixin);
        //分销绑定上下级
        $pid = $request->input('_pid_') ?? '';
        if (!empty($pid) && $pid != session('mid') && is_numeric($pid)) {
            (new MemberService())->bindParent($pid, session('mid'));
        }
        //end
        //显现店铺是否展示
        $storeService = new StoreService();
        $__storeNumber__ = $storeService->getStoreNum();
        view()->share('__storeNumber__', $__storeNumber__);

        if ($__weixin['is_sms'] == 1 && !session('mobile')) {
            $__isBind__ = 1;
        } else {
            $__isBind__ = 0;
        }
        if ($reqFrom == 'aliapp' || $reqFrom == 'baiduapp') {
            $__isBind__ = 0;      //支付宝小程序全部关闭账号打通
        }
        //绑定手机号默认全部已绑定
        view()->share('__isBind__', $__isBind__);

        $request->attributes->add(['source' => 1]);
        return $next($request);
    }
}