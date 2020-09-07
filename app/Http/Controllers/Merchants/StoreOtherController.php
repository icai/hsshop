<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/5/17
 * Time: 9:28
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\S\Store\StoreTopNavService;

class StoreOtherController extends Controller
{
    /**
     * todo 微商城顶部导航
     * @param Request $request
     * @param StoreTopNavService $storeTopNavService
     * @return array
     * @author jonzhang
     * @date 2018-05-17
     */
    public function selectTopNav(Request $request,StoreTopNavService $storeTopNavService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-10001;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //获取当前店铺导航
        return $storeTopNavService->getRow($wid);
    }
    /**
     * todo 处理微商城顶部导航
     * @param Request $request
     * @param StoreTopNavService $storeTopNavService
     * @return array
     * @author jonzhang
     * @date 2018-05-17
     */
    public function processTopNav(Request $request,StoreTopNavService $storeTopNavService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //接收数据[数组]
        $id = $request->input('id');
        $isON = $request->input('is_on')??0;
        $templateData = $request->input('data');
        $color_setting = $request->input('color_setting');//何书哲 2018年8月27日 顶部导航颜色设置
        $data['is_on'] = $isON;
        if (empty($templateData)||$templateData=='[]')
        {
            $returnData['errCode']=-10001;
            $returnData['errMsg']='导航数据不能够为空';
            return $returnData;
        }
        else
        {
            if(is_array($templateData))
            {
                $data['template_data'] = json_encode($templateData);
            }
            else if(is_string($templateData))
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($templateData,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-10002;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                $data['template_data'] = $templateData;
            }
            else
            {
                $returnData['errCode']=-10003;
                $returnData['errMsg']='导航数据不符合要求';
                return $returnData;
            }
        }
        //验证顶部导航颜色设置
        if (empty($color_setting)||$color_setting=='[]')
        {
            $returnData['errCode']=-10007;
            $returnData['errMsg']='设置数据不能够为空';
            return $returnData;
        }
        else
        {
            if(is_array($color_setting))
            {
                $data['color_setting'] = json_encode($color_setting);
            }
            else if(is_string($color_setting))
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($color_setting,true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-10008;
                    $returnData['errMsg']='设置数据格式不符合要求';
                    return $returnData;
                }
                $data['color_setting'] = $color_setting;
            }
            else
            {
                $returnData['errCode']=-10009;
                $returnData['errMsg']='设置数据不符合要求';
                return $returnData;
            }
        }

        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-10004;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        if (strlen($data['template_data']) > 65535-1)
        {
            $returnData['errCode'] = -10006;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }
        $result=$storeTopNavService->getRow($wid);
        if($result['errCode']!=0)
        {
            return $result;
        }
        else if($result['errCode']==0&&empty($result['data']))
        {
            $data['wid']=$wid;
            return $storeTopNavService->insertData($data);
        }
        if(empty($id))
        {
            if($result['errCode']==0&&!empty($result['data']))
                $id=$result['data']['id'];
        }
        if(empty($id))
        {
            $returnData['errCode']=-10005;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        //更改微商城导航信息
        return $storeTopNavService->updateData($id,$data);
    }
}