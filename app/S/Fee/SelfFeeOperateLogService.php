<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/13
 * Time: 17:34
 */

namespace App\S\Fee;
use App\S\S;

class SelfFeeOperateLogService  extends S
{
    public function __construct()
    {
        parent::__construct('SelfFeeOperateLog');
    }

    /**
     * todo 添加续费操作日志记录
     * @param array $data 要添加的数据
     * @return array
     * @author 张国军 2018年07月13日
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