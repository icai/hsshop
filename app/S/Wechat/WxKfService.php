<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/30
 * Time: 15:23
 */

namespace App\S\Wechat;


use App\S\S;

class WxKfService extends S
{

    public function __construct()
    {
        parent::__construct('WeixinCustomService');
    }

    public function save($wid,$data)
    {
        $input = [
            'wid' => $wid,
            'kf_account' => $data['kf_account'],
            'kf_headimgurl'    => $data['fileName'],
        ];
        $count = $this->model->where(['kf_account'=>$data['kf_account']])->count();
        if($count){
            return $this->model->where(['kf_account'=>$data['kf_account']])->update($input);
        }
        return $this->model->insertGetId($input);
    }

    public function getRowByWhere($where = [])
    {
        $obj = $this->model->where($where)->first();
        if($obj){
            return $obj->kf_headimgurl;
        }
        return [];
    }

    public function delete($where = [])
    {
        return $this->model->where($where)->delete();
    }


    public function getListById(array $idArr)
    {
        $mysqlData = $this->model->whereIn('id',$idArr)->get()->toArray();
        $mysqlData = array_column($mysqlData, null,'id');
        return sortArr($idArr,  $mysqlData );
    }
}