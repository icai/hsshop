<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/9
 * Time: 14:28
 */

namespace App\Http\Middleware;

use App\Module\ByteDance\BaseModule;
use Closure;
use WXXCXCache;
use CommonModule;
use App\S\Member\MemberService;
use App\Services\Permission\WeixinRoleService;

class XCX
{
    /**
     * 返回请求过滤器
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @update 张永辉 2019年9月23日判断是会否是字节跳动小程序token
     */
    public function handle($request, Closure $next)
    {
        // 获取token
        $token = $request->input('token');
        if (empty($token)) {
            xcxerror('token不能为空', 40004);
        }

        if (strpos($token, '_bytedance') !== false) {
            $request->offsetSet('come_from', 'byteDance');
            if (!((new BaseModule())->checkLogin($token, $request))) {
                xcxerror('请重新登录');
            }
        } else {
            // 判断token是否过期
            $xcxUser = WXXCXCache::get($token, '3rd_session');
            if (empty($xcxUser)) {
                xcxerror('登录超时', 40004);
            }

            // 获取mid wid等信息 并保存
            $mid = CommonModule::getMidByToken($token);
            if (empty($mid)) {
                xcxerror('请重新登录');
            }
            $request->offsetSet('mid', $mid);
            $wid = CommonModule::getWidByToken($token);
            if (empty($wid)) {
                xcxerror('请重新登录');
            }
            $request->offsetSet('wid', $wid);
        }
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        // add by jonzhang 2018-01-02
        $expireData = (new WeixinRoleService())->isExpire($wid);
        if ($expireData['errCode'] < 0 || ($expireData['errCode'] == 0 && $expireData['data'] == 1)) {
            xcxerror('该店铺过期', 40004);
        }

        $pid = $request->input('_pid_', '');
        if (!empty($pid) && $pid != $mid && is_numeric($pid)) {
            (new MemberService())->bindParent($pid, $mid);
        }
        //判断是否新用户标识
        $request->attributes->add(['source' => 2]);
        return $next($request);
    }
}