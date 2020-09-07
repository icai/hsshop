<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/17
 * Time: 13:32
 */

namespace App\Http\Controllers\WXXCX;
use App\Http\Controllers\Controller;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\Order;
use App\Module\OrderModule;
use App\Module\ProductModule;
use App\Module\RefundModule;
use App\S\Foundation\RegionService;
use App\S\Member\MemberService;
use App\S\Order\OrderZitiService;
use App\S\Product\RemarkService;
use App\S\Weixin\ShopService;
use App\Services\Permission\WeixinUserService;
use CommonModule;
use Illuminate\Http\Request;
use OrderDetailService;
use OrderLogService;
use OrderPointExtraRuleService;
use OrderPointRuleService;
use OrderService;
use PointRecordService;
use ProductService;
use Storage;
use Validator;
use WeixinService;

class OrderController extends Controller
{
    /**
     * todo 等待提交订单信息
     * @param Request $request
     * @param OrderModule $orderModule
     * @return array
     * @author jonzhang
     * @date 2017-08-17
     */
    public  function waitSubmitOrder(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $cartId=$request->input('cart_id');
        $token=$request->input('token');
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($cartId))
        {
            $returnData['code']=-101;
            $returnData['hint']='请选择购物车中的商品';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];

        //获取umid
        $member = (new MemberService())->getRowById($request->input('mid'));
        $umid = $member['umid'];
        $addressID = $request->input('address_id', 0);
        $return = $orderModule->processWaitSubmitOrder($wid,$mid,$cartId,1, $umid, $addressID);
        $return['list']['memberData'] = $member;
        return $return;
    }


    /**
     * todo 创建订单
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-11
     * @author 吴晓平 2018年06月26日 修改自提验证提示
     * @update 张永辉 2019年10月10日16:11:15  订单来源添加字节跳动小程序
     */
    public function submitOrder(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        //接收参数
        $token= $request->input('token');
        $cartId = $request->input('cart_id');
        $expressNo = $request->input('express_no')??1;
        $couponId = $request->input('coupon_id')??0;
        $remark = $request->input('remark')??'';
        $isSend = $request->input('is_send')??0;
        $formId = $request->input('formId')??0;
        // 自提订单添加传递字段 wuxiaoping 2018.06.05
        $isHexiao = $request->input('is_hexiao') ?? 0;
        $zitiContact = $request->input('zitiContact') ?? '';
        $zitiPhone  = $request->input('zitiPhone') ?? '';
        $zitiId = $request->input('zitiId') ?? 0;
        $zitiDatatime = $request->input('time') ?? '';
        $zitiData = [];
        $errMsg = '';
        if ($isHexiao && $isHexiao == 1) {
            if (!$zitiId) {
                $errMsg .= '请先选择自提点地址';
            }
            if (empty($zitiContact)) {
                $errMsg .= '请选填写提货人';
            }
            if (empty($zitiPhone)) {
                $errMsg .= '请选填写提货人手机';
            }
            $zitiData['ziti_id']       = $zitiId;
            $zitiData['ziti_contact']  = $zitiContact;
            $zitiData['ziti_phone']    = $zitiPhone;
            $zitiData['ziti_datetime'] = $zitiDatatime;
        }
        //使用积分
        $point=$request->input('point')??0;
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        //购物车id和快递方式为必传
        if (empty($cartId)) {
            $errMsg .= 'cart_id为null';
        }
        if (empty($expressNo)) {
            $errMsg .= 'express_no为null';
        }
        if (strlen($errMsg)>0) {
            $returnData['code'] = -103;
            $returnData['hint'] = $errMsg;
            return $returnData;
        }

        $data=['cartId'=>$cartId,'expressNo'=>$expressNo,'couponId'=>$couponId,'remark'=>$remark,'isSend'=>$isSend,'point'=>$point,'formId'=>$formId,'is_hexiao'=>$isHexiao,'ziti'=>$zitiData];

        //todo umid
        $mData = (new MemberService())->getRowById($request->input('mid'));
        $umid = $mData['umid'];
        $source = 1;
        if ($request->input('come_from') && $request->input('come_from') == 'byteDance') {
            $source = 4;
        }
        return $orderModule->submitOrder($wid, $mid, $data, $source, 8, $umid, $request);
    }

    /***
     * todo 订单列表
     * @param Request $request
     * @author jonzhang
     * @date 2017-08-22
     */
    public function showAllOrders(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];

        $token= $request->input('token');
        $status = $request->input('status');
        $page = $request->input('page')?$request->input('page'):1;
        //判断token
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        return $orderModule->showAllOrders($wid,$mid,$status,$page);
    }

    /**
     * todo 订单详情
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-08-22
     */
    public function showOrderDetail(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        //接收参数
        $token= $request->input('token');
        $orderNo = $request->input('oid') ?? '';
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo)||$orderNo=='undefined')
        {
            $returnData['code']=-103;
            $returnData['hint']='订单号为空';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        return $orderModule->showOrderDetail($wid,$mid,$orderNo);
    }

    /*
     *  取消订单  陈文豪
        todo 写的不好，写的太随意了
     */
    public function cancelOrder(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        //接收参数
        $token= $request->input('token');
        $orderNo = $request->input('order_no') ?? '';
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        return $orderModule->cancelOrder($wid,$mid,$orderNo);
    }

    /**
     * todo 统计用户某个店铺下的订单数据
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-08-22
     */
    public function statOrderData(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        //接收参数
        $token = $request->input('token');
        if (empty($token)) {
            $returnData['code'] = -100;
            $returnData['hint'] = '没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        return $orderModule->statOrderData($wid,$mid);
    }

    /**
     * todo 确认收货
     * @param Request $request
     * @return array
     * @date 2017-08-30
     */
    public function receive(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token= $request->input('token');
        $orderNo=$request->input('oid');
        //判断token
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo))
        {
            $returnData['code']=-101;
            $returnData['hint']='oid为空';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        return $orderModule->receive($wid,$mid,$orderNo);
    }

    /**
     * todo 获取订单的物流信息
     * @param Request $request
     * @param LogisticsService $logisticsService
     * @date 2017-09-04
     */
    public function getOrderTrackInfo(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token= $request->input('token');
        $orderNo=$request->input('orderNo');
        //判断token
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo))
        {
            $returnData['code']=-101;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        return $orderModule->getOrderTrackInfo($orderNo);
    }

    /**
     * todo 延迟收货
     * @param Request $request
     * @date 2017-09-04
     */
    public function delay(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token= $request->input('token');
        $orderNo=$request->input('orderNo');
        //判断token
        if(empty($token))
        {
            $returnData['code']=-100;
            $returnData['hint']='没有传递token';
            return $returnData;
        }
        if(empty($orderNo))
        {
            $returnData['code']=-101;
            $returnData['hint']='没有传递订单号';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1])||empty($userInfo[2]))
        {
            $returnData['code']=-102;
            $returnData['hint']='token存放数据有问题';
            return $returnData;
        }
        //取mid和wid
        $wid=$userInfo[1];
        $mid=$userInfo[2];
        return $orderModule->delay($wid,$mid,$orderNo);
    }

    /**
     * 获取该订单下的所有评论信息
     */
    public function comments($oid, OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];

        if(empty($oid))
        {
            $returnData['code']=-101;
            $returnData['hint']='没有传递订单号';
            return $returnData;
        }
        return $orderModule->getCommentList($oid);
    }

    /**
     * 添加订单评论
     */
    public function comment(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];

        //参数
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];

        $rule = Array(
                'odid'    		=> 'required',
                'content'    	=> 'required',
                'status'    	=> 'required',
                'depict'    	=> 'required',
                'service'    	=> 'required',
                'speed'    		=> 'required',
        );
        $message = Array(
                'odid.required' 		=> '订单详情ID不能为空',
                'content.required' 	=> '评论内容不能为空',
                'status.required' 	=> '总体评价不能为空',
                'depict.required' 	=> '商品描述不能为空',
                'service.required' 	=> '服务不能为空',
                'speed.required' 		=> '发货速度不能为空',
            );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails())
        {
            $returnData['code']=-103;
            $returnData['hint']=$validator->errors()->first();
            return $returnData;
        }
        $data=[
            'odid'=>$input['odid'],
            'content'=>$input['content'],
            'img'=>$input['img'],
            'status'=>$input['status'],
            'depict'=>$input['depict'],
            'service'=>$input['service'],
            'speed'=>$input['speed'],
            'is_hiden'=>$input['is_hiden']
        ];
        return  $orderModule->insertOrderComment($wid,$mid,$data);
    }

    /**
     * 申请退款
     */
    public function refundApply(Request $request, $oid, $pid, $propID = 0)
    {
        //参数
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];

        $resultArr = (new RefundModule())->refundApply($request, $oid, $pid, $wid, $mid, $propID);

        if ($resultArr['errCode'] == 1) {
            //普通报错
            xcxerror($resultArr['errMsg']);
        } elseif ($resultArr['errCode'] == 2) {
            //跳转指定url
            xcxerror($resultArr['errMsg']);
        }

        if ($request->isMethod('post')) {
            xcxsuccess($resultArr['errMsg']);
        } else {
            xcxsuccess($resultArr['errMsg'], ['data' => $resultArr['data']['order'], 'product' => $resultArr['data']['product']]);
        }
    }

    /**
     * 修改申请退款
     */
    public function refundApplyEdit(Request $request, $oid, $pid, $propID = 0)
    {
        $resultArr = (new RefundModule())->applyEdit($request, $oid, $pid, $propID);

        if ($resultArr['errCode'] == 1) {
            //普通报错
            xcxerror($resultArr['errMsg']);
        }

        if ($request->isMethod('post')) {
            xcxsuccess($resultArr['errMsg']);
        } else {
            $data = [
                'order' => $resultArr['data']['order'],
                'product' => $resultArr['data']['product'],
                'refund' => $resultArr['data']['refund']
            ];
            xcxsuccess($resultArr['errMsg'], $data);
        }
    }

    /**
     * 退款详情
     */
    public function refundDetail(Request $request, $oid, $pid, $propID = 0)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');

        $resultArr = (new RefundModule())->detail($oid, $pid, $wid, $mid, $propID);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            xcxsuccess('获取退款详情成功', $resultArr['data']);
        } else {
            xcxerror($resultArr['errMsg']);
        }
    }

    /**
     * 买家撤销退款
     */
    public function refundCancel(Request $request, $oid, $refundID)
    {
        //参数
        $wid = $request->input('wid');
        $mid = $request->input('mid');

        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->buyerCancel($oid, $refundID, $wid, $mid);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                xcxsuccess('撤销退款成功', $resultArr['data']);
            } else {
                xcxerror($resultArr['errMsg']);
            }
        } else {
            xcxerror('只允许POST提交');
        }
    }

    /**
     * 退款协商列表
     */
    public function refundMessages(Request $request, $oid, $pid, $propID = 0)
    {
        //参数
        $wid = $request->input('wid');
        $mid = $request->input('mid');

        $resultArr = (new RefundModule())->messages($oid, $pid, $wid, $mid, $propID);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            xcxsuccess('获取退款详情成功', ['data' => $resultArr['data']]);
        } else {
            xcxerror($resultArr['errMsg']);
        }
    }

    /**
     * 退款添加留言
     */
    public function refundAddMessage(Request $request, $refundID, $oid)
    {
        //参数
        $wid = $request->input('wid');

        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->addMessage($request, $oid, $wid, $refundID);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                xcxsuccess('添加留言成功', $resultArr['data']);
            } else {
                xcxerror($resultArr['errMsg']);
            }
        } else {
            xcxerror('只允许POST提交');
        }
    }

    /**
     * 买家退款发货
     */
    public function refundReturn(Request $request, $refundID)
    {
        if ($request->isMethod('post')) {
            $resultArr = (new RefundModule())->refundReturn($request, $refundID);
            //处理返回
            if ($resultArr['errCode'] == 0) {
                xcxsuccess('买家退款发货成功');
            } else {
                xcxerror($resultArr['errMsg']);
            }
        } else {
            xcxerror('只允许POST提交');
        }
    }

    /**
     * 订单分享信息
     */
    public function share($oid)
    {
        if (empty($oid)) {
            xcxerror('参数不完整');
        }

        list($orderDetail) = OrderDetailService::init()->where(['oid'=>$oid])->getList(false);

        xcxsuccess('', ['data' => $orderDetail['data']]);
    }

    /**
     * 退款订单列表
     * @param Request $request
     * @param int $status 退款状态 0全部 1待用户处理
     * @return array
     */
    public function refundList(Request $request, $status = 0)
    {
        //参数
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];
        //获取列表
        $resultArr = (new RefundModule())->list($wid, $mid, $status);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            xcxsuccess('获取退款列表成功', $resultArr['data']);
        } else {
            xcxerror($resultArr['errMsg']);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $oid
     */
    public function getOrderLog($oid)
    {
        list($res) = OrderLogService::init()->where(['oid'=>$oid])->order('id desc')->getList(false);
        xcxsuccess('操作成功',$res);
    }

    /**
     * 退款微信审核 钱款去向
     */
    public function refundVerify($refundID)
    {
        //获取列表
        $resultArr = (new RefundModule())->refundVerify($refundID);

        //处理返回
        if ($resultArr['errCode'] == 0) {
            xcxsuccess('获取退款列表成功', $resultArr['data']);
        } else {
            xcxerror($resultArr['errMsg']);
        }
    }

    public function waitSubmitShareEventOrder(Request $request)
    {
        $productModule=new ProductModule();
        $productReturnData=$productModule->getProductByShareEvent(2025,1116);
        return $productReturnData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180118
     * @desc 获取留言信息
     */
    public function getRemark(Request $request)
    {
        $remarkNo = $request->input('remarkNo');
        $remarkData = (new RemarkService())->getByRemarkNo($remarkNo);
        xcxsuccess('操作成功',$remarkData);
    }

    /**
     * 享立减卡片
     * Author: MeiJay
     * @param Request $request
     * @update 梅杰 获取指定小程序享立减二维码
     */
    public function getEventCard(Request $request)
    {
        $wid = $request->input(['wid']);
        $mid = $request->input(['mid']);
        $type = $request->input(['type']);
        $orderNo  = $request->input(['orderNo'],0);
        $token = $request->input('token');
        $where = [
            'mid' => $mid,
            'wid' => $wid,
            'status'         => 1,
            'share_event_id' => ['<>',0],
        ];
        $orderData = OrderService::init('wid', $wid)->model->wheres($where)->find($orderNo);
        if (!$orderData) {
            xcxerror('订单不存在');
        }
        $orderData = $orderData->load('orderDetail')->toArray();
        if ($type == 1) {
            $eventType = 'li';
        }else{
            $eventType = 'share';
        }
        if (!is_dir('./hsshop/xcx/'.$eventType.'/')) {
            Storage::makeDirectory('./hsshop/xcx/'.$eventType.'/');
        }
        $qrPath = "/hsshop/xcx/share/".$mid.'_'.$orderData['share_event_id'].'.jpg';
        $scene = "'sId':".$mid.",'aId':".$orderData['share_event_id'].",'list':1";
        $page = $type == 0 ? 'pages/activity/pages/shareSale/shareSale/shareSale':'pages/member/shareSaleli/shareSaleli';
        $configId = CommonModule::getXcxConfigIdByToken($token);
        $re = (new ThirdPlatform())->createQrCode($wid,$scene,$page,$configId);
        if ( $re['errCode'] != 0 ) {
           xcxerror('二维码获取失败');
        }
        $img = base64_decode($re['data']);
        file_put_contents('.'.$qrPath, $img);
        $data['productInfo']['productTitle'] = $orderData['orderDetail'][0]['title'];
        $data['productInfo']['productImg']   = $orderData['orderDetail'][0]['img'];
        $data['productInfo']['productPrice'] = $orderData['orderDetail'][0]['price'];
        $data['productInfo']['productOprice'] = $orderData['orderDetail'][0]['oprice'];
        $data['card']['codeImg'] =  $qrPath;
        xcxsuccess('',$data);
    }

    /**
     * 自提订单凭证页面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function zitiVoucher(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $result = $this->getZitiOrderInfo($oid);
        $orderData = $result['data'];
        if ($result['errCode'] && empty($orderData)) {
            xcxerror($result['errMsg']);
        }
        // 生成核销二维码
        $shopUrl = 'pages/main/pages/order/hexiaoConfirm/hexiaoConfirm?oid='.$orderData['id'];
        $qrcodeData = CommonModule::qrCode($orderData['wid'], $shopUrl,1);
        $orderData['qrcode'] = $qrcodeData;


        return $orderData;
    }

    /**
     * 长连接接口
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function scanLongConnet(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $result = $this->getZitiOrderInfo($oid);
        $orderData = $result['data'];
        if ($result['errCode'] && empty($orderData)) {
            xcxerror($result['errMsg']);
        }
        $time_count = 0;
        $returnData = ['status' => 0, 'msg' => ''];
        // 无限请求超时时间
        set_time_limit(0);
        while (true) {
            // 暂停0.5秒后执行
            usleep(500000);
            $time_count++;
            if ($orderData['status'] == 1) {
                $returnData = ['status' => 1, 'msg' => '订单扫码核销正常'];
                echo json_encode($returnData);
                break;
            } else if ($orderData['status'] == 2) {
                $returnData = ['status' => 200, 'msg' => '订单已核销成功'];
                echo json_encode($returnData);
                break;
            }

            if ($time_count >= 30) {
                $returnData['status'] = -504;
                $returnData['msg'] = '长时间未操作';
                echo json_encode($returnData);
                break;
            }
        }
    }

    /**
     * 商家扫码核销确认页面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function hexiaoConfirm(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $token = $request->input('token') ?? '';
        //获取缓存中保存的用户信息
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            xcxerror('token存放数据有问题');
        }
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        if (!$oid || !$wid || !$mid) {
            xcxerror('参数错误，数据异常');
        }
        $memberInfo = (new MemberService())->getRowById($mid);
        if (empty($memberInfo)) {
            xcxerror('该用户不存在');
        }
        // update 吴晓平 2019年09月24日 15:44:25 小程序核销优化处理（因为同一店铺微商城跟小程序授权的mid可能会不同，这里根据nickname获取用户id数组进行验证）
        $mids = (new MemberService())->getListByNickname($wid, $memberInfo['nickname']);
        if (!(new WeixinUserService())->isBindFromXcx($wid, $mids)) {
            xcxerror('未绑定店铺核销员,不能进行扫码核销操作');
        }
        $shop = (new ShopService())->getRowById($wid);
        $result = $this->getZitiOrderInfo($oid);
        if ($result['errCode']) {
            xcxerror($result['errMsg']);
        }
        if ($result['data']['status'] == 2) {
            xcxerror('订单已核销');
        }
        $result['data']['shop'] = $shop;
        xcxsuccess('', $result['data']);
    }

    /**
     * 用户展示核销码核销成功跳转页面
     * @return [type] [description]
     */
    public function hexiaoRedirect(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $token = $request->input('token');
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1]) || empty($userInfo[2])) {
            xcxerror('token存放数据有问题');
        }
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        if (!$oid || !$wid || !$mid) {
           xcxerror('参数错误，数据异常');
        }
        $shop = (new ShopService())->getRowById($wid);
        $result = $this->getZitiOrderInfo($oid);
        if ($result['errCode']) {
            xcxerror($result['errMsg']);
        }
        $result['data']['shop'] = $shop;
        xcxsuccess('',$result['data']);
    }

    /**
     * 确认核销
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function hexiaoSure(Request $request)
    {
        $oid = $request->input('oid') ?? 0;
        $token = $request->input('token');
        //获取缓存中保存的用户信息
        $userInfo=CommonModule::getAllByToken($token);
        if(empty($userInfo[1]) || empty($userInfo[2])) {
            xcxerror('token存放数据有问题');
        }
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        if (!$oid || !$wid || !$mid) {
           xcxerror('参数错误，数据异常');
        }
        $result = $this->getZitiOrderInfo($oid);
        if ($result['errCode']) {
            xcxerror($result['errMsg']);
        }
        if (!OrderService::init('wid',$wid)->where(['id'=>$oid])->update(['status' => 2],false)) {
            xcxerror('订单核销失败');
        }
        xcxsuccess();
    }

    /**
     * 获取自提订单相关信息
     * @author 吴晓平 <2018年09月21日>
     * @param  integer $oid [订单表id]
     * @return [type]       [description]
     */
    public function getZitiOrderInfo($oid = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($oid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '参数错误，数据异常';
            return $returnData;
        }
        $orderData = Order::with('orderDetail')->where(['id' => $oid,'is_hexiao' => 1])->first();
        if (!$orderData) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '该订单不存在，或已删除';
            return $returnData;
        }
        $orderData = $orderData->toArray();
        $where['oid'] = $oid;
        $zitiData = (new OrderZitiService())->getDataByCondition($where);
        if ($zitiData) {
            $temp = [$zitiData['orderZiti']['province_id'], $zitiData['orderZiti']['city_id'], $zitiData['orderZiti']['area_id']];
            $regionService = new RegionService();
            $region = $regionService->getListById($temp);

            $tmpAddr = [];
            foreach ($region as $val) {
                $tmpAddr[$val['id']] = $val['title'];
            }
            $zitiData['orderZiti']['province_title'] = $tmpAddr[$zitiData['orderZiti']['province_id']];
            $zitiData['orderZiti']['city_title'] = $tmpAddr[$zitiData['orderZiti']['city_id']];
            $zitiData['orderZiti']['area_title'] = $tmpAddr[$zitiData['orderZiti']['area_id']];
            $orderData['ziti'] = $zitiData;
        }
        $returnData['data'] = $orderData;
        return $returnData;
    }
}
