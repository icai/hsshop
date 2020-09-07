<?php

namespace App\Http\Controllers\WXXCX;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\S\Lift\ReceptionService;
use App\S\Foundation\RegionService;

class ReceptionController extends Controller
{
    
    /**
     * 获取自提列表排序
     * @return [type] [description]
     */
    public function getZitiListBySort(Request $request)
    {
        $returnData=['code'=>40000,'hint'=>'','list'=>0];
        $input = $request->input();
        $wid = $input['wid'] ?? 0;
        if(empty($wid))
        {
            $returnData['code']=-40001;
            $returnData['hint']='wid为空';
            return $returnData;
        }
        // longitude表示经度的值，latitude表示纬度的值
        if ((!isset($input['longitude']) && empty($input['longitude'])) || (!isset($input['latitude']) && empty($input['latitude']))) {
            $returnData['code']=-40002;
            $returnData['hint']='经纬度不能为空';
            return $returnData;
        }
        $where = [];
        $title = $input['title'] ?? '';
        if ($title) {
            $where['title'] = $title;
        }
        $from = [$input['longitude'],$input['latitude']];
        $zitiData = (new ReceptionService())->dealZitiList($wid,$where,$from);
        xcxsuccess('操作成功',$zitiData);
    }

    /**
     * 获取相应的自提点日期，时间
     * @author wuxiaoping 2018.05.24
     * @return [type] [description]
     */
    public function getZitiDates($id,Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '请先选择提货地址';
            return $returnData;
        }
        $receptionService = new ReceptionService();
        $data = $receptionService->getRowById($id);
        $zitiTimes = json_decode($data['ziti_times'],true);
        if (empty($zitiTimes)) {
            $returnData['data']  = '请尽快到店自提';
            return $returnData;
        }
        $date = $request->input('date') ?? '';
        $result = $receptionService->getZitiDates($zitiTimes);
        $returnData['data'] = $result;
        return $returnData;
    }

    /**
     * 获取对应的自提点地址（返回经纬度）
     * @author wuxiaoping 2018.06.11
     * @return [type] [description]
     */
    public function getZitiInfoById($id)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '请先选择提货地址';
            return $returnData;
        }
        $receptionService = new ReceptionService();
        $data = $receptionService->getRowById($id);
        if (empty($data)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg']  = '该地址不存在或已被删除';
            return $returnData;
        }
        $returnData['title']     = $data['title'];
        $returnData['longitude'] = $data['longitude'];
        $returnData['latitude']  = $data['latitude'];
        return $returnData;
    }
}
