<?php
namespace App\S\Wechat;
use App\S\S;


class WeixinCouponLogService extends S{


	public function __construct()
	{
		parent::__construct('WeixinCouponLog');
	}


	/**
	 * 根据条件获取单条记录
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function getInfoByWhere($where)
	{
		$obj = $this->model->wheres($where)->first();
		if(!$obj){
			return false;
		}
		$list = $obj->toArray();

		return $list;

	}

	public function add($data)
	{
		return  $this->model->insertGetId($data);
	}



}




?>