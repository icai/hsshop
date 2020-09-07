<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Lib\Redis\MicroPageRedis;
use App\Model\UserFile;
use App\Module\PermissionModule;
use App\Module\ProductModule;
use App\Module\StoreModule;
use App\S\File\FileInfoService;
use App\S\Store\TemplateMarketService;
use App\Services\ConfService;
use App\S\File\FileClassifyService;
use App\S\File\UserFileService;
use App\S\Product\ProductService;
use App\Services\Permission\PermissionService;
use App\Services\Permission\WeixinRoleService;
use DB;
use Illuminate\Http\Request;
use MemberHomeService;
use MicroPageNoticeService;
use MicroPageService;
use MicroPageTemplateService;
use MicroPageTypeRelationService;
use QrCode;
use Route;
use Session;
use StoreNavService;
use MallModule;
use WeixinService;
use MicroPageTypeService as StoreMicroPageTypeService;
use App\Lib\Redis\BiToOnline as BiToOnlineRedis;
use App\Module\DiyComponentValidatorModule;
use App\S\Customer\KefuService;

use Upyun\Config;
use Upyun\Signature;
use Upyun\Util;
use Cookie;
use App\S\Member\MemberHomeModuleService;
use App\Model\Member;
use App\S\Weixin\ShopService;

class StoreController extends Controller
{   
    /**
     * Create a new authentication controller instance.
     * 
     * @return void
     */
    public function __construct() {   
        $this->leftNav = 'store';
    }

