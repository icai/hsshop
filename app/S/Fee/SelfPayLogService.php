<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/12
 * Time: 11:51
 */

namespace App\S\Fee;
use App\S\S;

class SelfPayLogService  extends S
{
    public function __construct()
    {
        parent::__construct('SelfPayLog');
    }

    /**
     * todo 添加支付日志记录
     * @param array $data 要添加的数据
     * @return array
     * @author 张国军 2018年07月12日
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $errMsg="";
        if(empty($data['order_id']))
        {
            $errMsg.='订单编号不能为空';
        }
        if(empty($data['trade_no']))
        {
            $errMsg.='交易号不能为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['create_time']=time();
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }
}