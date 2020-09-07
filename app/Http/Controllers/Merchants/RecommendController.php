<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/4/9
 * Time: 14:32
 */

namespace App\Http\Controllers\Merchants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\S\Market\CommendInfoService;
use App\S\Market\CommendDetailService;

class RecommendController  extends Controller
{
    /***
     * todo 推荐列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang
     * @date 2018-04-03
     */
    public function  commendationList()
    {
        return view('merchants.recommend.commendation',[
            'leftNav'        => 'commend',
            'slidebar'       => 'list',
        ]);
    }

    /**
     * todo 显示推荐明细
     * @param Request $request
     * @author jonzhang
     * @date 2018-04-03
     */
    public  function  showCommendationDetails(Request $request,CommendInfoService $commendInfoService,CommendDetailService $commendDetailService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>[],'total'=>0,'currentPage'=>0,'pageSize'=>15];
        $type=$request->input('type')??0;
        $wid=session('wid');
        $errMsg='';
        if(empty($type))
        {
            $errMsg.='type为空';
        }
        if(empty($wid))
        {
            $errMsg.='登录超时';
        }
        if(!in_array($type,[1,2]))
        {
            $errMsg.='type数值不符合要求';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $commendInfoData=$commendInfoService->getListByCondition(['wid'=>$wid,'type'=>$type]);
        if($commendInfoData['errCode']==0&&!empty($commendInfoData['data']))
        {

            $cid=$commendInfoData['data'][0]['id'];
            $commendDetailData=$commendDetailService->getListByCondition(['cid'=>$cid,'current_status'=>0],'id','desc',15);
            foreach($commendDetailData['data'] as &$item)
            {
                $item['type']=$type;
            }
            $returnData['data']=$commendDetailData['data'];
            $returnData['total']=$commendDetailData['total'];
            $returnData['currentPage']=$commendDetailData['currentPage'];
            $returnData['pageSize']=$commendDetailData['pageSize'];
            return $returnData;
        }
        return $commendInfoData;
    }

    /***
     * todo 添加推荐
     * @param Request $request
     * @author jonzhang
     * @date 2018-04-04
     */
    public function processCommendation(Request $request,CommendInfoService $commendInfoService,CommendDetailService $commendDetailService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $type=$request->input('type')??0;
        $type=intval($type);
        $ids=$request->input('ids');
        $wid=session('wid');
        $errMsg='';
        if(empty($type))
        {
            $errMsg.='type为空';
        }
        if(empty($wid))
        {
            $errMsg.='登录超时';
        }
        if(empty($ids))
        {
            $errMsg.='ids为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }

        if(is_array($ids))
        {
            $isPass=true;
            foreach($ids as $item)
            {
                if(!isset($item['id'])||!isset($item['title']))
                {
                    $isPass=false;
                    break;
                }
            }
            if(!$isPass)
            {
                $returnData['errCode']=-1004;
                $returnData['errMsg']='数据不符合要求';
                return $returnData;
            }
        }
        else
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']='ids必须为数组';
            return $returnData;
        }

        $commendInfoData=$commendInfoService->getListByCondition(['wid'=>$wid,'type'=>$type]);
        if($commendInfoData['errCode']==0&&!empty($commendInfoData['data']))
        {
            $commendDetailData=$commendDetailService->getListBycondition(['cid'=>$commendInfoData['data'][0]['id'],'current_status'=>0]);
            if($commendDetailData['errCode']==0)
            {
                $target = [];
                if (!empty($commendDetailData['data']))
                {
                    foreach($commendDetailData['data'] as $commendItem)
                    {
                        array_push($target, $commendItem['recommendation_id']);
                    }
                }
                foreach ($ids as $item)
                {
                    if (!in_array($item['id'], $target))
                    {
                        $commendDetailService->insertData(['cid' => $commendInfoData['data'][0]['id'], 'recommendation_id'=>$item['id'],'title'=>$item['title']]);
                    }
                }
            }
        }
        else if($commendInfoData['errCode']==0&&empty($commendInfoData['data']))
        {
            $commendData=$commendInfoService->insertData(['type'=>$type,'wid'=>$wid]);
            if($commendData['errCode']==0&&!empty($commendData['data']))
            {
                $id=$commendData['data'];
                foreach($ids as $item)
                {
                    $commendDetailService->insertData(['cid'=>$id,'recommendation_id'=>$item['id'],'title'=>$item['title']]);
                }
            }
        }
        else
        {
            return $commendInfoData;
        }
        return $returnData;
    }

