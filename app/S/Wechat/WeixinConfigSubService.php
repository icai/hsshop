<?php
namespace App\S\Wechat;
use App\S\S;

class WeixinConfigSubService extends S{

	public function __construct()
	{
		parent::__construct('WeixinConfigSub');
	}

	/**
	 * [获取所有店铺的微信公众号授权]
	 */
	public function getAllList()
	{
        $list = [];
        //只统计微信认证的服务号
        $where['service_type_info'] = 2;
        $where['verify_type_info'] = 0;
		$obj = $this->model->where($where)->get();
        if($obj){
            $list = $obj->toArray();
        }

        return $list;
	}
}


