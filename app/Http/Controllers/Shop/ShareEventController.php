<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/4/28
 * Time: 13:44
 */

namespace App\Http\Controllers\Shop;
use App\Http\Controllers\Controller;
use App\Jobs\SendShareEventLog;
use App\Model\Favorite;
use App\Module\FavoriteModule;
use App\S\ShareEvent\ShareEventShareService;
use Illuminate\Http\Request;
use App\Lib\BLogger;
use App\S\ShareEvent\ShareEventService;
use App\Module\ProductModule;
use App\Module\RecommendModule;
use ProductService;
use App\S\ShareEvent\ShareEventRecordService;
use App\S\Product\ProductMsgService;
use App\S\WXXCX\WXXCXConfigService;
use App\Module\ShareEventModule;
use WeixinService;
use MemberService;
use App\Module\OrderModule;
use Validator;
use App\S\Weixin\ShopService;

class ShareEventController extends Controller
{
    public function preview(Request $request)
    {
        return view('shop.shareevent.preview', [
        ]);
    }

    /***
     * todo 享立减预览商品信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2018-04-28
     */
    public function showDetailForPreview(Request $request)
    {
        //定义返回数据数组
        $returnData=['errCode'=>0,'errMsg'=>'',
            'data'=>[
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
        $activityId=$request->input('activityId');
        $activityId=intval($activityId)??0;

        //取mid和wid
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="享立减活动id为空";
            unset($returnData['data']);
            return $returnData;
        }
        if(empty($activityId))
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']="享立减活动id为空";
            unset($returnData['data']);
            return $returnData;
        }

        try
        {

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
                    $returnData['data']['isExpire']=1;
                    $isEnd=1;
                }
                if($shareEventData['start_time']>time())
                {
                    $isStart=0;
                }
                $returnData['data']['startTime']=$shareEventData['start_time'];
                $returnData['data']['endTime']=$shareEventData['end_time'];

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
                $returnData['data']['ruleImg']=$shareEventData['rule_img']??'';
                $returnData['data']['ruleTitle']=$shareEventData['rule_title']??'';
                $returnData['data']['ruleContent']=$shareEventData['rule_text']??'';
                $returnData['data']['ruleContent']=nl2br($returnData['data']['ruleContent']);
            }
            else
            {
                $returnData['errCode'] = -1003;
                $returnData['errMsg'] = '享立减活动不存在';
                unset($returnData['data']);
                return $returnData;
            }

            if(empty($productId))
            {
                $returnData['errCode'] = -1004;
                $returnData['errMsg'] = '商品id不存在';
                unset($returnData['data']);
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
                $product['content'] = $productData['content'];
            } else {
                if ($productResult['errCode'] == 0 && empty($productResult['data'])) {
                    $returnData['errCode'] = -1005;
                    $returnData['errMsg'] = '没有商品数据信息';
                    unset($returnData['data']);
                    return $returnData;
                } else {
                    $returnData['errCode'] = $productResult['errCode'];
                    $returnData['errMsg'] = $productResult['errMsg'];
                    unset($returnData['data']);
                    return $returnData;
                }
            }

