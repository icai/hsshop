<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/15
 * Time: 18:57
 */

namespace App\S\WXXCX;
use App\S\S;

class WXXCXPaymentLogService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXPaymentLog');
    }
    /**
     * todo 添加微信小程序配置信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-09
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
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }
}