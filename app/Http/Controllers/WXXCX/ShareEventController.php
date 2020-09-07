<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/12/8
 * Time: 14:04
 */

namespace App\Http\Controllers\WXXCX;
use App\Http\Controllers\Controller;
use App\Jobs\SendShareEventLog;
use App\Lib\WXXCX\ThirdPlatform;
use App\S\Product\ProductMsgService;
use App\S\ShareEvent\ShareEventService;
use App\S\ShareEvent\ShareEventShareService;
use Illuminate\Http\Request;
use CommonModule;
use App\Module\ProductModule;
use App\S\ShareEvent\ShareEventRecordService;
use Illuminate\Support\Facades\Storage;
use MemberService;
use App\Module\ShareEventModule;
use App\Module\OrderModule;
use WeixinService;
use App\Lib\BLogger;
use App\S\WXXCX\WXXCXConfigService;
use ProductService;
use App\Module\RecommendModule;
use Validator;
use App\S\Weixin\ShopService;


class ShareEventController extends Controller
{
    /**
     * 获取所有帮我享立减的人
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-12-11
     * @update 陈文豪 20180814 处理id错误
     */
    public function getAllActorData($activity_id,$source_id,$wid,Request $request)
    {
        //通过token 获取信息
        $wid = $request->input('wid');

        $service = new ShareEventRecordService( );
        $where = [
            'share_event_id'    => $activity_id,
            'source_id'         => $source_id,
            'wid'               => $wid,
            'current_status'    => 0
        ];
        $data = $service->getListWithPage($where,'','',40);
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
	 * @update 梅杰 20180712 小程序名称修改
     * @update 张永辉 2018年7月25日 图片广告补丁处理
     * @update 何书哲 2018年8月6日 享立减记录发送到数据中心
     * @update 何书哲 2018年8月16日 分享记录添加分享来源，添加小程序配置id
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月09日 返回商品是否有规格字段
     */
     public function showProductDetail(Request $request,ShopService $shopService)
     {
         //定义返回数据数组
         $returnData=['code'=>40000,'hint'=>'',
             'list'=>[
                 'member'=>['total'=>0,'memberInfo'=>[]],
                 'count'=>0,
                 'product'=>[],
                 'unitAmount'=>0.00,
                 'lowerPrice'=>0.00,
                 'share_title' => '',
                 'share_img'   => '',
                 'xcxName'=>'',
                 'isShow'=>0,
                 'reduce_total' => 0.00,
                 'isShare'=>1,
                 'sharer'=>'',
                 'isExpire'=>0,
                 'currentTime'=>time(),
                 'startTime'=>0,
                 'endTime'=>0,
                 'ruleImg'=>'',
                 'ruleTitle'=>'',
                 'ruleContent'=>'',
                 'headImgUrl'=>'',
                 'isNew'=>0
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

         try
         {
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
             $xcx_config_id = $userInfo[3] ?? 0;

             $product = [];
             $shareEventService = new ShareEventService();
             $where = ['wid' => $wid, 'id' => $activityId, 'type' => 0, 'status' => 0];
             $productId=0;
             $unitAmount=0.00;
             $shareTitle = $shareImg = '';
             //add by jonzhang 享立减活动未开始不能够参与 2018-02-01
             $isStart=1;
             $isEnd=0;
             $shareEventResult = $shareEventService->list($where);
                 if (!empty($shareEventResult[0]['data'])) {
                 $shareEventData = $shareEventResult[0]['data'][0];
                 //add by jonzhang 判断享立减活动是否过期 2018-01-22
                 if($shareEventData['end_time']<time())
                 {
                     $returnData['list']['isExpire']=1;
                     $isEnd=1;
                 }
                 if($shareEventData['start_time']>time())
                 {
                     $isStart=0;
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
                 $product['lowerPrice'] = $lowerPrice;
                 $product['img'] = $productData['img'];
                 $product['productImg'] = $productData['productImg'];
                 //$product['content'] = $productData['content'];
                 $product['content'] = ProductModule::addProductContentHost($productData['content']);
                 $product['content'] =  $productModule->dealAdImg($product['content']);
                 //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
                 $product['content'] = dealWithProductContent($wid, $product['content']);

                 // add by zhangyh 20170118  商品留言信息
                 $product['noteList'] = (new ProductMsgService())->getListByProduct($product['productId']);
                 //end

                 // 是否有规格
                 $product['sku_flag'] = $productData['sku_flag'];
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
             $shareEventRecordService=new ShareEventRecordService();

             // todo 分享者业务逻辑
             // todo 享立减活动 参与者用户信息
             $shareEventModule = new ShareEventModule();
             $shareEventMember = $shareEventModule->showActorData(['shareId' => $shareId, 'activityId' => $activityId],$targetAmount,$unitAmount,$mid);
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

             //红包金额
             $returnData['list']['reduce_total'] = $shareEventMember['data']['amount'];
             //保底价
             $returnData['list']['lowerPrice']=$lowerPrice;
             //商品信息
             $returnData['list']['product']=$product;

             // add by zhangyh 20170118  商品留言信息
             $product['noteList'] = (new ProductMsgService())->getListByProduct($product['productId']);
             //end

             //$shop = WeixinService::getStore($wid);
             $store = $shopService->getRowById($wid);
             $shop['data'] = $store;
             $returnData['list']['shop'] = $shop;
             
             // todo 参与者逻辑
             if($shareId!=$mid)
             {
                 $returnData['list']['isShare']=0;
                 $shareData=MemberService::getRowById($shareId);
                 $returnData['list']['sharer']=$shareData['nickname']??'';
                 $returnData['list']['headImgUrl']=$shareData['headimgurl']??'';
                 
                 $key=$shop['data']['share_event_key']??'';
                 $statusData=$shareEventModule->isShareEvent(['shareEventId'=>$activityId,'actorId'=>$mid,'key'=>$key,'shareId'=>$shareId]);
                 //参与者是否为新用户
                 $returnData['list']['isNew']=0;
                 //参与者可以参与活动
                 if($statusData['errCode']==0&&$statusData['data']==1)
                 {
                     if(!$isStart)
                     {
                         $returnData['code'] = -109;
                         $returnData['hint'] = '享立减活动未开始';
                         return $returnData;
                     }
                     if($isEnd)
                     {
                         $returnData['code'] = -110;
                         $returnData['hint'] = '享立减活动已结束';
                         return $returnData;
                     }
                     $member = MemberService::getRowById($mid);
                     $input = [
                         'key' => $key,
                         'actor_id' => $mid,
                         'wid' => $wid,
                         'source_id' => $shareId,
                         'share_event_id' => $activityId,
                         'avatar_url' => $member['headimgurl']??'',
                         'nick_name' => $member['nickname']??'',
                         'source' => 2, //何书哲 2018年8月16日 分享记录添加分享来源
                         'xcx_config_id' => $xcx_config_id, //何书哲 2018年8月16日 添加小程序配置id
                     ];
                     $shareEventRecord = $shareEventRecordService->createShareEventRecord($input);
                     if ($shareEventRecord['err_code'] != 0) {
                         $returnData['code'] = -108;
                         $returnData['hint'] = $shareEventRecord['msg'];
                         return $returnData;
                     }
                     else
                     {
                         $returnData['list']['isNew']=1;
                         $shareEventService->incrementReduceTotal($activityId);
                         //何书哲 2018年8月6日 享立减记录发送到数据中心
                         dispatch((new SendShareEventLog($shareEventRecord['data']))->onQueue('shareEvent'));
                     }
                 }//参与者不可以参与活动
                 return $returnData;
             }
             return $returnData;
         }
         catch(\Exception $ex)
         {
             $returnData['code'] = -1000;
             $returnData['hint'] = $ex->getMessage();
             return $returnData;
         }

     }

    /**
     * todo 待结算订单
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     * @author jonzhang
     * @date 2017-12-11
     */
    public function processWaitSubmitShareEventOrder(Request $request,ShareEventModule $shareEventModule)
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
    public function submitShareEventOrder(Request $request,ShareEventModule $shareEventModule)
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
         $num=intval($num)< 1?1:intval($num);
         $formId=$request->input('formId');
         $formId=intval($formId);
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
             $where=['activityId'=>$activityId,'skuId'=>$skuId,'num'=>$num,'addressId'=>$addressId,'formId'=>$formId];
             $result=$shareEventModule->submitShareEventOrder($wid,$mid,$where,1,10,$remarkNo);
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
            $freight = $orderModule->getFreightByCartIDArr([], $wid, $mid, $umid, $addressId, [['product_id' =>$productId, 'prop_id' => $skuId, 'num' => 1]],false);
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
    public function getRedPacket(Request $request,ShareEventModule $shareEventModule)
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
    public function useRedPacket(Request $request,ShareEventModule $shareEventModule)
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
    public function showMoreShareEvent(Request $request,ShareEventService $shareEventService)
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
        //add by jonzhang 2018-04-08 添加享立减推荐
        $shareGoodsData=(new RecommendModule())->processShareGoods($wid,$activityId);
        if($shareGoodsData['errCode']==0)
        {
            $returnData['list']=$shareGoodsData['data'];
            return $returnData;
        }
        else if($shareGoodsData['errCode']<0)
        {
            return $returnData;
        }
        //add by jonzhang 更多享立减 过滤过期和没有开始的
        $where = ['wid' => $wid, 'type' => 0, 'status' => 0 ,'startTime'=>['<=',time()],'endTime'=>['>=',time()]];
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
					//update by 吴晓平 2018年08月22日  取消分销商品不能够享立减限制
                    /*if(isset($productData['is_distribution']))
                    {
                        //分销商品不能够享立减
                        if($productData['is_distribution']==1)
                        {
                            continue;
                        }
                    }*/
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
                        "id"=>$item['id'],
                        "startTime"=>$item['start_time']??'',
                        "endTime"=>$item['end_time']??'',
                        "currentTime"=>time(),
                        "thumbnail" => $productData['img'],
                        "name" => $productData['title'],
                        "product_id"=>$productData['id']
                    ];
                    $sourceId[]=$item['id'];
                }
            }
            $targetData=[];
            if(!empty($sourceData)&&!empty($sourceId)&&count($sourceId)>=4)
            {
                $keys=array_rand($sourceId, 4);
                for($i = 0; $i < 4; $i++)
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

                if(count($sourceId)%2!=0)
                {
                    unset($sourceData[0]);
                    $returnData['list'] = $sourceData;
                }
                else
                {
                    $returnData['list'] = $sourceData;
                }
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
        $service = new ShareEventRecordService();
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
    public function getProcess(Request $request , ShareEventModule $shareEventModule)
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
     * @update 梅杰 20180710 或者指定小程序分享卡片数据
     */
    public function getShareCode(Request $request ,ShareEventService $shareEventService,ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'msg' => '','data' => [] ];
        $token = $request->input('token');
        $activityId = $request->input(['activityId']);
        $version = $request->input(['version'],0);
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
        
        if (!is_dir('./hsshop/xcx/share/')) {
            Storage::makeDirectory('./hsshop/xcx/share/');
        }

        $qrPath = "/hsshop/xcx/share/".$userInfo[2].'_'.$activityId.'.jpg';
        $scene = "'sId':".$userInfo[2].",'aId':".$activityId.",'list':1";
        $page = $version == 1 ? 'pages/activity/pages/shareSale/shareSale/shareSale':'pages/member/shareSale/shareSale/shareSale';
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
     * 享立减新获取分享卡片
     * @param Request $request
     * @param ShareEventService $shareEventService
     * @param ThirdPlatform $thirdPlatform
     * @return array
     */
    public function getShareCode2(Request $request ,ShareEventService $shareEventService,ThirdPlatform $thirdPlatform)
    {
        $returnData = ['errCode' => 0, 'msg' => '','data' => [] ];
        $token = $request->input('token');
        $activityId = $request->input(['activityId']);
        $version = $request->input(['version'],0);
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

        if (!is_dir('./hsshop/xcx/share/')) {
            Storage::makeDirectory('./hsshop/xcx/share/');
        }

        $qrPath = "/hsshop/xcx/share/".$userInfo[2].'_'.$activityId.'.jpg';
        $scene = "'s':".$userInfo[2].",'a':".$activityId.",'l':e";
        $page = $version == 1 ? 'pages/index/index':'pages/member/shareSale/shareSale/shareSale';
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
     * 添加分享记录
     * @param Request $request 请求参数
     * @return json
     * @auhtor 何书哲 2018年8月6日
     */
    public function shareRecord(Request $request) {
        $token=$request->input('token');
        if(empty($token))
        {
            xcxerror('token为空');
        }
        //获取缓存中保存的用户信息
        $userInfo = CommonModule::getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            xcxerror('token存放数据有问题');
        }
        //取mid和wid
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        $xcx_config_id = $userInfo[3] ?? 0;
        $input = $request->only(['share_event_id']);
        $rule = Array(
            'share_event_id' => 'required'
        );
        $message = Array(
            'share_event_id.required' => '享立减id不能为空'
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()){
            xcxerror($validator->errors()->first());
        }
        //添加记录
        $input['wid'] = $wid;
        $input['share_id'] = $mid;
        $input['share_at'] = time();
        //何书哲 2018年8月16日 添加分享来源和小程序配置id
        $input['source'] = 2;
        $input['xcx_config_id'] = $xcx_config_id;
        $res = (new ShareEventShareService())->add($input);
        if ($res) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }


}