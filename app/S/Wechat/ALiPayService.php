<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/30
 * Time: 15:23
 */

namespace App\S\Wechat;


use App\S\S;

class ALiPayService extends S
{

    public function __construct()
    {
        parent::__construct('ALiPay');
    }

    public function saveData($wid,$data)
    {
        $input = [
            'wid' => $wid,
            'payee' => $data['config']['payee'],
            'partner' => $data['config']['partner'],
            'seller_id' => $data['config']['seller_id'],
            'key'       => $data['config']['key'],
            'status'    => $data['status']
        ];
        if (empty($data['id'])){
            //新增数据
            $rs = $this->model->insertGetId($input);
        }else{
         //更新数据
            $rs = $this->model->where(['id'=> $data['id']])->update($input);
        }
        return $rs;
    }

    public function getRowByWid($wid)
    {
        $where['wid'] = $wid;
        $data = $this->getList($where);
        return  $data[0] ?? [];
    }

    public function getConfByWid($wid)
    {
        $data = $this->model->where(['wid'=>$wid])->first();
        if(!empty($data)){
            $data = $data->toArray();
        }
        return $data;
    }

    public function getListById(array $idArr)
    {
        $mysqlData = $this->model->whereIn('id',$idArr)->get()->toArray();
        $mysqlData = array_column($mysqlData, null,'id');
        return sortArr($idArr,  $mysqlData );
    }
}