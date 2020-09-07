<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/1/24
 * Time: 10:07
 */

namespace App\Http\Controllers\WXXCX;
use App\Http\Controllers\Controller;
use App\Lib\WXXCX\ThirdPlatform;
use App\Module\MemberModule;
use App\S\Product\ProductMsgService;

use App\S\ShareEvent\LiEventRecordService;
use App\S\ShareEvent\LiEventService;
use App\S\ShareEvent\LiFriendService;
use App\S\ShareEvent\LiRegisterService;
use Illuminate\Http\Request;
use CommonModule;
use App\Module\ProductModule;
use Illuminate\Support\Facades\Storage;
use MemberService;
use App\Module\OrderModule;
use WeixinService;
use App\Lib\BLogger;
use App\S\WXXCX\WXXCXConfigService;
use ProductService;
use App\Module\LiShareEventModule;
use Validator;
use App\S\ShareEvent\LiRewardService;
use App\S\ShareEvent\LiDetailService;// add by jonzhang 2018-02-06
use OrderService;
use App\S\Wechat\WeChatShopConfService;
use App\S\Weixin\ShopService;

class LiShareEventController extends Controller
{
    //获取参与信息（头像） add meijie
    public function getAllActorData($activity_id,$source_id,$wid,Request $request)
    {
        //通过token 获取信息
        $service = new LiEventRecordService( );
        $wid = $request->input('wid');
        $where = [
            'share_event_id'    => $activity_id,
            'source_id'         => $source_id,
            'wid'               => $wid
        ];
        $data = $service->getListWithPage($where,'','',100);
        //add by jonzhang
        if(!empty($data[0]['data']))
        {
            foreach($data[0]['data'] as &$item)
            {
                $item['created_at']    = date('Y/m/d H:i:s',$item['created_at']);
                $item['is_red_packet'] = $item['red_packet_id'] ;
            }
        }
        xcxsuccess('',$data);
    }