    /**
     * 店铺概况
     * @return [type] [description]
     * @update 许立 2018年09月03日 只统计出售中的商品数
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function home(Request $request,ProductService $productService,ShopService $shopService){
        $wid = session('wid');
        $userInfo = session('userInfo');
        if(empty($wid)||empty($userInfo))
        {
            error('登录超时');
        }
        $uid=$userInfo['id'];
        //获取店铺信息
        //$store = WeixinService::init('uid', $uid)->getInfo($wid);
        $store = $shopService->getRowById($wid);
        if($request->isMethod('post')) {
            if ($store) {
                $input = $request->input();
                $updateData = [];
                if (!empty($input['logo'])) {
                    $updateData['logo'] = $input['logo'];
                }
                if ($updateData) {
                    //$flag = WeixinService::init('uid', $uid)->where(['id' => $input['id']])->update($updateData, false);
                    $flag = $shopService->update($input['id'],$updateData);
                    if ($flag) {
                        /* 存店铺logo */
                        $request->session()->put('logo', $input['logo']);
                        /* 手动保存session */
                        $request->session()->save();
                        return mysuccess('设置店铺成功');
                    } else {
                        return myerror('设置店铺失败');
                    }
                }
            }
        }
        $storeLogo=session('logo');
        //统计店铺下的微页面数
        $result=MicroPageService::statMicroPage($wid);
        $microPageNum=0;
        if($result['errCode']==0)
        {
            $microPageNum=$result['data'];
        }
        //统计店铺下商品数量
        $productNum = $productService->getCountByWhere(['wid' => $wid, 'status' => 1, 'stock' => ['>', 0]]);
        $redis = new BiToOnlineRedis();
        $biData = $redis->getWidBi($wid);
        
        return view('merchants.store.home',array(
            'title'        => '店铺概况',
            'wid'          => $wid,
            'leftNav'      => $this->leftNav,
            'slidebar'     => 'home',
            'store'        => $store,
            'page_num'     => $microPageNum,
            'product_num'  => $productNum,
            'store_logo'   => $storeLogo,
            'biData'      => $biData
        ));
    }

    /**
     * 微页面
     * @return [type] [description]
     * @author jonzhang guo.jun.zhang@163.com
     */
    public function index(Request $request,PermissionModule $permissionModule)
    {
        $type=$request->input('type');
        $title=$request->input('title');
        $isShow=$request->input('is_show')??1;
        $orderBy=$request->input('orderby')??'created_at';
        $order=$request->input('order')??'desc';

        $wid=session('wid');
        $storeLogo=session('logo');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }

        //获取用户信息
        $data=[];
        $data['wid']=$wid;
        if(!empty($type))
        {
            $microPageID = [];
            //通过微页面类型id来查询对应的微页面id
            $microPageIDList = MicroPageTypeRelationService::getRelationData(['micro_page_type_id' => $type]);
            if ($microPageIDList['errCode']==0&&!empty($microPageIDList['data']))
            {
                foreach ($microPageIDList['data'] as $value)
                {
                    array_push($microPageID, $value['micro_page_id']);
                }
            }
            if(empty($microPageID))
            {
                $microPageID=[-9999999];
            }
            $data['id'] = $microPageID;
        }
        if(!empty($title))
        {
            $data['page_title']=$title;
        }
        $data['is_show']=$isShow;

        $pageHtml='';
        $list=MicroPageService::getListByConditionWithPage($data,$orderBy,$order);
        if(!empty($list[1]))
        {
            $pageHtml=$list[1];
        }
        //查询当前店铺下微页面数据
        //定义存放微页面的数组
        $page=[];
        //查询有微页面数据时
        if(!empty($list[0]['data']))
        {
            $microPageId = [];
            foreach($list[0]['data'] as $microPage)
            {
                $productNum=0;
                $pageItem=[];
                $microPageId[] = $microPage['id'];
                //微页面模板中有数据时
                if(!empty($microPage['page_template_info'])&&$microPage['page_template_info']!='[]')
                {
                    //统计微页面下商品数
                    $productNum=MicroPageService::statProductNum($microPage['page_template_info']);
                }
                $pageItem['product_num']=$productNum;
                $pageItem['id']=$microPage['id'];
                $pageItem['page_title']=$microPage['page_title'];
                $pageItem['created_at']=$microPage['created_at'];
                $pageItem['sequence_number']=$microPage['sequence_number'];
                $pageItem['is_home']=$microPage['is_home'];
                $pageItem['type_title'] = '未分类'; //每个微页面设置一个页面类型 add by 吴晓平 2018.10.22 微页面列表增加分类名称显示
                $pageItem['type_flag'] = 0;
                $page[]=$pageItem;
            }
            /**add by 吴晓平 2018.10.22 微页面列表增加分类名称显示**/
            $microPageTypeRelationList = MicroPageTypeRelationService::getRelationTypeData(['micro_page_id' => ['in',$microPageId]]);
            if ($microPageTypeRelationList['data']) {
                foreach ($microPageTypeRelationList['data'] as $key => $microType) {
                    foreach ($page as &$p) {
                        if ($microType['micro_page_id'] == $p['id']) {
                            $p['type_title'] = $microType['belongsToMicroPageType']['title'];
                            $p['type_flag']  = 1;
                            $p['type_id'] = $microType['micro_page_type_id'];
                        }
                    }
                }
            }
        }
        //查询微页面类型数据
        $pageType=StoreMicroPageTypeService::getListByCondition(['wid'=>$wid]);
        $pageTypeList=[];
        if($pageType['errCode']==0&&!empty($pageType['data']))
        {
            foreach ($pageType['data'] as $item) {
                $data = [];
                $data['id'] = $item['id'];
                $data['title'] = $item['title'];
                $pageTypeList[] = $data;
            }
        }

        //查询当前店铺 店铺首页数据
        $whereHome=[];
        $whereHome['is_home']=1;
        $whereHome['wid']=$wid;
        $storeList=MicroPageService::getRowByCondition($whereHome);
        $store=[];
        if($storeList['errCode']==0&&!empty($storeList['data']))
        {
            $store['id']=$storeList['data']['id'];
            $store['page_title']=$storeList['data']['page_title'];
            $store['created_at']=$storeList['data']['created_at'];
        }

        $biData = $pageid =[];
        if (!empty($page)) {
            foreach ($page as $key => $value) {
                $pageid[] = $value['id'];
            }
            $redis = new BiToOnlineRedis();
            $biData = $redis->getPageBi($pageid, $wid, 1);
        }

        //是否可以创建微页面
        $isCreate = 1;
        if (session('role_id') == '10' && !$permissionModule->checkPermission(session('role_id'),session('wid'),'create_page')){
            $isCreate = 0;
        }

        return view('merchants.store.index',array(
            'title'         => '微页面',
            'leftNav'       => $this->leftNav,
            'slidebar'      => 'index',
            'microPageList' => $page,
            'pageHtml'      => $pageHtml,
            'pageTypeList'  => $pageTypeList,
            'store'         => $store,
            'wid'           => $wid,
            'store_logo'    => $storeLogo,
            'biData'        => $biData,
            'isCreate'     => $isCreate,
        ));
    }

    /**
     * todo 添加微页面/编辑微页面
     * @return view
     * @author jonzhang  guo.jun.zhang@163.com
     * @date 2017-03-23
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年9月18日 添加购物车链接cart_url
     * @update 张永辉 2018年10月23日 基础版本最多只能创建20个微页面
     */
    public function showMicroPage(Request $request,TemplateMarketService $templateMarketService,ShopService $shopService,PermissionModule $permissionModule,$option,$id)
    {
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $pageTitle='';
        $microPageData=[];
        $microPageType=[];
        //创建微页面
        if(!empty($option)&&$option=='create')
        {
            if (session('role_id') == '10' && !$permissionModule->checkPermission(session('role_id'),session('wid'),'create_page')){
                $permissionModule->returnInfo('页面数量已超过上限，请联系客服升级处理');
            }
            $pageTitle='新建微页面';
            $templateMarketData=$templateMarketService->getRowById($id);
            if($templateMarketData['errCode']==0&&!empty($templateMarketData['data']))
            {
                $microPageData['template_id']=$id;
                $microPageData['template_info']=$templateMarketData['data']['template_data'];
                $microPageData['is_custom']=$templateMarketData['data']['is_custom'];
            }
            else
            {
                error('模板不存在');
            }
        }//微页面编辑
        else if(!empty($option)&&$option=='edit')
        {
            //微页面模板信息
            $result=MicroPageService::getRowById($id);
            if($result['errCode']==0&&!empty($result['data']))
            {
                $templateData = $result['data'];
                $microPageData['id'] = $templateData['id'];
                //判断该页面是否属于该店铺
                if($templateData['wid']!=$wid)
                {
                    error('该页面你没有权限');
                }
                $microPageData['title'] = $templateData['page_title'];
                $microPageData['description'] = $templateData['page_description'];
                $microPageData['bg_color'] = $templateData['page_bgcolor'];
                $microPageData['is_custom']=1;

                /*add by wuxiaoping 2017.09.01*/
                $microPageData['share_title']=$result['data']['share_title'] ?? '';
                $microPageData['share_desc']=$result['data']['share_desc'] ?? '';
                $microPageData['share_img']=$result['data']['share_img'] ?? '';
                //判断添加该微页面的客服QQ是否被删除
                if($result['data']['qq']){
                    $res = (new KefuService())->getRowByCondition(['qq' => $result['data']['qq']]);
                    if ($res) {
                        $microPageData['qq'] = $result['data']['qq'];
                    }else{
                        $microPageData['qq'] = '';
                    }
                }else{
                    $microPageData['qq'] = '';
                }
                if(!empty($templateData['template_id'])) {
                    $templateMarketData = $templateMarketService->getRowById($templateData['template_id']);
                    if ($templateMarketData['errCode']==0&&!empty($templateMarketData['data'])) {
                        $microPageData['is_custom'] = $templateMarketData['data']['is_custom'];
                    }
                }
                //对商品信息进行处理 [通过商品ID找商品对应的商品信息]
                $storeTemplateData=$templateData['page_template_info'];
                if(!empty($storeTemplateData))
                {
                    $storeTemplateData = MallModule::processTemplateData($wid,$storeTemplateData,1);
                }
                $microPageData['template_info'] = $storeTemplateData;
                //查询微页面所拥有的页面类型
                $microPageTypeList=MicroPageTypeRelationService::getRelationData(['micro_page_id'=>$id]);
                if($microPageTypeList['errCode']==0&&!empty($microPageTypeList['data']))
                {
                    foreach ($microPageTypeList['data'] as $value)
                    {
                        array_push($microPageType, $value['micro_page_type_id']);
                    }
                }
            }
            else
            {
                error('该页面不存在');
            }
            $pageTitle='编辑微页面';
        }
        //店铺信息
        $store=[];
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store=$storeInfo;
        }
        //何书哲 2018年9月18日 添加购物车链接cart_url，店铺主页url
        $store['cart_url'] = '/shop/cart/index/'.$wid;
        $store['url'] = '/shop/index/'.$wid;
        //页面分类
        $result=StoreMicroPageTypeService::getListByCondition(['wid'=>$wid]);
        $typeList=[];
        if($result['errCode']==0&&!empty($result['data']))
        {
            foreach ($result['data'] as $item) {
                $data = [];
                $data['id'] = $item['id'];
                $data['title'] = $item['title'];
                $typeList[] = $data;
            }
        }
       $permission = (new PermissionService())->getSpecialPermission();
        //add by zhangyh 20180612
        $microPageData['template_info'] = ProductModule::addProductContentHost($microPageData['template_info']);
        //end
        return view('merchants.store.microPageAdd',array(
            'title'     => '微页面',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'page_template'=>json_encode($microPageData),
            'store'=>json_encode($store),
            'page_type'=>$typeList,
            'microPageType'=>$microPageType,
            'page_title'=>$pageTitle,
            'wid'=>$wid,
            'permission'    => $permission,
        ));
    }
    /**
     * 页面分类
     * @return [type] [description]
     */
    public function pagecat(Request $request)
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时', '/auth/login');
        }
        $where = [];
        $where['wid'] = $wid;
        $title = $request->input('title');
        if (!empty($title)) {
            $where['title'] = $title;
        }
        $id = $request->input('id') ?? 0;
        if ($id) {
            $where['id'] = [$id];
        }
        //查询微页面分类数据
        $pageHtml = '';
        $microPageTypeList = [];
        $list = StoreMicroPageTypeService::getListByConditionWithPage($where);
        if(!empty($list[1]))
        {
            $pageHtml=$list[1];
        }
        if (!empty($list[0]['data']))
        {
            //过滤微页面分类数据
            foreach ($list[0]['data'] as $item)
            {
                $typeItem = [];
                $typeItem['id'] = $item['id'];
                //统计使用微页面分类的页面数
                $typeItem['page_num'] = MicroPageTypeRelationService::statMicroPageNum($item['id']);
                $typeItem['title'] = $item['title'];
                $typeItem['is_auto'] = $item['is_auto'];
                $typeItem['created_at'] = $item['created_at'];
                $microPageTypeList[] = $typeItem;
            }
        }
        return view('merchants.store.pagecat',array(
            'title'     => '页面分类',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'pagecat',
            'list'=>$microPageTypeList,
            'pageHtml'=>$pageHtml
        ));
    }

    /**
     * 新建微页面分类
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function pagecatAdd(Request $request,ShopService $shopService,$id=0){

        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $data=[];
        $pageTitle='新建微页面分类';
        if(!empty($id))
        {
            $result=StoreMicroPageTypeService::getRowById($id);
            if($result['errCode']==0&&!empty($result['data']))
            {
                $templateData=$result['data'];
                $data['id']=$templateData['id'];
                //对商品信息进行处理 [通过商品ID找商品对应的商品信息]
                $typeTemplateData=$templateData['type_template_info'];
                if(!empty($typeTemplateData)) {
                    $typeTemplateData = MallModule::processTemplateData($wid,$typeTemplateData,1);
                }
                //获取微页面分类被那些微页面引用
                $pageList= MicroPageTypeRelationService::selectMicroPage($data['id']);
                if(!empty($pageList)&&!empty($typeTemplateData)&&$typeTemplateData!='[]')
                {
                    //把模板数据转化为数组
                    $typeArrayTemplateData=json_decode($typeTemplateData,true);
                    if(!empty($typeArrayTemplateData))
                    {
                        //把微页面数据给对应的数组
                        $typeArrayTemplateData[0]['pageList']=$pageList;
                        //模板数组转化为json
                        $typeTemplateData=json_encode($typeArrayTemplateData);
                    }
                }
                //把模板数据赋值给变量
                $data['template_info']=$typeTemplateData;
            }
            $pageTitle='编辑微页面分类';
        }
        $store=[];
        //店铺信息
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store=$storeInfo;
        }
        //add by zhangyh
        $data['template_info'] = ProductModule::addProductContentHost($data['template_info']??'');
        //end
        return view('merchants.store.pagecatAdd',array(
            'title'     => '新建微页面分类',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'pagecat',
            'type_template'=>json_encode($data),
            'store'=>json_encode($store),
            'page_title'=>$pageTitle,
            'wid'=>$wid
        ));
    }

    /**
     * 会员主页
     * @return [type] [description]
     * @update 张永辉 2019年8月20日 是否开启财富眼返回整型
     */
    public function userCenter(Request $request,ShopService $shopService){
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $store=[];
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store=$storeInfo;
        }

        $memberHome=[];
        $memberhomeInfo=MemberHomeService::getRow($wid);
        if($memberhomeInfo['errCode']==0&&!empty($memberhomeInfo['data']))
        {
            $memberHome['id']=$memberhomeInfo['data']['id'];
            //对商品信息进行处理 [通过商品ID找商品对应的商品信息]
            $homeTemplateData=$memberhomeInfo['data']['custom_info'];
            if(!empty($homeTemplateData)) {
                $homeTemplateData = MallModule::processTemplateData($wid,$homeTemplateData,1);
            }
            $memberHome['editors']=$homeTemplateData;
        }
        $mainHome=config('app.url').'shop/member/index/'.$wid;
        //add by zhangyh
        $memberHome['editors'] = ProductModule::addProductContentHost($memberHome['editors']);
        //end
        $store['is_open_weath'] = intval($store['is_open_weath'] ?? 0);
        return view('merchants.store.userCenter',array(
            'title'     => '会员主页',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'userCenter',
            'bodyClass' => ' ng-controller=myCtrl',
            'store'=>json_encode($store),
            'memberHome'=>json_encode($memberHome),
            'mainHome'=>$mainHome,
            'wid'=>$wid
        ));
    }

    /**
     * 商家后台设置开启财富眼
     * @author 吴晓平 <2018年08月29日>
     * @param  Request $request [description]
     * @return boolean          [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isOpenWeath(Request $request,ShopService $shopService)
    {
        $wid = session('wid');
        $userInfo = session('userInfo');
        if(empty($wid)||empty($userInfo))
        {
            error('登录超时');
        }
        $uid = $userInfo['id'];
        $is_open_weath = $request->input('is_open_weath') ?? 0;
        $updateData['is_open_weath'] = $is_open_weath;
        //$res = WeixinService::init('uid', $uid)->where(['id' => $wid])->update($updateData, false);
        $res = $shopService->update($wid,$updateData);
        if ($res) {
            success();
        }
        error();
    }

    /**
     * 获取个人中心的所有功能模块
     * @return [type] [description]
     */
    public function getUserCenterModule()
    {
        $returnData = ['errCode' => 0,'errMsg' => '','data' => []];
        $wid = session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '请重新登陆';
        }
        $list = (new MemberHomeModuleService())->getAllList($wid);
        $returnData['data'] = $list;
        return $returnData;
    }

    /**
     * 店铺导航
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function shopNav(Request $request,ShopService $shopService){
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $store=[];
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store=$storeInfo;
        }
        return view('merchants.store.shopNav',array(
            'title'     => '店铺导航',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'shopNav',
            'bodyClass' => ' ng-controller=myCtrl',
            'store'=>json_encode($store),
            'wid'=>$wid
        ));
    }

    /**
     * 全店风格
     * @return [type] [description]
     */
    public function globalTemplate( ConfService $confService, Request $request, $wid = null ) {
        if ( $request->isMethod('post') ) {
            /* 数据验证 */
            $input = $confService->verify(['overall_style']);

            error('test');

            /* 更新数据 */
            if ( isset($input['id']) ) {
                $where['id'] = $input['id'];
            }
            $where['wid'] = session('wid');
            $confService->init('wid', session('wid'))->where($where)->update($input);
        }
        return view('merchants.store.globalTemplate',array(
            'title'     => '全店风格',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'globalTemplate'
        ));
    }

    /**
     * 公共广告
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function ad(Request $request,ShopService $shopService){
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $store=[];
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store=$storeInfo;
        }
        return view('merchants.store.ad',array(
            'title'     => '公共广告',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'ad',
            'wid'=>$wid,
            'store'=>json_encode($store)
        ));
    }

    /**
     * 自定义模块
     * @return [type] [description]
     */
    public function component(Request $request){
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $where=[];
        $where['wid']=$wid;
        $title=$request->input('title');
        if(!empty($title))
        {
            $where['template_name']=$title;
        }
        $pageHtml='';
        $templateData=[];
        $list=MicroPageTemplateService::getListByConditionWithPage($where);
        if(!empty($list[1]))
        {
            $pageHtml=$list[1];
        }
        if(!empty($list[0]['data']))
        {
            $templateData=$list[0]['data'];
        }
        return view('merchants.store.component',array(
            'title'     => '自定义模块',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'component',
            'templateList'=>$templateData,
            'wid'=>$wid,
            'pageHtml'=>$pageHtml
        ));
    }

    /**
     * 自定义模块 - 新增编辑
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function componentAdd(Request $request,ShopService $shopService,$id=0){
        $wid=session('wid');
        if(empty($wid))
        {
            error('登录超时','/auth/login');
        }
        $data=[];
        if(!empty($id))
        {
            $result=MicroPageTemplateService::getRowById($id);
            if($result['errCode']==0&&!empty($result['data']))
            {
                $templateData=$result['data'];
                $data['id']=$templateData['id'];
                //对商品信息进行处理 [通过商品ID找商品对应的商品信息]
                $compentTemplateData=$templateData['template_info'];
                if(!empty($compentTemplateData)) {
                    $compentTemplateData = MallModule::processTemplateData($wid,$compentTemplateData,1);
                }
                $data['template_info']=$compentTemplateData;
            }
        }
        $store=[];
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById($wid);
        if (!empty($storeInfo)) {
            $store=$storeInfo;
        }

        //add by zhangyh
        $data['template_info'] = ProductModule::addProductContentHost( $data['template_info']??'');
        //end
        return view('merchants.store.componentAdd',array(
            'title'     => '自定义模块',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'component',
            'template_info'=>json_encode($data),
            'store'=>json_encode($store),
            'wid'=>$wid
        ));
    }

    /**
     * 我的文件 - 图片
     * @return [type] [description]
     */
    public function attachmentImage(Request $request,FileClassifyService $fileClassifyService,UserFileService $userFileService,PermissionModule $permissionModule){
        $file_mine = $request->input('file_mine', 1);
        //获取我分类
        $classify = $fileClassifyService->getMyClassify($request->session()->get('userInfo')['id'],$file_mine);
        //获取我的文件
        $fileData = $userFileService->getUserFileByClassify(0,$file_mine);
//        show_debug($fileData);
        $isCreate = 1;
        if (session('role_id') == '10' && $permissionModule->checkPermission(session('role_id'),session('wid'),'up_file')){
            $isCreate = 0;
        }
        return view('merchants.store.attachmentImage',array(
            'title'     => '我的文件 - 图片',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'attachmentImage',
            'classify'  => json_encode($classify),
            'fileData'  => json_encode($fileData),
            'isCreate'  => $isCreate,
        ));
    }

    /**
     * 我的文件 - 语音
     * @return [type] [description]
     */
    public function attachmentVoice(Request $request){
        return view('merchants.store.attachmentVoice',array(
            'title'     => '我的文件 - 语音',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'attachmentVoice'
        ));
    }

    public function getVideoSign(Request $request)
    {
        $config = new Config('huisoucn', 'phpteam', 'phpteam123456');
        $config->setFormApiKey('Mv83tlocuzkmfKKUFbz2s04FzTw=');
        $data['save-key'] = $request->input('save_key');
        $data['expiration'] = time() + 120;
        $data['bucket'] = 'huisoucn';
        $policy = Util::base64Json($data);
        $method = 'POST';
        $uri = '/' . $data['bucket'];
        $signature = Signature::getBodySignature($config, $method, $uri, null, $policy);
        echo json_encode(array(
            'policy' => $policy,
            'authorization' => $signature
        ));
    }

    /**
     * 我的文件 - 视频
     * @return [type] [description]
     */
    public function attachmentVideo(Request $request,FileClassifyService $fileClassifyService,UserFileService $userFileService,PermissionModule $permissionModule){
        //获取我分类
        $classify = $fileClassifyService->getMyClassify($request->session()->get('userInfo')['id'],2);
        //获取我的文件

        $fileData = $userFileService->getUserFileByClassify(0);
        $isCreate = 1;
        if (session('role_id') == 10 && $permissionModule->checkPermission(session('role_id'),session('wid'),'up_video')){
            $isCreate = 0;
        }

        return view('merchants.store.attachmentVideo',array(
            'title'     => '我的文件 - 视频',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'attachmentImage',
            'classify'  => json_encode($classify),
            'fileData'  => json_encode($fileData),
            'isCreate'  => $isCreate,
        ));
    }
    /**
     * todo 处理公共广告信息 不存在就添加 存在就更改
     * @param Request $request
     * @return string
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-13
     */
    public function processNotice(Request $request)
    {
        $returnData = array('errCode' => 0, 'errMsg' => '');
        //接收到的数据是数组
        $id = $request->input('id');
        $isON=$request->input('ison');
        $showPage=$request->input('showPage');
        $showPosition=$request->input('showPosition');
        $userTemplateData = $request->input('data');
        //add by zhangyh
        $userTemplateData = ProductModule::delProductContentHost($userTemplateData);
        //end
        $errMsg = '';
        $data=[];
        if (empty($showPosition))
        {
            $errMsg.='展示位置为空';
        }
        $data['position'] =$showPosition;
        $data['is_used'] =$isON;
        if(empty($showPage)||$showPage=='[]')
        {
            $data['apply_location']=null;
        }
        else
        {
            if(is_array($showPage))
            {
                $data['apply_location'] = json_encode($showPage);
            }
            else
            {
                $validateData=json_decode($showPage,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-1;
                    $returnData['errMsg']='出现的页面数据格式不符合要求';
                    return $returnData;
                }
                $data['apply_location']=$showPage;
            }
        }
        if(empty($userTemplateData)||$userTemplateData=='[]')
        {
            $data['notice_template_info']=null;
        }
        else
        {
            if(is_array($userTemplateData))
            {
                $data['notice_template_info'] = json_encode($userTemplateData);
            }
            else
            {
                $validateData=json_decode($userTemplateData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='模板数据格式不符合要求';
                    return $returnData;
                }
                $data['notice_template_info'] = $userTemplateData;
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }

        $result=MicroPageNoticeService::getRow($wid);
        if($result['errCode']!=0)
        {
            $returnData['errCode'] = $result['errCode'];
            $returnData['errMsg'] = $result['errMsg'];
            return $returnData;
        }
        else if($result['errCode']==0&&empty($result['data']))
        {
            $data['wid']=$wid;
            $insertResult = MicroPageNoticeService::insertData($data);
            if ($insertResult['errCode']!=0)
            {
                $returnData['errCode'] = $insertResult['errCode'];
                $returnData['errMsg'] = $insertResult['errMsg'];
                return $returnData;
            }
            return $insertResult;
        }

        if(empty($id))
        {
            $returnData['errCode']=-7;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        //更改公共广告信息
        $updateResult=MicroPageNoticeService::updateData($id,$data);
        if($updateResult['errCode']!=0)
        {
            $returnData['errCode'] = $updateResult['errCode'];
            $returnData['errMsg'] = $updateResult['errMsg'];
            return $returnData;
        }
        return  $updateResult;
    }

    /**
     * todo 查询用户公共广告信息
     * @param Request $request
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-14
     */
    public function selectNotice(Request $request)
    {
        $returnData=array('errCode'=>0,'errMsg'=>'','data'=>[]);
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $result=MicroPageNoticeService::getRow($wid);
        if($result['errCode']!=0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='查询数据出现问题';
            return $returnData;
        }
        else if($result['errCode']==0&&!empty($result['data']))
        {
            $userNoticeInfo=[];
            $userNoticeInfo['id']=$result['data']['id'];
            $userNoticeInfo['ison']=$result['data']['is_used'];
            $userNoticeInfo['showPosition']=$result['data']['position'];
            $userNoticeInfo['showPage']=json_decode($result['data']['apply_location'],true);
            $returnData['data']['baseInfo']=$userNoticeInfo;
            //对商品信息进行处理 [通过商品ID找商品对应的商品信息]
            $noticeTemplateData=$result['data']['notice_template_info'];
            if(!empty($noticeTemplateData))
            {
                $noticeTemplateData = MallModule::processTemplateData($wid,$noticeTemplateData,1);
            }
            $returnData['data']['editors']=json_decode($noticeTemplateData,true);
        }
        //add by zhangyh
        if (!empty($returnData['data']['editors'])){
            foreach ($returnData['data']['editors'] as &$val){
                if ($val['type'] == 'rich_text'){
                    $val['content'] = ProductModule::addProductContentHost($val['content']);
                }
            }
        }

        //end
        return $returnData;
    }

    /**
     * todo 把某个页面设置为微页面店铺主页
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-20
     */
    public function updateMicroPageHome(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $id = $request->input('id');
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //查询出要更改数据的id
        $result=MicroPageService::getListByCondition(['is_home'=>1,'wid'=>$wid]);
        if($result['errCode']==0&&!empty($result['data']))
        {
            foreach ($result['data'] as $item)
            {
                MicroPageService::updateData($item['id'], ['is_home' => 0]);
            }
        }
        return MicroPageService::updateData($id,['is_home'=>1,'is_show'=>1]);
    }

    /**
     * todo 复制微页面数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-21
     */
    public function copyMicroPage(Request $request)
    {
        //定义返回数据的格式
        $returnData=['errCode'=>0,'errMsg'=>''];
        $id=$request->input('id');
        //判断前端传递过来的id数值
        if(empty($id))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //查询要复制的数据是否存在
        $list=MicroPageService::getRowById($id);
        if($list['errCode']!=0)
        {
            $returnData['errCode'] = $list['errCode'];
            $returnData['errMsg'] = $list['errMsg'];
            return $returnData;
        }
        else if($list['errCode']==0&&empty($list['data']))
        {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '没有查询到符合要求的数据';
            return $returnData;
        }
        //查询出来的数据是数组，只取第一个数组数据
        $data=$list['data'];
        //剔除不需要的数据
        unset($data['id']);
        //复制时间改变
        $data['created_at']=date('Y-m-d H:i:s');
        unset($data['updated_at']);
        unset($data['deleted_at']);
        //店铺主页只有一个 复制的数据不能够成为店铺主页
        $data['is_home']=0;
        return MicroPageService::insertData($data);
    }
    /**
     * todo 插入微页面信息
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-15
     */
    public function insertPage(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>0];
        //存放要添加数据
        $title = $request->input('page_title');
        $description = $request->input('page_description');
        $type = $request->input('page_type');
        $bgColor = $request->input('page_bgcolor');
        $isShow = $request->input('is_show');
        $requestData = $request->input('data');
        $templateId= $request->input('template_id')??0;

        $requestData = ProductModule::delProductContentHost($requestData); //add by zhangyh 20180612

        /*add by wuxiaoping 2017.09.01*/
        $share_title = $request->input('share_title');
        $share_desc  = $request->input('share_desc');
        $share_img   = $request->input('share_img');
        $qq = $request->input('qq') ?? '';
        $wid = session('wid');
        if (empty($wid))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '登录超时';
            return $returnData;
        }
        //定义添加数据数组
        $data = [];
        $errMsg = '';
        if (empty($title))
        {
            $errMsg .= '微页面名称为空';
        }
        else
        {
            $data['page_title'] = $title;
        }
        if (is_null($isShow))
        {
            $errMsg .= 'is_show参数为null';
        }
        else
        {
            $data['is_show'] = $isShow;
        }
        if (!empty($description))
        {
            $data['page_description'] = $description;
        }
        if (!empty($bgColor))
        {
            $data['page_bgcolor'] = $bgColor;
        }
        $data['wid'] = $wid;
        $data['template_id'] = $templateId;
        $data['share_title'] = $share_title ?? '';
        $data['share_desc']  = $share_desc ?? '';
        $data['share_img']   = $share_img ?? '';

        $data['qq'] = $qq;

        //前端传递过来的是数组格式的字符串,如果没有数据就会传递'[]'
		$validateModule = new DiyComponentValidatorModule();
		$validateResult = $validateModule->validateComponents($requestData);
		if (!$validateResult) {
			$returnData['errCode']=-3;
			$returnData['errMsg'] = $validateModule->getErrmsg();
			return $returnData;
		}
        if (empty($requestData)||$requestData=='[]')
        {
            $data['page_template_info']=null;
        }
        else
        {
            if(is_array($requestData))
            {
                $data['page_template_info'] = json_encode($requestData);
            }
            else if(is_string($requestData))
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($requestData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-3;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                $data['page_template_info']=$requestData;
            }
            else
            {
                $returnData['errCode']=-7;
                $returnData['errMsg']='传入数据格式不符合要求';
                return $returnData;
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }

        //add by jonzhang 2018-04-20 效验模板数据
        if(!empty($data['page_template_info']))
        {
            $dcvm=new DiyComponentValidatorModule();
            $checkResult=$dcvm->checkModel($data['page_template_info']);
            if($checkResult['errCode']==0&&!empty($checkResult['data']))
            {
                $data['page_template_info']=$checkResult['data'];
            }
            else
            {
                return $checkResult;
            }
        }

        // add by jonzhang 数据长度效验
        if (strlen($data['page_template_info']) > 16777215 - 1)
        {
            $returnData['errCode'] = -6;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }
        $microPageID=0;
        //事务
        DB::beginTransaction();
        try {
            //插入微页面数据
            $microPageID = MicroPageService::insertData($data);
            if($microPageID['errCode']!=0)
            {
                throw new \Exception('插入微页面失败');
            }
            if($type=='[]'&&empty($type))
            {
                $type=[];
            }
            if (!empty($type)) {
                if(!is_array($type)) {
                    $type = json_decode($type, true);
                }
                foreach ($type as $item) {
                    $relationData = ['wid' => $wid, 'micro_page_id' => $microPageID['data'], 'micro_page_type_id' => $item];
                    //插入微页面对应的分类信息
                    $typeReturn=MicroPageTypeRelationService::insertData($relationData);
                    if($typeReturn['errCode']!=0)
                    {
                        throw new \Exception('插入页面分类失败');
                    }
                }
            }
            DB::commit();
            $returnData['data']=$microPageID['data'];
        }
        catch(\Exception $e)
        {
            DB::rollback();//事务回滚
            $message=$e->getMessage();
            $returnData['errCode'] = -3;
            $returnData['errMsg'] =$message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 通过id来更改微页面数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-15
     */
    public function updatePage(Request $request)
    {
        $returnData=array('errCode'=>0,'errMsg'=>'');
        $id=$request->input('id');
        $title=$request->input('page_title');
        $description=$request->input('page_description');
        $type=$request->input('page_type');
        $bgColor=$request->input('page_bgcolor');
        $isShow=$request->input('is_show');
        $requestData=$request->input('data');
        //add by zhangyh 20180612 delete host
        $requestData = ProductModule::delProductContentHost($requestData);
        //end

        /*add by wuxiaoping 2017.09.01*/
        $share_title = $request->input('share_title');
        $share_desc  = $request->input('share_desc');
        $share_img   = $request->input('share_img');

        /*add by wuxiaoping 2017.09.28 添加客服*/
        $qq = $request->input('qq') ?? '';

        //定义更改数据数组
        $data=[];
        $errMsg='';
        if(empty($id))
        {
            $errMsg.='id为空';
        }
        if(empty($title))
        {
            $errMsg.='微页面名称为空';
        }
        else
        {
            $data['page_title'] = $title;
        }
        if(!is_null($isShow))
        {
            $data['is_show']=$isShow;
        }
        $data['page_description']=$description;
        $data['page_bgcolor']=$bgColor;
        $data['share_title'] = $share_title ?? '';
        $data['share_desc']  = $share_desc ?? '';
        $data['share_img']   = $share_img ?? '';
        /*添加qq号数据表字段*/
        $data['qq'] = $qq;

		$validateModule = new DiyComponentValidatorModule();
		$validateResult = $validateModule->validateComponents($requestData);
		if (!$validateResult) {
			$returnData['errCode']=-3;
			$returnData['errMsg'] = $validateModule->getErrmsg();
			return $returnData;
		}
        if(empty($requestData)||$requestData=='[]')
        {
            $data['page_template_info']=null;
        }
        else
        {
            if(is_array($requestData))
            {
                $data['page_template_info'] = json_encode($requestData);
            }
            else if(is_string($requestData))
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($requestData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-3;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                foreach($validateData as &$item)
                {
                    if($item['type']=='spell_goods')
                    {
                        $item['groups']=[];
                    }
                }
                $data['page_template_info'] = json_encode($validateData);
            }
            else
            {
                $returnData['errCode']=-7;
                $returnData['errMsg']='传入数据格式不符合要求';
                return $returnData;
            }
        }

        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }

        //add by jonzhang 2018-01-04
        //json数据进行效验
        if(!empty($data['page_template_info']))
        {
            $dcvm=new DiyComponentValidatorModule();
            $checkResult=$dcvm->checkModel($data['page_template_info']);
            if($checkResult['errCode']==0&&!empty($checkResult['data']))
            {
                $data['page_template_info']=$checkResult['data'];
            }
            else
            {
                return $checkResult;
            }
        }

        // add by jonzhang 数据长度效验
        if (strlen($data['page_template_info']) > 16777215 - 1)
        {
            $returnData['errCode'] = -6;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }

        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //事务
        DB::beginTransaction();
        try {
            $pageReturn = MicroPageService::updateData($id,$data);
            if ($pageReturn['errCode']!=0) {
                throw new \Exception('更改微页面数据失败');
            }
            if($type=='[]'||empty($type))
            {
                $type=[];
            }
            if(!empty($type)&&!is_array($type))
            {
                $type=json_decode($type,true);
            }
            $existTypeData=[];
            $deletedTypeData=[];
            $relationData = MicroPageTypeRelationService::getRelationData(['micro_page_id' => $id]);
            if ($relationData['errCode'] == 0 && !empty($relationData['data']))
            {
                foreach ($relationData['data'] as $typeIdItem)
                {
                    if(in_array($typeIdItem['micro_page_type_id'],$type))
                    {
                        //数据表中原来存在，不删除的数据
                        array_push($existTypeData, $typeIdItem['micro_page_type_id']);
                    }
                    else
                    {
                        //数据表中原来存在，现在要删除的数据
                        array_push($deletedTypeData,$typeIdItem['micro_page_type_id']);
                    }
                }
            }
            else if ($relationData['errCode'] != 0)
            {
                throw new \Exception('查询微页面关系数据失败');
            }
            foreach ($type as $typeID)
            {
                if (!in_array($typeID, $existTypeData))
                {
                    $relationData = ['wid' => $wid, 'micro_page_id' => $id, 'micro_page_type_id' => $typeID];
                    $typeReturn = MicroPageTypeRelationService::insertData($relationData);
                    if ($typeReturn['errCode'] != 0)
                    {
                        throw new \Exception('添加页面分类失败');
                    }
                }
            }
            if(!empty($deletedTypeData))
            {
                foreach($deletedTypeData as $deletedTypeItem)
                {
                    $relationData=['micro_page_id'=>$id,'micro_page_type_id'=>$deletedTypeItem];
                    $typeReturn = MicroPageTypeRelationService::deleteData($relationData);
                    if ($typeReturn['errCode'] != 0) {
                        throw new \Exception('删除页面分类失败');
                    }
                }
            }
            //提交事务
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();//事务回滚
            $message=$e->getMessage();
            $returnData['errCode'] = -4;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 删除微页面信息
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-15
     */
    public function deletePage(Request $request)
    {
        $returnData = array('errCode' => 0, 'errMsg' => '');
        //接收前端传递过来的数据
        $id =$request->input('id');
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '接收数据为空';
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $list=MicroPageService::getRowByCondition(['id'=>$id,'is_home'=>1]);
        if($list['errCode']!=0)
        {
            $returnData['errCode'] = $list['errCode'];
            $returnData['errMsg'] = $list['errMsg'];
            return $returnData;
        }
        //事务
        DB::beginTransaction();
        try {
            $pageReturn = MicroPageService::delete($id);
            if ($pageReturn['errCode']!=0) {
                throw new \Exception('删除微页面失败');
            }
            $relationReturn = MicroPageTypeRelationService::deleteData(['micro_page_id' => $id]);
            if ($relationReturn['errCode']!=0) {
                throw new \Exception('删除微页面对应的分类失败');
            }
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();//事务回滚
            $message=$e->getMessage();
            $returnData['errCode'] = -4;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 更改微页面对应的序号
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-16
     */
    public function updateSequenceNumber(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $id=$request->input('id');
        $number=$request->input('number')??0;
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '接收数据为空';
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $updateResult=MicroPageService::updateData($id,['sequence_number'=>$number]);
        if($updateResult['errCode']!=0)
        {
            $returnData['errCode'] = $updateResult['errCode'];
            $returnData['errMsg'] = $updateResult['errMsg'];
            return $returnData;
        }
        return  $returnData;
    }
    /**
     * todo 批量改分类
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-21
     */
    public function processCategorys(Request $request)
    {
        $returnData = array('errCode' => 0, 'errMsg' => '');
        //前端传递json格式字符串
        $categorys=$request->input('categorys');
        //json格式字符串转化为数组
        try {
            if($categorys=='[]'||empty($categorys))
            {
                $categorys=[];
            }
            if(!empty($categorys)&&!is_array($categorys)) {
                $categorys = json_decode($categorys, true);
            }
        }
        catch(\Exception $e)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='categorys参数有问题';
            return  $returnData;
        }
        if(empty($categorys))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='categorys未传递数值';
            return  $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //事务
        DB::beginTransaction();
        try {
            foreach ($categorys as $key => $value)
            {
                //查询微页面对应那些分类
                $relationData = MicroPageTypeRelationService::getRelationData(['micro_page_id' => $key]);
                //定义存放原来类型的数组
                $existTypeData = [];
                //定义存放要删除类型的数组
                $deletedTypeData=[];
                if ($relationData['errCode'] == 0 && !empty($relationData['data']))
                {
                    foreach ($relationData['data'] as $typeIdItem)
                    {
                        if(in_array($typeIdItem['micro_page_type_id'],$value))
                        {
                            //数据表中原来存在，不删除的数据
                            array_push($existTypeData, $typeIdItem['micro_page_type_id']);
                        }
                        else
                        {
                            //数据表中原来存在，现在要删除的数据
                            array_push($deletedTypeData,$typeIdItem['micro_page_type_id']);
                        }
                    }
                }
                else if ($relationData['errCode'] != 0)
                {
                    throw new \Exception('查询微页面关系数据失败');
                }
                foreach ($value as $typeID)
                {
                    if (!in_array($typeID, $existTypeData))
                    {
                        $relationData = ['wid' => $wid, 'micro_page_id' => $key, 'micro_page_type_id' => $typeID];
                        $typeReturn = MicroPageTypeRelationService::insertData($relationData);
                        if ($typeReturn['errCode'] != 0)
                        {
                            throw new \Exception('添加页面分类失败');
                        }
                    }
                }
                if(!empty($deletedTypeData))
                {
                    foreach($deletedTypeData as $deletedTypeItem)
                    {
                        $relationData=['micro_page_id'=>$key,'micro_page_type_id'=>$deletedTypeItem];
                        $typeReturn = MicroPageTypeRelationService::deleteData($relationData);
                        if ($typeReturn['errCode'] != 0) {
                            throw new \Exception('删除页面分类失败');
                        }
                    }
                }
            }
            //提交事务
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();//事务回滚
            $message=$e->getMessage();
            $returnData['errCode'] = -4;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        return $returnData;
    }
    /**
     * todo 添加自定义模块数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-15
     */
    public function insertModule(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        //存放要添加数据
        $requestName=$request->input('name');
        $requestData=$request->input('data');

        $requestData = ProductModule::delProductContentHost($requestData); //add by zhangyh
        //定义添加数据数组
        $data=array();
        $errMsg='';
        if(empty($requestName))
        {
            $errMsg.='模板名称为空';
        }
        else{
            $data['template_name']=$requestName;
        }
        if(empty($requestData)||$requestData=='[]'){
            $errMsg.='模板数据为空';
        }
        else {
            if(is_array($requestData)) {
                $data['template_info'] = json_encode($requestData);
            }
            else{
                //验证数据是否为标准的json字符串
                $validateData=json_decode($requestData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-4;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                $data['template_info'] = $requestData;
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $data['wid']=$wid;
        return MicroPageTemplateService::insertData($data);
    }

    /**
     * todo 通过id来更改自定义模板信息
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang
     * @date 2017-03-15
     */
    public function updateModule(Request $request,$filter=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        //接收前端传递过来的数据
        $requestId=$request->input('id');
        $name=$request->input('name');
        $requestData=$request->input('data');
        //add by zhangyh
        $requestData = ProductModule::delProductContentHost($requestData);
        //end

        //定义要更新数据数组
        $data=[];
        $errMsg='';
        if(empty($requestId))
        {
            $errMsg.='id不能为空';
        }
        if(!empty($name))
        {
            $data['template_name']=$name;
        }
        //改名的时候 过滤到模板信息判断
        if(empty($filter))
        {
            if(empty($requestData))
            {
                $errMsg.='模板数据为空';
            }
            else {
                if (is_array($requestData)) {
                    $data['template_info'] = json_encode($requestData);
                }
                else{
                    //验证数据是否为标准的json字符串
                    $validateData=json_decode($requestData,true);
                    if(empty($validateData))
                    {
                        $returnData['errCode']=-4;
                        $returnData['errMsg']='数据格式不符合要求';
                        return $returnData;
                    }
                    $data['template_info']=$requestData;
                }
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        return MicroPageTemplateService::updateData($requestId,$data);
    }

    /**
     * todo 通过id删除自定义模块
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-15
     */
    public function deleteModule(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //接收前端传递过来的数据
        $id =$request->input('id');
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '接收数据为空';
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        return MicroPageTemplateService::delete($id);
    }

    /**
     * todo 处理会员主页信息 存在更改 不存在添加
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-16
     */
    public function processMemberHome(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //接收数据[数组]
        $id = $request->input('id');
        $name = $request->input('name');
        $homeData = $request->input('data');
        $module_ids = $request->input('module_ids') ?? [];
        //add by zhangyh
        $homeData = ProductModule::delProductContentHost($homeData);
        //end

        $data = [];
        $data['module_ids'] = join(',',$module_ids);
        if (empty($name))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '页面名称为空';
            return $returnData;
        }//获取用户主页模板数据
        $data['home_name'] = $name;
        if (empty($homeData)||$homeData=='[]')
        {
            $data['custom_info'] =null;
        }
        else
        {
            if(is_array($homeData))
            {
                $data['custom_info'] = json_encode($homeData);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($homeData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                $data['custom_info'] = $homeData;
            }
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $result=MemberHomeService::getRow($wid);
        if($result['errCode']!=0)
        {
            $returnData['errCode'] = $result['errCode'];
            $returnData['errMsg'] = $result['errMsg'];
            return $returnData;
        }
        else if($result['errCode']==0&&empty($result['data']))
        {
            $data['wid']=$wid;
            $insertResult = MemberHomeService::insertData($data);
            if ($insertResult['errCode']!=0) {
                $returnData['errCode'] = $insertResult['errCode'];
                $returnData['errMsg'] = $insertResult['errMsg'];
                return $returnData;
            }
            return $returnData;
        }

        if(empty($id))
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        //更改用户主页信息
        $updateResult=MemberHomeService::updateData($id,$data);
        if($updateResult['errCode']!=0)
        {
            $returnData['errCode'] = $updateResult['errCode'];
            $returnData['errMsg'] = $updateResult['errMsg'];
            return $returnData;
        }
        return  $returnData;
    }

    /**
     * todo 插入微页面类型数据
     * @param Request $request
     * @param MicroPageTypeService $microPageTypeService
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-17
     */
    public function insertMicroPageType(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //存放要添加数据
        $title=$request->input('name');
        $firstChoose=$request->input('first_choose')?:1;
        $secondChoose=$request->input('second_choose')?:2;
        $showStyle=$request->input('style')?:1;
        $description=$request->input('description')?:'';
        $pageTypeData=$request->input('data');
        $microPage=$request->input('page_id');

        $pageTypeData = ProductModule::delProductContentHost($pageTypeData);  //add by zhangyh

        //定义添加数据数组
        $data=[];
        $errMsg='';
        if(empty($title))
        {
            $errMsg.='分类名为空';
        }
        else{
            $data['title']=$title;
            $where['name'] = $title;
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $where['wid'] = $wid;
        $result = StoreMicroPageTypeService::getListByConditionWithPage($where);
        if ($result[0]['data']) {
            $returnData['errCode']=-4;
            $returnData['errMsg']='页面分类已存在';
            return $returnData;
        }
        $data['wid']=$wid;
        $data['first_choose']=$firstChoose;
        $data['second_choose']=$secondChoose;
        $data['show_style']=$showStyle;
        $data['type_description']=$description;
        if(empty($pageTypeData)||$pageTypeData=='[]') {
            $data['type_template_info']=null;
        }
        else{
            if(is_array($pageTypeData)) {
                $data['type_template_info'] = json_encode($pageTypeData);
            }
            else {
                $data['type_template_info'] = $pageTypeData;
            }
        }
        //开启事务
        DB::beginTransaction();
        try {
            $typeData = StoreMicroPageTypeService::insertData($data);
            if($typeData['errCode']!=0||empty($typeData['data']))
            {
                throw new \Exception('插入页面分类失败');
            }
            if(!empty($microPage)&&$microPage!='[]')
            {
                if(!is_array($microPage)) {
                    $microPage = json_decode($microPage, true);
                }
                foreach ($microPage as $item) {
                    $relationData = ['wid' => $wid, 'micro_page_id' => $item, 'micro_page_type_id' => $typeData['data']];
                    //把微页面分类与微页面关系数据插入到数据表中
                    $typeReturn=MicroPageTypeRelationService::insertData($relationData);
                    if($typeReturn['errCode']!=0)
                    {
                        throw new \Exception('插入失败');
                    }
                }
            }
            //提交事务
            DB::commit();
        }
        catch(\Exception $e)
        {
            //回滚事务
            DB::rollback();
            $message=$e->getMessage();
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 修改微页面类型信息
     * @param Request $request
     * @param MicroPageTypeService $microPageTypeService
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-17
     */
    public function updateMicroPageType(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $id=$request->input('id');
        $title=$request->input('name');
        $firstChoose=$request->input('first_choose')?:1;
        $secondChoose=$request->input('second_choose')?:2;
        $showStyle=$request->input('style')?:1;
        $description=$request->input('description')?:'';
        $pageTypeData=$request->input('data');
        $microPage=$request->input('page_id');

        //add by zhangyh
        $pageTypeData = ProductModule::delProductContentHost($pageTypeData);
        //end

        //定义添加数据数组
        $data=[];
        $errMsg='';
        if(empty($id))
        {
            $errMsg.='id为空';
        }
        if(empty($title))
        {
            $errMsg.='分类名为空';
        }
        else{
            $data['title']=$title;
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['first_choose']=$firstChoose;
        $data['second_choose']=$secondChoose;
        $data['show_style']=$showStyle;
        $data['type_description']=$description;
        if(empty($pageTypeData)||$pageTypeData=='[]')
        {
            $data['type_template_info']=null;
        }
        else {
            if (is_array($pageTypeData)) {
                $data['type_template_info'] = json_encode($pageTypeData);
            }
            else{
                $data['type_template_info'] = $pageTypeData;
            }
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //开启事务
        DB::beginTransaction();
        try {
            $result=StoreMicroPageTypeService::updateData($id,$data);
            if($result['errCode']!=0)
            {
                throw new \Exception('更改页面分类失败');
            }
            $existMicroPage=[];
            $deletedMicroPage=[];
            if($microPage=='[]'||empty($microPage))
            {
                $microPage=[];
            }
            if(!is_array($microPage)) {
                $microPage = json_decode($microPage, true);
            }
            $relationData = MicroPageTypeRelationService::getRelationData(['micro_page_type_id' => $id]);
            if($relationData['errCode']==0&&!empty($relationData['data']))
            {
                foreach($relationData['data'] as $relationItem)
                {
                    if(in_array($relationItem['micro_page_id'],$microPage))
                    {
                        array_push($existMicroPage,$relationItem['micro_page_id']);
                    }
                    else
                    {
                        array_push($deletedMicroPage,$relationItem['micro_page_id']);
                    }
                }
            }
            else if($relationData['errCode']!=0)
            {
                throw new \Exception('查询数据失败');
            }
            if(!empty($microPage))
            {
                foreach ($microPage as $microPageItem)
                {
                    if(!in_array($microPageItem,$existMicroPage))
                    {
                        $addRelationData = ['wid' => $wid, 'micro_page_id' => $microPageItem, 'micro_page_type_id' => $id];
                        //把微页面分类与微页面关系数据插入到数据表中
                        $addRelationReturn = MicroPageTypeRelationService::insertData($addRelationData);
                        if ($addRelationReturn['errCode']!=0) {
                            throw new \Exception('插入失败');
                        }
                    }
                }
            }
            if(!empty($deletedMicroPage))
            {
                foreach($deletedMicroPage as $deleteItem) {
                    $deleteRelationReturn = MicroPageTypeRelationService::deleteData(['micro_page_id'=>$deleteItem,'micro_page_type_id' => $id]);
                    //微页面分类与微页面关系可能不存在 返回值为false
                    if ($deleteRelationReturn['errCode'] != 0) {
                        throw new \Exception('删除失败');
                    }
                }
            }
            //提交事务
            DB::commit();
        }
        catch(\Exception $e)
        {
            //回滚事务
            DB::rollback();
            $message=$e->getMessage();
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * @param Request $request
     * @param MicroPageTypeService $microPageTypeService
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-25
     */
    public function deleteMicroPageType(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //接收前端传递过来的数据
        $id =$request->input('id');
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '接收数据为空';
            return $returnData;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $isExistsData=StoreMicroPageTypeService::getListByCondition(['id'=>$id,'is_auto'=>1]);
        if($isExistsData['errCode']==0&&!empty($isExistsData['data']))
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='系统添加的数据不能够删除';
            return $returnData;
        }
        else if($isExistsData['errCode']!=0)
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='查询微页面分类数据出现问题';
            return $returnData;
        }
        //开始事务
        DB::beginTransaction();
        try {
            //删除微页面分类信息
            $result=StoreMicroPageTypeService::delete($id);
            if ($result['errCode']!=0) {
                throw new \Exception('删除微页面分类失败');
            }
            //微页面分类删除后 删除微页面分类与微页面关系信息
            $relationReturn = MicroPageTypeRelationService::deleteData(['micro_page_type_id' => $id]);
            //微页面分类与微页面关系可能不存在 返回值为false
            if ($relationReturn['errCode']!=0) {
                throw new \Exception('删除失败');
            }
            //提交事务
            DB::commit();
        }
        catch(\Exception $e)
        {
            //捕获到异常 回滚事务
            DB::rollback();
            $message=$e->getMessage();
            $returnData['errCode'] = -4;
            $returnData['errMsg'] = $message;
            return $returnData;
        }
        return $returnData;
    }

    /**
     * todo 查询微页面类型数据
     * @param Request $request
     * @param MicroPageTypeService $microPageTypeService
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-21
     */
    public function selectMicroPageType(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $result=StoreMicroPageTypeService::getListByCondition(['wid'=>$wid]);
        $list=[];
        if($result['errCode']==0&&!empty($result['data']))
        {
            foreach ($result['data'] as $item) {
                $data = [];
                $data['id'] = $item['id'];
                $data['title'] = $item['title'];
                $list[] = $data;
            }
        }
        $returnData['data']=$list;
        return $returnData;
    }
    /**
     * todo 店铺的导航信息存在则更改 不存在则添加
     * @param Request $request
     * @param StoreNavService $storeNavService
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-17
     */
    public function processStoreNav(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //接收数据[数组]
        $id = $request->input('id');
        $apply = $request->input('is_used')?:0;
        $applyPage = $request->input('apply_page');
        $navData = $request->input('page_nav_data');
        $data = [];
        if (empty($navData))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '导航模板数据为空';
            return $returnData;
        }
        if(empty($applyPage)||$applyPage=='[]')
        {
            $data['apply_page'] =null;
        }
        else
        {
            if(is_array($applyPage))
            {
                $data['apply_page'] = json_encode($applyPage);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($applyPage,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                $data['apply_page']=$applyPage;
            }
        }
        $data['is_used']=$apply;
        if(empty($navData)||$navData=='[]')
        {
            $data['nav_template_info']=null;
        }
        else
        {
            if(is_array($navData))
            {
                $data['nav_template_info'] = json_encode($navData);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($navData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-3;
                    $returnData['errMsg']='模板数据格式不符合要求';
                    return $returnData;
                }
                $data['nav_template_info'] = $navData;
            }
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $result=StoreNavService::getRow($wid);
        if($result['errCode']!=0)
        {
            $returnData['errCode'] = $result['errCode'];
            $returnData['errMsg'] = $result['errMsg'];
            return $returnData;
        }
        else if($result['errCode']==0&&empty($result['data']))
        {
            $data['wid'] = $wid;
            //添加店铺导航信息
            $insertResult =StoreNavService::insertData($data);
            if ($insertResult['errCode']!=0) {
                $returnData['errCode'] = $insertResult['errCode'];
                $returnData['errMsg'] = $insertResult['errMsg'];
                return $returnData;
            }
            return $returnData;
        }

        if(empty($id)) {
            $returnData['errCode'] = -5;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }
        //更改店铺导航信息
        $updateResult=StoreNavService::updateData($id,$data);
        if($updateResult['errCode']!=0)
        {
            $returnData['errCode'] = $updateResult['errCode'];
            $returnData['errMsg'] = $updateResult['errMsg'];
            return $returnData;
        }
        return  $returnData;
    }

    /**
     * todo 查询店铺的导航信息
     * @param Request $request
     * @param StoreNavService $storeNavService
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-17
     */
    public function selectStoreNav(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //获取当前店铺导航
        $result=StoreNavService::getRow($wid);
        if($result['errCode']!=0)
        {
            $returnData['errCode']=$result['errCode'];
            $returnData['errMsg']=$result['errMsg'];
            return $returnData;
        }
        else if($result['errCode']==0&&!empty($result['data']))
        {
            $returnData['data']['id']=$result['data']['id'];
            $returnData['data']['is_used']=$result['data']['is_used'];
            $returnData['data']['apply_page']=json_decode($result['data']['apply_page'],true);
            $returnData['data']['menu']=json_decode($result['data']['nav_template_info'],true);
        }
        return $returnData;
    }

    /**
     * todo 查询会员主页数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-09
     */
    public function getMemberHome(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //查询会员主页数据
        $homeList=MemberHomeService::getRow($wid);
        if($homeList['errCode']!=0)
        {
            $returnData['errCode']=$homeList['errCode'];
            $returnData['errMsg']=$homeList['errMsg'];
            return $returnData;
        }
        else if($homeList['errCode']==0&&!empty($homeList['data']))
        {
            $pageData=[];
            foreach($homeList['data'] as $item)
            {
                $typeItem=[];
                //$typeItem['id']=$item['id'];
                $typeItem['name']=$item['home_name'];
                $typeItem['description']='会员主页';
                $typeItem['picUrl']='';
                $customInfo=json_decode($item['custom_info'],true);
                if(!empty($customInfo))
                {
                    $typeItem['picUrl']=$customInfo[0]['thumbnail'];
                }
                $typeItem['url']=config('app.url').'shop/member/index/'.$wid;
                $pageData[]=$typeItem;
            }
            //符合要求数据
            $returnData['data']=$pageData;
        }
        return $returnData;
    }

    /**
     * todo 查询店铺首页数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-09
     */
    public function getStoreHome(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //查询店铺首页数据
        $list=MicroPageService::getRowByCondition(['wid'=>$wid,'is_home'=>1]);
        if($list['errCode']==0&&!empty($list['data']))
        {
            $pageData=[];
            foreach($list['data'] as $item)
            {
                $typeItem=[];
                $typeItem['id']=$item['id'];
                $typeItem['name']=$item['page_title'];
                $typeItem['created_at']=$item['created_at'];
                $typeItem['template_info']=$item['page_template_info'];
                $typeItem['url']=config('app.url').'shop/index/'.$wid;
                $pageData[]=$typeItem;
            }
            //符合要求数据
            $returnData['data']=$pageData;
        }
        return $returnData;
    }

    /**
     * todo 查询微页面分类数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-09
     */
    public function getMicroPageType(Request $request)
    {
        //page表示分页参数，当前页面
        //每页数据
        $perPage= config('database.perPage');
        $returnData=['errCode'=>0,'errMsg'=>'','currentPage'=>0,'pageSize'=>$perPage,'total'=>0,'data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //查询微页面类型数据
        $list=StoreMicroPageTypeService::getListByConditionWithPage(['wid'=>$wid]);
        if(!empty($list[0]))
        {
            $typeList=$list[0];
            $pageData=[];
            foreach($typeList['data'] as $item)
            {
                $typeItem=[];
                $typeItem['id']=$item['id'];
                $typeItem['name']=$item['title'];
                $typeItem['created_at']=$item['created_at'];
                $typeItem['template_info']=$item['type_template_info'];
                $typeItem['url']='#';
                $pageData[]=$typeItem;
            }
            //当前页面
            $returnData['currentPage']=$typeList['current_page'];
            //每页页面数
            $returnData['pageSize']=$typeList['per_page'];
            //总数据
            $returnData['total']=$typeList['total'];
            //符合要求数据
            $returnData['data']=$pageData;
        }
        return $returnData;
    }

    /**
     * todo 查询微页面数据
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-03-20
     */
    public function selectPage(Request $request)
    {
        $title=$request->input('title');
        $whereData=[];
        if(!empty($title))
        {
            $whereData['page_title']=$title;
        }
        //page表示分页参数，当前页面
        //每页数据
        $perPage= config('database.perPage');
        $returnData=['errCode'=>0,'errMsg'=>'','currentPage'=>0,'pageSize'=>$perPage,'total'=>0,'data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $whereData['wid']=$wid;
        //查询微页面数据
        $list=MicroPageService::getListByConditionWithPage($whereData,'created_at','desc',5);
        if(!empty($list[0]['data']))
        {
            $pageList=$list[0];
            $pageData=[];
            foreach($pageList['data'] as $item)
            {
                $typeItem=[];
                $typeItem['id']=$item['id'];
                $typeItem['page_title']=$item['page_title'];
                $typeItem['created_at']=$item['created_at'];
                $typeItem['is_home']=$item['is_home'];
                $typeItem['url']='/shop/microPage/index/'.$wid.'/'.$item['id'];
                $pageData[]=$typeItem;
            }
            //当前页面
            $returnData['currentPage']=$pageList['current_page'];
            //每页页面数
            $returnData['pageSize']=$pageList['per_page'];
            //总数据
            $returnData['total']=$pageList['total'];
            //符合要求数据
            $returnData['data']=$pageData;
        }
        return $returnData;
    }

    /**
     * todo 显示自定义模板信息
     * @param Request $request
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-22
     */
    public function getCustomTemplate(Request $request)
    {
        $title=$request->input('title');
        $whereData=[];
        if(!empty($title))
        {
            $whereData['template_name']=$title;
        }
        //page表示分页参数，当前页面
        //每页数据
        $perPage= config('database.perPage');
        $returnData=['errCode'=>0,'errMsg'=>'','currentPage'=>0,'pageSize'=>$perPage,'total'=>0,'data'=>[]];
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $whereData['wid']=$wid;
        //查询微页面数据
        $list=MicroPageTemplateService::getListByConditionWithPage($whereData);
        if(!empty($list[0]['data']))
        {
            $templateList=$list[0];
            $pageData=[];
            foreach($templateList['data'] as $item)
            {
                $typeItem=[];
                $typeItem['id']=$item['id'];
                $typeItem['name']=$item['template_name'];
                $typeItem['created_at']=$item['created_at'];
                $typeItem['template_info']=$item['template_info'];
                $pageData[]=$typeItem;
            }
            //当前页面
            $returnData['currentPage']=$templateList['current_page'];
            //每页页面数
            $returnData['pageSize']=$templateList['per_page'];
            //总数据
            $returnData['total']=$templateList['total'];
            //符合要求数据
            $returnData['data']=$pageData;
        }
        return $returnData;
    }

    /**
     * todo 获取页面模板
     * @param Request $request
     * @author jonzhang
     * @date 2017-06-13
     * @update 何书哲 2018年9月27日 增加模板类型template_type筛选
     * @update 何书哲 2018年9月20日 小程序和微商城公用，小程序只展示自定义模版、商品分类模板
     */
    public function getTemplateMarket(Request $request,TemplateMarketService $templateMarketService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $useCase=$request->input('type');
        $templateType = $request->input('template_type', 0);
        $source = $request->input('source', 0);
        if(empty($useCase))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'type为空';
            return $returnData;
        }
        $data = ['use_case'=>$useCase, 'current_status'=>0];
        $templateType && $data['type'] = $templateType;
        $templateMarketData = $templateMarketService->getListByConditionWithPage($data, ['type','id'],'asc');
        if(!empty($templateMarketData[0]['data']))
        {
            foreach($templateMarketData[0]['data'] as $item)
            {
                $templateMarketItem = [];
                $templateMarketItem['title'] = $item['title'];
                $source ? ($url = '/merchants/marketing/liteAddPage?id='.$item['id'].'&option=create') : ($url = '/merchants/store/showMicroPage/create/'.$item['id']);
                $templateMarketItem['url'] = $url;
                $templateMarketItem['screenshot'] = $item['thumb_url'];
                $returnData['data'][] = $templateMarketItem;
            }
        }
        return  $returnData;
    }

    /**
     * todo 处理缓存数据 [此入口仅仅是开发来使用]
     * @param Request $request
     * @author jonzhang
     * @date 2017-07-05
     */
    public function processCacheData(Request $request)
    {
        $type=$request->input('type')??'all';
        $key=$request->input('key');
        if(empty($key))
        {
            dd('key为null');
        }
        $redis = new MicroPageRedis();
        if($type=='delete')
        {
           $result=$redis->deleteByKey($key);
           dd($result);
        }
        else if($type=='all')
        {
            $keys=$redis->getALLKeys($key);
            dd($keys);
        }
        else if($type='one')
        {
            $value=$redis->getValueByKey($key);
            dd($value);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180509
     * @desc 店铺升级
     */
    public function shopUpgrade(Request $request,WeixinRoleService $weixinRoleService)
    {
        $now = date("Y-m-d H:i:s");
        $where['wid'] = session('wid');
        $where['start_time'] = ['<=',$now];
        $where['end_time'] = ['>=',$now];
        $roleData = $weixinRoleService->init()->where($where)->getList(false);
        if (!$roleData[0]['data']){
            error('店铺权限错误');
        }
        $roleData = current($roleData[0]['data']);
        if ($roleData['admin_role_id'] != '6'){
            error('对不起！您的店铺暂时不能升级');
        }

        $res = $weixinRoleService->init()->where(['id'=>$roleData['id']])->update(['admin_role_id'=>'5'],false);
        if ($res){
            $request->session()->flush();
            $cookie = Cookie::forget('auth');
            return  mysuccess('恭喜您!升级成功!请重新登陆');
        }else{
            error();
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180509
     * @desc 关闭弹框
     */
    public function closeFrame()
    {
        (new StoreModule())->setRedisFrameInfo(session('wid'))?success():error();
    }

    /**
     * 店铺基本信息设置
     * @param Request $request 参数类
     * @param ShopService $shopService 店铺类
     * @return json
     * @author 许立 2018年12月24日
     */
    public function set(Request $request, ShopService $shopService)
    {
        $result = true;
        $update = [];
        $distributeGradeTitle = $request->input('grade_title', '');
        $distributeGradeTitle && $update['distribute_default_grade_title'] = $distributeGradeTitle;
        $update && $result = $shopService->update(session('wid'), $update);
        $result ? success() : error();
    }

}
