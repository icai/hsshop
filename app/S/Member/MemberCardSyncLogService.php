<?php
namespace App\S\Member;
use App\S\S;

class MemberCardSyncLogService extends S{

	public function __construct()
	{
		parent::__construct('MemberCardSyncLog');
	}


	//添加同步会员卡日志记录
	public function add($data)
	{
		return  $this->model->insertGetId($data);
	}

	//根据条件获得数据
	public function getRowByWhere($where)
	{
		$list = [];
		$obj = $this->model->wheres($where)->get();
		if($obj){
			$list = $obj->toArray();
		}

		return $list;
	}

}