<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Module\AliApp\VersionManageModule;
use Illuminate\Http\Request;
use App\S\AliApp\AliappConfigService;
use WeixinService;
use App\S\Weixin\ShopService;

/**
 * 第三方代商家小程序部署版本
 * @author 许立 2018年07月26日
 */
class AliappController extends Controller
{
    /**
     * todo 支付宝小程序配置列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张国军2018-07-30
     */
    public function configList()
    {
        return view('staff.alixcx.list',array(
            'title'     => '支付宝小程序',
            'sliderbar'  => 'slidebar'
        ));
    }

    /***
     * todo 查询所有的支付宝小程序配置信息
     * @return mixed
     * @author 张国军
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function selectAll(ShopService $shopService)
    {
        $aliAppConfigService=new AliappConfigService();
        $aliConfigData=$aliAppConfigService->getListWithPage();
        if($aliConfigData[0]['data'])
        {
            foreach($aliConfigData[0]['data'] as &$item)
            {
                $item['statusName']="-";
                switch ($item['status'])
                {
                    case 0:
                        $item['statusName'] = "授权成功";
                        break;
                    case 1:
                        $item['statusName'] = "基于模板上传";
                        break;
                    case 2:
                        $item['statusName'] = "提交审核";
                        break;
                    case 3:
                        $item['statusName'] = "审核通过";
                        break;
                    case 4:
                        $item['statusName'] = "灰度上架";
                        break;
                    case 5:
                        $item['statusName'] = "结束灰度";
                        break;
                    case 6:
                        $item['statusName'] = "上架";
                        break;
                    case 7:
                        $item['statusName'] = "下架";
                        break;
                    case 8:
                        $item['statusName'] = "设置白名单";
                        break;
                }
                $item['widName']="-";
                /*$widData=WeixinService::getStore($item['wid']);
                if($widData['errCode']==0&&!empty($widData['data']))
                {
                    $item['widName']=$widData['data']['shop_name'];
                }*/
                $shopData = $shopService->getRowById($item['wid']);
                if (!empty($shopData)) {
                    $item['widName'] = $shopData['shop_name'];
                }
            }
        }
        return $aliConfigData;
    }
    /**
     * 小程序基于模板上传版本
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月26日
     * @update 许立 2018年07月30日 添加操作者信息
     */
    public function versionUpload(Request $request)
    {
        $input = $request->input();
        if (empty($input['configId']) || empty($input['appVersion']) || empty($input['templateId'])) {
            error('参数不完整');
        }
        $operatorId = session('userData')['id']??0;
        $operator = session('userData')['login_name']??'';
        $result = (new VersionManageModule())->versionUpload($input['configId'], $input['appVersion'], $input['templateId'], $operatorId, $operator, $input['templateVersion'] ?? '');
        if ($result['errCode']) {
            error($result['errMsg']);
        } else {
            success();
        }
    }

    /**
     * 小程序提交审核
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月27日
     * @update 许立 2018年07月30日 添加操作者信息
     */
    public function versionAudit(Request $request)
    {
        $input = $request->input();
        if (empty($input['configId']) || empty($input['appVersion']) || empty($input['versionDesc']) || empty($input['licenseValidDate'])) {
            error('参数不完整');
        }
        $operatorId = session('userData')['id']??0;
        $operator = session('userData')['login_name']??'';
        $result = (new VersionManageModule())->versionAudit($input['configId'], $input, $operatorId, $operator);
        if ($result['errCode']) {
            error($result['errMsg']);
        } else {
            success();
        }
    }

    /**
     * 小程序详情
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月31日
     */
    public function detail($id)
    {
        if (empty($id) ) {
            error('参数不完整');
        }
        $result = (new VersionManageModule())->detail($id);
        if ($result['errCode']) {
            error($result['errMsg']);
        } else {
            success('', '', $result['data']);
        }
    }

    /***
     * todo 添加体验者
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return array
     * @author 张
     */
    public function createMembers(Request $request,VersionManageModule $versionManageModule)
    {
        $loginId=$request->input('logonId');
        $id=$request->input('id');
        $role=$request->input('role');
        $errMsg="";
        if(empty($id))
        {
            $errMsg.="id为空";
        }
        if(empty($loginId))
        {
            $errMsg.="logonId为空";
        }
        if(strlen($errMsg)>0)
        {
            error($errMsg);
        }
        if(empty($role))
        {
            $role="EXPERIENCER";
        }
        $result= $versionManageModule->createMembers($id,$loginId,$role);
        if($result["errCode"]==1)
        {
            success();
        }
        else
        {
            error($result["errMsg"]);
        }
    }

    /***
     * todo 删除体验者
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return array
     * @author 张国军 2018年07月27日
     */
    public function deleteMembers(Request $request,VersionManageModule $versionManageModule)
    {
        $loginId=$request->input('logonId');
        $id=$request->input('id');
        $role=$request->input('role');
        $errMsg="";
        if(empty($id))
        {
            $errMsg.="id为空";
        }
        if(empty($loginId))
        {
            $errMsg.="logonId为空";
        }
        if(strlen($errMsg)>0)
        {
            error($errMsg);
        }
        if(empty($role))
        {
            $role="EXPERIENCER";
        }
        $result=$versionManageModule->deleteMembers($id,$loginId,$role);
        if($result['errCode']==1)
        {
            success();
        }
        else
        {
            error($result["errMsg"]);
        }
    }

    /***
     * todo 创建体验版
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return mixed
     * @author 张国军 2018年08月02日
     */
    public function createExperience(Request $request,VersionManageModule $versionManageModule)
    {
        $id=$request->input('id');
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        $result=$versionManageModule->createExperience($id);
        if($result["errCode"]==1)
        {
            success("操作成功","",$result["data"]);
        }
        else
        {
            error($result["errMsg"]);
        }
    }

    /***
     * todo 取消体验版
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return mixed
     * @author 张国军 2018年08月02日
     */
    public function cancelExperience(Request $request,VersionManageModule $versionManageModule)
    {
        $id=$request->input('id');
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        $result=$versionManageModule->cancelExperience($id);
        if($result["errCode"]==1)
        {
            success("操作成功","",$result["data"]);
        }
        else
        {
            error($result["errMsg"]);
        }
    }

    /***
     * todo 体验者二维码
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return array
     * @author 张国军 2018年07月27日
     */
    public function queryExperience(Request $request,VersionManageModule $versionManageModule)
    {
        $id=$request->input('id');
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        $result=$versionManageModule->queryExperience($id);
        if($result["errCode"]==1)
        {
            success("操作成功","",$result["data"]);
        }
        else
        {
            error($result["errMsg"]);
        }
    }

    /***
     * todo 上架支付宝小程序
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return mixed
     * @author 张国军 2018年07月27日
     */
    public function onlineVersion(Request $request,VersionManageModule $versionManageModule)
    {
        $id=$request->input('id');
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        $operatorId=session('userData')['id']??0;
        $operator=session('userData')['login_name']??'';
        $result=$versionManageModule->onlineVersion($id,$operatorId,$operator);
        if($result["errCode"]==1)
        {
            success();
        }
        else
        {
            error($result["errMsg"]);
        }
    }

    /**
     * todo 下架支付宝小程序
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return mixed
     * @author 张国军 2018年07月27日
     */
    public function offlineVersion(Request $request,VersionManageModule $versionManageModule)
    {
        $id=$request->input('id');
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        $operatorId=session('userData')['id']??0;
        $operator=session('userData')['login_name']??'';
        $result=$versionManageModule->offlineVersion($id,$operatorId,$operator);
        if($result["errCode"]==1)
        {
            success();
        }
        else
        {
            error($result["errMsg"]);
        }
    }


    /**
     * todo 设置白名单
     * @param Request $request
     * @param VersionManageModule $versionManageModule
     * @return mixed
     * @author 张国军 2018年07月27日
     */
    public function createSafeDomain(Request $request,VersionManageModule $versionManageModule)
    {
        $id=$request->input('id');
        $safeDomain=$request->input('safeDomain');
        if(empty($id))
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']="id为空";
            return $returnData;
        }
        if(empty($safeDomain))
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']="白名单为空";
            return $returnData;
        }
        $operatorId=session('userData')['id']??0;
        $operator=session('userData')['login_name']??'';
        $result=$versionManageModule->createSafeDomain($id,$safeDomain,$operatorId,$operator);
        if($result["errCode"]==1)
        {
            success();
        }
        else
        {
            error($result["errMsg"]);
        }
    }


}