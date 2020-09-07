<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/10/11
 * Time: 10:04
 */

namespace App\Http\Controllers\Merchants;


use App\Model\WXXCXConfig;
use App\S\MarketTools\MessagesPushService;
use App\S\Wechat\WeChatShopConfService;
use App\S\Weixin\ShopService;
use Illuminate\Http\Request;

class MessagesPushController
{

    /**
     * 消息推送首页
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月11日
     */
    public function index(Request $request,MessagesPushService $messagesPushService)
    {
        //数据库获取所有的
        $wid = $request->session()->get('wid',0);

        $dbData = $messagesPushService->getRowByWhere(['wid'=>$wid]);


        $re = $messagesPushService->getSetting($dbData);
        //分类

        $conf = (new WeChatShopConfService())->getRowByWid(session('wid'));
        return view('merchants.marketing.messagesPush.index',[
            'data' => $re,
            'leftNav' => 'marketing',
            'slidebar' => 'index',
            'conf' => $conf

        ]);

    }


    /**
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月11日
     */
    public function custom(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                'send_way'  =>implode(',',$sendWay),
                'wid'       =>$wid,
                'type'      => MessagesPushService::Notification,
                'message_type' => MessagesPushService::CustomMessage]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::CustomMessage);
        return view('merchants.marketing.messagesPush.custom',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'custom',
        ]);

    }

    /**
     * 在线报名
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @param ShopService $shopService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 何书哲 2018年10月11日
     */
    public function enroll(Request $request,MessagesPushService $messagesPushService,ShopService $shopService)
    {
        $wid = $request->session()->get('wid',0);
        if ($request->isMethod('post')) {
            $sendWay = $request->input('sendWay',[]);
            $re = $messagesPushService->save([
                'send_way'  =>  implode(',',$sendWay),
                'wid'       =>$wid,
                'type'      => MessagesPushService::Notification,
                'message_type' => MessagesPushService::EnrollOnline]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,$messagesPushService::EnrollOnline);
        //添加uid绑定微信
        $shopData = $shopService->getRowById($wid);
        $uid = $shopData ? $shopData['uid'] : 0;
        return view('merchants.marketing.messagesPush.enroll',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'enroll',
            'uid' => $uid,
        ]);
    }



    /**
     * 订单催付
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月17日
     */
    public function tradeUrge(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                'send_way'  => implode(',',$sendWay),
                'wid'       => $wid,
                'type'      => MessagesPushService::TradeLogistic,
                'message_type' => MessagesPushService::TradeUrge]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::TradeUrge);
        return view('merchants.marketing.messagesPush.tradeUrge',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'tradeUrge',
        ]);

    }

    /**
     * 付款成功
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月17日
     */
    public function paySuccess(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                    'send_way'  => implode(',',$sendWay),
                    'wid'       => $wid,
                    'type'      => MessagesPushService::TradeLogistic,
                    'message_type' => MessagesPushService::PaySuccess]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::PaySuccess);
        return view('merchants.marketing.messagesPush.paySuccess',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'paySuccess',
        ]);

    }

    /**
     * 发货成功
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月17日
     */
    public function deliverySuccess(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                    'send_way'  => implode(',',$sendWay),
                    'wid'       => $wid,
                    'type'      => MessagesPushService::TradeLogistic,
                    'message_type' => MessagesPushService::DeliverySuccess]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::DeliverySuccess);
        return view('merchants.marketing.messagesPush.deliverySuccess',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'deliverySuccess',
        ]);

    }

    /**
     * 发货成功
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月17日
     */
    public function orderRefund(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                    'send_way'  => implode(',',$sendWay),
                    'wid'       => $wid,
                    'type'      => MessagesPushService::TradeLogistic,
                    'message_type' => MessagesPushService::OrderRefund]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::OrderRefund);
        return view('merchants.marketing.messagesPush.orderRefund',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'orderRefund',
        ]);

    }


    /**
     * 新订单
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月19日
     */
    public function newOrder(Request $request,MessagesPushService $messagesPushService,ShopService $shopService)
    {
        $wid = $request->session()->get('wid',0);
        if ($request->isMethod('post')) {
            $sendWay = $request->input('sendWay',[]);
            $re = $messagesPushService->save([
                    'send_way'  => implode(',',$sendWay),
                    'wid'       => $wid,
                    'type'      => MessagesPushService::Notification,
                    'message_type' => MessagesPushService::NewOrder]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::NewOrder);
        //添加uid绑定微信
        $shopData = $shopService->getRowById($wid);
        $uid = $shopData ? $shopData['uid'] : 0;
        return view('merchants.marketing.messagesPush.newOrder',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'newOrder',
            'uid' => $uid,
        ]);
    }




    /**
     * 拼团
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年10月19日
     */
    public function group(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                    'send_way'  => implode(',',$sendWay),
                    'wid'       => $wid,
                    'type'      => MessagesPushService::MarketingCare,
                    'message_type' => MessagesPushService::ActivityGroup]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::ActivityGroup);
        return view('merchants.marketing.messagesPush.group',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'group',
        ]);

    }

    public function getMemberCard(Request $request,MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid',0);
        $sendWay = $request->input('sendWay',[]);
        if ($request->isMethod('post')) {
            //修改保存 发送方式
            $re = $messagesPushService->save([
                    'send_way'  => implode(',',$sendWay),
                    'wid'       => $wid,
                    'type'      => MessagesPushService::MarketingCare,
                    'message_type' => MessagesPushService::GetMemberCard]
            );
            $re === false && error();
            success();
        }
        $data = $messagesPushService->handDbData($wid,MessagesPushService::GetMemberCard);
        return view('merchants.marketing.messagesPush.getMemberCard',[
            'data' => $data,
            'leftNav' => 'marketing',
            'slidebar' => 'getMemberCard',
        ]);
    }

    /**
     * 客服留言回复
     *
     * @param Request $request
     * @param MessagesPushService $messagesPushService
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月23日 15:49:46
     */
    public function customReply(Request $request, MessagesPushService $messagesPushService)
    {
        $wid = $request->session()->get('wid', 0);
        $sendWay = $request->input('sendWay', []);
        $config = WXXCXConfig::query()
            ->where('wid', $wid)
            ->where('current_status', 0)
            ->first(['status', 'version']);
        $onlineVersion = str_replace('v', '', $config->version ?? null);
        $version = str_replace('v', '', "v3.2.4");
        $flag = false;
        if ($config && $config->status == 5 && version_compare($onlineVersion, $version, '>=')) {
            $flag = true;
        }
        if ($request->isMethod('post')) {
            if (!$flag && in_array(4, $sendWay)) {
                error('无法勾选小程序模板消息：小程序版本不满足');
            }
            // 修改保存 发送方式
            $re = $messagesPushService->save([
                    'send_way' => implode(',', $sendWay),
                    'wid' => $wid,
                    'type' => MessagesPushService::Notification,
                    'message_type' => MessagesPushService::MSG_REPLY]
            );
            if (false === $re) {
                error();
            }
            success();
        }
        $data = $messagesPushService->handDbData($wid, MessagesPushService::MSG_REPLY);
        return view('merchants.marketing.messagesPush.customReply', [
            'data' => $data,
            'version' => $flag,
            'leftNav' => 'marketing',
            'slidebar' => 'customReply',
        ]);
    }

}