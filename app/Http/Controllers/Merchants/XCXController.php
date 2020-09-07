<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/12
 * Time: 17:23
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use App\Module\LiveRoomModule;
use App\Module\ProductModule;
use App\S\Store\TemplateMarketService;
use App\Services\WeixinService;
use Illuminate\Http\Request;

use App\S\WXXCX\WXXCXConfigService;
use App\Lib\WXXCX\ThirdPlatform;
use WXXCXMicroPageService;
use MallModule;
use Log;
use App\Module\GroupsRuleModule;
use App\S\Order\OrderService;
use App\Lib\BLogger;
use App\Module\XCXModule;
use App\Module\DiyComponentValidatorModule;
use App\S\Weixin\ShopService;
use App\Model\WXXCXMicroPage;
use DB;

class XCXController extends Controller
{
    /**
     * todo 处理小程序配置信息
     * @param Request $request
     * @param WXXCXConfigService $wxxcxConfigService
     * @return array
     * @author jonzhang
     * @date 2017-08-16
     * @update 梅杰 20180705 更新通过店铺id以及记录id查询
     * @update 梅杰 20180712 修改商户信息时批量修改所有的绑定小程序
     * @update 梅杰 20180723 每个小程序支持商户号
     * @update 陈文豪 20180828 去除参数空格
     */
    public function processConfigData(Request $request,WXXCXConfigService $wxxcxConfigService)
    {
        $returnData   = ['code'=>40000,'hint'=>'','list'=>[]];
        $appId        = trim($request->input('app_id'));
        $appSecret    = trim($request->input('app_secret'));
        $appPaySecret = trim($request->input('app_pay_secret'));
        $merchantNo   = trim($request->input('merchant_no'));
        $wid          = session('wid');
        $id           = $request->input('id',0);
        if(empty($wid))
        {
            $returnData['code']=-10;
            $returnData['hint']='登录超时';
            return $returnData;
        }
        //判断前端传递过来的参数
        $strMsg='';
        if(empty($appId))
        {
            $strMsg.='appId为空';
        }
        if(empty($appSecret))
        {
            $strMsg.='appSecret为空';
        }
        if(strlen($strMsg)>0)
        {
            $returnData['code']=-11;
            $returnData['hint']=$strMsg;
            return $returnData;
        }
        //add by jonzhang 2018-01-11 剔除空格
        $appId=trim($appId);
        $appSecret=trim($appSecret);
        if(!empty($appPaySecret))
            $appPaySecret=trim($appPaySecret);
        if(!empty($merchantNo))
            $merchantNo=trim($merchantNo);
        $xcxConfig=$wxxcxConfigService->getRowByIdWid($wid,$id);
        $data=['wid'=>$wid,'app_id'=>$appId,'app_secret'=>$appSecret,'app_pay_secret'=>$appPaySecret,'merchant_no'=>$merchantNo];
        //不存在配置信息，则添加
        if($xcxConfig['errCode']==0&&empty($xcxConfig['data']))
        {
            $insertResult=$wxxcxConfigService->insertData($data);
            $returnData['code']=$insertResult['errCode']==0?40000:$insertResult['errCode'];
            $returnData['hint']=$insertResult['errMsg'];
            $returnData['list']=$insertResult['data'];
            return $returnData;
        }//存在，则更改
        else if($xcxConfig['errCode']==0&&!empty($xcxConfig['data']))
        {
            unset($data['wid']);
            //add by jonzhang 2018-05-10 更改配置信息 不能够更改app_id
            unset($data['app_id']);
            $id=$xcxConfig['data']['id'];
//            $default = $wxxcxConfigService->getRowById($id,$wid);
//            if ($default['errCode'] == 0 && ($default['data']['app_secret'] != $data['app_secret'] || $default['data']['merchant_no'] != $data['merchant_no'])) {
//                $wxxcxConfigService->updateByWid($wid,['app_pay_secret'=>$appPaySecret,'merchant_no'=>$merchantNo]);
//            }
            $updateResult=$wxxcxConfigService->updateData($id,$data);
            $returnData['code']=$updateResult['errCode']==0?40000:$updateResult['errCode'];
            $returnData['hint']=$updateResult['errMsg'];
            $returnData['list']=$id;
            return $returnData;
        }//直接输出错误信息
        else
        {
            $returnData['code']=$xcxConfig['errCode'];
            $returnData['hint']=$xcxConfig['errMsg'];
            return $returnData;
        }
    }

    /**
     * 流量主信息配置
     * @param Request $request 请求参数
     * @param WXXCXConfigService $wxxcxConfigService 小程序配置service
     * @return array
     * @author 何书哲 2018年10月10日
     */
    public function processUnitData(Request $request, WXXCXConfigService $wxxcxConfigService)
    {
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        $wid = session('wid');
        $id = $request->input('id', 0);
        $unit_id = trim($request->input('unit_id', ''));
        if (empty($wid)) {
            $returnData['code'] = -10;
            $returnData['hint'] = '登录超时';
            return $returnData;
        }
        if (empty($unit_id)) {
            $returnData['code'] = -11;
            $returnData['hint'] = 'unitId为空';
            return $returnData;
        }
        $xcxConfig = $wxxcxConfigService->getRowById($id);
        if (empty($xcxConfig['data'])) {
            $returnData['code'] = -12;
            $returnData['hint'] = '小程序配置信息不存在';
            return $returnData;
        }
        $data = ['unit_id' => $unit_id];
        $updateResult = $wxxcxConfigService->updateData($id, $data);
        $returnData['code'] = $updateResult['errCode'] == 0 ? 40000 : $updateResult['errCode'];
        $returnData['hint'] = $updateResult['errMsg'];
        $returnData['list'] = $id;
        return $returnData;
    }

