<?php

namespace App\S\Applet;
use App\S\S;

class AppletService extends S{

	//定义model类
	public function __construct()
	{
		parent::__construct('Application');
	}

	//添加数据
	public function add($data)
	{
		return  $this->model->insertGetId($data);
	}

	//统计数据
	public function getDataCountList()
	{
		//获取全部的报名信息
		$data = [];
		$obj = $this->model->get();
		if($obj){
			$data = $obj->toArray();
		}
		//定义返回的数组
		$returnData = [];
		if($data){
			//根据分享的手机号进行分类数组
			foreach($data as $val){
				if($val['refer']){  //通过分享过来的有推荐手机号
					$returnData[$val['refer']]['data'][] = $val;
					$returnData[$val['refer']]['count'] = count($returnData[$val['refer']]['data']);
				}else{   //默认通过链接进去
					$returnData['default']['data'][] = $val;
					$returnData['default']['count']  = count($returnData['default']['data']);
				}
			}
		}

		return $returnData;

	}

	/**
	 * [getSignerListByPhone description]
	 * @param  [type] $phone [description]
	 * @return [type]        [description]
	 */
	public function getSignerListByPhone($phone)
	{
		$data = [];
		if($phone == 'default'){ //默认情况，没有填手机分享
			$where['refer'] = '';
		}else{
			$where['refer'] = $phone;
		}
		$obj = $this->model->wheres($where)->get();
		if($obj){
			$data = $obj->toArray();
		}

		return $data;
	}

}