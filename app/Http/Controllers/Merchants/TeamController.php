<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Lib\Redis\SMSKeys;
use App\S\Foundation\RegionService;
use App\S\Foundation\VerifyCodeService;
use App\S\Member\MemberService;
use App\S\Store\TemplateMarketService;
use App\Services\Permission\AdrolePermissionService;
use App\Services\Permission\WeixinRoleService;
use App\Services\Permission\WeixinUserService;
use App\Services\Wechat\AuthorizationService;
use App\Services\WeixinBusinessService;
use Illuminate\Http\Request;
use MemberHomeService;
use MicroPageService;
use ProductService;
use StoreNavService;
use Validator;
use WeixinService;
use App\Lib\Redis\Wechat;
use MicroPageTypeService as TeamMicroPageTypeService;
use PermissionService;
use WXXCXMicroPageService;
use App\S\WXXCX\WXXCXConfigService;
use App\S\Weixin\ShopService;


class TeamController extends Controller {
    /**
     * 选择公司/店铺
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月21日 15:12:18
     * 
     * @return 视图
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function index(WeixinUserService $weixinUserService,ShopService $shopService) {
        
        /* 获取数据 */
        //list($list, $pageHtml)  = WeixinService::getMyShop();
        list($list, $pageHtml) = $shopService->getMyShops();
        //dd($list);
        if ($list['data']) {
            foreach ($list['data'] as $key => &$value) {
                $value['wxxcxConfig'] = (new WXXCXConfigService())->getRow($value['id']);
                $value['show_color'] = 'yellow';
                $weixinRoleData = (new WeixinRoleService())->init()->where(['wid'=>$value['id']])->getList(false)[0]['data'];
                if ($weixinRoleData[0]) {
                    if (strtotime($weixinRoleData[0]['end_time']) < time()) {
                        $value['is_overdue'] = 1;

                    }else {
                        if (strtotime($weixinRoleData[0]['end_time']) > strtotime('+7 days')) {  //有效期大于15天时，说明是续费店铺,店铺显示为绿色
                            $value['show_color'] = 'green';

                        }
                        $value['is_overdue'] = 0;
                        $dateArr = explode(' ',$weixinRoleData[0]['end_time']);
                        $value['limited'] = $dateArr[0];
                    }
                }
                (new AdrolePermissionService())->judgeShopPermission($value,$weixinRoleData);
            }
        }
        $count = (new WeixinUserService())->init()->wheres(['uid'=>session('userInfo')['id'],'deleted_at' => null])->count();
        /* 返回视图 */
        return view('merchants.team.index', array(
            'title'    => '选择公司/店铺',
            'list'     => $list['data'],
            'pageHtml' => $pageHtml,
            'myShopNum'=> $count
        ));
    }

    /**
     * 创建店铺
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function create( Request $request, RegionService $regionService, WeixinBusinessService $weixinBusinessService,WeixinUserService $weixinUserService,WeixinRoleService $weixinRoleService,ShopService $shopService,$id = 0 ) {
        /* 获取用户信息 */
        $userInfo = $request->session()->get('userInfo');
        $count = (new WeixinUserService())->init()->wheres(['uid'=>$userInfo['id'],'deleted_at' => null])->count();
        if ($count > 30) {
            return myerror("超过可建立店铺数量");
        }
        /* 操作名称 */
        $webTitle = empty($id) ? '创建店铺' : '编辑店铺';

        if ( $request->isMethod('post') ) {
            /* 接收参数 */
            $input = $request->only(['uid', 'company_name', 'province_id', 'city_id', 'area_id', 'address', 'shop_name', 'agreement']);
            /* 店铺id */
            $id = isset($input['id']) ? $input['id'] : 0;
            /* 验证规则 */
            $rules = array(
                'shop_name'    => 'required|between:1,50',
                //'business_id'  => 'required',
                'company_name' => 'between:1,50',
                'province_id'  => 'required',
                'city_id'      => 'required',
                'area_id'  => 'required',
                'address'      => 'required|between:1,200',
                //'agreement'    => 'accepted',
            );
            /* 错误消息 */
            $messages = array(
                'shop_name.required'    => '请填写店铺名称',
                'shop_name.between'     => '店铺名称最多填写50个字符',
                //'business_id.required'  => '请选择主营商品',
                'company_name.between'  => '公司名称最多填写50个字符',
                'province_id.required'  => '请选择省份',
                'city_id.required'      => '请选择城市',
                'area_id.required'      => '请选择地区',
                'address.required'      => '请填写联系地址',
                'address.between'       => '联系地址最多填写200个字符',
                //'agreement.accepted'    => '请查看并同意协议'
            );
            $validator = Validator::make($input, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            unset($input['agreement']);
            //店铺名称创建不允许重复 hsz 2018/6/15
           /* if (WeixinService::init()->checkStoreIsExist(['shop_name'=>$input['shop_name']])) {
                error("该店铺名称已存在，请更改店铺名称");
            }*/
            //update by 吴晓平 2018年09月12日
            if ($shopService->checkStoreIsExist(['shop_name'=>$input['shop_name']])) {
                error("该店铺名称已存在，请更改店铺名称");
            }

            if ( empty($id) ) {
                /* 新增 */
                //$dbResult = WeixinService::init('uid', $userInfo['id'])->add($input, false);
                $dbResult = $shopService->add($input);
                $id = $dbResult;

                //店铺创建成功后 默认新建两个默认商品分组
                ProductService::createDefaultGroups($id);

                //店铺创建成功后 默认新建两个默认商品
                $idArr = ProductService::createDefaultProducts($id);

                //创建默认店铺主页 add by jonzhang
                #begin
                $data=[
                    'wid'=>$id,'page_title'=>'店铺主页','is_show'=>1,'is_home'=>1,
                     'page_template_info'=>'[{"showRight":true,"cardRight":4,"type":"goods","editing":"editing","listStyle":1,"cardStyle":1,"showSell":true,"btnStyle":1,"goodName":false,"goodInfo":false,"priceShow":true,"nodate":false,"products_id":'.json_encode($idArr).',"goods":[],"thGoods":[]}]'
                ];
                MicroPageService::insertData($data);
                #end

                //创建小程序默认店铺主页 add by jonzhang  2017-09-27
                #begin
                $xcxData=[
                    'wid'=>$id,'title'=>'店铺主页','is_home'=>1,'title_color'=>'black',
                    'template_info'=>'[{"showRight":true,"cardRight":4,"type":"goods","editing":"editing","listStyle":1,"cardStyle":1,"showSell":true,"btnStyle":1,"goodName":false,"goodInfo":false,"priceShow":true,"nodate":false,"products_id":'.json_encode($idArr).',"goods":[],"thGoods":[]}]'
                ];
                WXXCXMicroPageService::insertData($xcxData);
                #end

                //添加默认微页面分类 add by jonzhang
                #begin
                $microPageTypeData=[];
                $pagetypeHostData1=['wid'=>$id,'title'=>'*最热分类','is_auto'=>1,'show_style'=>1,
                    'type_template_info'=>'[{"showRight":true,"cardRight":2,"type":"category","title":"*最热分类","editing":"editing","desc":"","firstChoose":0,"secondChoose":3,"showStyle":0,"pageList":[]}]'];
                $microPageTypeData[]=$pagetypeHostData1;
                $pagetypeHostData2=['wid'=>$id,'title'=>'*最新分类','is_auto'=>1,'show_style'=>1,
                    'type_template_info'=>'[{"showRight":true,"cardRight":2,"type":"category","title":"*最新分类","editing":"editing","desc":"","firstChoose":0,"secondChoose":3,"showStyle":0,"pageList":[]}]'
                ];
                $microPageTypeData[]=$pagetypeHostData2;
                $pagetypeOtherData=['wid'=>$id,'title'=>'*未分类','is_auto'=>1,'show_style'=>1,
                    'type_template_info'=>'[{"showRight":true,"cardRight":2,"type":"category","title":"*未分类","editing":"editing","desc":"","firstChoose":0,"secondChoose":3,"showStyle":0,"pageList":[]}]'
                ];
                $microPageTypeData[]=$pagetypeOtherData;
                foreach($microPageTypeData as $item)
                {
                    TeamMicroPageTypeService::insertData($item);
                }
                #end

                //添加默认店铺导航 add by jonzhang
                #begin
                $sourceUrl=config('app.source_url');
                $navTemplate='{"menusType":1,"menu":[';
                $navTemplate.='{"title":"首页","linkUrl":"/shop/index/'.$id.'","linkUrlName":"首页","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/01.png","iconActive":"/static/images/01_on.png","dropDown":false},';
                $navTemplate.='{"title":"商品","linkUrl":"/shop/product/index/'.$id.'","linkUrlName":"商品","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/02.png","iconActive":"/static/images/02_on.png","dropDown":false},';
                $navTemplate.='{"title":"购物车","linkUrl":"/shop/cart/index/'.$id.'","linkUrlName":"购物车","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/03.png","iconActive":"/static/images/03_on.png","dropDown":false},';
                $navTemplate.='{"title":"我的","linkUrl":"/shop/member/index/'.$id.'","linkUrlName":"我的","submenusShow":false,"submenusLeft":"-15px","submenus":[],"width":"25%","icon":"/static/images/04.png","iconActive":"/static/images/04_on.png","dropDown":false}';
                $navTemplate.='],"bgColor":"#ffffff","title":"微信公众号自定义菜单样式"}';

                $storeNavData=[
                    'wid'=>$id,'is_used'=>1,'apply_page'=>'[1,2,3,4,5]',
                    'nav_template_info'=>$navTemplate
                ];
                $result=StoreNavService::insertData($storeNavData);
                #end

                //添加会员主页 add by jonzhang
                #begin
                //获取店铺名称
                $storeName=$request->input('shop_name');
                $homeInfo='[';
                $homeInfo.='{"showRight":true,"cardRight":1,"type":"member","title":"会员主页","editing":"editing","thumbnail":"mctsource/images/personal.png","levelShow":true,"showCredit":false},';
                $homeInfo.='{"showRight":false,"cardRight":7,"type":"store","editing":"","id":'.$id.',"store_name":"'.$storeName.'","url":"'.config('app.url').'shop/index/'.$id.'"}';
                $homeInfo.=']';
                $memberHome=[
                    'wid'=>$id,
                    'home_name'=>'会员主页',
                    'custom_info'=>$homeInfo
                ];
                MemberHomeService::insertData($memberHome);
                #end

                /*创建店铺成功添加店铺，用户，角色关联信息add modify by zhangyh 201703151457*/
                $weixinUserData = [
                    'wid'       => $id,
                    'uid'       => $userInfo['id'],
                    'oper_id'  => $userInfo['id'],
                    'role_id'  => 1
                ];
                $weixinUserService->init()->add($weixinUserData,false);
                /*添加店铺角色*/
                $weixinRoleData =[
                    'wid'               => $id,
                    'admin_role_id'    => 1,
                    'end_time'          => date('Y-m-d H:i:s',strtotime('+7 day'))// xiugai  fuguowei  体验时间改为7天
                ];
                $weixinRoleService->init()->add($weixinRoleData,false);
                $request->session()->put('wid',$id);
                PermissionService::addPermissionToRedis();
                $request->session()->save();

                /*****************fuguowei  客服要求删除行业 默认模版id为13  如果创建店铺的时候需有模版,请删除此添加的代码,并修改跳转链接,css样式***********************/
                //定义存放模板的变量
                $templateMarketData=[];
                $templateData=(new TemplateMarketService())->getRowById(13);
                if($templateData['errCode']==0&&!empty($templateData['data']))
                {
                    $defaultTemplateData=$templateData['data']['template_data'];
                    $defaultTemplateData=json_decode($defaultTemplateData,true);
                    //模板数据中 添加默认的商品
                    if(!empty($defaultTemplateData)) {
                        foreach ($defaultTemplateData as $item)
                        {
                            $item['products_id'] = [];
                            if ($item['type'] == 'goods') {
                                $item['products_id'] = $idArr;
                            }
                            $templateMarketData[] = $item;
                        }
                    }
                }
                //更改默认的店铺主页信息
                if(!empty($templateMarketData))
                {
                    $microPageData['page_template_info'] =json_encode($templateMarketData);
                    $microPageData['template_id']=13;
                    $resultValue=MicroPageService::selectIDByCondition(['wid' => $id, 'is_home' => 1]);
                    if($resultValue['errCode']==0&&!empty($resultValue['data']))
                    {
                        MicroPageService::updateData($resultValue['data']['id'],$microPageData);
                    }
                }
                /*****************************************/

            } else {
                /* 编辑 */
                //$detail = WeixinService::init('uid', $userInfo['id'])->getInfo($id);
                $detail = $shopService->getRowById($id);
                if ( $detail['id'] != $id ) {
                    error('店铺不存在或已被删除');
                }
                //$dbResult = WeixinService::where(['id' => $id])->update($input, false);
                $dbResult = $shopService->update($id,$input);
            }
            if ( $dbResult !== false ) {
                success($webTitle.'成功', '/merchants/team/complete/' . $id);
            }
            error($webTitle.'失败');
        }

        /* 查询店铺详情 */
        $detail = array();
        if ( !empty($id) ) {
            //$detail = WeixinService::init('uid', $userInfo['id'])->getInfo($id);
            $detail = $shopService->getRowById($id);
            if ( $detail['id'] != $id ) {
                error('店铺不存在或已被删除');
            }
        }

        // 省市区
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }
        $provinceList = $regionList[-1];



        return view('merchants.team.create',array(
            'title'          => $webTitle,
            'regions_datas'  => json_encode($regionList),
            'regionList'     => $regionList,
            'provinceList'   => $provinceList,
            //'businessList'   => $business_datas[0],
            //'business_datas' => json_encode($business_datas),
            'detail'         => $detail
        ));
    }

    /**
     * [getTeamPhone 删除店铺获取对应的手机号]
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getTeamPhone(Request $request,MemberService $memberService,ShopService $shopService)
    {
        $wid = $request->input['wid'];
        //$weixinData = WeixinService::init()->getInfo(82);
        $weixinData = $shopService->getRowById(82);
        if($weixinData){
            $memberData = $memberService->getRowById($weixinData['uid']);
            if($memberData){
                success('操作成功','',$memberData['mobile']);
            }else{
                error();
            }
        }else{
            error();
        }
    }

    /**
     * [sendCode 删除店铺发送验证码]
     * @return [type] [description]
     */
    public function sendCode(Request $request,VerifyCodeService $verifyCodeService,$wid)
    {
        $phone = $request->input('phone');
        if(!$phone){
            error('请输入您的手机号码');
        }
        $sms_code = $request->input('SMS_code');
        //生成验证码 随机生成4位
        $code = rand(1000,9999);
        $datas = [$code,1,'0571-87796692'];
        $result = $verifyCodeService->sendCode($phone,$datas,$sms_code);
        if($result->statusCode!=0) {
            error((string)$result->statusMsg);
        }else{
            $smsKeys = new SMSKeys('del'.$wid);
            $smsKeys->set($code); 
            success('验证码发送成功');
        }
    }
    /**
     * 删除店铺
     * 
     * @param  Request $request [http请求类]
     * @param  Integer $id      [店铺id]
     * @modify by zhangyh 删除-逻辑重新修改
     * @return json
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function delete( Request $request,ShopService $shopService, $id ) {
        if ( $request->isMethod('post') ) {
            $smsKeys = new SMSKeys('del'.$id);
            $verify = $smsKeys->get(); 
            if(trim($request->input('code')) != $verify){
                error('验证码输入不正确');
            } 
            //$res = WeixinService::delMyShop($id);
            $res = $shopService->delShopWithRole($id);
            if ($res['success'] == 1){
                success();
            }else{
                error($res['message']);
            }
        }
        error('非法操作');
    }

    /**
     * 创建店铺
     * @return [type] [description]
     */
    public function template(Request $request,TemplateMarketService $templateMarketService, $id ) {

        if ( $request->isMethod('post') ) {

            $templateId=$request->input('template_id');

            if(empty($templateId))
            {
                error('请选择模版');
            }
            //$templateId=234;

            //定义存放默认商品id的变量
            $productsId=[];
            //查询店铺下的默认商品信息
            $productData=ProductService::GetProductList($id,['is_default'=>1]);
            if($productData['errCode']==0&&!empty($productData['data']))
            {
                $productsId=$productData['data']['products_id'];
            }

            //定义存放模板的变量
            $templateMarketData=[];

            $templateData=$templateMarketService->getRowById($templateId);
            if($templateData['errCode']==0&&!empty($templateData['data']))
            {
                $defaultTemplateData=$templateData['data']['template_data'];
                $defaultTemplateData=json_decode($defaultTemplateData,true);
                //模板数据中 添加默认的商品
                if(!empty($defaultTemplateData)) {
                    foreach ($defaultTemplateData as $item)
                    {
                        $item['products_id'] = [];
                        if ($item['type'] == 'goods') {
                            $item['products_id'] = $productsId;
                        }
                        $templateMarketData[] = $item;
                    }
                }
            }
            //更改默认的店铺主页信息
            if(!empty($templateMarketData))
            {
                $microPageData['page_template_info'] =json_encode($templateMarketData);
                $microPageData['template_id']=$templateId;
                $resultValue=MicroPageService::selectIDByCondition(['wid' => $id, 'is_home' => 1]);
                if($resultValue['errCode']==0&&!empty($resultValue['data']))
                {
                    MicroPageService::updateData($resultValue['data']['id'],$microPageData);
                }
            }

            success('选择模版成功', '/merchants/team/complete/' . $id);
        }

        // 查询所有模版
        $storeTemplateData=$templateMarketService->getListByConditionWithPage(['current_status'=>0,'use_case'=>1],'weight','desc');
        $list =[];
        if(!empty($storeTemplateData[0]['data']))
        {
            $list=$storeTemplateData[0]['data'];
        }

        return view('merchants.team.template',array(
            'title'   => '选择模版',
            'list'    => $list,
            'wid'     =>$id,
        ));
    }

    /**
     * 发布完成
     * 
     * @param  Request $request [http请求类]
     * @return view
     */
    public function complete( Request $request, $id ) {
        //获取微信开放平台的预授权码
        $authorizationService = new AuthorizationService();
        $re = $authorizationService->getPreAuthCode();
        $pre_auth_code = $re['pre_auth_code'];
        $authUrl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.config('app.auth_appid').'&pre_auth_code='.$pre_auth_code.'&redirect_uri='.config('app.url').'merchants/wechat/weixinSet';

        return view('merchants.team.complete', [
            'title'   => '发布完成',
            'id'      => $id,
            'authUrl' => $authUrl
        ]);
    }
}
