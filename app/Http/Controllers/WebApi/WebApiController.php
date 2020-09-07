<?php

namespace App\Http\Controllers\WebApi;

use App\Http\Controllers\Controller;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\Member;
use App\Model\Weixin;
use App\Model\WXXCXConfig;
use App\Module\MessagePushModule;
use App\Module\NotificationModule;
use App\Module\WechatBakModule;
use App\S\MarketTools\MessagesPushService;
use App\S\WXXCX\WXXCXCollectFormIdService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Permission\WeixinUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use WeixinService;
use MemberService;
use App\S\Weixin\ShopService;


use App\Module\ChatThirdModule;

/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/20
 * Time: 11:28
 */
class WebApiController extends Controller
{
    public function getChatData(Request $request)
    {
        header("Access-Control-Allow-Origin: *"); 
        $userId = $request->input('userId', 0);     //取用户信息
        $shopId = $request->input('shopId', 0);     //取店铺信息
        $productId = $request->input('productId', 0);  //取商品信息
        $orderId = $request->input('orderId', 0);    //取订单信息
        $userOrder = $request->input('userOrder', 0);  //获取用户订单
        return (new ChatThirdModule())->getChatData($userId, $shopId, $productId, $orderId, $userOrder);
    }

    public function getUserOrderData(Request $request)
    {
        header("Access-Control-Allow-Origin: *"); 
        $userId = $request->input('userId', 0);     //取用户信息
        $shopId = $request->input('shopId', 0);     //取店铺信息
        return (new ChatThirdModule())->getUserOrderData($shopId, $userId);
    }

    /** todo 获取店铺信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-07-20
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getStore(Request $request,ShopService $shopService)
    {
        $rangeData = ['huisou'];
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $id = $request->input('id');
        $appId = $request->input('app_id');
        $errMsg = '';
        if (empty($appId)) {
            $errMsg .= 'app_id为空';
        }
        if (empty($id)) {
            $errMsg .= 'id为空';
        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        if (!in_array($appId, $rangeData)) {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '非法请求';
            return $returnData;
        }
        //modify by zhangyh 20170807
        //$storeData = WeixinService::getStore($id);
        $store = $shopService->getRowById($id);
        $storeData['data'] = $store;
        // 获取管理员信息;
        $manager = (new WeixinUserService())->getUser($id);
        $storeData['data']['manager'] = $manager;
        //end
        return $storeData;

    }

    /** todo 获取用户信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-07-20
     */
    public function getUser(Request $request)
    {
        $rangeData = ['huisou'];
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $id = $request->input('id');
        $appId = $request->input('app_id');
        $errMsg = '';
        if (empty($appId)) {
            $errMsg .= 'app_id为空';
        }
        if (empty($id)) {
            $errMsg .= 'id为空';
        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        if (!in_array($appId, $rangeData)) {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '非法请求';
            return $returnData;
        }
        $returnData['data'] = MemberService::getRowById($id);
        return $returnData;
    }


    /**
     * 获取新订单消息数量
     * Author: MeiJay
     * @param Request $request
     * @return array
     */
    public function getNewOrderCount(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = $request->input(['shopId'],0);
        if (!$wid) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id错误';
            return $returnData;
        }
        $where = [
            'recv_id'           => $wid,
            'is_read'           => 0,
            'notification_type' => 2
        ];
        $data = (new NotificationModule())->getNotificationCount($where);
        $returnData['data'] = ['order_count' => $data];
        return $returnData;
    }

