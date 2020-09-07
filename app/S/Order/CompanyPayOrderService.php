<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/4/8
 * Time: 10:28
 */

namespace App\S\Order;


use App\Model\CompanyPayOrder;

class CompanyPayOrderService
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180408
     * @desc 添加企业打款订单
     * @param $data
     */
    public function add($data)
    {
        $res = CompanyPayOrder::insertGetId($data);
        return $res;
    }

    public function isExist($relation_id,$type)
    {
        $res = CompanyPayOrder::where('relation_id',$relation_id)->where('type',$type)->first();
        if ($res){
                return $res->toArray();
        }else{
            return false;
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180411
     * @desc
     */
    public function getRowById($id)
    {
        $result = CompanyPayOrder::find($id);
        if ($result){
            return $result->toArray();
        }else{
            return [];
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180411
     * @desc 更新支付订单信息
     */
    public function update($where,$data)
    {
        $res = CompanyPayOrder::wheres($where)->update($data);
        return $res;
    }


}