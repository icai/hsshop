<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/30
 * Time: 14:07
 */

namespace App\S\AliApp;
use App\S\S;

class AliappConfigOperateLogService  extends S
{
    public function __construct()
    {
        parent::__construct('AliappConfigOperateLog');
    }

    /***
     * todo 添加数据
     * @param array $data
     * @return array
     * @author 张国军 2018年07月30日
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
            $returnData['errCode']=-3;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

}