    /**
     * 获取所有新订单消息列表
     * Author: MeiJay
     * @param Request $request
     * @return array
     */
    public function getNewOrderNotification(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = $request->input(['shopId'],0);
        if (!$wid) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id错误';
            return $returnData;
        }
        $page = $request->input(['page'],1);
        $size = $request->input(['size'],30);
        $data = (new NotificationModule())->getNewOrderNotification($wid,$page,$size);
        $returnData['data'] = $data;
        return $returnData;
    }

    /**
     * 清空新订单消息
     * Author: MeiJay
     * @param Request $request
     * @return array
     */
    public function clearOrderNotification(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = $request->input(['shopId'],0);
        if (!$wid) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id错误';
            return $returnData;
        }
        $re = (new NotificationModule())->allReadNotification($wid,2);
        if (!$re) {
            $returnData['errCode']  = -2;
            $returnData['errMsg']   = 'fail';
        }
        return $returnData;
    }

    /**
     * 获取小程序token
     * Author: MeiJay
     * @param Request $request
     * @return array|bool|mixed|string
     */
    public function getAccessToken(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = $request->input(['shopId'],0);
        if (!$wid) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id错误';
            return $returnData;
        }
        $data = (new ThirdPlatform())->getAuthorizerAccessToken(['wid'=>$wid]);
        if($data['errCode'] != 0) {
            $returnData['errCode'] = $data['errCode'];
            $returnData['errMsg'] = $data['errMsg'];
        }else {
            $returnData['data']['token'] = $data['data'];
            $returnData['data']['expireTime'] = $data['expireTime'];
        }
        return $returnData;
    }

    /**
     * 客服消息未读
     * @param Request $request
     * @return array 错误信息
     * @author: 梅杰 2018年10月11日
     */
    public function customMessageUnread(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if ($shopIds = $request->input('shopIds', [])) {
            $weiXinUserService = new WeixinUserService();
            $messageService = (new MessagesPushService());

            $wid = $messageService->model->select(['wid'])
                ->whereIn('wid', $shopIds)
                ->where(['message_type' => MessagesPushService::CustomMessage])
                ->get();

            if ($wid->isEmpty()) {
                return $returnData;
            }
            $userData = $weiXinUserService->init()->model
                ->with('shop:id,shop_name')
                ->whereIn('wid', $wid)
                ->where('role_id', 4)
                ->where('open_id', '<>', null)
                ->get(['open_id', 'id', 'wid']);
            foreach ($userData as &$v) {
                (new MessagePushModule($v->wid, MessagesPushService::CustomMessage))->sendMsg([
                    'open_id' => $v->open_id,
                    'shop_name' => $v->shop->shop_name
                ]);
            }
            return $returnData;
        }
        return $returnData;

    }

    /**
     * 回招用户消息
     * @param Request $request
     * @return array
     * @author: 梅杰 2018年10月11日
     */
    public function contactUser(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if ($shopId = $request->input('shopId')) {
            if (!$member = Member::where(['wid'=> $shopId])->select(['openid','xcx_openid'])->find($request->input('toUserId'))) {
                $returnData['errCode'] = 41000;
                $returnData['errMsg'] = '用户不存在';
                return $returnData;
            }
            if ($request->input('joinway') == 'small') {
                $data['touser'] = $member->xcx_openid;
                $data['form_id'] = (new WXXCXCollectFormIdService())->getFormId($request->input('toUserId'));
                if (!$data['form_id']) {
                    $returnData['errCode'] = 40000;
                    $returnData['errMsg'] = '该用户长时间未进入小程序或最近没有与小程序进行交互无法发送信息';
                    return $returnData;
                }
                $data['data']['keyword1'] = [
                    'value' => Weixin::select('shop_name')->find($shopId)->shop_name
                ];
                $data['data']['keyword2'] = [
                    'value' => $request->input('content')
                ];
                $data['data']['keyword3'] = [
                    'value' => date('Y-m-d H:i:s'),
                ];
                $sendTplService  = new WXXCXSendTplService($shopId);
                $re = $sendTplService->sendTplNotify($data,WXXCXSendTplService::MESSAGE_NOTIFY);
            }else {
                $data['touser']             = $member->openid;
                $data['url']                = $request->input('skip_url');
                $data['data']['first']      = ['value' => $request->input('content')];
                $data['data']['keyword1']   = ['value' => '商家消息'];
                $data['data']['keyword2']   = ['value' => date('Y-m-d H:i:s')];
                $data['data']['remark']     = ['value' => '感谢您对会搜云的支持'];
                $re = (new WechatBakModule())->sendTplNotify($shopId,$data,WechatBakModule::COMMON_NOTIFY);
            }
            if ($re['errcode']) {
                $returnData['errCode'] = $re['errcode'];
                $returnData['errMsg'] = $re['errmsg'];
            }
        }
        return $returnData;

    }

    /**
     * 客服留言
     *
     * @param Request $request
     *
     * @return array 错误信息
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月24日 13:40:24
     */
    public function customMessageReply(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $input = $request->only(["toUser", "shopId", "toXcx"]);
        if (empty($input['toUser']) || empty($input['toXcx']) || empty($input['shopId'])) {
            return [
                'errCode' => -1,
                'errMsg' => '参数缺失',
                'data' => []
            ];
        }
        $to = $input['toXcx'] == "weixin" ? MessagePushModule::SEND_TARGET_WECHAT : MessagePushModule::SEND_TARGET_WECHAT_XCX;
        if ($to == MessagePushModule::SEND_TARGET_WECHAT_XCX) {
            if (!Redis::ZCARD('form_ids:' . $input['toUser'])) {
                $returnData['errCode'] = 40000;
                $returnData['errMsg'] = '该用户长时间未进入小程序或最近没有与小程序进行交互无法发送信息';
                return $returnData;
            }
            $config = WXXCXConfig::query()
                ->where('wid', $input['shopId'])
                ->where('current_status', 0)
                ->first(['version','status']);
            $onlineVersion = str_replace('v', '', $config->version ?? null);
            $version = str_replace('v', '', "v3.2.4");
            if ($config->status != 5 || !version_compare($onlineVersion, $version, '>=')) {
                $returnData['errCode'] = 40001;
                $returnData['errMsg'] = '当前小程序版本不满足';
                return $returnData;
            }
        }
        (new MessagePushModule($input['shopId'], MessagesPushService::MSG_REPLY, $to))->sendMsg($input);
        return $returnData;
    }

}