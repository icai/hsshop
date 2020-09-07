<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/5/26
 * Time: 10:21
 */

namespace App\Services;
use DB;


class IncomeService
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171130
     * @desc 获取订单IDS
     * @return mixed
     */
    public function getOrderIds()
    {
        $sql = 'SELECT id,oid FROM ds_income WHERE `status`=0 AND oid<>0 GROUP BY oid';
        $res = DB::select($sql);
        if ($res){
            $res  = json_decode(json_encode($res),true);
            $ids = array_column($res,'oid');
        }else{
            $ids = [];
        }
        return $ids;
    }


}