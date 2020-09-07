<?php
/**
 * 享立减 商家后台
 *
 * @package
 * @author  cwh
 */
namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\Favorite;
use App\Module\FavoriteModule;
use App\Module\ShareEventModule;
use App\S\Member\MemberService;
use App\S\ShareEvent\ShareEventRecordService;
use App\S\ShareEvent\ShareEventShareService;
use App\S\WXXCX\WXXCXConfigService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Validator;
use ProductService;
use WeixinService;
use OrderService;

use App\S\ShareEvent\ShareEventService;
use Illuminate\Validation\Rule;
use App\S\ShareEvent\ShareRewardService;
use App\Module\CommonModule;
use App\S\Weixin\ShopService;

class ShareEventController extends Controller
{

    /*
     * @return void
     */
    public function __construct()
    {
        $this->leftNav = 'shareEvent';
    }

    /**
     * 享立减后台列表
     * @update 许立 2018年09月06日 返回收藏量
     */
    public function list(Request $request, ShareEventService $shareEventService, FavoriteModule $favoriteModule)
    {
        $wid    = session('wid');
        $shareRewardService = new ShareRewardService();
        $rewardSet = $shareRewardService->getAllList($wid,'',false);
        $is_open = $rewardId = 0;
        $reduceData = ['is_open' => $is_open,'id' => $rewardId,'card_img' => '','share_title' => '','share_img' => ''];
        if ($rewardSet) {
            $is_open = $rewardSet[0]['is_open'];
            $rewardId = $rewardSet[0]['id'];
            $reduceData['is_open']     = $is_open;
            $reduceData['id']          = $rewardId;
            $reduceData['card_img']    = $rewardSet[0]['card_img'];
            $reduceData['share_title'] = $rewardSet[0]['share_title'];
            $reduceData['share_img']   = $rewardSet[0]['share_img'];
        }
        $where['status'] = 0;
        $where['wid']=session('wid');
        $data = $request->input();
        if (isset($data['type'])) {
            $where['type'] = $data['type'] ?? 0;
        }
        //add by jonzhang 2018-04-17 享立减添加商品搜索
        if(!empty($data['product_name']))
        {
            $where['product_name']=$data['product_name'];
        }
        if(isset($data['activityStatus']))
        {
            $where['activityStatus'] = $data['activityStatus'];
        }
        list($list, $pageHtml) = $shareEventService->list($where);
        //add by jonzhang 2018-04-20
        foreach($list['data'] as &$item)
        {
            $currentTime=time();
            $item['activityStatusName']="";
            if($item['start_time']<=$currentTime&&$currentTime<=$item['end_time'])
            {
                $item['activityStatusName'] = "进行中";
            }
            else if($item['start_time']>$currentTime)
            {
                $item['activityStatusName'] = "未开始";
            }
            else if($item['end_time']<$currentTime)
            {
                $item['activityStatusName'] = "已结束";
            }

            $item['start_time']=date('Y-m-d H:i:s',$item['start_time']);
            $item['end_time']=date('Y-m-d H:i:s',$item['end_time']);
            $item['created_time']=date('Y-m-d H:i:s',$item['created_time']);
        }
        //add by zhangyh 20180622 判断是否配置小程序
        /*$isMinApp = 0;
        $config = (new WXXCXConfigService())->getRow($wid);
        if (empty($config['errCode']) && !empty($config['data'])) {
            $isMinApp = 1;
        } update by 吴晓平 2018.08.13 微商城也有做享立减，所以不需要判断是否判断小程序是否有配置*/

        // 收藏量
        $list['data'] = $favoriteModule->handleListFavoriteCount($list['data'], Favorite::FAVORITE_TYPE_SHARE);

        return view('merchants.shareEvent.shareList',array(
            'title'      => '享立减',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'list',
            'pageHtml'   => $pageHtml,
            'list'       => $list['data'],
            'reduceData' => $reduceData,
            //'isMinApp'   => $isMinApp,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180622
     * @desc
     * @other 修改请通知zhangyh
     * @update 张永辉 20180628 添加小程序微页面获取二维码
     * @update 张永辉 2018年7月20日 获取团购二维码
     */
    public function getMinAppQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id','');
        $from = $request->input('from','');
        switch ($from){
            case 'litePage' :
                $url = 'pages/micropage/index/index?id='.$id;
                break;
            case 'groups':
                $url = 'pages/activity/pages/grouppurchase/detail/detail?ruleId='.$id;
                break;
            default:
                $url = 'pages/activity/pages/shareSale/shareSale/shareSale?activityId='.$id.'&list_Or_url=0';
        }
        if ($request->input('url')){
            $url = $request->input('url');
        }
        $result = (new ThirdPlatform())->getXCXQRCode($wid, 200, $url);
        if ($result['errCode'] == '0'){
            $url = $result['data'];
        
        }else{
            $url = '';
            error($result['errMsg']);
        }

        success('','',$url);
    }

    /**
     * 获取微商城推广二维码
     * @return [type] [description]
     */
    public function extendQrCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            error('请选择要生成活动的推广二维码');
        }
        $eventData = (new ShareEventService())->getRow(['id' => $id]);
        if ($eventData['errCode'] <> 0) {
            error('该活动不存在或已被删除');
        }

        $commonModule = new CommonModule(); 
        $shopUrl = config('app.url') . 'shop/product/detail/' . session('wid').'/'.$eventData['data']['product_id'].'?activityId='.$id;
        $url = $commonModule->qrCode($wid, $shopUrl);
        $data['qrcode'] = $url;
        $data['url'] = $shopUrl;
        
        success('','',$data);
    }

