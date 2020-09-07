<?php
namespace App\S\Order;
use App\S\S;
use App\Lib\Redis\Order;
use DB;
use App\Lib\BLogger;

class OrderZitiService extends S{

	//定义模型类
	public function __construct()
	{
		parent::__construct('OrderZiti');
	}

	//添加数据
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}


	public function getDataByCondition($where=[])
	{
		$returnData = [];
		$obj = $this->model->wheres($where)->with('orderZiti')->first();
		if ($obj) {
			$returnData =  $obj->toArray();
		}

		return $returnData;
	}


}