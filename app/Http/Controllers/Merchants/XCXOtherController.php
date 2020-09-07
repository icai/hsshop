<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/12/27
 * Time: 17:33
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\S\WXXCX\WXXCXTopNavService;

class XCXOtherController extends Controller
{
    /**
     * todo 小程序首部导航
     * @param Request $request
     * @param WXXCXTopNavService $wxxcxTopNavService
     * @return array
     * @author jonzhang
     * @date 2017-12-27
     */
    public function selectTopNav(Request $request,WXXCXTopNavService $wxxcxTopNavService)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$request->input('wid')??session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        //获取当前店铺导航
        return $wxxcxTopNavService->getRow($wid);
    }
    /**
     * todo 处理小程序首部导航
     * @param Request $request
     * @param WXXCXTopNavService $wxxcxTopNavService
     * @return array
     * @author jonzhang
     * @date 2017-12-27
     */
    public function processTopNav(Request $request,WXXCXTopNavService $wxxcxTopNavService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        //接收数据[数组]
        $id = $request->input('id');
        $isON = $request->input('is_on')??0;
        $templateData = $request->input('data');
        $data['is_on'] = $isON;
        if (empty($templateData)||$templateData=='[]')
        {
            $returnData['errCode']=-1;
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
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
                $data['template_data'] = $templateData;
            }
            else
            {
                $returnData['errCode']=-7;
                $returnData['errMsg']='导航数据格式不对';
                return $returnData;
            }
        }
        $wid=session('wid');
        if(empty($wid))
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='登录超时';
            return $returnData;
        }
        if (strlen($data['template_data']) > 65535-1)
        {
            $returnData['errCode'] = -6;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }
        $result=$wxxcxTopNavService->getRow($wid);
        if($result['errCode']!=0)
        {
            return $result;
        }
        else if($result['errCode']==0&&empty($result['data']))
        {
            $data['wid']=$wid;
            return $wxxcxTopNavService->insertData($data);
        }
        //add by jonzhang 2018-01-12
        if(empty($id))
        {
            if($result['errCode']==0&&!empty($result['data']))
                $id=$result['data']['id'];
        }
        if(empty($id))
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        //更改小程序导航信息
        return $wxxcxTopNavService->updateData($id,$data);
    }

}