    /**
     * 获取小程序推广二维码
     * @return [type] [description]
     */
    public function extendQrCodeXcx(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            error('请选择要生成活动的推广二维码');
        }
        $commonModule = new CommonModule(); 
        $shopUrl = 'pages/activity/pages/shareSale/shareSale/shareSale?activityId='.$id.'&list_Or_url=0';
        $result = $commonModule->qrCode($wid, $shopUrl,1);
        
        success('','',$result);
    }

    /**
     * 下载微商城享立减活动二维码
     * @author 吴晓平 <2018年08月13日>
     */
    public function qrCodeDownload(Request $request)
    {
        $url = $request->input('url') ?? '';
        if (empty($url)) {
            error('生成二维码的活动链接为空');
        }
        return (new CommonModule())->qrCodeDownload(session('wid'), $url);
    }

    /**
     * 下载小程序享立减活动二维码
     * @author 吴晓平 <2018年08月13日>
     */
    public function qrCodeDownloadXcx(Request $request)
    {
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            error('请选择要生成活动的推广二维码');
        }
        $url = 'pages/activity/pages/shareSale/shareSale/shareSale?activityId='.$id.'&list_Or_url=0';
        return (new CommonModule())->qrCodeDownload(session('wid'), $url,1);
    }

    /**
     * 设置红包开关
     * @return [type] [description]
     */
    public function openReward(Request $request)
    {
        $is_open = $request->input('is_open') ?? 0;
        $id = $request->input('id') ?? 0;
        $wid    = session('wid');
        $shareRewardService = new ShareRewardService();
        $data['is_open'] = $is_open;
        if (!$id) {
            $data['wid'] = $wid;
            $rs = $shareRewardService->add($data);
        }else {
            $rs = $shareRewardService->update($id,$data);
        }

        if ($rs) {
            success();
        }

        error();

    }

    public function del(Request $request)
    {
        $wid    = session('wid');
        $id     = $request->input('id',0);
        $data['type']   = $request->input('type',0);
        $data['status'] = $request->input('status',0);

        if ($data['status'] == 1) {
            $data['type'] = 1;
        }
        $where = ['id' => $id, 'wid' => $wid];
        (new ShareEventService())->update($where, $data);
        return mysuccess('操作成功', '/merchants/shareEvent/list');
    }

    public function create(Request $request, ShareEventService $shareEventService)
    {
        if ( $request->isMethod('post') ) {
            return $this->_create($request->input());
        }
        $data = $showImgs = [];
        $id = $request->input('id',0);
        $wid = session('wid');
        $shareRewardService = new ShareRewardService();
        $rewardSet = $shareRewardService->getAllList($wid);
        if ($rewardSet[0]['data']) {
            $data['card_img']    = $rewardSet[0]['data'][0]['card_img'];
            $data['share_title'] = $rewardSet[0]['data'][0]['share_title'];
            $data['share_img']   = $rewardSet[0]['data'][0]['share_img'];
        }
        if ($id > 0) {
            $data = $shareEventService->getOne($id, $wid);
            $showImgs = explode(',',$data['show_imgs']);
            //add by jonzhang
            if(!empty($data['unit_amount']))
                $data['unit_amount']=sprintf('%.2f',$data['unit_amount']/100);
            if(!empty($data['lower_price']))
                $data['lower_price']=sprintf('%.2f',$data['lower_price']/100);

            $data['product_detail'] = ProductService::getDetail($data['product_id']);
            $data['start_time']     = $data['start_time'] ? date('Y-m-d H:i:s',$data['start_time']) : '';
            $data['end_time']       = $data['end_time'] ? date('Y-m-d H:i:s',$data['end_time']) : '';
            // $data['rule_text']      = nl2br($data['rule_text']);
        }
        return view('merchants.shareEvent.shareCreate',array(
            'title'    => '享立减',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'create',
            'data'      => $data,
            'showImgs'  => $showImgs,
        ));
    }

    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    private function _create($input)
    {
        $wid = session('wid');
        $shopService = new ShopService();
        /* 数据接收 */
        /* 数据验证 */
        $rules = array(
            'title'         => 'required',
            'product_id'    => 'required',
            'lower_price'   => 'required',
            'unit_amount'   => 'required',
            'share_title'   => 'required',
            'share_img'     => 'required',
            'act_img'       => 'required',
            'show_imgs'     => 'required',
            'subtitle'      => 'required',
            'btn_title'     => 'required',
//            'card_img'      => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required'
        );
        $messages = array(
            'mphone.required'       => '请输入标题',
            'product_id.required'   => '请输入商品ID',
            'lower_price.required'  => '请输入保底价',
            'unit_amount.required'  => '请输入递减金额',
            'share_title.required'  => '请输入分享标题',
            'share_img.required'    => '请设置享立减的分享图片',
            'act_img.required'      => '请设置享立减活动图片',
            'show_imgs.required'    => '请设置享立减商品图片',
            'subtitle.required'     => '请输入活动副标题',
            'btn_title.required'    => '请输按钮名称' ,
//            'card_img.required'     => '请设置卡片图片',
            'start_time.required'   => '请设置生效开始时间',
            'end_time.required'     => '请设置生效结束时间'

        );
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            return myerror( $validator->errors()->first() );
        }

       if ($input['is_initial'] == 1) {
           if (!isset($input['initial_value']) && !$input['initial_value']) {
               return myerror('请输入助减人数初始值');
           }
       }

       //$showImg = explode(',',$input['show_imgs']);
       if (count($input['show_imgs']) > 10) {
           return myerror('活动商品图片最多只能添加10张');
       }

       if (strtotime($input['start_time']) >= strtotime($input['end_time'])) {
           return myerror('请设置生效结束时间大于开始时间');
       }

        $data['title']       = $input['title'];
        $data['product_id']  = $input['product_id'];
        $data['lower_price'] = $input['lower_price'];
        $data['unit_amount'] = $input['unit_amount'];
        $data['wid'] = $wid;
        /*add by wuxiaoping 添加分享内容到数据库*/
        $data['share_title'] = $input['share_title'] ?? '';
        $data['share_img']   = $input['share_img'] ?? '';

        $data['card_img']      = $input['card_img'] ?? '';
        $data['act_img']       = $input['act_img'] ?? '';
        $data['subtitle']      = $input['subtitle'] ?? '';
        $data['is_initial']    = $input['is_initial'] ?? 0;
        $data['initial_value'] = $input['initial_value'] ?? 0;
        $data['show_imgs']     = join(',',$input['show_imgs']);
        $data['button_title']  = $input['btn_title'] ?? '';
        $data['start_time']    = strtotime($input['start_time']);
        $data['end_time']      = strtotime($input['end_time']);
        $data['rule_title']    = $input['rule_title'] ?? '享立减规则';
        $data['rule_img']      = $input['rule_img'] ?? '';
        $data['rule_text']     = $input['rule_text'] ?? '';
        //add by jonzhang
        $data['product_name']=$input['product_name']??'';
        
        if ($data['unit_amount']<0||$data['lower_price']<0) {
            return myerror( '价格设置错误' );
        }
        $id = $input['id'] ?? 0;
        //add by jonzhang 2018-01-12 保底价不能高于商品价
        $productData=ProductService::getDetail($data['product_id']);
        if($data['lower_price']>=$productData['price'])
        {
            return myerror( '保底价必须低于商品价格' );
        }
        if($data['unit_amount']>$productData['price'])
        {
            return myerror( '逐减金额不能够高于商品价格' );
        }
        if($data['lower_price']+$data['unit_amount']>$productData['price'])
        {
            return myerror( '保底价与逐减金额之和不能够高于商品价格' );
        }

        if ( $id > 0 ) {
            $where = ['id' => $id, 'wid' => $wid];
            (new ShareEventService())->update($where, $data);
            return mysuccess('修改成功', '/merchants/shareEvent/list');
        }
        (new ShareEventService())->create($data);
        //add 生成默认key MayJay
        //$shopData = WeixinService::getStore($wid);
        $shopData = $shopService->getRowById($wid);
        if(empty($shopData['share_event_key'])){
            $saveShopData['share_event_key'] = $wid.'_'.time();
            //WeixinService::init('wid',$wid)->where(['id'=>$wid])->update($data,false);
            $shopService->update($wid,$saveShopData);
        }
        return mysuccess('添加成功', '/merchants/shareEvent/list');

    }

    /**
     * 一键翻新
     * Author: MeiJay
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function refreshKey(Request $request,ShopService $shopService)
    {
        if($request->isMethod('post')){
            $wid = session('wid');
            //$shopData = WeixinService::getStore($wid);
            $shopData = $shopService->getRowById($wid);
            if($shopData){
                $data['share_event_key'] = $wid.'_'.time();
                //$re = WeixinService::init('wid',$wid)->where(['id'=>$wid])->update($data,false);
                $re = $shopService->update($wid,$data);
                if($re){
                    success();
                }            }
        }
        error();
    }

    /**
     * 微页面列表获取
     * @param Request $request 请求参数
     * @param ShareEventService $shareEventService 享立减service
     * @update 何书哲 2018年9月18日 添加微页面列表分页
     */
    public function getList(Request $request, ShareEventService $shareEventService)
    {
        $pageSize = $request->input('pageSize', 0);
        $where['wid'] = session('wid');
        $where['status'] = 0;
        $where['type']   = 0;
        $data = $request->input();
        if (isset($data['type'])) {
            $where['type'] = $data['type'] ?? 0;
        }
        list($list, $pageHtml) = $shareEventService->list($where, '', '', $pageSize);
        success('','',$list);
    }

    /**
     * 红包设置
     * @return [type] [description]
     */
    public function rewardSet(Request $request)
    {
        $wid = session('wid');
        $shareRewardService = new ShareRewardService();
        if ($request->isMethod('post')) {
            $input = $request->input();
            $id = $input['id'] ?? 0;
            if (isset($input['source']) && $input['source']== 'share') {
                $rules = [
                    'card_img'    => 'required',
                    'share_title' => 'required',
                    'share_img'   => 'required'
                ];

                $messages = [
                    'card_img.required'    => '请上传卡片图片',
                    'share_title.required' => '请输入分享标题',
                    'share_img.required'   => '请上传分享图片',
                ];

                $validator = Validator::make($input,$rules,$messages);
                if ($validator->fails()) {
                    error($validator->errors()->first());
                }
                $data['card_img']    = $input['card_img'];
                $data['share_title'] = $input['share_title'];
                $data['share_img']   = $input['share_img'];

            }else {
                if ($input['type'] == 0) {
                    if (!isset($input['fixed']) || !$input['fixed']) {
                        error('请输入红包固定的助减金额');
                    }else {
                        if (!is_numeric($input['fixed'])) {
                            error('红包助减金额只能为整数或小数');
                        }
                    }
                }
                if ($input['type'] == 1) {
                    if (!isset($input['minimum']) || (!$input['minimum'] && $input['minimum'] <> 0) || !isset($input['maximum']) || (!$input['maximum'] && $input['maximum'] <> 0)) {
                        error('请输入随机红包助减金额范围');
                    }else {
                        $reg = '/^[0-9][0-9]*$/';
                        if (!preg_match($reg,$input['minimum']) || !preg_match($reg,$input['maximum'])) {
                            error('红包随机范围请输入整数');
                        }

                        if ($input['maximum'] <= $input['minimum']) {
                            error('红包范围设置不正确,请查证后重新输入');
                        }
                    }
                }
                $minimum = 0;
                $maximum = 0;
                $fixed_money = 0;
                if ($input['type'] == 1) {
                    $minimum = $input['minimum'];
                    $maximum = $input['maximum'];
                }else {
                    $fixed_money = $input['fixed'];
                }
                $data['type']        = $input['type'];
                $data['fixed_money'] = $fixed_money;
                $data['minimum']     = $minimum;
                $data['maximum']     = $maximum;
            }
            
            if ($id) {
                $rs = $shareRewardService->update($id,$data);
            }else {
                $data['wid'] = $wid;
                $rs = $shareRewardService->add($data);
            }
            if ($rs) {
                success();
            }
            error();
        }
        list($list,$pageHtml) = $shareRewardService->getAllList($wid);

        $returnData = ['status' => 1,'info' => '','data' => []];
        if ($list['data']) {
            $returnData['data'] = $list['data'][0];
        }
        return $returnData;

    }

    /**
     * 享立减查看数据页面渲染
     * @param Request $request 请求参数
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 何书哲 2018年8月10日
     */
    public function ShareEventDataAnalysis(Request $request) {
        $id = $request->input('id');
        return view('merchants.shareEvent.dataAnalysis',array(
            'title'      => '享立减',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'list',
            'id'         => $id
        ));
    }

    /**
     * 获取享立减活动概览
     * @param Request $request 请求参数
     * @return json
     * @author 何书哲 2018年8月9日
     */
    public function ShareEventDataStatistics(Request $request)
    {
        $id = $request->input('id');
        $wid = session('wid');
        if (empty($id)) {
            error('享立减id不能为空');
        }
        $queryUrl = config('app.dc_url').'/api/v1/shareEventAnalysisData?share_event_id='.$id.'&wid='.$wid;
        $res = jsonCurl($queryUrl);
        //获取享立减活动信息
        $shareEventData = (new ShareEventService())->getOne($id, $wid);
        $res['title'] = $shareEventData ? $shareEventData['title'] : '';
        $res['product_name'] = $shareEventData ? $shareEventData['product_name'] : '';
        $res['start_time'] = $shareEventData ? date('Y-m-d H:i:s', $shareEventData['start_time']) : '';
        $res['end_time'] = $shareEventData ? date('Y-m-d H:i:s', $shareEventData['end_time']) : '';
        $res['lower_price'] = $shareEventData? sprintf('%.2f', $shareEventData['lower_price']/100) : 0.00;
        $res['unit_amount'] = $shareEventData ? sprintf('%.2f', $shareEventData['unit_amount']/100) : 0.00;
        $sale_price = 0.00;
        if ($shareEventData) {
            $productInfo = ProductService::getDetail($shareEventData['product_id']);
            if ($productInfo) {
                $sale_price = $productInfo['price'];
            }
        }
        $res['sale_price'] = sprintf('%.2f', $sale_price);
        success('', '', $res);
    }

    /**
     * 获取享立减用户分析
     * @param Request $request 请求参数
     * @return json
     * @author 何书哲 2018年8月9日
     * @update 何书哲 2018年9月21日 修复享立减用户分析是否购买不正确
     */
    public function ShareEventMemberAnalysis(Request $request) {
        $id = $request->input('id');
        $wid = session('wid');
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 5);
        $shareEventModule = new ShareEventModule();
        $memberService = new MemberService();
        $shareEventShareService = new ShareEventShareService();
        $shareEventService = new ShareEventService();
        if (empty($id)) {
            error('享立减id不能为空');
        }
        $sourceData = [];
        $queryUrl = config('app.dc_url').'/api/v1/shareEventAnalysisList?share_event_id='.$id.'&wid='.$wid.'&page='.$page.'&pageSize='.$pageSize;
        $res = jsonCurl($queryUrl);
        $sourceIds = array_column($res['data'], 'source_id');
        //分享者的头像和昵称
        $sourceData = $memberService->getListById($sourceIds);
        foreach ($sourceData as $member) {
            $sourceData[$member['id']] = $member;
        }
        if (isset($res['data']) && $res['data']) {
            foreach ($res['data'] as &$item) {
                //是否购买
                $orderData = 0;
                //$purchasedData = $shareEventModule->isSharePurchasedByMid(['source_id'=>$item['source_id'], 'share_event_id'=>$id, 'source'=>$item['source']]);
                (($item['source'] == 1 && OrderService::init()->model->wheres(['wid'=>$wid, 'mid'=>$item['source_id'], 'share_event_id'=>$id, 'pay_way'=>['<>',0], 'source'=>0])->first())
                    || ($item['source'] == 2 && OrderService::init()->model->wheres(['wid'=>$wid, 'mid'=>$item['source_id'], 'share_event_id'=>$id, 'pay_way'=>['<>',0], 'source'=>1])->first())
                    || (OrderService::init()->model->wheres(['wid'=>$wid, 'mid'=>$item['source_id'], 'share_event_id'=>$id, 'pay_way'=>['<>',0]])->first())) && $orderData = 1;
                $item['is_purchased'] = $orderData;
                //分享者的头像和昵称
                $item['headimgurl'] = isset($sourceData[$item['source_id']]['headimgurl']) ? $sourceData[$item['source_id']]['headimgurl'] : config('app.url').'static/images/member_default.png';
                $item['nickname'] = isset($sourceData[$item['source_id']]['nickname']) ? $sourceData[$item['source_id']]['nickname'] : '';
                //获取分享时间 如果有分享记录，则获取；如果没有分享记录，则获取活动的开始时间
                $shareShareData = $shareEventShareService->getRowOrderByWhere(['share_event_id'=>$id, 'share_id'=>$item['source_id'], 'source'=>$item['source']], 'share_at', 'ASC');
                $shareData = $shareEventService->getOne($id, $wid);
                $item['share_at'] = $shareShareData ? date('Y-m-d H:i:s', $shareShareData['share_at']) : date('Y-m-d H:i:s', $shareData['start_time']);
                //享立减达到保底价时间，如果未达到，则按下单时间来算
                $item['complete_time'] = $shareEventModule->reachLowerPriceTime($id, $wid, $item['share_at'], $item['source_id'], $item['is_purchased'], $item['source']);
                $item['source'] = ($item['source'] == 1 ? '(微商城)' : ($item['source'] == 2 ? '(小程序)' : ''));
                //获取小程序名称
                $item['xcxTitle'] = '';
                $shareShareData && ($xcxInfo = (new WXXCXConfigService())->getListByCondition(['wid'=>$wid,'id'=>$shareShareData['xcx_config_id']]))
                && ($item['xcxTitle'] = $xcxInfo['data'] ? $xcxInfo['data'][0]['title']  : '');
            }
        }
        $paginator = new LengthAwarePaginator([], $res['total'], $pageSize, null, ['path' => $request->url()]);
        $list = $paginator->appends($request->input());
        $pageHtml = $list->links();
        $res['pageHtml'] = $pageHtml;
        success('', '', $res);
    }

    /**
     * 享立减用户分析数据导出
     * @param Request $request 请求参数
     * @author 何书哲 2018年8月10日
     */
    public function ShareEventDataExport(Request $request) {
        $id = $request->input('id');
        $wid = session('wid');
        $shareEventModule = new ShareEventModule();
        $memberService = new MemberService();
        $shareEventShareService = new ShareEventShareService();
        $shareEventService = new ShareEventService();
        if (empty($id)) {
            error('享立减id不能为空');
        }
        $sourceData = $purchasedData = $orderData = [];
        $queryUrl = config('app.dc_url').'/api/v1/shareEventAnalysisList?share_event_id='.$id.'&wid='.$wid.'&type=1';
        $res = jsonCurl($queryUrl);
        $sourceIds = array_column($res['data'], 'source_id');
        //分享者的头像和昵称
        $sourceData = $memberService->getListById($sourceIds);
        foreach ($sourceData as $member) {
            $sourceData[$member['id']] = $member;
        }
        if (isset($res['data']) && $res['data']) {
            foreach ($res['data'] as &$item) {
                //是否购买
                $orderData = 0;
                //$purchasedData = $shareEventModule->isSharePurchasedByMid(['source_id'=>$item['source_id'], 'share_event_id'=>$id, 'source'=>$item['source']]);
                (($item['source'] == 1 && OrderService::init()->model->wheres(['wid'=>$wid, 'mid'=>$item['source_id'], 'share_event_id'=>$id, 'pay_way'=>['<>',0], 'source'=>0])->first())
                    || ($item['source'] == 2 && OrderService::init()->model->wheres(['wid'=>$wid, 'mid'=>$item['source_id'], 'share_event_id'=>$id, 'pay_way'=>['<>',0], 'source'=>1])->first())
                    || (OrderService::init()->model->wheres(['wid'=>$wid, 'mid'=>$item['source_id'], 'share_event_id'=>$id, 'pay_way'=>['<>',0]])->first())) && $orderData = 1;
                $item['is_purchased'] = $orderData;
                //分享者的头像和昵称
                $item['headimgurl'] = isset($sourceData[$item['source_id']]['headimgurl']) ? $sourceData[$item['source_id']]['headimgurl'] : config('app.url').'static/images/member_default.png';
                $item['nickname'] = isset($sourceData[$item['source_id']]['nickname']) ? $sourceData[$item['source_id']]['nickname'] : '';
                //获取分享时间 如果有分享记录，则获取；如果没有分享记录，则获取活动的开始时间
                $shareShareData = $shareEventShareService->getRowOrderByWhere(['share_event_id'=>$id, 'share_id'=>$item['source_id'], 'source'=>$item['source']], 'share_at', 'ASC');
                $shareData = $shareEventService->getOne($id, $wid);
                $item['share_at'] = $shareShareData ? date('Y-m-d H:i:s', $shareShareData['share_at']) : date('Y-m-d H:i:s', $shareData['start_time']);
                //享立减达到保底价时间，如果未达到，则按下单时间来算
                $item['complete_time'] = $shareEventModule->reachLowerPriceTime($id, $wid, $item['share_at'], $item['source_id'], $item['is_purchased'], $item['source']);
                $item['source'] = ($item['source'] == 1 ? '微商城' : ($item['source'] == 2 ? '小程序' : ''));
                //获取小程序名称
                $item['xcxTitle'] = '';
                $shareShareData && ($xcxInfo = (new WXXCXConfigService())->getListByCondition(['wid'=>$wid,'id'=>$shareShareData['xcx_config_id']]))
                && ($item['xcxTitle'] = $xcxInfo['data'] ? $xcxInfo['data'][0]['title']  : '');
            }
        }
        (new ShareEventRecordService())->exportExcel($res['data']);
    }

}