    /**
     * todo 享立减商品详情 分享着和参与者都可以使用
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-12-11
	 * @update 20180712 小程序名称
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function showProductDetail(Request $request,ShopService $shopService)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'',
            'list'=>[
                'member'=>['total'=>0,'memberInfo'=>[]],
                'count'=>0,
                'product'=>[],
                'unitAmount'=>0.00,  //单价
                'lowerPrice'=>0.00,  //保底价
                'share_title' => '', // 分享标题
                'share_img'   => '', //分享图片
                'xcxName'=>'',
                'isShow'=>0,
                'reduce_total' => 0.00,
                'isShare'=>1, //是否为分享者 0表示参与者 1表示分享者
                'sharer'=>'', //分享者昵称
                'headImgUrl'=>'', //分享者头像
                'isRegister'=>0,//是否注册过 0表示未注册 1表示注册
                'isCard'=>0,//是否生成卡片 0表示否 1表示是
                'isNew'=>0,//是否为新用户 0表示老用户 1表示新用户
                'ruleImg'=>'',//规则图片
                'ruleTitle'=>'',//规则标题
                'ruleContent'=>'',//规则内容
                'isExpire'=>0,//活动是否结束
                'currentTime'=>time(),//服务端时间
                'startTime'=>0,//活动开始时间
                'endTime'=>0,//活动结束时间
                'isFill'=>0,//是否集满 0表示未集满 1表示集满
                'isUpvote'=>0,//是否点赞 0未点赞 1点赞
                'isReceive'=>0,//是否领取 0未领取 1领取
                'likeCount'=>0,//点赞人数
                'isFirst'=>0,//是否首次 0表示不是首次 1表示首次
                'isStart'=>1,//活动是否开始 0表示未开始 1表示开始
                'wechatName'=>'', //微信公众号名称
                'wechatCode'=>'', //微信公众号二维码
                'rank'      => 0
            ]
        ];
        $token=$request->input('token');
        $activityId=$request->input('activityId');
        $activityId=intval($activityId)??0;
        $shareId=$request->input('shareId');
        $shareId=intval($shareId);
        if(empty($token))
        {
            $returnData['code']=40004;
            $returnData['hint']="token为空";
            return $returnData;
        }

        if(empty($activityId))
        {
            $returnData['code']=-101;
            $returnData['hint']="享立减活动id为空";
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

            $liDetailService=new LiDetailService();
            $product = [];
            $shareEventService = new LiEventService();
            $where = ['wid' => $wid, 'id' => $activityId, 'type' => 0, 'status' => 0];
            $productId=0;
            $unitAmount=0.00;
            $shareTitle = $shareImg = '';
            $shareEventResult = $shareEventService->list($where);
            if (!empty($shareEventResult[0]['data'])) {
                $shareEventData = $shareEventResult[0]['data'][0];
                $returnData['list']['likeCount']=$shareEventData['like_count'];
                //add by jonzhang 判断享立减活动是否过期 2018-01-22
                if($shareEventData['start_time']>time())
                {
                    $returnData['list']['isStart']=0;
                    $returnData['code'] = -1001;
                    $returnData['hint'] = '该享立减活动还没开始';
                    return $returnData;
                }
                if($shareEventData['end_time']<time())
                {
                    $returnData['list']['isExpire']=1;
                    $returnData['code'] = -1002;
                    $returnData['hint'] = '该享立减活动已过期';
                    return $returnData;
                }
                $returnData['list']['startTime']=$shareEventData['start_time'];
                $returnData['list']['endTime']=$shareEventData['end_time'];

                $unitAmount =  sprintf('%.2f',$shareEventData['unit_amount']/100);
                $lowerPrice=sprintf('%.2f',$shareEventData['lower_price']/100);
                $productId=$shareEventData['product_id'];
                /*add by wuxiaoping 2017.12.22*/
                $shareTitle = $shareEventData['share_title'];
                $shareImg = $shareEventData['share_img'];
                // todo 享立减二期 add by jonzhang 2018-01-10
                //活动标题
                $product['title']=$shareEventData['title'];
                //活动副标题
                $product['subtitle']=$shareEventData['subtitle'];
                //活动图片
                $product['activityImg']=$shareEventData['show_imgs'];
                //按钮图片
                $product['buttonTitle']=$shareEventData['button_title'];
                // todo 新享立减 add by jonzhang 2018-01-27
                $returnData['list']['ruleImg']=$shareEventData['rule_img']??'';
                $returnData['list']['ruleTitle']=$shareEventData['rule_title']??'';
                $returnData['list']['ruleContent']=$shareEventData['rule_text']??'';
                $returnData['list']['ruleContent']=nl2br($returnData['list']['ruleContent']);
            }
            else
            {
                $returnData['code'] = -104;
                $returnData['hint'] = '享立减活动不存在';
                return $returnData;
            }

            if(empty($productId))
            {
                $returnData['code'] = -105;
                $returnData['hint'] = '商品id不存在';
                return $returnData;
            }

            $productModule = new ProductModule();
            $productResult = $productModule->getProductByShareEvent($productId, 0, false);
            if ($productResult['errCode'] == 0 && !empty($productResult['data'])) {
                $productData = $productResult['data'];
                $product['productId'] = $productData['id'];
                $product['productName'] = $productData['title'];
                $product['oprice'] = $productData['oprice'];
                $product['price'] = $productData['price'];
                $product['img'] = $productData['img'];
                $product['productImg'] = $productData['productImg'];
                $product['content'] = $productData['content'];
                $product['quota'] =  $productData['quota'];
                $product['buy_min'] =  $productData['buy_min'];
                // add by zhangyh 20170118  商品留言信息
                $product['noteList'] = (new ProductMsgService())->getListByProduct($product['productId']);
                //end
            } else {
                if ($productResult['errCode'] == 0 && empty($productResult['data'])) {
                    $returnData['code'] = -106;
                    $returnData['hint'] = '没有商品数据信息';
                    return $returnData;
                } else {
                    $returnData['code'] = $productResult['errCode'];
                    $returnData['hint'] = $productResult['errMsg'];
                    return $returnData;
                }
            }

            //add by jonzhang
            //可以逐减最大金额
            $targetAmount=0.00;
            if($product['price']-$lowerPrice>0)
            {
                $targetAmount = $product['price'] - $lowerPrice;
                $targetAmount = sprintf('%.2f', $targetAmount);
            }
            //add by jonzhang
            //享立减每次逐减金额
            $returnData['list']['unitAmount']=$unitAmount;
            //后台设置的分享内容
            $returnData['list']['share_title'] = $shareTitle;
            $returnData['list']['share_img']   = $shareImg;

            //小程序名称信息 add by jonzhang 2017-01-08
            $xcxConfigData=(new WXXCXConfigService())->getRowById($userInfo[3]);
            if($xcxConfigData['errCode']==0&&!empty($xcxConfigData['data'])) {
                $returnData['list']['xcxName'] = $xcxConfigData['data']['title'];
                if ($xcxConfigData['data']['app_id']=='wxdcc591f311e441a7')
                {
                    $returnData['list']['isShow'] = 1;
                }
            }
            $liRewardService=new LiRewardService();
            $liRewardData=$liRewardService->getAllList($wid);
            if(!empty($liRewardData[0]['data']))
            {
                //是否分享卡片
                $returnData['list']['isCard']=$liRewardData[0]['data'][0]['is_open_card']??0;
            }

            //$shop = WeixinService::getStore($wid);
            $shop = $shopService->getRowById($wid);
            $wechatQRcode=$shop['wechat_qrcode']??'';
            if(!empty($wechatQRcode))
            {
                $wechatQRcode=imgUrl().$wechatQRcode;
            }
            //微信二维码
            $returnData['list']['wechatCode']=$wechatQRcode;
            $key  = $shop['share_event_key']??'';
            $liShareEventModule=new LiShareEventModule();
            $statusData = $liShareEventModule->isShareEvent(['shareEventId'=>$activityId,'actorId'=>$mid,'key'=>$key,'shareId'=>$shareId]);
            if($statusData['errCode'] == 0)
            {
                $returnData['list']['isNew']=$statusData['data'];
            }
            // todo 分享者业务逻辑
            // todo 享立减活动 参与者用户信息
            $shareEventModule = new LiShareEventModule();
            $shareEventMember = $shareEventModule->showActorData(['shareId' => $shareId, 'activityId' => $activityId],$targetAmount,$unitAmount);
            if ($shareEventMember['errCode'] < 0) {
                $returnData['code'] = $shareEventMember['errCode'];
                $returnData['hint'] = $shareEventMember['errMsg'];
                return $returnData;
            }
            //助减用户信息
            $returnData['list']['member']['memberInfo'] = $shareEventMember['data']['members']??[];
            //助减次数
            $returnData['list']['member']['total'] = $shareEventMember['data']['total']??0;
            //有效助减次数
            $returnData['list']['count'] = $shareEventMember['data']['memberCount']??0;
            //助减金额
            $returnData['list']['reduce_total'] = $shareEventMember['data']['amount'];
            //保底价
            $returnData['list']['lowerPrice']=$lowerPrice;
            //商品数据
            $returnData['list']['product']=$product;

            $isFirst=0;
            $liDetailServiceData=$liDetailService->getRowByMidAndEventId($shareId,$activityId);
            if(!empty($liDetailServiceData))
            {
                //是否集满
                $returnData['list']['isFill']=$liDetailServiceData['is_full'];
                //是否领取
                $returnData['list']['isReceive']=$liDetailServiceData['is_buy'];
                //是否首次
                if(isset($liDetailServiceData['is_first']))
                {
                    $isFirst=$liDetailServiceData['is_first'];
                }

            }
            //微信公众号名称
            $wechatData=(new WeChatShopConfService())->getRowByWid($wid);
            $returnData['list']['wechatName']=$wechatData['name']??'';
            // todo 参与者逻辑
            if($shareId!=$mid)
            {
                $returnData['list']['isShare']=0;
                $shareData=MemberService::getRowById($shareId);
                $returnData['list']['sharer']=$shareData['nickname']??'';
                $returnData['list']['headImgUrl']=$shareData['headimgurl']??'';
                //插入好友信息
                $friends = [
                    'mid'  => $shareId,
                    'mmid' => $mid,
                    'event_id' => $activityId
                ];
                (new LiFriendService())->createLiFriend($friends);
                
                $liEventRecordData= (new LiEventRecordService())->getList(['actor_id' =>$mid]);
                if(!empty($liEventRecordData))
                {
                    if(!empty($liEventRecordData[0]['share_event_id'])&&$liEventRecordData[0]['share_event_id']==$activityId
                        &&!empty($liEventRecordData[0]['key'])&&$liEventRecordData[0]['key']==$key
                        &&!empty($liEventRecordData[0]['source_id'])&&$liEventRecordData[0]['source_id']==$shareId
                    )
                    {
                        $returnData['list']['isUpvote'] = 1;
                    }
                }
                return $returnData;
            }
            $returnData['list']['rank'] = $liDetailServiceData['rank'];
            $returnData['list']['isFirst']=$isFirst;
            return $returnData;
    }

    /**
     * todo 待结算订单
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     * @author jonzhang
     * @date 2017-12-11
     */
    public function processWaitSubmitShareEventOrder(Request $request,LiShareEventModule $shareEventModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'', 'list'=>[]];
        try
        {
            $token=$request->input('token');
            $productId=$request->input('productId');
            $productId=intval($productId);
            $skuId=$request->input('skuId');
            $num=$request->input('num');
            $skuId=intval($skuId);
            $activityId=$request->input('activityId');
            $where=['activityId'=>$activityId,'skuId'=>$skuId,'num'=>1];
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
            $result=$shareEventModule->processWaitSubmitShareEventOrder($wid,$mid,$where);
            if($result['errCode']==0)
            {
                $returnData['list']=$result['data'];
            }
            else
            {
                $returnData['code']=$result['errCode'];
                $returnData['hint']=$result['errMsg'];
            }
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['code']=-1000;
            $returnData['hint']=$ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 提交享立减订单
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     * @author jonzhang
     * @date 2017-12-12
     */
    public function submitShareEventOrder(Request $request,LiShareEventModule $shareEventModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'', 'list'=>[]];
        $token=$request->input('token');
        $addressId=$request->input('addressId');
        $addressId=intval($addressId);
        $productId=$request->input('productId');
        $productId=intval($productId);
        $activityId=$request->input('activityId');
        $activityId=intval($activityId);
        $skuId=$request->input('skuId');
        $skuId=intval($skuId);
        $num=$request->input('num');
        $num=intval($num);
        $formId=$request->input('formId');
        //add by zhangyh 20180119
        $remarkNo = $request->input('remark_no','');
        //end

        try
        {
            $errMsg="";
            if(empty($activityId))
            {
                $errMsg.="享立减活动id不能为空";
            }
//            if(empty($addressId))
//            {
//                $errMsg.="收货地址id不能为空";
//            }
            if(strlen($errMsg)>0)
            {
                $returnData['code']=-10;
                $returnData['hint']=$errMsg;
                return $returnData;
            }
            if(empty($token))
            {
                $returnData['code'] = 40004;
                $returnData['hint'] = 'token为空';
            }
            $userInfo = CommonModule::getAllByToken($token);
            if (empty($userInfo[1]) || empty($userInfo[2])) {
                $returnData['code'] = -102;
                $returnData['hint'] = 'token存放数据有问题';
                return $returnData;
            }
            //取mid和wid
            $wid = $userInfo[1];
            $mid = $userInfo[2];
            //add by jonzhang 2018-02-07 定制版享立减 同一个用户同一个活动只能够参加一次
            $orderStatusInfo=OrderService::getOrderData(['wid'=>$wid,'mid'=>$mid,'zan_id'=>$activityId]);
            if(!empty($orderStatusInfo))
            {
                $returnData['code'] = -103;
                $returnData['hint'] = '该活动你已经领取过';
                return $returnData;
            }

            $where=['activityId'=>$activityId,'skuId'=>$skuId,'num'=>$num,'addressId'=>$addressId,'formId'=>$formId];
            $result=$shareEventModule->submitShareEventOrder($wid,$mid,$where,1,11,$remarkNo);
            if($result['errCode']==0)
            {
                $returnData['list']=$result['data'];
            }
            else
            {
                $returnData['code']=$result['errCode'];
                $returnData['hint']=$result['errMsg'];
            }
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['code']=-1000;
            $returnData['hint']=$ex->getMessage();
            return $returnData;
        }
    }


    /*
     * todo 通过地址计算运费
     * @author jonzhang
     * @date 2017-12-12
     */
    public function statFreight(Request $request,OrderModule $orderModule)
    {
        //定义返回数据数组
        $returnData=['code'=>40000,'hint'=>'', 'list'=>0];
        try
        {
            $token=$request->input('token');
            $addressId=$request->input('addressId');
            $addressId=intval($addressId);
            $productId=$request->input('productId');
            $productId=intval($productId);
            $skuId=$request->input('skuId');
            $skuId=intval($skuId);
            if(empty($token))
            {
                $returnData['code'] = 40004;
                $returnData['hint'] = 'token为空';
                return $returnData;
            }
            if(empty($addressId))
            {
                $returnData['code'] = -103;
                $returnData['hint'] = '地址id为空';
                return $returnData;
            }
            if(empty($productId))
            {
                $returnData['code'] = -104;
                $returnData['hint'] = '商品id为空';
                return $returnData;
            }

            $userInfo = CommonModule::getAllByToken($token);
            if (empty($userInfo[1]) || empty($userInfo[2])) {
                $returnData['code'] = -102;
                $returnData['hint'] = 'token存放数据有问题';
                return $returnData;
            }
            //取mid和wid
            $wid = $userInfo[1];
            $mid = $userInfo[2];
            //此处为商品的运费
            $member = MemberService::getRowById($mid);
            $umid = $member['umid']??0;
            $freight = $orderModule->getFreightByCartIDArr([], $wid, $mid, $umid, $addressId, ['product_id' =>$productId, 'prop_id' => $skuId, 'num' => 1]);
            $returnData['list'] = sprintf('%.2f', $freight);
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['code']=-1000;
            $returnData['hint']=$ex->getMessage();
            return $returnData;
        }
    }


    /**
     * Author: MeiJay
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     */
    public function getRedPacket(Request $request,LiShareEventModule $shareEventModule)
    {
        $token = $request->input('token');
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['msg'] = 'token存放数据有问题';
            return $returnData;
        }
        return $shareEventModule->getRedPacket($userInfo[2],$userInfo[1]);
    }

    /**
     * Author: MeiJay
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     */
    public function useRedPacket(Request $request,LiShareEventModule $shareEventModule)
    {
        $token = $request->input('token');
        $packetId = $request->input(['packetId']);
        $activityId = $request->input(['activityId']);
        if(!$packetId || !$activityId) {
            $returnData['code'] = -103;
            $returnData['msg'] = 'miss activityId or packetId';
            return $returnData;
        }
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['msg'] = 'token存放数据有问题';
            return $returnData;
        }
        return $shareEventModule->useRedPacket($userInfo[2],$userInfo[1],$packetId,$activityId);
    }

    /***
     * todo 显示更多的享立减活动
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return mixed
     * @author jonzhang
     * @date 2018-01-10
     */
    public function showMoreShareEvent(Request $request,LiEventService $shareEventService)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token = $request->input('token');
        $activityId=$request->input('activityId')??0;
        if(empty($token))
        {
            $returnData['code']=40004;
            $returnData['hint']="token为空";
            return $returnData;
        }
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        $wid = $userInfo[1];
        $where = ['wid' => $wid, 'type' => 0, 'status' => 0];
        $shareEventData=$shareEventService->list($where,'id','desc',20);
        if(!empty($shareEventData[0]['data']))
        {
            $sourceId=[];
            $sourceData=[];
            foreach($shareEventData[0]['data'] as $item)
            {
                //剔除重复的活动id
                if($item['id']!=$activityId)
                {
                    $productData = ProductService::getDetail($item['product_id']);
                    if(isset($productData['status']))
                    {
                        //status为1表示上架
                        if($productData['status']!=1)
                        {
                            continue;
                        }
                    }
                    if(isset($productData['is_distribution']))
                    {
                        //分销商品不能够享立减
                        if($productData['is_distribution']==1)
                        {
                            continue;
                        }
                    }
                    if(isset($item['lower_price'])&&$item['lower_price']>0)
                    {
                        $item['lower_price'] = sprintf('%.2f', $item['lower_price'] / 100);
                    }

                    //逐减人数
                    $total=0;
                    $total=$total+$item['reduce_total'];
                    //开启初始值
                    if($item['is_initial'])
                    {
                        $total= $item['initial_value'];
                    }

                    $sourceData[] = [
                        "price"=>$productData['price'],
                        "buttonTitle"=>$item['button_title'],
                        "lowerPrice"=>$item['lower_price'],
                        "attendCount"=>$total,
                        "title"=>$item['title'],
                        "subtitle"=>$item['subtitle'],
                        "activityImg"=>$item['act_img'],
                        "id"=>$item['id']
                    ];
                    $sourceId[]=$item['id'];
                }
            }
            $targetData=[];
            if(!empty($sourceData)&&!empty($sourceId)&&count($sourceId)>6)
            {
                $keys=array_rand($sourceId, 6);
                for($i = 0; $i < 6; $i++)
                {
                    foreach($sourceData as $sourceItem)
                    {
                        if($sourceId[$keys[$i]]==$sourceItem['id'])
                        {
                            $targetData[]=$sourceItem;
                            break;
                        }
                    }
                }
                $returnData['list']=$targetData;
            }
            else
            {
                $returnData['list']=$sourceData;
            }
        }
        return $returnData;
    }

    /**
     * todo 查询最新享立减活动参与者信息
     * @param Request $request
     * @author jonzhang
     * @date 2018-01-10
     */
    public function  showShareEventRecord(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $token = $request->input('token');
        if(empty($token))
        {
            $returnData['code']=40004;
            $returnData['hint']="token为空";
            return $returnData;
        }
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2]))
        {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        $wid = $userInfo[1];
        //通过token 获取信息
        $service = new LiEventRecordService();
        $data = $service->getListWithPage(['wid'=> $wid],'id','desc',20);
        if(!empty($data[0]['data']))
        {
            foreach($data[0]['data'] as &$item)
            {
                if(isset($item['wid']))
                    unset($item['wid']);
                if(isset($item['share_event_id']))
                    unset($item['share_event_id']);
                if(isset($item['source_id']))
                    unset($item['source_id']);
                if(isset($item['actor_id']))
                    unset($item['actor_id']);
                if(isset($item['current_status']))
                    unset($item['current_status']);
                if(isset($item['updated_at']))
                    unset($item['updated_at']);
                if(isset($item['key']))
                    unset($item['key']);
                if(isset($item['ip']))
                    unset($item['ip']);
                if(isset($item['red_packet_id']))
                    unset($item['red_packet_id']);
                if(isset($item['red_packet_money']))
                    unset($item['red_packet_money']);
                $item['created_at']=date('Y/m/d H:i:s',$item['created_at']);
            }
            $returnData['list']=$data[0]['data'];
        }
        return $returnData;
    }


    /**
     * 活动进度
     * Author: MeiJay
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     */
    public function getProcess(Request $request , LiShareEventModule $shareEventModule)
    {
        $token = $request->input('token');
        $sourceId = $request->input(['sourceId']);
        $activityId = $request->input(['activityId']);
        if(!isset($sourceId) || !$activityId) {
            $returnData['code'] = -103;
            $returnData['msg'] = 'miss activityId or sourceId';
            return $returnData;
        }
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['msg'] = 'token存放数据有问题';
            return $returnData;
        }

        return $shareEventModule->activityProcess($userInfo[2],$sourceId,$activityId);
    }


    /**
     * 享立减分享卡片所需要的数据
     * Author: MeiJay
     * @param Request $request
     * @param ShareEventService $shareEventService
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
	 * @update 梅杰 20180710 获取指定小程序分享卡片数据
     */
    public function getShareCode(Request $request ,LiEventService $shareEventService,ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'msg' => '','data' => [] ];
        $token = $request->input('token');
        $activityId = $request->input(['activityId']);
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['errCode'] = -102;
            $returnData['msg'] = 'token存放数据有问题';
            return $returnData;
        }

        if( !$activityId) {
            $returnData['errCode'] = -103;
            $returnData['msg'] = 'miss activityId ';
            return $returnData;
        }
        $activityData = $shareEventService->getOne($activityId,$userInfo[1]);
        if (!$activityData) {
            $returnData['errCode'] = -104;
            $returnData['msg'] = '活动不存在';
            return $returnData;
        }
        if (!is_dir('./hsshop/xcx/li/')) {
            Storage::makeDirectory('./hsshop/xcx/li/');
        }
        $qrPath = "/hsshop/xcx/li/".$userInfo[2].'_'.$activityId.'.jpg';
        $scene = "'sId':".$userInfo[2].",'aId':".$activityId.",'list':1";
        $page = 'pages/member/shareSalezan/shareSalezan';
        $configId = CommonModule::getXcxConfigIdByToken($token);
        $data = $thirdPlatform->createQrCode($userInfo[1],$scene,$page,$configId);
        if ( $data['errCode'] != 0 ) {
            $returnData['errCode'] = -105;
            $returnData['msg'] = '二维码获取失败:'.$data['errMsg'];
            return $returnData;
        }
        $img = base64_decode($data['data']);
        file_put_contents('.'.$qrPath, $img);
        $returnData['data'] = [
            'card_img' => $activityData['card_img'],
            'Qr_code'  => $qrPath
        ];
        return $returnData;
    }

    /**
     * 注册信息
     */
    public function register(Request $request)
    {
        //参数
        $input = $request->input();
        $mid = $input['mid'];

        $registerService = new LiRegisterService();
        if (!$registerService->isRegistered($mid)) {
            //验证参数
            //去掉手机号输入 直接从member或unified_member表获取 2018012
            $rule = Array(
                'name'         => 'required|between:1,10',
                //'phone'        => 'required|regex:mobile',
                'company_name' => 'required|between:1,26',
                //'company_address' => 'required',
                //'company_position' => 'between:1,20',
            );
            $message = Array(
                'name.required'    => '请输入姓名',
                'name.between'         => '姓名长度为1-10个字符',
                //'phone.required'    => '请输入手机号码',
                //'phone.regex'       => '手机号码格式不正确',
                'company_name.required'    => '请输入公司名称',
                //'company_address.required' => '请输入公司地址',
                'company_name.between' => '公司名称长度为1-26个字符',
                //'company_position.between' => '职务长度为1-20个字符',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                xcxerror($validator->errors()->first());
            }
            $data = [
                'mid' => $mid,
                'name' => $input['name'],
                //'phone' => $input['phone'],
                'company_name' => $input['company_name'],
                'company_position' => $input['company_position'],
//                'company_address' => $input['company_address'],
                'business_licence_url' => $input['business_licence_url'] ?? '',
                'id_card_on' => $input['id_card_on'] ?? '',
                'id_card_off' => $input['id_card_off'] ?? '',
            ];

            //获取用户手机号
            //$data['phone'] = (new MemberModule())->getMemberPhoneByID($mid);
            //运营又要求手机号用户输入 20180201
            $data['phone'] = $input['phone'];

            if (!$registerService->model->insertGetId($data)) {
                xcxerror('领取失败');
            }
            xcxsuccess('领取成功');
        }
        xcxerror('该用户已经领取过');
    }

    /**
     * 助减
     * Author: MeiJay
     * @param Request $request
     * @param LiRegisterService $registerService
     */
    public function reduceLi(Request $request ,LiRegisterService $registerService)
    {
        $returnData=['code'=>40000,'hint'=>''];
        $input = $request->input();
        $mid = $input['mid'];
        $wid = $input['wid'];
        $shareId = $input['shareId'];
        $activityId = $input['activityId'];
        if (!$shareId || !$activityId) {
            xcxerror('missing shareId or activityId');
        }
        //add by jonzhang 2018-02-02 享立减活动判断
        $shareEventService = new LiEventService();
        $where = ['wid' => $wid, 'id' => $activityId, 'type' => 0, 'status' => 0];
        $shareEventResult = $shareEventService->list($where);
        if (!empty($shareEventResult[0]['data'])) {
            $shareEventData = $shareEventResult[0]['data'][0];
            if($shareEventData['start_time']>time())
            {
               xcxerror('活动未开始','-1001');
            }
            if($shareEventData['end_time']<time())
            {
                xcxerror('活动已结束','-1002');
            }
        } else {
            xcxerror('享立减活动不存在','-1003');
        }
        //是否集满
        $liDetailServiceData=(new LiDetailService())->getRowByMidAndEventId($shareId,$activityId);
        if(!empty($liDetailServiceData))
        {
            if($liDetailServiceData['is_full'])
            {
                xcxerror('活动已集满','1');
            }
        }
        //减钱
        $res = (new LiShareEventModule())->insertLiShareEventRecord($wid,$shareId,$mid,$activityId);
        if ($res['code'] == 0  ) {
            xcxsuccess('',$res['data']);
        }
        xcxerror($res['msg']);
    }



    /**
     * 分享成功回调
     * Author: MeiJay
     * @param Request $request
     * @param LiShareEventModule $liShareEventModule
     * @update 梅杰 20180709 增加指定小程序id
     */
    public function shareCallBack(Request $request,LiShareEventModule $liShareEventModule)
    {
        $input = $request->input();
        $mid = $input['mid'];
        $wid = $input['wid'];
        $formId = $input['formIds']['form_ids'];
        $activityId = $input['activityId'];
        if (!$activityId) {
            xcxerror('missing  activityId');
        }
        $xcx_config_id =  CommonModule::getXcxConfigIdByToken($input['token']);
        $res = $liShareEventModule->shareCallBack($mid,$activityId,$wid,$formId,$xcx_config_id);
        if ($res['err_code'] == 0) {
            xcxsuccess('',['shareCount'=>$res['data']['is_share']]);
        }
        xcxerror($res['msg']);
    }

    /**
     * 助减
     * Author: cwh
     * @param Request $request
     */    
    public function friendLi(Request $request)
    {
        //参数
        $input = $request->input();
        $mid = $input['mid'];
        $activityId = $request->input(['activityId']);

        $res = (new LiShareEventModule())->getMyFriend($mid,$activityId);
        xcxsuccess('',$res);
    }
}