    /***
     * todo 剔除推荐明细
     * @param Request $request
     * @author jonzhang
     * @date 2018-04-03
     */
    public function deleteCommendation(Request $request,CommendDetailService $commendDetailService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $wid=session('wid');
        $id=$request->input('id')??0;
        $id=intval($id);
        $errMsg='';
        if(empty($id))
        {
            $errMsg.='id为空';
        }
        if(empty($wid))
        {
            $errMsg.='登录超时';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $result=$commendDetailService->updateData($id,['current_status'=>-1]);
        if($result['errCode']==1)
        {
            $returnData['errCode']=-1002;
            $returnData['errMsg']=$result['errMsg'];
            return $returnData;
        }
        return $result;
    }

    /***
     * todo 更改推荐
     * @param Request $request
     * @param CommendInfoService $commendInfoService
     * @return array
     * @author jonzhang
     * @date 2018-04-04
     */
    public function updateCommendation(Request $request,CommendInfoService $commendInfoService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $type = $request->input('type')??0;
        $type=intval($type);
        $isAuto=$request->input('isAuto')??0;
        $isAuto=intval($isAuto);
        $wid=session('wid');
        $errMsg='';
        if(empty($type))
        {
            $errMsg.='type为空';
        }
        if(empty($wid))
        {
            $errMsg.='登录超时';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $commendInfoData=$commendInfoService->getListByCondition(['wid'=>$wid,'type'=>$type]);
        if($commendInfoData['errCode']==0&&!empty($commendInfoData['data']))
        {
            $id=$commendInfoData['data'][0]['id'];
            return $commendInfoService->updateData($id,['is_auto'=>$isAuto]);
        }
        else if($commendInfoData['errCode']==0&&empty($commendInfoData['data']))
        {
            return $commendInfoService->insertData(['type'=>$type,'wid'=>$wid,'is_auto'=>$isAuto]);
        }
        return $commendInfoData;
    }

    /**
     * todo 显示推荐
     * @param Request $request
     * @param CommendInfoService $commendInfoService
     * @return array
     * @author jonzhang
     * @date 2018-04-08
     */
    public function showCommendation(Request $request,CommendInfoService $commendInfoService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $type=$request->input('type')??0;
        $wid=session('wid');
        $errMsg='';
        if(empty($type))
        {
            $errMsg.='type为空';
        }
        if(empty($wid))
        {
            $errMsg.='登录超时';
        }
        if(!in_array($type,[1,2]))
        {
            $errMsg.='type数值不符合要求';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        return $commendInfoService->getListByCondition(['wid'=>$wid,'type'=>$type]);
    }

    /***
     * todo 获取活动id
     * @param Request $request
     * @param CommendDetailService $commendDetailService
     * @return array
     * @author jonzhang
     * @date 2018-04-08
     */
    public function showCommendationDetailID(Request $request,CommendInfoService $commendInfoService,CommendDetailService $commendDetailService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '','data'=>[]];
        $type=$request->input('type')??0;
        $type=intval($type);
        $wid=session('wid');
        $errMsg='';
        if(empty($type))
        {
            $errMsg.='type为空';
        }
        if(empty($wid))
        {
            $errMsg.='登录超时';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1001;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $commendInfoData=$commendInfoService->getListByCondition(['wid'=>$wid,'type'=>$type]);
        if($commendInfoData['errCode']==0&&!empty($commendInfoData['data']))
        {
            $commendDetailData = $commendDetailService->getListBycondition(['cid' => $commendInfoData['data'][0]['id'], 'current_status' => 0]);
            if ($commendDetailData['errCode'] == 0&&!empty($commendDetailData['data']))
            {
                foreach($commendDetailData['data'] as $item)
                {
                    array_push($returnData['data'],$item['recommendation_id']);
                }
                return $returnData;
            }
        }
        else
        {
            return  $commendInfoData;
        }
        return $returnData;
    }
}