            //add by jonzhang
            //享立减每次逐减金额
            $returnData['data']['unitAmount']=$unitAmount;
            //后台设置的分享内容
            $returnData['data']['share_title'] = $shareTitle;
            $returnData['data']['share_img']   = $shareImg;
            //保底价
            $returnData['data']['lowerPrice']=$lowerPrice;
            //商品信息
            $returnData['data']['product']=$product;
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['errCode'] = -1000;
            $returnData['errMsg'] = $ex->getMessage();
            unset($returnData['data']);
            return $returnData;
        }
    }

    /***
     * todo 享立减推荐
     * @param Request $request
     * @param ShareEventService $shareEventService
     * @return array
     * @author jonzhang
     * @date 2018-05-04
     */
    public function showMoreShareEvent(Request $request,ShareEventService $shareEventService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $activityId=$request->input('activityId')??0;
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-10001;
            $returnData['errMsg']="wid为空";
            unset($returnData['data']);
            return $returnData;
        }
        if(empty($activityId))
        {
            $returnData['errCode']=-10002;
            $returnData['errMsg']="活动id为空";
            unset($returnData['data']);
            return $returnData;
        }
        //add by jonzhang 2018-04-08 添加享立减推荐
        $shareGoodsData=(new RecommendModule())->processShareGoods($wid,$activityId);
        if($shareGoodsData['errCode']==0)
        {
            $returnData['data']=$shareGoodsData['data'];
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
					////update by 吴晓平 2018年08月22日  取消分销商品不能够享立减限制
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
                $returnData['data']=$targetData;
            }
            else
            {
                if(count($sourceId)%2!=0)
                {
                    unset($sourceData[0]);
                    $returnData['data'] = $sourceData;
                }
                else
                {
                    $returnData['data'] = $sourceData;
                }
            }
        }
        return $returnData;
    }

    /**
     * todo 获取参与者头像信息
     * @param Request $request 请求参数对象
     * @author jonzhang
     * @date 2018-06-19
     */
    public function getAllActorData(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $activityId=$request->input('activityId');
        $sourceId=$request->input('sourceId');
        $wid=session('wid');

        if (empty($wid))
        {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
        if (empty($activityId))
        {
            $returnData['errCode'] = -102;
            $returnData['errMsg'] = '活动id为空';
            return $returnData;
        }
        if (empty($sourceId))
        {
            $returnData['errCode'] = -103;
            $returnData['errMsg'] = '分享者为空';
            return $returnData;
        }
        $service = new ShareEventRecordService();
        $where = [
            'share_event_id'    => $activityId,
            'source_id'         => $sourceId,
            'wid'               => $wid,
            'current_status'    => 0
        ];
        $data = $service->getListWithPage($where,'','',40);
        if(!empty($data[0]['data']))
        {
            foreach($data[0]['data'] as &$item)
            {
                $item['created_at']    = date('Y/m/d H:i:s',$item['created_at']);
                $item['is_red_packet'] = $item['red_packet_id'] ;
            }
        }
        $returnData['data']=$data;
        return $returnData;
    }

    /**
     * todo 享立减商品详情 分享着和参与者都可以使用
     * @param Request $request
     * @param FavoriteModule $favoriteModule 收藏module
     * @return array
     * @author jonzhang
     * @date 2017-12-11
     * @update 新用户处理
     * @update 何书哲 2018年8月6日 享立减记录发送到数据中心
     * @update 何书哲 2018年8月16日 分享记录添加分享来源
     * @update 许立 2018年09月06日 增加是否收藏活动字段
     * @update 吴晓平 2018年09月12日 把weixinService中的操作迁移到S/ShopService
     */
    public function showProductDetail(Request $request,FavoriteModule $favoriteModule,ShopService $shopService)
    {
        //定义返回数据数组
        $returnData=['errCode'=>0,'errMsg'=>'',
            'data'=>[
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
        $activityId=$request->input('activityId');
        $activityId=intval($activityId)??0;
        $shareId=$request->input('shareId');
        $shareId=intval($shareId);
        $wid=session('wid');
        $mid=session('mid');

        if(empty($activityId))
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']="享立减活动id为空";
            return $returnData;
        }
        if(empty($shareId))
        {
            $returnData['errCode']=-103;
            $returnData['errMsg']="分享者id为空";
            return $returnData;
        }
        if (empty($wid) || empty($mid))
        {
            $returnData['errCode'] = -102;
            $returnData['errMsg'] = 'mid或者wid为空';
            return $returnData;
        }

        try
        {
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
                    $returnData['data']['isExpire']=1;
                    $isEnd=1;
                }
                if($shareEventData['start_time']>time())
                {
                    $isStart=0;
                }
                $returnData['data']['startTime']=$shareEventData['start_time'];
                $returnData['data']['endTime']=$shareEventData['end_time'];

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
                $returnData['data']['ruleImg']=$shareEventData['rule_img']??'';
                $returnData['data']['ruleTitle']=$shareEventData['rule_title']??'';
                $returnData['data']['ruleContent']=$shareEventData['rule_text']??'';
                $returnData['data']['ruleContent']=nl2br($returnData['data']['ruleContent']);
            }
            else
            {
                $returnData['errCode'] = -104;
                $returnData['errMsg'] = '享立减活动不存在';
                return $returnData;
            }

            if(empty($productId))
            {
                $returnData['errCode'] = -105;
                $returnData['errMsg'] = '商品id不存在';
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

                //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
                $product['content'] = dealWithProductContent($wid, $product['content']);

                // add by zhangyh 20170118  商品留言信息
                $product['noteList'] = (new ProductMsgService())->getListByProduct($product['productId']);
                //end
            } else {
                if ($productResult['errCode'] == 0 && empty($productResult['data'])) {
                    $returnData['errCode'] = -106;
                    $returnData['errMsg'] = '没有商品数据信息';
                    return $returnData;
                } else {
                    $returnData['errCode'] = $productResult['errCode'];
                    $returnData['errMsg'] = $productResult['errMsg'];
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
            $returnData['data']['unitAmount']=$unitAmount;
            //后台设置的分享内容
            $returnData['data']['share_title'] = $shareTitle;
            $returnData['data']['share_img']   = $shareImg;

            //小程序名称信息 add by jonzhang 2017-01-08
            $xcxConfigData=(new WXXCXConfigService())->getRow($wid);
            if($xcxConfigData['errCode']==0&&!empty($xcxConfigData['data'])) {
                $returnData['data']['xcxName'] = $xcxConfigData['data']['title'];
                if ($xcxConfigData['data']['app_id']=='wxdcc591f311e441a7')
                {
                    $returnData['data']['isShow'] = 1;
                }
            }
            $shareEventRecordService=new ShareEventRecordService();

            // todo 分享者业务逻辑
            // todo 享立减活动 参与者用户信息
            $shareEventModule = new ShareEventModule();
            $shareEventMember = $shareEventModule->showActorData(['shareId' => $shareId, 'activityId' => $activityId],$targetAmount,$unitAmount,$mid);
            if ($shareEventMember['errCode'] < 0) {
                $returnData['errCode'] = $shareEventMember['errCode'];
                $returnData['errMsg'] = $shareEventMember['errMsg'];
                return $returnData;
            }
            //助减用户信息
            $returnData['data']['member']['memberInfo'] = $shareEventMember['data']['members']??[];
            //助减次数
            $returnData['data']['member']['total'] = $shareEventMember['data']['total']??0;
            //有效助减次数
            $returnData['data']['count'] = $shareEventMember['data']['memberCount']??0;

            //红包金额
            $returnData['data']['reduce_total'] = $shareEventMember['data']['amount'];
            //保底价
            $returnData['data']['lowerPrice']=$lowerPrice;
            //商品信息
            $returnData['data']['product']=$product;

            // add by zhangyh 20170118  商品留言信息
            $product['noteList'] = (new ProductMsgService())->getListByProduct($product['productId']);
            //end

            //$shop = WeixinService::getStore($wid);
            //$returnData['data']['shop'] = $shop;
            $shop = $shopService->getRowById($wid);
            $returnData['data']['shop'] = ['data' => $shop]; //同步以前的数据格式

            // 是否收藏
            $returnData['data']['isFavorite'] = $favoriteModule->isFavorite($mid, $activityId, Favorite::FAVORITE_TYPE_SHARE);

            // 是否收藏
            $returnData['data']['isFavorite'] = $favoriteModule->isFavorite($mid, $activityId, Favorite::FAVORITE_TYPE_SHARE);

            // todo 参与者逻辑
            if($shareId!=$mid)
            {
                $returnData['data']['isShare']=0;
                $shareData=MemberService::getRowById($shareId);
                $returnData['data']['sharer']=$shareData['nickname']??'';
                $returnData['data']['headImgUrl']=$shareData['headimgurl']??'';

                $key=$shop['data']['share_event_key']??'';
                $statusData=$shareEventModule->isShareEvent(['shareEventId'=>$activityId,'actorId'=>$mid,'key'=>$key,'shareId'=>$shareId]);
                //参与者是否为新用户
                $returnData['data']['isNew']=0;
                //参与者可以参与活动
                if($statusData['errCode']==0&&$statusData['data']==1)
                {
                    if(!$isStart)
                    {
                        $returnData['errCode'] = -109;
                        $returnData['errMsg'] = '享立减活动未开始';
                        return $returnData;
                    }
                    if($isEnd)
                    {
                        $returnData['errCode'] = -110;
                        $returnData['errMsg'] = '享立减活动已结束';
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
                        'source' => 1, //何书哲 2018年8月16日 分享记录添加分享来源
                        'xcx_config_id' => 0, //何书哲 2018年8月16日 添加小程序配置id
                    ];
                    $shareEventRecord = $shareEventRecordService->createShareEventRecord($input);
                    if ($shareEventRecord['err_code'] != 0) {
                        $returnData['errCode'] = -108;
                        $returnData['errMsg'] = $shareEventRecord['msg'];
                        return $returnData;
                    }
                    else
                    {
                        $returnData['data']['isNew']=1;
                        $shareEventService->incrementReduceTotal($activityId);
                        //何书哲 2018年8月6日 享立减记录发送到数据中心
                        dispatch((new SendShareEventLog($shareEventRecord['data']))->onQueue('shareEvent'));
                    }
                }//参与者不可以参与活动
                else {
                    $returnData['errCode'] = $statusData['errCode'];
                    $returnData['errMsg']  = $statusData['errMsg'];
                    return $returnData;
                }
                return $returnData;
            }
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['errCode'] = -1000;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }

    }

    /**
     * 享立减待支付页面
     * @author wuxiaoping <2018.06.22>
     * @return [type] [description]
     */
    public function waitSubmitShareOrder()
    {
        return view('shop.shareevent.waitSubmitOrder',[
            'title' =>  '享立减待支付页面'
        ]);
    }

    /**
     * todo 待结算订单
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     * @author jonzhang
     * @date 2017-12-11
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function processWaitSubmitShareEventOrder(Request $request,ShareEventModule $shareEventModule,ShopService $shopService)
    {
        //定义返回数据数组
        //$returnData=['errCode'=>0,'errMsg'=>'', 'data'=>[]];
        //try
        //{
            $productId=$request->input('productId');
            $productId=intval($productId);
            $skuId=$request->input('skuId');
            $num=$request->input('num') ?? 1;
            $skuId=intval($skuId);
            $activityId=$request->input('activityId');
            $addressId = $request->input('address_id') ?? 0;

            $wid=session('wid');
            $mid=session('mid');
            if (empty($wid) || empty($mid)) {
                $returnData['errCode'] = -102;
                $returnData['errMsg'] = 'mid或者wid为空';
                error('mid或者wid为空');
                //return $returnData;
            }

            $where=['activityId'=>$activityId,'skuId'=>$skuId,'num'=>$num];
            $result=$shareEventModule->processWaitSubmitShareEventOrder($wid,$mid,$where);

            /**update 吴晓平 2018年6月27日 快递收货地址显示**/
            $addressData = [];
            if (!$addressId) {
                if (empty($result['data']['address']['default']) && !empty($result['data']['address']['all'])) {
                    $addressData = $result['data']['address']['all'][0];
                }else if (!empty($result['data']['address']['default'])) {
                    $addressData = $result['data']['address']['default'][0];
                }
            }else {
                foreach ($result['data']['address']['all'] as $key => $value) {
                    if ($addressId == $value['id']) {
                        $addressData = $value;
                    }
                }
            }
            /**end**/

            if ($result['errCode'] != 0) {
                error($result['errMsg']);
            }
            //$store = WeixinService::getStageShop($wid);
            $store = $shopService->getRowById($wid);
            $shopName='';
            if(!empty($store))
            {
                $shopName=$store['shop_name'];
            }
            $shopUrl=config('app.url').'shop/index/'.$wid;
            $productAmount = $result['data']['product_data']['price'] * $result['data']['product_data']['num'];
            $lastAmount = ($result['data']['product_data']['price']*$num) + $result['data']['freight'];
            return view('shop.shareevent.waitSubmitOrder',[
                'title'          => '享立减待支付页面',
                'userAddress'    => $result['data']['address'],
                'shop_name'      => $shopName,
                'freight'        => $result['data']['freight'],
                'shop_url'       => $shopUrl,
                'productData'    => $result['data']['product_data'],
                'product_amount' => $productAmount,
                'last_amount'    => $lastAmount,
                'balance'        => $result['data']['balance'],
                'addressData'    => $addressData,
                'wid'            => $wid,

            ]);
            /*if($result['errCode']==0)
            {
                $returnData['data']=$result['data'];

            }
            else
            {
                $returnData['errCode']=$result['errCode'];
                $returnData['errMsg']=$result['errMsg'];
            }
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1000;
            $returnData['errMsg']=$ex->getMessage();
            return $returnData;
        }*/
    }

    /**
     * todo 提交享立减订单
     * @param Request $request
     * @param ShareEventModule $shareEventModule
     * @return array
     * @author jonzhang
     * @date 2017-12-12
     * @update 何书哲 2018年8月20日 修改创建微商城享立减订单来源 1->0
     * @update 许立 2018年10月16日 百度小程序来源处理
     */
    public function submitShareEventOrder(Request $request,ShareEventModule $shareEventModule)
    {
        //定义返回数据数组
        $returnData=['errCode'=>0,'errMsg'=>'', 'data'=>[]];
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
                $returnData['code']=-101;
                $returnData['hint']=$errMsg;
                return $returnData;
            }
            $wid=session('wid');
            $mid=session('mid');
            if (empty($wid) || empty($mid)) {
                $returnData['errCode'] = -102;
                $returnData['errMsg'] = 'mid或者wid为空';
                return $returnData;
            }
            $where=['activityId'=>$activityId,'skuId'=>$skuId,'num'=>$num,'addressId'=>$addressId,'formId'=>$formId];
            if (session('reqFrom') == 'aliapp') {
                $source = 2;
            } elseif (session('reqFrom') == 'baiduapp') {
                $source = 3;
            } else {
                $source = 0;
            }
            $result=$shareEventModule->submitShareEventOrder($wid,$mid,$where,$source,10,$remarkNo);

            if($result['errCode']==0)
            {
                $returnData['data']=$result['data'];
            }
            else
            {
                $returnData['errCode']=$result['errCode'];
                $returnData['errMsg']=$result['errMsg'];
            }
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1000;
            $returnData['errMsg']=$ex->getMessage();
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
        $returnData=['errCode'=>0,'errMsg'=>'', 'data'=>0];
        try
        {
            $addressId=$request->input('addressId');
            $addressId=intval($addressId);
            $productId=$request->input('productId');
            $productId=intval($productId);
            $skuId=$request->input('skuId');
            $skuId=intval($skuId);
            if(empty($addressId))
            {
                $returnData['errCode'] = -103;
                $returnData['errMsg'] = '地址id为空';
                return $returnData;
            }
            if(empty($productId))
            {
                $returnData['errCode'] = -104;
                $returnData['errMsg'] = '商品id为空';
                return $returnData;
            }

            $wid=session('wid');
            $mid=session('mid');
            if (empty($wid) || empty($mid)) {
                $returnData['errCode'] = -102;
                $returnData['errMsg'] = 'mid或者wid为空';
                return $returnData;
            }
            //此处为商品的运费
            $member = MemberService::getRowById($mid);
            $umid = $member['umid']??0;
            $freight = $orderModule->getFreightByCartIDArr([], $wid, $mid, $umid, $addressId, [['product_id' =>$productId, 'prop_id' => $skuId, 'num' => 1]],false);
            $returnData['data'] = sprintf('%.2f', $freight);
            return $returnData;
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1000;
            $returnData['errMsg']=$ex->getMessage();
            return $returnData;
        }
    }

    /**
     * todo 查询最新享立减活动参与者信息
     * @param Request $request
     * @author jonzhang
     * @date 2018-01-10
     */
    public function  showShareEventRecord()
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
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
            $returnData['data']=$data[0]['data'];
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
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $sourceId = $request->input(['sourceId']);
        $activityId = $request->input(['activityId']);
        $mid=session('mid');
        if(!isset($sourceId) || !$activityId) {
            $returnData['errCode'] = -103;
            $returnData['errMsg'] = 'miss activityId or sourceId';
            return $returnData;
        }
        if (empty($mid))
        {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
        $result=$shareEventModule->activityProcess($mid,$sourceId,$activityId);
        $returnData['errCode']=$result['code'];
        $returnData['errCode']=$result['msg'];
        return $returnData;
    }

    //享立减详情
    public function showEventDetail()
    {
        return view('shop.shareevent.showEventDetail',[
            'title' => '享立减详情'
        ]);
    }

    /**
     * 添加分享记录
     * @param Request $request 请求参数
     * @return json
     * @auhtor 何书哲 2018年8月6日
     * @update 何书哲 2018年8月16日 添加分享来源和小程序配置id
     */
    public function shareRecord(Request $request) {
        $input = $request->only(['share_event_id']);
        $rule = Array(
            'share_event_id' => 'required'
        );
        $message = Array(
            'share_event_id.required' => '享立减id不能为空'
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $wid=session('wid');
        $mid=session('mid');
        //添加记录
        $input['wid'] = $wid;
        $input['share_id'] = $mid;
        $input['share_at'] = time();
        //何书哲 2018年8月16日 添加分享来源和小程序配置id
        $input['source'] = 1;
        $input['xcx_config_id'] = 0;
        $res = (new ShareEventShareService())->add($input);
        if ($res) {
            success();
        } else {
            error();
        }
    }

}