    /**
     * todo 查询小程序配置信息
     * @param Request $request
     * @param WXXCXConfigService $wxxcxConfigService
     * @author jonzhang
     * @date 2017-08-16
     * @update 梅杰 20180705 查询通过店铺id以及记录id查询
     * @upadte 陈文豪 2018年07月06日 处理小程序被拒的消息
     * @update 梅杰 20180716 reason 为空时bug
     */
    public function selectData(Request $request,WXXCXConfigService $wxxcxConfigService)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>[]];
        $wid = session('wid');
        $id = $request->input('id',0);
        if(empty($wid))
        {
            $returnData['code']=-1;
            $returnData['hint']='session过期';
            return $returnData;
        }
        $configList=[];
        //查询小程序配置信息是否存在
        $xcxConfig=$wxxcxConfigService->getRowByIdWid($wid,$id);
        if($xcxConfig['errCode']==0&&!empty($xcxConfig['data']))
        {
            $item=$xcxConfig['data'];
            if($item['status']==0)
            {
                $item['statusName']='未上传';
            }
            else if($item['status']==1)
            {
                $item['statusName']='已上传代码';
            }
            else if($item['status']==2)
            {
                $item['statusName']='审核中';
            }
            else if($item['status']==3)
            {
                $item['statusName']='审核被拒';
            }
            else if($item['status']==4)
            {
                $item['statusName']='审核成功';
            }
            else if($item['status']==5)
            {
                $item['statusName']='已发布';
            }
            $item['func_info_name']=$wxxcxConfigService->processFuncInfo($item['func_info']);
            $configList=$item;
        }
        $returnData['code']=$xcxConfig['errCode']==0?40000:$xcxConfig['errCode'];
        $returnData['hint']=$xcxConfig['errMsg'];
        $returnData['list']=$configList;
        $returnData['list']['reason']=html_entity_decode($returnData['list']['reason'] ?? '');
        return $returnData;
    }

    /**
     * todo 授权URL
     * @param ThirdPlatform $thirdPlatform
     * @return array
     * @author jonzhang
     * @date 2017-09-11
     * update by wuxiaoping 2018.01.23
     * 小程序设置添加重新授权按钮，添加一个type参数
     * 当type不为空，且type==updateauthorized时，表示更新授权
     * @update 张永辉 2017年7月13日 限制绑定的数量
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function startAuthorizer(ThirdPlatform $thirdPlatform,Request $request,WXXCXConfigService $WXXCXConfigService,ShopService $shopService)
    {
        $type = $request->input('type') ?? '';
        $num = $WXXCXConfigService->count(['wid'=>session('wid'),'current_status'=>0]);
        //$res = (new WeixinService())->getStore(session('wid'));
        $res = $shopService->getRowById(session('wid'));
        if ( $type != 'updateauthorized' && $res['xcx_num'] <= $num){
            $returnData=["errCode"=>-1,"errMsg"=>"已达到上线","data"=>''];
            return $returnData;
        }
        $returnData=["errCode"=>0,"errMsg"=>"","data"=>''];
        $wid=session('wid');

        if(empty($wid))
        {
            $returnData['code']=-1;
            $returnData['hint']='session过期';
            return $returnData;
        }
        return $thirdPlatform->startAuthorizer($wid,$type);
    }

    /**
     * todo 取消小程序第三方平台授权
     * @author jonzhang
     * @date 2017-09-12
     * @update 梅杰 20180705 更新店铺指定小程序
     */
    public function cancelAuthorization(Request $request,WXXCXConfigService $wxxcxConfigService)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $wid=session('wid');
        $id = $request->input('id',0);
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $wxxcxConfigData=$wxxcxConfigService->getListByCondition(['id'=>$id,'wid'=>$wid,'current_status'=>0]);
        if($wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data']))
        {
            $id=$wxxcxConfigData['data'][0]['id'];
            $result=$wxxcxConfigService->updateData($id,['current_status'=>-1]);
            return $result;
        }
        if($wxxcxConfigData['errCode']==0&&empty($wxxcxConfigData['data']))
        {
            $returnData['errCode']=-100;
            $returnData['errMsg']='该店铺没有绑定小程序,无法取消授权';
            return $returnData;
        }
        else
            return $wxxcxConfigData;
    }

    /**
     * todo 小程序授权成功跳转
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang
     * @date 2017-09-13
     */
    public function authorizePrompt(Request $request)
    {
        $status=$request->input('status')??0;
        return view('merchants.xcx.authPrompt',[
            'status' =>$status,
        ]);
    }

    /**
     * todo 已经授权的小程序 取消授权[开发使用]
     * @param Request $request
     * @param WXXCXConfigService $wxxcxConfigService
     * @return array
     * @author jonzhang
     * @date 2017-09-13
     */
    public function deleteAuthorizeTest(Request $request,WXXCXConfigService $wxxcxConfigService)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $appId=$request->input('appId');
        if(empty($appId))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='appid为空';
            return $returnData;
        }
        $wxxcxConfigData=$wxxcxConfigService->getListByCondition(['current_status'=>0,'app_id'=>$appId]);
        if($wxxcxConfigData['errCode']==0&&!empty($wxxcxConfigData['data']))
        {
            $id=$wxxcxConfigData['data'][0]['id'];
            return $wxxcxConfigService->updateData($id,['current_status'=>-1]);
        }
        else if($wxxcxConfigData['errCode']==0&&empty($wxxcxConfigData['data']))
        {
            $returnData['errCode']=-100;
            $returnData['errMsg']='数据不存在';
            return $returnData;
        }
        else
        {
            return $wxxcxConfigData;
        }
    }

    /**
     * todo 查询已经授权的小程序[开发使用]
     * @param Request $request
     * @param WXXCXConfigService $wxxcxConfigService
     * @return array
     * @author jonzhang
     * @date 2017-09-13
     */
    public function selectAuthorizeTest(Request $request,WXXCXConfigService $wxxcxConfigService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $appId=$request->input('appId');
        if(empty($appId))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='appid为空';
            return $returnData;
        }
        $wxxcxConfigData=$wxxcxConfigService->getListByCondition(['current_status'=>0,'app_id'=>$appId]);
        return  $wxxcxConfigData;
    }

    /**
     * todo authorizer_access_token 测试
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @author jonzhang
     * @date 2017-09-13
     */
    public function getAuthorizerAccessTokenTest(Request $request,ThirdPlatform $thirdPlatform)
    {
        $id=$request->input('id')??0;
        $result=$thirdPlatform->getAuthorizerAccessToken(['id'=>$id]);
        dd($result);
    }

    /**
     * todo 添加小程序微页面信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function insertXCXPage(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //存放要添加数据
        $title = $request->input('title');
        $description = $request->input('description');
        $bgColor = $request->input('bgcolor');
        $titleColor = $request->input('title_color') ?? 'white';
        $requestData = $request->input('template_info');
        $requestData = ProductModule::delProductContentHost($requestData); //add by zhangyh
        //add MayJay 添加规则说明
        $rule = $request->input('rule') ?? 0;
        $ruleTitle = $request->input('rule_title');
        $ruleDesc  = $request->input('rule_desc');
        //end
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
        $data['wid']=$wid;
        $data['title_color'] = $titleColor;
        //add MayJay
        if($rule){
            $data['rule'] = 1;
            if(empty($ruleTitle) || empty($ruleDesc)){
                $returnData['errCode'] = -4;
                $returnData['errMsg'] = '请填写规则标题或者规则描述';
                return $returnData;
            }
            if(mb_strlen($ruleTitle) > 10){
                $returnData['errCode'] = -5;
                $returnData['errMsg'] = '规则标题最多十个字符';
                return $returnData;
            }
            $data['rule_title'] = $ruleTitle;
            $data['rule_desc'] = $ruleDesc;
        }
        //end
        if(!empty($bgColor))
        {
            $data['bg_color'] = $bgColor;
        }
        if(!empty($description)) {
            $data['description'] = $description;
        }
        if (empty($title))
        {
            $errMsg .= '微页面名称为空';
        }
        else
        {
            $data['title'] = $title;
        }

        if (empty($requestData)||$requestData=='[]')
        {
            $data['template_info']=null;
        }
        else
        {
            if(is_array($requestData))
            {
                $data['template_info'] = json_encode($requestData);
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
                $data['template_info']=$requestData;
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

        //add by jonzhang 2018-04-20 模板数据效验
        if(!empty($data['template_info']))
        {
            $dcvm=new DiyComponentValidatorModule();
            $checkResult=$dcvm->checkModel($data['template_info']);
            if($checkResult['errCode']==0&&!empty($checkResult['data']))
            {
                $data['template_info']=$checkResult['data'];
            }
            else
            {
                return $checkResult;
            }
        }

        //add by jonzhang 添加数据长度效验
        if (strlen($data['template_info']) > 16777215 - 1)
        {
            $returnData['errCode'] = -6;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }
        return WXXCXMicroPageService::insertData($data);
    }

    /**
     * todo 更改小程序微页面数据
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function updateXCXPage(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //存放要添加数据
        $id = $request->input('id');
        $title = $request->input('title');
        $description = $request->input('description');
        $bgColor = $request->input('bgcolor');
        $titleColor = $request->input('title_color') == '#ffffff' ? 'white' : 'black';
        $requestData = $request->input('template_info');
        //add by zhangyh
        $requestData = ProductModule::delProductContentHost($requestData);
        //end
        //add MayJay 添加规则说明
        $rule = $request->input('rule') ?? 0;
        $ruleTitle = $request->input('rule_title');
        $ruleDesc  = $request->input('rule_desc');
        //end

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
        //add MayJay
        if($rule){
            $data['rule'] = 1;
            if(empty($ruleTitle) || empty($ruleDesc)){
                $returnData['errCode'] = -4;
                $returnData['errMsg'] = '请填写规则标题或者规则描述';
                return $returnData;
            }
            if(mb_strlen($ruleTitle) > 10){
                $returnData['errCode'] = -5;
                $returnData['errMsg'] = '规则标题最多十个字符';
                return $returnData;
            }
            $data['rule_title'] = $ruleTitle;
            $data['rule_desc'] = $ruleDesc;
        }else{
            $data['rule'] = 0;
            $data['rule_title'] = null;
            $data['rule_desc'] = null;
        }
        //end

        $data['title_color'] = $titleColor;
        if(!empty($bgColor))
        {
            $data['bg_color'] = $bgColor;
        }
        if(!empty($description)) {
            $data['description'] = $description;
        }
        if (empty($id))
        {
            $errMsg .= 'id为空';
        }
        if (empty($title))
        {
            $errMsg .= '微页面名称为空';
        }
        else
        {
            $data['title'] = $title;
        }

        if (empty($requestData)||$requestData=='[]')
        {
            $data['template_info']=null;
        }
        else
        {
            if(is_array($requestData))
            {
                $data['template_info'] = json_encode($requestData);
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
                $data['template_info']=json_encode($validateData);

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

        //add by jonzhang 2018-01-03
        //json数据进行效验
        if(!empty($data['template_info']))
        {
            $dcvm=new DiyComponentValidatorModule();
            $checkResult=$dcvm->checkModel($data['template_info']);
            if($checkResult['errCode']==0&&!empty($checkResult['data']))
            {
                $data['template_info']=$checkResult['data'];
            }
            else
            {
                return $checkResult;
            }
        }

        //add by jonzhang 添加数据长度效验
        if (strlen($data['template_info']) > 16777215 - 1)
        {
            $returnData['errCode'] = -6;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }

        return WXXCXMicroPageService::updateData($id,$data);
    }

    /***
     * todo 删除微页面小程序信息
     * @param Request $request
     * @return array
     * @author jonzhagn
     * @date 2017-09-14
     */
    public function deleteXCXPage(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //存放要添加数据
        $id = $request->input('id');
        if (empty($id))
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
        return WXXCXMicroPageService::delete($id);
    }

    /**
     * todo 通过id查询某个小程序微页面数据
     * @param Request $request
     * @return array
     * @author jonzhagn
     * @date 2017-09-14
     * @update 何书哲 2018年10月10日 由外层移入里层，防止微页面errCode等于0,data为空的情况
     */
    public function selectOneXCX(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>[]];
        //存放要添加数据
        $id = $request->input('id');
        $option = $request->input('option');
        if (empty($id))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }
        $wid = session('wid');
        if(empty($wid))
        {
            $returnData['errCode'] = -2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        if ($option == 'create') {
            $microPageData = [];
            $templateMarketData = (new TemplateMarketService())->getRowById($id);
            if ($templateMarketData['errCode'] != 0 || empty($templateMarketData['data'])) {
                $returnData['errCode'] = -4;
                $returnData['errMsg'] = '模板不存在';
                return $returnData;
            }
            $microPageData['template_id']=$id;
            $microPageData['template_info']=$templateMarketData['data']['template_data'];
            $microPageData['is_custom']=$templateMarketData['data']['is_custom'];
            //add by zhangyh 20180612
            $microPageData['template_info'] = ProductModule::addProductContentHost($microPageData['template_info']);
            //end
            $returnData['data'] = $microPageData;
            return $returnData;
        }
        $xcxData= WXXCXMicroPageService::getRowById($id);
        if($xcxData['errCode']==0&&!empty($xcxData['data']))
        {
            if($wid!=$xcxData['data']['wid'])
            {
                $returnData['errCode']=-3;
                $returnData['errMsg']='该页面你不可以访问';
                return $returnData;
            }
            //主要是方便前端显示
            $xcxData['data']['title_color'] = $xcxData['data']['title_color'] == 'black' ? '#000000' : '#ffffff';
            $templateInfo=$xcxData['data']['template_info'];
            if(!empty($templateInfo)&&$templateInfo!='[]')
            {
                $templateInfo=MallModule::processTemplateData($wid,$templateInfo,1,1);
                $xcxData['data']['template_info']=$templateInfo;
            }
        }
        $xcxData['data']['template_info'] =  ProductModule::addProductContentHost($xcxData['data']['template_info']);
        return $xcxData;
    }

    /***
     * todo 复制小程序微页面
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function copyXCX(Request $request)
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
        $xcxData=WXXCXMicroPageService::getRowById($id);
        if($xcxData['errCode']!=0)
        {
            return $xcxData;
        }
        else if($xcxData['errCode']==0&&empty($xcxData['data']))
        {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '没有查询到符合要求的数据';
            return $returnData;
        }
        //查询出来的数据是数组，只取第一个数组数据
        $data=$xcxData['data'];
        //剔除不需要的数据
        unset($data['id']);
        //店铺主页只有一个 复制的数据不能够成为店铺主页
        $data['is_home']=0;
        return WXXCXMicroPageService::insertData($data);
    }

    /**
     * todo 把某个小程序页面设置为小程序微页面店铺主页
     * @param Request $request
     * @return array
     * @date 2017-09-14
     */
    public function updateXCXMainHome(Request $request)
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
        $result=WXXCXMicroPageService::getListByConditionWithPage(['current_status'=>0,'is_home'=>1,'wid'=>$wid]);
        if (!empty($result[0]['data']))
        {
            //过滤微页面分类数据
            foreach ($result[0]['data'] as $item)
            {
                WXXCXMicroPageService::updateData($item['id'], ['is_home' => 0]);
            }
        }
        return WXXCXMicroPageService::updateData($id,['is_home'=>1]);
    }

    /**
     * todo 查询小程序微页面数据
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-09-14
     */
    public function selectAllXCX(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>[]];
        $title= $request->input('title');
        $data=[];
        //存放要添加数据
        if(!empty($title))
        {
            $data['title']=$title;
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        $data['current_status']=0;
        $data['wid']=$wid;
        $xcxData=WXXCXMicroPageService::getListByConditionWithPage($data,'','',8);
        //查询有微页面数据时
        if(!empty($xcxData[0]['data']))
        {
            $xcxMicroPageData=[];
            foreach ($xcxData[0]['data'] as $item)
            {
                $xcxMicroPage=[];
                $xcxMicroPage['id']=$item['id'];
                //时间戳转化为时间
                $xcxMicroPage['create_time']=date("Y-m-d H:i:s",$item['create_time']);
                $xcxMicroPage['is_home']=$item['is_home'];
                $xcxMicroPage['title']=$item['title'];
                $xcxMicroPageData[]=$xcxMicroPage;
            }
            $returnData['data']=$xcxMicroPageData;
        }
        $returnData['total']=$xcxData[0]['total'];
        $returnData['currentPage']=$xcxData[0]['current_page'];
        $returnData['pageSize']=$xcxData[0]['per_page'];
        return $returnData;
    }

    /**
     * todo 获取生成小程序的二维码
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array
     * @author jonzhang
     * @date
     * @update 梅杰 20180710 增加指定小程序首页小程序码并做兼容
     */
    public function getXCXCode(Request $request, ThirdPlatform $thirdPlatform)
    {
        $path=$request->input('path')??'pages/index/index';
        $wid=session('wid');
        $xcxConfigId = $request->input('xcxConfigId',0);
        if(empty($wid))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //add by jonzhang 2018-04-25
        if(empty($path))
        {
            $path="pages/index/index";
        }
        return $thirdPlatform->getXCXQRCode($wid,430,$path,$xcxConfigId);
    }

    /**
     * todo 通过拼团id查询拼团信息
     * @param Request $request
     * @param GroupsRuleModule $groupsRuleModule
     * @author jonzhang
     */
    public function getTestBySpell(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        $id=$request->input('id')??0;
        if(empty($id))
        {
            error('id为空');
        }
        $result=$groupsRuleModule->getRuleById($id);
        dd($result);
    }

    /***
     * todo 小程序访问页面数据分析
     * @param array $data
     * @author jonzhang
     * @date 2017-10-26
     */
    public function accessPages(Request $request,ThirdPlatform $thirdPlatform)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $wid=$request->input('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $wid=$wid??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
       return  $thirdPlatform->accessPages(['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate]);
    }

    /**
     * todo 日趋势
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public function visitTrendForDaily(Request $request,ThirdPlatform $thirdPlatform)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $wid=$request->input('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $wid=$wid??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
        return  $thirdPlatform->visitTrendForDaily(['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate]);
    }

    /***
     * todo 周趋势
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public function visitTrendForWeekly(Request $request,ThirdPlatform $thirdPlatform)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $wid=$request->input('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $wid=$wid??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
        return  $thirdPlatform->visitTrendForWeekly(['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate]);
    }

    /**
     * todo 月趋势
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public function visitTrendForMonthly(Request $request,ThirdPlatform $thirdPlatform)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $wid=$request->input('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $wid=$wid??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
        return  $thirdPlatform->visitTrendForMonthly(['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate]);
    }

    /**
     * todo 访问分布
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @return array|bool|mixed|string
     * @author jonzhang
     * @date 2017-10-30
     */
    public function visitDistribution(Request $request,ThirdPlatform $thirdPlatform)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $wid=$request->input('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $wid=$wid??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='wid为空';
            return $returnData;
        }
        return  $thirdPlatform->visitDistribution(['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate]);
    }

    /**
     * todo 统计昨日概况
     * @return array
     * @author jonzhang
     * @date 2017-10-31
     */
    public function statOverviewTest(Request $request)
    {
        $returnData=[
            'errCode'=>0,'errMsg'=>'', 'data'=>[
                'pay_amount'=>['value'=>0.00,'growth'=>'-'],
                'pv'=>['value'=>0,'growth'=>'-'],
                'uv'=>['value'=>0,'growth'=>'-'],
                'pay_pv'=>['value'=>0,'growth'=>'-'],
                'pay_uv'=>['value'=>0,'growth'=>'-']]
        ];
        return $returnData;
    }

    /**
     * todo 统计流量
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-10-31
     */
    public function statFlowTest(Request $request)
    {
        $returnData=[
            'errCode'=>0,'errMsg'=>'', 'data'=>[
                //曲线统计数据
                'date_data'=>[
                    ["pv"=>0, "uv"=>0, "new_uv"=>0,"stay_time_uv"=> 0,"visit_depth"=>0, "date"=>"2017-10-24"],
                    ["pv"=>0, "uv"=>0, "new_uv"=>0,"stay_time_uv"=> 0,"visit_depth"=>0, "date"=>"2017-10-25"]
                ],//统计数据
                "total"=>[
                    "pv"=>["value"=>0, "growth"=>"-"],
                    "uv"=>["value"=>0, "growth"=>"-"],
                    "new_uv"=>["value"=>0, "growth"=>"-"],
                    "stay_time_uv"=>["value"=>0, "growth"=>"-"],
                    "visit_depth"=>["value"=>0, "growth"=>"-"]
                ], //访问来源
                "visit_distribution"=>[
                    "二维码"=>["value"=>1,"rage"=>20.00],
                    "其他"=>["value"=>1,"rage"=>20.00],
                    "小程序历史列表"=>["value"=>1,"rage"=>20.00],
                    "搜索"=>["value"=>2,"rage"=>40.00]
                ]]
        ];
        return $returnData;
    }


    /***
     * todo 昨日概况
     * @param Request $request
     * @param OrderService $orderService
     * @author jonzhang
     * @date 2017-11-03
     */
    public function statOverview(Request $request,OrderService $orderService,ThirdPlatform $thirdPlatform)
    {
        $returnData=[
            'errCode'=>0,'errMsg'=>'', 'data'=>[
                'pay_amount'=>['value'=>0.00,'growth'=>'-'],//付款金额
                'pv'=>['value'=>0,'growth'=>'-'],//浏览量
                'uv'=>['value'=>0,'growth'=>'-'],// 访客数
                'pay_order_count'=>['value'=>0,'growth'=>'-'],//付款订单数
                'pay_customer_count'=>['value'=>0,'growth'=>'-'] //付款客户数
            ]
        ];
        $wid=$request->input('wid');
        if(empty($wid))
        {
            $wid=session('wid');
        }
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        try
        {
            //取昨天的日期
            $beginDate= date("Y-m-d", strtotime("-1 day"));
            //获取前天的日期
            $beforeDate=date("Y-m-d", strtotime("-2 day"));
            $endDate=$beginDate;
            $orderData=$orderService->statXCXOrderTradeByDate($wid,$beforeDate,$endDate);
            if(!empty($orderData))
            {
                $result=[];
                foreach($orderData as $item)
                {
                    //取出前天的数据
                    if($item['stat_date']==$beforeDate)
                    {
                         $result['before']=$item;
                    }
                    //取出昨天的数据
                    else if($item['stat_date']==$beginDate)
                    {
                        $result['yesterday']=$item;
                    }
                }
             //付款金额统计
             $ya=0;
             if(!empty($result['yesterday']['pay_total_amount']))
             {
                 $ya=$returnData['data']['pay_amount']['value']=$result['yesterday']['pay_total_amount'];
             }
             if(!empty($result['before']['pay_total_amount']))
             {
                $ba=$result['before']['pay_total_amount'];
                //上升
                if($ya>$ba)
                {
                    //此处需要对$b=0做特殊处理
                    if($ba>0)
                        $returnData['data']['pay_amount']['growth']='+'.sprintf("%01.2f",($ya-$ba)/$ba*100).'%';
                    else
                        $returnData['data']['pay_amount']['growth']='+0.00%';
                }//下降
                else if($ba>$ya)
                {
                    $returnData['data']['pay_amount']['growth']='-'.sprintf("%01.2f",($ba-$ya)/$ba*100).'%';
                }
                //如果相等 那百分比没有变化
             }
            //此处为金额数据统计 end


            //此处为付款订单数数据统计 begin
            $yo=0;
            if(!empty($result['yesterday']['pay_cnt']))
            {
                $yo=$returnData['data']['pay_order_count']['value']=$result['yesterday']['pay_cnt'];
            }
            if(!empty($result['before']['pay_cnt']))
            {
                $bo=$result['before']['pay_cnt'];
                //上升
                if($yo>$bo)
                {
                    //此处需要对$b=0做特殊处理
                    if($bo>0)
                        $returnData['data']['pay_order_count']['growth']='+'.sprintf("%01.2f",($yo-$bo)/$bo*100).'%';
                    else
                        $returnData['data']['pay_order_count']['growth']='+0.00%';
                }//下降
                else if($bo>$yo)
                {
                    $returnData['data']['pay_order_count']['growth']='-'.sprintf("%01.2f",($bo-$yo)/$bo*100).'%';
                }
            }
            //此处为付款订单数数据统计 end

            //此处为付款客户数数据统计 begin
            $ym=0;
            if(!empty($result['yesterday']['pay_mid_cnt']))
            {
                $ym=$returnData['data']['pay_customer_count']['value']=$result['yesterday']['pay_mid_cnt'];
            }
            if(!empty($result['before']['pay_mid_cnt']))
            {
                $bm=$result['before']['pay_mid_cnt'];
                //上升
                if($ym>$bm)
                {
                    //此处需要对$b=0做特殊处理
                    if($bm>0)
                        $returnData['data']['pay_customer_count']['growth']='+'.sprintf("%01.2f",($ym-$bm)/$bm*100).'%';
                    else
                        $returnData['data']['pay_customer_count']['growth']='+0.00%';
                }//下降
                else if($bm>$ym)
                {
                    $returnData['data']['pay_customer_count']['growth']='-'.sprintf("%01.2f",($bm-$ym)/$bm*100).'%';
                }
            }
            //此处为付款订单数数据统计 end

            //浏览量和访客数数据统计 begin
            $yp=0;
            $yu=0;
            //利用循环取出昨天和前天的浏览量和访客数
            for($i=1;$i<3;$i++)
            {
                $date= date("Ymd", strtotime("-$i day"));
                $where = ['wid' => $wid, 'beginDate' => $date, 'endDate' => $date];
                $visitTrendData = $thirdPlatform->visitTrendForDaily($where);
                if ($visitTrendData['errCode'] == 0 && !empty($visitTrendData['data']))
                {
                    //昨天的浏览量和访客数
                    if($i==1)
                    {
                        $yp=$returnData['data']['pv']['value'] = $visitTrendData['data'][0]['visit_pv']??0;
                        $yu=$returnData['data']['uv']['value']=$visitTrendData['data'][0]['visit_uv']??0;
                    }//前二天的浏览量和访客数
                    else if($i==2)
                    {
                        $bp= $visitTrendData['data'][0]['visit_pv']??0;
                        $bu=$visitTrendData['data'][0]['visit_uv']??0;
                        //浏览量
                        if($yp>$bp)
                        {
                            //此处需要对$b=0做特殊处理
                            if($bp>0)
                                $returnData['data']['pv']['growth']='+'.sprintf("%01.2f",($yp-$bp)/$bp*100).'%';
                            else
                                $returnData['data']['pv']['growth']='+0.00%';
                        }
                        else if($bp>$yp)
                        {
                            $returnData['data']['pv']['growth']='-'.sprintf("%01.2f",($bp-$yp)/$bp*100).'%';
                        }
                        //访客数
                        if($yu>$bu)
                        {
                            //此处需要对$b=0做特殊处理
                            if($bu>0)
                                $returnData['data']['uv']['growth']='+'.sprintf("%01.2f",($yu-$bu)/$bu*100).'%';
                            else
                                $returnData['data']['uv']['growth']='+0.00%';
                        }
                        else if($bu>$yu)
                        {
                            $returnData['data']['uv']['growth']='-'.sprintf("%01.2f",($bu-$yu)/$bu*100).'%';
                        }
                    }
                }
                else
                {
                    BLogger::getLogger('error')->error('wid:'.$wid.'date:'.$date.'visitTrendForDaily:'.json_encode($visitTrendData));
                }
            }
            //浏览量和访客数数据统计 end
            }
        }
        catch(\Exception $e)
        {
            BLogger::getLogger('error')->error('昨日概要抛出异常：'.$e->getMessage());
        }
        return $returnData;
    }

    /***
     * todo 流量统计
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @param XCXModule $xcxModule
     * @return array
     * @author jonzhang
     * @date2017-11-07
     */
    public function statFlow(Request $request,ThirdPlatform $thirdPlatform,XCXModule $xcxModule)
    {
        $returnData=[
            'errCode'=>0,'errMsg'=>'', 'data'=>[
                //曲线统计数据
                'date_data'=>[
                ],//统计数据
                "total"=>[
                    "pv"=>["value"=>0, "growth"=>"-"],//浏览量
                    "uv"=>["value"=>0, "growth"=>"-"],//访客数
                    "new_uv"=>["value"=>0, "growth"=>"-"],//新访客数
                    "stay_time_uv"=>["value"=>0, "growth"=>"-"],//人均停留时长
                    "visit_depth"=>["value"=>0, "growth"=>"-"]//平均访问深度
                ], //访问来源
                "visit_distribution"=>[
                ]
            ]
        ];
        //$wid = $request->input('wid');
        $wid = session('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $type = $request->input('type')??1;
        $type = intval($type)??1;
        $errorDate=$beginDate;
        try
        {

            if (empty($wid))
            {
                $wid = session('wid');
            }
            if (empty($wid))
            {
                $returnData['errCode'] = -1;
                $returnData['errMsg'] = '登录超时';
                return $returnData;
            }
            if (empty($beginDate)||empty($endDate)) {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '日期为空';
                return $returnData;
            }
            $scopeData=[1,2,3];
            //type必须为 1,2,3
            if(!in_array($type,$scopeData))
            {
                $returnData['errCode'] = -3;
                $returnData['errMsg'] = 'type不在范围内';
                return $returnData;
            }//天 必须相等
            if($type==1&&$beginDate!=$endDate)
            {
                $returnData['errCode'] = -4;
                $returnData['errMsg'] = '日期必须相等';
                return $returnData;
            }//周 必须是7天
            else if($type==2)
            {
                $compareDate=date("Y-m-d", strtotime("+6 day", strtotime($beginDate)));
                if(strtotime($compareDate)!=strtotime($endDate))
                {
                    $returnData['errCode'] = -5;
                    $returnData['errMsg'] = 'beginDate与endDate不匹配';
                    return $returnData;
                }
            }//月 必须是月初和月尾
            else if($type==3)
            {
                $num=date("t",strtotime($beginDate));
                $num=$num-1;
                $compareDate=date("Y-m-d", strtotime("+$num day", strtotime($beginDate)));
                if(strtotime($compareDate)!=strtotime($endDate))
                {
                    $returnData['errCode'] = -6;
                    $returnData['errMsg'] = 'beginDate与endDate不匹配';
                    return $returnData;
                }
            }
            $where=['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate,'type'=>$type];

            //流量统计
            $visitTrendData=$xcxModule->visitTrend($where);
            if($visitTrendData['errCode']==0)
            {
                $returnData['data']['total']= $visitTrendData['data']['total'];
            }
            else
            {
                BLogger::getLogger('error')->error('流量统计微信接口:'.json_encode($visitTrendData));
            }
            $cnt=1;
            //天趋势
            if ($type==1)
            {
                //日期格式处理
                $beginDate=date("Ymd",strtotime($beginDate));
                for($i=6;$i>=0;$i--)
                {
                    $baseDate = date("Ymd", strtotime("-$i day", strtotime($beginDate)));
                    $query=['wid'=>$wid,'beginDate'=>$baseDate,'endDate'=>$baseDate];
                    //$queryData=$thirdPlatform->visitTrendForDaily($query);
                    $queryData = $xcxModule->getVisitData($wid, $baseDate, $baseDate);
                    $baseDate=date("Y-m-d",strtotime($baseDate));
                    if($queryData['errCode']==0&&!empty($queryData['data']))
                    {
                        $returnData['data']['date_data'][]=[
                        "pv"=>$queryData['data'][0]['visit_pv']??0,
                        "uv"=>$queryData['data'][0]['visit_uv']??0,
                        "new_uv"=>$queryData['data'][0]['visit_uv_new']??0,
                        "stay_time_uv"=>$queryData['data'][0]['stay_time_uv']??0,
                        "visit_depth"=>$queryData['data'][0]['visit_depth']??0,
                        "date" => $baseDate
                        ];
                    }
                    else
                    {
                        $returnData['data']['date_data'][] = ["pv" => 0, "uv" => 0, "new_uv" => 0, "stay_time_uv" => 0, "visit_depth" => 0, "date" => $baseDate];
                    }
                }
            }//周趋势
            else if($type==2)
            {
                $cnt=7;
                //日期转化成微信接口所需要的数据格式
                $beginDate=date("Ymd",strtotime($beginDate));
                for($i=0;$i<7;$i++)
                {
                    $baseDate = date("Ymd", strtotime("+$i day", strtotime($beginDate)));
                    $query=['wid'=>$wid,'beginDate'=>$baseDate,'endDate'=>$baseDate];
                    //$queryData=$thirdPlatform->visitTrendForDaily($query);
                    $queryData = $xcxModule->getVisitData($wid, $baseDate, $baseDate);
                    //日期转化成前端所需要的数据格式
                    $baseDate=date("Y-m-d",strtotime($baseDate));
                    if($queryData['errCode']==0&&!empty($queryData['data']))
                    {
                        $returnData['data']['date_data'][]=[
                            "pv"=>$queryData['data'][0]['visit_pv'],
                            "uv"=>$queryData['data'][0]['visit_uv'],
                            "new_uv"=>$queryData['data'][0]['visit_uv_new'],
                            "stay_time_uv"=>$queryData['data'][0]['stay_time_uv'],
                            "visit_depth"=>$queryData['data'][0]['visit_depth'],
                            "date" => $baseDate
                        ];
                    }
                    else
                    {
                        $returnData['data']['date_data'][] = ["pv" => 0, "uv" => 0, "new_uv" => 0, "stay_time_uv" => 0, "visit_depth" => 0, "date" => $baseDate];
                    }
                }
            }//月趋势
            else if ($type == 3)
            {
                //取当前月的上一个月
                //$beginDate=date("Ymd",strtotime("-1 month",strtotime($beginDate)));
                //获取月有多少天数
                $num=date("t",strtotime($beginDate));
                $cnt=$num;
                for($i=0;$i<$num;$i++)
                {
                    $baseDate = date("Ymd", strtotime("+$i day", strtotime($beginDate)));
                    $query=['wid'=>$wid,'beginDate'=>$baseDate,'endDate'=>$baseDate];
                    //$queryData=$thirdPlatform->visitTrendForDaily($query);
                    $queryData = $xcxModule->getVisitData($wid, $baseDate, $baseDate);
                    $baseDate=date("Y-m-d",strtotime($baseDate));
                    if($queryData['errCode']==0&&!empty($queryData['data']))
                    {
                        $returnData['data']['date_data'][]=[
                            "pv"=>$queryData['data'][0]['visit_pv'],
                            "uv"=>$queryData['data'][0]['visit_uv'],
                            "new_uv"=>$queryData['data'][0]['visit_uv_new'],
                            "stay_time_uv"=>$queryData['data'][0]['stay_time_uv'],
                            "visit_depth"=>$queryData['data'][0]['visit_depth'],
                            "date" => $baseDate
                        ];
                    }
                    else
                    {
                        $returnData['data']['date_data'][] = ["pv" => 0, "uv" => 0, "new_uv" => 0, "stay_time_uv" => 0, "visit_depth" => 0, "date" => $baseDate];
                    }
                }
            }

            //访问来源
            unset($where['type']);
            //定义变量
            $history=0;
            $search=0;
            $session=0;
            $code=0;
            $homepage=0;
            $payoff=0;
            $total=0;
            $other=0;

            for($n=0;$n<$cnt;$n++)
            {
                //日期转化为微信接口所需要的数据格式
                $where['beginDate'] = date("Ymd", strtotime("+$n day", strtotime($beginDate)));
                $where['endDate'] = $where['beginDate'];
                $visitDistributionData = $thirdPlatform->visitDistribution($where);
                if ($visitDistributionData['errCode'] == 0 && !empty($visitDistributionData['data']))
                {
                    $sourceData = [];
                    foreach ($visitDistributionData['data'] as $item)
                    {
                        //访问来源分布
                        if ($item['index'] == "access_source_session_cnt")
                        {
                            $sourceData = $item['item_list'];
                            break;
                        }
                    }
                    if (!empty($sourceData))
                    {
                        //查询数据
                        foreach ($sourceData as $source) {
                            //小程序历史列表
                            if ($source['key'] == 1 && $source['value'] > 0) {
                                $history =$history+ $source['value'];
                            }//搜索
                            else if ($source['key'] == 2 && $source['value'] > 0) {
                                $search =$search+ $source['value'];
                            }//会话
                            else if ($source['key'] == 3 && $source['value'] > 0) {
                                $session = $session+$source['value'];
                            }//二维码
                            else if ($source['key'] == 4 && $source['value'] > 0) {
                                $code =$code+ $source['value'];
                            }//小程序主页
                            else if ($source['key'] == 8 && $source['value'] > 0) {
                                $homepage =$homepage+ $source['value'];
                            }//支付完成页
                            else if ($source['key'] == 15 && $source['value'] > 0) {
                                $payoff =$payoff+ $source['value'];
                            }//其他
                            else {
                                $other =$other+ $source['value'];
                            }
                            $total = $total + $source['value'];
                        }
                    }
                }
                else
                {
                    BLogger::getLogger('error')->error('statFlow visitDistribution:' . json_encode($visitDistributionData));
                }
            }
            //来源分布统计
            //小程序历史列表
            if (!empty($history) && !empty($total)) {
                $returnData['data']['visit_distribution']['小程序历史列表'] = [
                    'value' => $history, 'rate' => sprintf("%01.2f", $history / $total * 100) . '%'
                ];
            }//搜索
            if (!empty($search) && !empty($total)) {
                $returnData['data']['visit_distribution']['搜索'] = [
                    'value' => $search, 'rate' => sprintf("%01.2f", $search / $total * 100) . '%'
                ];
            }//会话
            if (!empty($session) && !empty($total)) {
                $returnData['data']['visit_distribution']['会话'] = [
                    'value' => $session, 'rate' => sprintf("%01.2f", $session / $total * 100) . '%'
                ];
            }//二维码
            if (!empty($code) && !empty($total)) {
                $returnData['data']['visit_distribution']['二维码'] = [
                    'value' => $code, 'rate' => sprintf("%01.2f", $code / $total * 100) . '%'
                ];
            }//小程序主页
            if (!empty($homepage) && !empty($total)) {
                $returnData['data']['visit_distribution']['小程序主页'] = [
                    'value' => $homepage, 'rate' => sprintf("%01.2f", $homepage / $total * 100) . '%'
                ];
            }//支付完成页
            if (!empty($payoff) && !empty($total)) {
                $returnData['data']['visit_distribution']['支付完成页'] = [
                    'value' => $payoff, 'rate' => sprintf("%01.2f", $payoff / $total * 100) . '%'
                ];
            }//其他
            if (!empty($other) && !empty($total)) {
                $returnData['data']['visit_distribution']['其他'] = [
                    'value' => $other, 'rate' => sprintf("%01.2f", $other / $total * 100) . '%'
                ];
            }
        }
        catch(\Exception $ex)
        {
            BLogger::getLogger('error')->error('访问来源抛出异常:'.$ex->getMessage());
            //出现异常时，拼凑流量统计所需要的数据
            $beginDate=date("Ymd",strtotime($errorDate));
            $returnData['data']['date_data']=[];
            if($type==1)
            {
                for ($i=6;$i>=0;$i--)
                {
                    $basicDate = date("Y-m-d", strtotime("-$i day", strtotime($beginDate)));
                    $returnData['data']['date_data'][] = ["pv" => 0, "uv" => 0, "new_uv" => 0, "stay_time_uv" => 0, "visit_depth" => 0, "date" => $basicDate];
                }
            }
            else if($type==2)
            {
                for ($i=0;$i<7;$i++)
                {
                    $basicDate = date("Y-m-d", strtotime("+$i day", strtotime($beginDate)));
                    $returnData['data']['date_data'][] = ["pv" => 0, "uv" => 0, "new_uv" => 0, "stay_time_uv" => 0, "visit_depth" => 0, "date" => $basicDate];
                }
            }
            else if($type==3)
            {
                $num=date("t",strtotime($beginDate));
                for ($i=0;$i<$num;$i++)
                {
                    $basicDate = date("Y-m-d", strtotime("+$i day", strtotime($beginDate)));
                    $returnData['data']['date_data'][] = ["pv" => 0, "uv" => 0, "new_uv" => 0, "stay_time_uv" => 0, "visit_depth" => 0, "date" => $basicDate];
                }
            }
        }
        return $returnData;
    }

    /**
     * todo 交易统计
     * @param Request $request
     * @param ThirdPlatform $thirdPlatform
     * @param OrderService $orderService
     * @return array
     * @author jonzhang
     * @date 2017-11-09
     */
    public function statTrade(Request $request,ThirdPlatform $thirdPlatform,OrderService $orderService)
    {
        $returnData=[
            "errCode"=>0,"errMsg"=>"","data"=>[
             "overview"=>[
                "uv"=>["value"=>0,"growth"=>"-"],//访客数
                "order_count"=>["value"=>0,"growth"=>"-"],//订单笔数
                "pay_count"=>["value"=>0,"growth"=>"-"],//付款笔数
                "pay_amount"=>["value"=>0,"growth"=>"-"], //付款金额
                "order_amount"=>["value"=>0,"growth"=>"-"],//下单金额
                "pay_mid_count"=>["value"=>0,"growth"=>"-"],//付款人数
                "order_mid_count"=>["value"=>0,"growth"=>"-"],//下单人数
                "avg_price"=>["value"=>0,"growth"=>"-"]//客单价
                ],
             "pay_trends"=>[]//
            ]
        ];
        $wid = $request->input('wid');
        $beginDate=$request->input('beginDate');
        $endDate=$request->input('endDate');
        $type = $request->input('type')??1;
        $type = intval($type)??1;
        $errorDate=$beginDate;
        try
        {
            if (empty($wid))
            {
                $wid = session('wid');
            }
            if (empty($wid))
            {
                $returnData['errCode'] = -1;
                $returnData['errMsg'] = '登录超时';
                return $returnData;
            }
            if (empty($beginDate)||empty($endDate)) {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '日期为空';
                return $returnData;
            }

            $scopeData=[1,2,3];
            //type必须为 1,2,3
            if(!in_array($type,$scopeData))
            {
                $returnData['errCode'] = -3;
                $returnData['errMsg'] = 'type不在范围内';
                return $returnData;
            }//天 必须相等
            if($type==1&&$beginDate!=$endDate)
            {
                $returnData['errCode'] = -4;
                $returnData['errMsg'] = '日期必须相等';
                return $returnData;
            }//周 必须是7天
            else if($type==2)
            {
                $compareDate=date("Y-m-d", strtotime("+6 day", strtotime($beginDate)));
                if(strtotime($compareDate)!=strtotime($endDate))
                {
                    $returnData['errCode'] = -5;
                    $returnData['errMsg'] = 'beginDate与endDate不匹配';
                    return $returnData;
                }
            }//月 必须是月初和月尾
            else if($type==3)
            {
                $num=date("t",strtotime($beginDate));
                $num=$num-1;
                $compareDate=date("Y-m-d", strtotime("+$num day", strtotime($beginDate)));
                if(strtotime($compareDate)!=strtotime($endDate))
                {
                    $returnData['errCode'] = -6;
                    $returnData['errMsg'] = 'beginDate与endDate不匹配';
                    return $returnData;
                }
            }

            $where=['wid'=>$wid,'beginDate'=>$beginDate,'endDate'=>$endDate];
            $yoc=0;
            $ypc=0;
            $ypmc=0;
            $yomc=0;
            $ypa=0;
            $yoa=0;
            $yap=0;
            if($type==1)
            {
                $yesterdayUV=0;
                $beforeUV=0;
                //前一天数据
                $visitTrendData=$thirdPlatform->visitTrendForDaily($where);
                if($visitTrendData['errCode']==0&&!empty($visitTrendData['data']))
                {
                    $yesterdayUV=$visitTrendData['data'][0]['visit_uv']??0;
                }
                else
                {
                    BLogger::getLogger('error')->error('statTrade type==1 condition:'.json_encode($where).' visitTrendForDaily:'.json_encode($visitTrendData));
                }
                //前两天数据
                $where['beginDate']=date("Ymd", strtotime("-1 day", strtotime($beginDate)));
                $where['endDate']=$where['beginDate'];
                $beforeVisitTrendData=$thirdPlatform->visitTrendForDaily($where);
                if($beforeVisitTrendData['errCode']==0&&!empty($beforeVisitTrendData['data']))
                {
                    $beforeUV=$beforeVisitTrendData['data'][0]['visit_uv']??0;
                }
                else
                {
                    BLogger::getLogger('error')->error('statTrade type==1 before condition:'.json_encode($where).' visitTrendForDaily:'.json_encode($beforeVisitTrendData));
                }

                //访客量
                if($yesterdayUV>$beforeUV)
                {
                    //此处需要对$b=0做特殊处理
                    if($beforeUV>0)
                        $returnData['data']['overview']['uv']['growth']='+'.sprintf("%01.2f",($yesterdayUV-$beforeUV)/$beforeUV*100).'%';
                    else
                        $returnData['data']['overview']['uv']['growth']='+0.00%';
                }
                else if($yesterdayUV<$beforeUV)
                {
                    $returnData['data']['overview']['uv']['growth']='-'.sprintf("%01.2f",($beforeUV-$yesterdayUV)/$beforeUV*100).'%';
                }
                $result=['before'=>[],'yesterday'=>[]];
                $dayLastDate=date("Y-m-d", strtotime("-1 day", strtotime($beginDate)));
                $orderTradeData1 = $orderService->statXCXOrderTradeByDate($wid, $dayLastDate, $endDate);
                if(!empty($orderTradeData1))
                {
                    $beforeDate=date("Y-m-d", strtotime("-1 day", strtotime($beginDate)));
                    foreach($orderTradeData1 as $item)
                    {
                        if($item['stat_date']==$beforeDate)
                        {
                            $result['before']=$item;
                        }
                        else if($item['stat_date']==$beginDate)
                        {
                            $result['yesterday']=$item;
                        }
                    }
                }

            }
            else if($type==2)
            {
                $beforeBeginDate=date("Y-m-d", strtotime("-7 day", strtotime($beginDate)));
                $beforeEndDate=date("Y-m-d", strtotime("-7 day", strtotime($endDate)));
                $result['before']=$orderService->statXCXOrderTradeByDate($wid,$beforeBeginDate,$beforeEndDate);
                $result['yesterday']=$orderService->statXCXOrderTradeByDate($wid,$beginDate,$endDate);

                $yesterdayUV=0;
                $beforeUV=0;
                //前一天数据
                $visitTrendData=$thirdPlatform->visitTrendForWeekly($where);
                if($visitTrendData['errCode']==0&&!empty($visitTrendData['data']))
                {
                    $yesterdayUV=$visitTrendData['data'][0]['visit_uv']??0;
                }
                else
                {
                    BLogger::getLogger('error')->error('statTrade type==2 condition:'.json_encode($where).'visitTrendForWeekly:'.json_encode($visitTrendData));
                }
                //前两天数据
                $where['beginDate']=$beforeBeginDate;
                $where['endDate']=$beforeEndDate;
                $beforeVisitTrendData=$thirdPlatform->visitTrendForWeekly($where);
                if($beforeVisitTrendData['errCode']==0&&!empty($beforeVisitTrendData['data']))
                {
                    $beforeUV=$beforeVisitTrendData['data'][0]['visit_uv']??0;
                }
                else
                {
                    BLogger::getLogger('error')->error('statTrade type==2 before condition:'.json_encode($where).'visitTrendForWeekly:'.json_encode($beforeVisitTrendData));
                }
                //访客量
                if($yesterdayUV>$beforeUV)
                {
                    if($beforeUV>0)
                        $returnData['data']['overview']['uv']['growth']='+'.sprintf("%01.2f",($yesterdayUV-$beforeUV)/$beforeUV*100).'%';
                    else
                        $returnData['data']['overview']['uv']['growth']='+0.00%';
                }
                else if($yesterdayUV<$beforeUV)
                {
                    $returnData['data']['overview']['uv']['growth']='-'.sprintf("%01.2f",($beforeUV-$yesterdayUV)/$beforeUV*100).'%';
                }
            }
            else if($type==3)
            {
                $yesterdayUV=0;
                $beforeUV=0;
                //前一天数据
                $visitTrendData=$thirdPlatform->visitTrendForMonthly($where);
                if($visitTrendData['errCode']==0&&!empty($visitTrendData['data']))
                {
                    $yesterdayUV=$visitTrendData['data'][0]['visit_uv']??0;
                }
                else
                {
                    BLogger::getLogger('error')->error('statTrade type==3 condition:'.json_encode($where).'visitTrendForMonthly:'.json_encode($visitTrendData));
                }
                //取当前月的上一个月
                $lastBeginDate=date("Ymd",strtotime("-1 month",strtotime($beginDate)));
                $lastMonth=date("Ym",strtotime($lastBeginDate));
                //获取月有多少天数
                $num=date("t",strtotime($lastBeginDate));
                $lastEndDate=$lastMonth.$num;
                $where['beginDate']=$lastBeginDate;
                $where['endDate']=$lastEndDate;
                $beforeVisitTrendData=$thirdPlatform->visitTrendForMonthly($where);
                if($beforeVisitTrendData['errCode']==0&&!empty($beforeVisitTrendData['data']))
                {
                    $beforeUV=$beforeVisitTrendData['data'][0]['visit_uv']??0;
                }
                else
                {
                    BLogger::getLogger('error')->error('statTrade type==3 before condition:'.json_encode($where).'visitTrendForMonthly:'.json_encode($beforeVisitTrendData));
                }
                //访客量
                if($yesterdayUV>$beforeUV)
                {
                    if($beforeUV>0)
                        $returnData['data']['overview']['uv']['growth']='+'.sprintf("%01.2f",($yesterdayUV-$beforeUV)/$beforeUV*100).'%';
                    else
                        $returnData['data']['overview']['uv']['growth']='+0.00%';
                }
                else if($yesterdayUV<$beforeUV)
                {
                    $returnData['data']['overview']['uv']['growth']='-'.sprintf("%01.2f",($beforeUV-$yesterdayUV)/$beforeUV*100).'%';
                }
                $result = $orderService->statXCXOrderTradeForMonthly($wid, $beginDate, $endDate);
            }

            if(!empty($result['yesterday']))
                {
                    $dailyItem=$result['yesterday'];
                    $yoc=$returnData['data']['overview']['order_count']['value']=$dailyItem['cnt']??0;//订单笔数
                    $ypc=$returnData['data']['overview']['pay_count']['value']=$dailyItem['pay_cnt']??0;//支付笔数
                    $ypmc=$returnData['data']['overview']['pay_mid_count']['value']=$dailyItem['pay_mid_cnt']??0;//付款人数
                    $yomc=$returnData['data']['overview']['order_mid_count']['value']=$dailyItem['mid_cnt']??0;//下单人数
                    $ypa=$returnData['data']['overview']['pay_amount']['value']=$dailyItem['pay_total_amount']??0;//付款金额
                    $yoa=$returnData['data']['overview']['order_amount']['value']=$dailyItem['total_amount']??0;//下单金额
                    $yap=$returnData['data']['overview']['avg_price']['value']=$dailyItem['avg_price']??0;//客单价
                }
            if(!empty($result['before']))
                {
                    $dailyItem=$result['before'];

                    //订单笔数
                    $boc=$dailyItem['cnt']??0;
                    if($yoc>$boc)
                    {
                        if($boc>0)
                            $returnData['data']['overview']['order_count']['growth']='+'.sprintf("%01.2f",($yoc-$boc/$boc*100)).'%';
                        else
                            $returnData['data']['overview']['order_count']['growth']='+0.00%';
                    }
                    else if($yoc<$boc)
                    {
                        $returnData['data']['overview']['order_count']['growth']='-'.sprintf("%01.2f",($boc-$yoc/$boc*100)).'%';
                    }

                    //付款笔数
                    $bpc=$dailyItem['pay_cnt']??0;
                    if($ypc>$bpc)
                    {
                        if($bpc>0)
                            $returnData['data']['overview']['pay_count']['growth']='+'.sprintf("%01.2f",($ypc-$bpc/$bpc*100)).'%';
                        else
                            $returnData['data']['overview']['pay_count']['growth']='+0.00%';
                    }
                    else if($ypc<$bpc)
                    {
                        $returnData['data']['overview']['pay_count']['growth']='-'.sprintf("%01.2f",($bpc-$ypc/$bpc*100)).'%';
                    }

                    //付款人数
                    $bpmc=$dailyItem['pay_mid_cnt']??0;
                    if($ypmc>$bpmc)
                    {
                        if($bpmc>0)
                            $returnData['data']['overview']['pay_mid_count']['growth']='+'.sprintf("%01.2f",($ypmc-$bpmc/$bpmc*100)).'%';
                        else
                            $returnData['data']['overview']['pay_mid_count']['growth']='+0.00%';
                    }
                    else if($ypmc<$bpmc)
                    {
                        $returnData['data']['overview']['pay_mid_count']['growth']='-'.sprintf("%01.2f",($bpmc-$ypmc/$bpmc*100)).'%';
                    }

                    //下单人数
                    $bomc=$dailyItem['mid_cnt']??0;
                    if($yomc>$bomc)
                    {
                        if($bomc>0)
                            $returnData['data']['overview']['order_mid_count']['growth']='+'.sprintf("%01.2f",($yomc-$bomc/$bomc*100)).'%';
                        else
                            $returnData['data']['overview']['order_mid_count']['growth']='+0.00%';
                    }
                    else if($yomc<$bomc)
                    {
                        $returnData['data']['overview']['order_mid_count']['growth']='-'.sprintf("%01.2f",($bomc-$yomc/$bomc*100)).'%';
                    }

                    //付款金额
                    $bpa=$dailyItem['pay_total_amount']??0;
                    if($ypa>$bpa)
                    {
                        if($bpa>0)
                            $returnData['data']['overview']['pay_amount']['growth']='+'.sprintf("%01.2f",($ypa-$bpa/$bpa*100)).'%';
                        else
                            $returnData['data']['overview']['pay_amount']['growth']='+0.00%';
                    }
                    else if($ypa<$bpa)
                    {
                        $returnData['data']['overview']['pay_amount']['growth']='-'.sprintf("%01.2f",($bpa-$ypa/$bpa*100)).'%';
                    }

                    //下单金额
                    $boa=$dailyItem['total_amount']??0;
                    if($yoa>$boa)
                    {
                        if($boa>0)
                            $returnData['data']['overview']['order_amount']['growth']='+'.sprintf("%01.2f",($yoa-$boa/$boa*100)).'%';
                        else
                            $returnData['data']['overview']['order_amount']['growth']='+0.00%';
                    }
                    else if($yoa<$boa)
                    {
                        $returnData['data']['overview']['order_amount']['growth']='-'.sprintf("%01.2f",($boa-$yoa/$boa*100)).'%';
                    }

                    //客单价
                    $bap=$dailyItem['avg_price']??0;
                    if($yap>$bap)
                    {
                        if($bap>0)
                            $returnData['data']['overview']['avg_price']['growth']='+'.sprintf("%01.2f",($yap-$bap/$bap*100)).'%';
                        else
                            $returnData['data']['overview']['avg_price']['growth']='+0.00%';
                    }
                    else if($yap<$bap)
                    {
                        $returnData['data']['overview']['avg_price']['growth']='-'.sprintf("%01.2f",($bap-$yap/$bap*100)).'%';
                    }

                }

            //交易统计曲线图所需要的数据
            $n=7;
            if($type==1)
            {
                $beginDate = date("Y-m-d", strtotime("-6 day", strtotime($beginDate)));
            }
            else if($type==3)
            {
                $n=date("t",strtotime($beginDate));
            }
            $orderStatData=$orderService->statXCXOrderTradeByDate($wid,$beginDate,$endDate);
            for($i=0;$i<$n;$i++)
            {
                $scopeDate=date("Y-m-d", strtotime("+$i day", strtotime($beginDate)));
                BLogger::getLogger('info')->info('trade:'.$scopeDate);
                $uvCnt=0;
                $select=['wid'=>$wid,
                    'beginDate'=>date("Ymd", strtotime("+$i day", strtotime($beginDate))),
                    'endDate'=>date("Ymd", strtotime("+$i day", strtotime($beginDate)))
                ];
                $vtfData=$thirdPlatform->visitTrendForDaily($select);
                if($vtfData['errCode']==0&&!empty($vtfData['data']))
                {
                    $uvCnt=$vtfData['data'][0]['visit_uv']??0;
                }
                if(!empty($orderStatData))
                {
                    $isExist=0;
                    foreach ($orderStatData as $item)
                    {
                        if (!empty($item['stat_date']) && $item['stat_date'] == $scopeDate)
                        {
                            $isExist=1;
                            $totalRate="0.00";
                            if(!empty($uvCnt)&&!empty($item['pay_mid_cnt']))
                            {
                                $totalRate=strval(sprintf("%01.2f",$item['pay_mid_cnt']/$uvCnt));
                            }
                            $returnData['data']['pay_trends'][] = [
                                'pay_cnt' => $item['pay_cnt'], 'pay_mid_cnt' => $item['pay_mid_cnt'],
                                'pay_total_amount' => $item['pay_total_amount'], 'avg_price' => $item['avg_price'],
                                'pay_rate' => $item['pay_rate'], 'order_rate' => $item['order_rate'],
                                'total_rate'=>$totalRate,'date' => $scopeDate
                            ];
                            break;
                        }
                    }
                    if(!$isExist)
                    {
                        $returnData['data']['pay_trends'][] = [
                            'pay_cnt' => 0, 'pay_mid_cnt' => 0, 'pay_total_amount' => "0.00",
                            'avg_price' => "0.00", 'pay_rate' => "0.00",
                            'order_rate' => "0.00",'total_rate'=>"0.00", 'date' => $scopeDate
                        ];
                    }
                }
                else
                {
                    $returnData['data']['pay_trends'][] = [
                        'pay_cnt' => 0, 'pay_mid_cnt' => 0, 'pay_total_amount' => "0.00",
                        'avg_price' => "0.00", 'pay_rate' => "0.00",
                        'order_rate' => "0.00", 'total_rate'=>"0.00",'date' => $scopeDate
                    ];
                }
            }
        }
        catch(\Exception $e)
        {
            BLogger::getLogger('error')->error('交易统计抛出异常：'.$e->getMessage());
            $n=7;
            if($type==1)
            {
                $errorDate = date("Y-m-d", strtotime("-6 day", strtotime($errorDate)));
            }
            else if($type==3)
            {
                $n=date("t",strtotime($errorDate));
            }
            for($i=0;$i<$n;$i++)
            {
                $scopeDate = date("Y-m-d", strtotime("+$i day", strtotime($errorDate)));
                $returnData['data']['pay_trends'][] = [
                    'pay_cnt' => 0, 'pay_mid_cnt' => 0, 'pay_total_amount' => "0.00",
                    'avg_price' => "0.00", 'pay_rate' => "0.00",
                    'order_rate' => "0.00",'total_rate'=>"0.00", 'date' => $scopeDate
                ];
            }
        }
        return $returnData;
    }

    /**
     * 批量删除小程序微页面
     * @param Request $request 请求参数
     * @return array
     * @author 何书哲 2019年01月03日
     */
    public function batchDeleteXCXPage(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $ids = $request->input('ids');
        if (empty($ids))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '参数为空';
            return $returnData;
        }

        $homePageId = WXXCXMicroPage::where(['wid'=>session('wid'), 'is_home'=>1])->value('id');
        if (!empty($homePageId) && in_array($homePageId, $ids)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '店铺主页不能删除';
            return $returnData;
        }

        DB::beginTransaction();
        foreach ($ids as $id) {
            $result = WXXCXMicroPageService::delete($id);
            if ($result['errCode']) {
                DB::rollBack();
                $returnData['errCode'] = -3;
                $returnData['errMsg'] = '删除失败';
                return $returnData;
            }
        }
        DB::commit();
        return $returnData;
    }

    /**
     * @desc 获取直播房间列表
     * @param Request $request
     * @param LiveRoomModule $module
     * @return array
     * @author 焦建荣【945184949@qq.com】2020年03月06日
     */
    public function liveRoom(Request $request, LiveRoomModule $module)
    {
        $mId = session('wid');

        if (empty($mId)) {
            $returnData['code'] = -1;
            $returnData['hint'] = 'session过期';
            return $returnData;
        }

        $flush = $request->input('flush', 0);
        $page = $request->input('page', 1);

        return $module->liveRoom($mId, $flush, $page);
    }

}