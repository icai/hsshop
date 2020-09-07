<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/4/10
 * Time: 14:49
 */

namespace App\Module;
use WeixinService;
use OrderPointRuleService;
use App\S\Weixin\ShopService;

class PointModule
{
    /**
     * @param int $wid
     * @return array
     * @author jonzhang
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public  function  isGivePoint($wid=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($wid))
        {
            $returnData['errCode']=-101;
            $returnData['errMsg']='店铺id为空';
            return $returnData;
        }
        //$result=WeixinService::selectPointStatus(['id'=>$wid]);
        $shopService = new ShopService();
        $result = $shopService->getRowById($wid);
        if(!empty($result))
        {
            $isPoint=$result['is_point'];
            if($isPoint)
            {
                $orderPointData = OrderPointRuleService::getRowByCondition(['wid' => $wid, 'is_on' => 1]);
                if ($orderPointData['errCode'] == 0 && !empty($orderPointData['data']))
                {
                    $returnData['data']=1;
                }
            }
        }
        return $returnData;
    }
}