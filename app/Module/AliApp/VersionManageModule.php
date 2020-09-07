<?php

namespace App\Module\AliApp;

use App\S\AliApp\AliappConfigService;
use App\S\AliApp\AliappConfigOperateLogService;
use DB;
use Log;

/**
 * 第三方代商家小程序部署版本
 * @author 许立 2018年07月26日
 */
class VersionManageModule
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年07月26日
     */
    public function __construct()
    {
        $this->return = [
            'errCode' => 1,
            'errMsg' => ''
        ];
    }

    /**
     * 小程序基于模板上传版本
     * @param int $configId 小程序配置id
     * @param string $appVersion 小程序版本号，版本号必须满足 x.y.z, 且均为数字
     * @param int $templateId 模板id
     * @param int $operatorId 操作者id
     * @param string $operator 操作者昵称
     * @param string $templateVersion 模板版本号，版本号必须满足 x.y.z, 且均为数字
     * @return array
     * @author 许立 2018年07月26日
     * @update 许立 2018年07月27日 更新数据库状态和时间字段
     */
    public function versionUpload($configId, $appVersion, $templateId, $operatorId = 0, $operator = "", $templateVersion = '')
    {
        // 获取配置
        $aliConfigService = new AliappConfigService();
        $config = $aliConfigService->getRowById($configId);
        if (empty($config)) {
            $this->return['errMsg'] = '小程序配置信息不存在';
            return $this->return;
        }
        // 接口参数
        $aliClient = new AliClientModule();
//        $aliClient->appId = $config['auth_app_id'];
        $request = new AlipayOpenMiniVersionUploadRequest();
        // 前端使用的配置
        $ext = [
            'wid' => $config['wid'],
            'aliappConfigId' => $configId
        ];
        $bizContent = [
            'app_version' => $appVersion,
            'template_id' => $templateId,
            'template_version' => $templateVersion,
            'ext' => json_encode($ext)
        ];
        $request->setBizContent(json_encode($bizContent));
        // 调用接口
        $result = $aliClient->execute($request, null, $config['app_auth_token']);
        // 处理返回
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000){
            $this->return['errCode'] = 0;
            // 更新数据库
            $aliConfigService->update($configId, ['status' => 1]);
            // 操作日志
            $aliAppConfigOperateLogService = new AliappConfigOperateLogService();
            $data['logon_id'] = $config['user_id'];
            $data['request_parameter'] = json_encode($bizContent);
            $data['operator_id'] = $operatorId;
            $data['operator'] = $operator;
            $data['action'] = 1;
            $aliAppConfigOperateLogService->insertData($data);
        } else {
            $this->return['errCode'] = $resultCode ?? 1;
            $this->return['errMsg'] = $result->$responseNode->sub_msg ?? '上传版本失败';
        }

        return $this->return;
    }

    /**
     * 小程序基于模板上传版本
     * @param int $configId 小程序配置id
     * @param array $input 请求参数
     * @param int $templateId 模板id
     * @param int $operatorId 操作者id
     * @return array
     * @author 许立 2018年07月27日
     */
    public function versionAudit($configId, $input, $operatorId = 0, $operator = "")
    {
        // 获取配置
        $aliConfigService = new AliappConfigService();
        $config = $aliConfigService->getRowById($configId);
        if (empty($config)) {
            $this->return['errMsg'] = '小程序配置信息不存在';
            return $this->return;
        }
        // 接口参数
        $aliClient = new AliClientModule();
        $aliClient->appId = $config['auth_app_id'];
        $request = new AlipayOpenMiniVersionAuditApplyRequest();
        $request->setAppVersion($input['appVersion']);
        $request->setAppName($input['appName'] ?? '');
        $request->setAppEnglishName($input['appEnglishName'] ?? '');
        /*$request->setAppSlogan("这是一个支付示例");
        $request->setAppLogo("@"."本地文件路径");
        $request->setAppCategoryIds("11_12;12_13");
        $request->setAppDesc("这是一个小程序的描述这是一个小程序的描述这是一个小程序的描述这是一个小程序的描述");
        $request->setServicePhone("13110101010");
        $request->setServiceEmail("example@mail.com");*/
        $request->setVersionDesc($input['versionDesc']);
        /*$request->setMemo("小程序示例");
        $request->setRegionType("LOCATION");
        $regionInfo = new RegionInfo();
        $regionInfo->province_code = $"310000";
        $regionInfo->province_name = $"浙江省";
        $regionInfo->city_code = $"310000";
        $regionInfo->city_name = $"杭州市";
        $regionInfo->area_code = $"311100";
        $regionInfo->area_name = $"余杭区";
        $request->service_region_info = $regionInfo;
        $request->setFirstScreenShot("@"."本地文件路径");
        $request->setSecondScreenShot("@"."本地文件路径");
        $request->setThirdScreenShot("@"."本地文件路径");
        $request->setFourthScreenShot("@"."本地文件路径");
        $request->setFifthScreenShot("@"."本地文件路径");
        $request->setLicenseNo("licenseNo");
        $request->setLicenseName("营业执照名称");
        $request->setFirstLicensePic("@"."本地文件路径");
        $request->setSecondLicensePic("@"."本地文件路径");
        $request->setThirdLicensePic("@"."本地文件路径");
        $request->setFourthLicensePic("@"."本地文件路径");
        $request->setFifthLicensePic("@"."本地文件路径");
        $request->setLicenseValidDate($input['licenseValidDate']);
        $request->setOutDoorPic("@"."本地文件路径");*/
        // 调用接口
        $result = $aliClient->execute($request);
        // 处理返回
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000){
            $this->return['errCode'] = 0;
            // 更新数据库
            $aliConfigService->update($configId, ['status' => 2]);
            // 操作日志
            $aliAppConfigOperateLogService = new AliappConfigOperateLogService();
            $data['logon_id'] = $config['user_id'];
            $data['request_parameter'] = '';
            $data['operator_id'] = $operatorId;
            $data['operator'] = $operator;
            $data['action'] = 2;
            $aliAppConfigOperateLogService->insertData($data);
        } else {
            $this->return['errCode'] = $resultCode ?? 1;
            $this->return['errMsg'] = $result->$responseNode->sub_msg ?? '审核失败';
        }

        return $this->return;
    }

    /**
     * 小程序查看详情
     * @param int $configId 小程序配置id
     * @return array
     * @author 许立 2018年07月31日
     */
    public function detail($configId)
    {
        // 获取配置
        $aliConfigService = new AliappConfigService();
        $config = $aliConfigService->getRowById($configId);
        if (empty($config)) {
            $this->return['errMsg'] = '小程序配置信息不存在';
            return $this->return;
        }

        return [
            'errCode' => 0,
            'data' => $config
        ];
    }

    /**
     * 小程序版本详情查询
     * @return array
     * @author 许立 2018年07月31日
     */
    public function versionDetailQuery()
    {
        DB::table('aliapp_config')->select('id', 'app_auth_token', 'app_version')
            ->where('status', 2)
            ->chunk(100, function ($configs) {
                $aliConfigService = new AliappConfigService();
                $aliClient = new AliClientModule();
                $request = new AlipayOpenMiniVersionDetailQueryRequest();
                foreach ($configs as $config) {
                    // 接口参数
                    $bizContent = [
                        'app_version' => $config->app_version
                    ];
                    $request->setBizContent(json_encode($bizContent));
                    // 调用接口
                    $result = $aliClient->execute($request, null, $config->app_auth_token);
                    // 处理返回
                    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
                    $response = $result->$responseNode;
                    if (!empty($response->code) && $response->code == 10000){
                        $this->return['errCode'] = 0;
                        // 暂时只更新审核通过和驳回两种情况
                        if (in_array($response->status, ['WAIT_RELEASE', 'AUDIT_REJECT'])) {
                            $update_array = [];
                            if ($response->status == 'WAIT_RELEASE') {
                                $update_array['status'] = 3;
                            } else {
                                $update_array['status'] = 9;
                            }
                            $update_array['audit_reject_reason'] = $response->reject_reason;
                            $update_array['screen_shot_list'] = $response->screen_shot_list;
                            $update_array['memo'] = $response->memo;
                            $update_array['category_info_list'] = json_encode($response->category_info_list);
                            $update_array['package_info_list'] = json_encode($response->package_info_list);
                            $aliConfigService->update($config->id, $update_array);
                        }
                    } else {
                        Log::info($response->sub_msg ?? '上传版本失败');
                    }
                }
            });
    }

    /***
     * todo 添加体验者/开发者
     * @param int $id
     * @param string $role
     * @return array
     * @author 张国军 2018年7月27日
     */
    public function createMembers($id=0,$loginId="",$role="EXPERIENCER")
    {
        $errMsg="";
        if(empty($id))
        {
            $errMsg.="id为空";
        }
        if(empty($loginId))
        {
            $errMsg.="loginId为空";
        }
        if(strlen($errMsg)>0)
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']=$errMsg;
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }
        $aop = new AliClientModule();
        $request = new AlipayOpenAppMembersCreateRequest();
        $bizContent=[];
        $bizContent['logon_id']= $loginId;
        $bizContent['role']= $role;
        $request->setBizContent(json_encode($bizContent));
        $result = $aop->execute ($request,null,$aliAppConfigData['app_auth_token']);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode=$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            return $this->return;
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    /***
     * todo 删除体验者/开发者
     * @param int $id
     * @param string $role
     * @return array
     * @author 张国军 2018年07月27日
     */
    public function deleteMembers($id=0,$loginId="",$role="EXPERIENCER")
    {
        $errMsg="";
        if(empty($id))
        {
            $errMsg.="id为空";
        }
        if(empty($loginId))
        {
            $errMsg.="loginId为空";
        }
        if(strlen($errMsg)>0)
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']=$errMsg;
            return $this->return;
        }

        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }

        $aop = new AliClientModule();
        $request = new AlipayOpenAppMembersDeleteRequest();
        $bizContent=[];
        $bizContent['logon_id']= $loginId;
        $bizContent['role']= $role;
        $request->setBizContent(json_encode($bizContent));
        $result = $aop->execute ($request,null,$aliAppConfigData['app_auth_token']);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode=$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            return $this->return;
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    /***
     * todo 创建体验版
     * @param int $id
     * @param string $role
     * @return array
     * @author 张国军 2018年08月02日
     */
    public function createExperience($id=0)
    {
        if(empty($id))
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']="id为空";
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_version']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序版本号为空";
            return $this->return;
        }
        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-104;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }
        $aop = new AliClientModule();
        $request = new AlipayOpenMiniExperienceCreateRequest();
        $bizContent=[];
        $bizContent['app_version']= $aliAppConfigData['app_version'];
        $request->setBizContent(json_encode($bizContent));

        $result = $aop->execute($request,null,$aliAppConfigData['app_auth_token']);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode =$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            return $this->return;
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    /***
     * todo 取消体验版
     * @param int $id
     * @param string $role
     * @return array
     * @author 张国军 2018年08月02日
     */
    public function cancelExperience($id=0)
    {
        if(empty($id))
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']="id为空";
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_version']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序版本号为空";
            return $this->return;
        }
        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-104;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }
        $aop = new AliClientModule();
        $request = new AlipayOpenMiniExperienceCancelRequest();
        $bizContent=[];
        $bizContent['app_version']= $aliAppConfigData['app_version'];
        $request->setBizContent(json_encode($bizContent));

        $result = $aop->execute($request,null,$aliAppConfigData['app_auth_token']);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode =$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            return $this->return;
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    
    /***
     * todo 体验二维码
     * @param int $id
     * @param string $role
     * @return array
     * @author 张国军 2018年07月27日
     */
    public function queryExperience($id=0)
    {
        if(empty($id))
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']="id为空";
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_version']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序版本号为空";
            return $this->return;
        }
        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-104;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }
        $aop = new AliClientModule();
        $request = new AlipayOpenMiniExperienceQueryRequest();
        $bizContent=[];
        $bizContent['app_version']= $aliAppConfigData['app_version'];
        $request->setBizContent(json_encode($bizContent));

        $result = $aop->execute ($request,null,$aliAppConfigData['app_auth_token']);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode =$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            $qrCodeUrl=$result->$responseNode->exp_qr_code_url??"";
            if(!empty($qrCodeUrl))
            {
                $this->return['data']=$qrCodeUrl;
                return $this->return;
            }
            else
            {
                $this->return['errCode']=-105;
                $this->return['errMsg']="小程序体验版url不存在";
                return $this->return;
            }

        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    /***
     * todo 上架支付宝小程序
     * @param $id
     * @return array
     * @author 张国军 2018年07月27日
     */
    public function onlineVersion($id,$operatorId=0,$operator="")
    {
        if(empty($id))
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']="id为空";
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_version']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序版本号为空";
            return $this->return;
        }

        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-104;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }

        if(empty($aliAppConfigData['user_id']))
        {
            $this->return['errCode']=-105;
            $this->return['errMsg']="支付宝登录账号ID为空";
            return $this->return;
        }

        $aop = new AliClientModule();
        $request = new AlipayOpenMiniVersionOnlineRequest();
        $bizContent=[];
        $bizContent['app_version']= $aliAppConfigData['app_version'];
        $request->setBizContent(json_encode($bizContent));

        $aliAppConfigOperateLogService=new AliappConfigOperateLogService();
        $data['logon_id']=$aliAppConfigData['user_id'];
        $data['request_parameter']=json_encode($bizContent);
        $data['operator_id']=$operatorId;
        $data['operator']=$operator;
        $data['action']=6;
        $aliAppConfigOperateLogService->insertData($data);

        $result = $aop->execute($request,null,$aliAppConfigData['app_auth_token']);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode=$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            //status为6 表示上架
            $updateReturn=$aliAppConfigService->update($id,['status'=>6]);
            if($updateReturn)
            {
                return $this->return;
            }
            else
            {
                $this->return['errCode']=-106;
                $this->return['errMsg']="更改数据库数据失败";
                return $this->return;
            }
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    /***
     * todo 下架支付宝小程序
     * @param $id
     * @return array
     * @author 张国军 2018年07月27日
     */
    public function offlineVersion($id=0,$operatorId=0,$operator="")
    {
        if(empty($id))
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']="id为空";
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['app_version']))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="小程序版本号为空";
            return $this->return;
        }

        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-104;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }

        if(empty($aliAppConfigData['user_id']))
        {
            $this->return['errCode']=-105;
            $this->return['errMsg']="支付宝登录账号ID为空";
            return $this->return;
        }

        $aop = new AliClientModule();
        $request = new AlipayOpenMiniVersionOfflineRequest();
        $bizContent=[];
        $bizContent['app_version']= $aliAppConfigData['app_version'];
        $request->setBizContent(json_encode($bizContent));

        //操作日志
        $aliAppConfigOperateLogService=new AliappConfigOperateLogService();
        $data['logon_id']=$aliAppConfigData['user_id'];
        $data['request_parameter']=json_encode($bizContent);
        $data['operator_id']=$operatorId;
        $data['operator']=$operator;
        $data['action']=7;
        $aliAppConfigOperateLogService->insertData($data);

        $result = $aop->execute ($request,null,$aliAppConfigData['app_auth_token']);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode=$result->$responseNode->code??0;
        if(!empty($resultCode)&&$resultCode==10000)
        {
            //调用接口成功操作日志
            $data['status']=1;
            $aliAppConfigOperateLogService->insertData($data);
            //status为6 表示下架
            $updateReturn=$aliAppConfigService->update($id,['status'=>7]);
            if($updateReturn)
            {
                return $this->return;
            }
            else
            {
                $this->return['errCode']=-106;
                $this->return['errMsg']="更改数据库数据失败";
                return $this->return;
            }
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }

    /***
     * todo 设置白名单
     * @param int $id
     * @param string $safeDomain
     * @return array
     * @author 张国军 2018年07月30日
     */
    public function  createSafeDomain($id=0,$safeDomain="",$operatorId=0,$operator="")
    {
        if(empty($id))
        {
            $this->return['errCode']=-101;
            $this->return['errMsg']="id为空";
            return $this->return;
        }

        if(empty($safeDomain))
        {
            $this->return['errCode']=-103;
            $this->return['errMsg']="httpRequest域白名单为空";
            return $this->return;
        }
        $aliAppConfigService=new AliappConfigService();
        $aliAppConfigData=$aliAppConfigService->getRowById($id);
        if(empty($aliAppConfigData['user_id']))
        {
            $this->return['errCode']=-104;
            $this->return['errMsg']="该id对应的数据不存在";
            return $this->return;
        }

        if(empty($aliAppConfigData['app_auth_token']))
        {
            $this->return['errCode']=-105;
            $this->return['errMsg']="小程序app_auth_token为空";
            return $this->return;
        }

        $aop = new AliClientModule();
        $request = new AlipayOpenMiniSafedomainCreateRequest();
        $bizContent=[];
        $bizContent['safe_domain']= $safeDomain;
        $request->setBizContent(json_encode($bizContent));
        $result = $aop->execute ($request,null,$aliAppConfigData['app_auth_token']);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode=$result->$responseNode->code??0;

        //记录日志
        $aliAppConfigOperateLogService=new AliappConfigOperateLogService();
        $data['logon_id']=$aliAppConfigData['user_id'];
        $data['request_parameter']=json_encode($bizContent);
        $data['operator_id']=$operatorId;
        $data['operator']=$operator;
        $data['action']=8;
        $aliAppConfigOperateLogService->insertData($data);

        if(!empty($resultCode)&&$resultCode==10000)
        {
            //成功日志
            $data['status']=1;
            $aliAppConfigOperateLogService->insertData($data);

            $updateReturn=$aliAppConfigService->update($id,['safe_domain'=>$safeDomain]);
            if($updateReturn)
            {
                return $this->return;
            }
            else
            {
                $this->return['errCode']=-106;
                $this->return['errMsg']="更改数据库数据失败";
                return $this->return;
            }
        }
        else
        {
            $code=$result->$responseNode->code??'';
            $msg=$result->$responseNode->msg??'';
            $subCode=$result->$responseNode->sub_code??'';
            $subMsg=$result->$responseNode->sub_msg??'';
            $this->return['errCode']=-102;
            $this->return['errMsg']="code:".$code.",msg:".$msg.",subCode:".$subCode.",subMsg:".$subMsg;
            return $this->return;
        }
    }


    /**
     * 获取小程序基本信息
     * @param $data
     * @author 张永辉
     */
    public function getAliappInfo($data)
    {
        $result = ['errCode'=>'0','errMsg'=>''];
        $aop = new AliClientModule();
        $request = new AlipayOpenMiniBaseinfoQueryRequest ();
        $resData= $aop->execute ($request,null,$data['app_auth_token']);
        $resData = json_decode(json_encode($resData),true);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(!empty($resData[$responseNode]['code']) && $resData[$responseNode]['code'] == '10000'){
               $info = $resData[$responseNode];
               $upData = [
                   'app_name'   => $info['app_name'],
                   'app_english_name'   => $info['app_english_name'],
                   'app_slogan'   => $info['app_slogan'],
                   'app_logo'   => $info['app_logo'],
                   'category_names'   => $info['category_names'],
                   'app_desc'   => $info['app_desc'],
                   'service_phone'   => $info['service_phone']??'',
                   'service_email'   => $info['service_email']??'',
                   'safe_domain'   => json_encode($info['safe_domains']??''),
                   'package_names'   => json_encode($info['package_names']??''),
               ];
            $res = (new AliappConfigService())->update($data['id'],$upData);
            if ($res){
                return $result;
            }else{
                $result['errCode'] = -40026;
                $result['errMsg'] = '更新失败';
                return $result;
            }
        } else {
            \Log::info('获取支付宝小程序基础信息错误：');
            \Log::info($result);
            $result['errCode'] = 400540;
            $result['errMsg'] = '获取接口失败';
            return $result;
        }
    }


}