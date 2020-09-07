<?php
namespace App\S\Wechat;
use App\S\S;

class ComponentTicketService extends S{

	//定义model类
	public function __construct()
	{
		parent::__construct('ComponentTicket');
	}

	//获取ticket值
	public function getTicketVal()
	{
		$info = [];
		$obj = $this->model->order('updated_at DESC')->first();
		if($obj){
			$info = $obj->toArray();
		}
		return $info;
	}

	//更新ticket数据
	public function update($component_verify_ticket)
	{
		return $this->model->wheres(['id' => 1])->update(['component_verify_ticket' => $component_verify_ticket]);
	}

	//添加数据
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}


	public function storageTicket($component_verify_ticket)
	{
		$info = $this->getTicketVal();
		if($info){
			$this->update($component_verify_ticket);
		}else{
			$this->add(['component_verify_ticket' => $component_verify_ticket]);
		}

		return true;
	}

}


