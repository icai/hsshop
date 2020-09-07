<?php
namespace App\S\Staff;
use App\Lib\Redis\Wechat;
use App\S\S;

class WeixinTemplateService extends S{

	//对应相应的模型类
	public function __construct()
	{
		parent::__construct('WeixinTemplate');
	}

	/**
	 * [getListAll 获取全部数据]
	 * @return [type] [description]
	 */
	public function getListAll()
	{
		$result = $this->getListWithPage(['status'=>1]);
		return $result;
	}

	//添加通用模板
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}

	/**
	 * [编辑模板]
	 * @param  [int]   $id   [模板id]
	 * @param  [array] $data [更新的相应模板数据]
	 * @return [type]       [description]
	 */
	public function update($id,$data)
	{
		$wechatRedis = new Wechat();
		if($this->model->wheres(['id'=>$id])->update($data)){
			$data['id'] = $id;
			$wechatRedis->updateHashRow($id, $data);
			return true;
		}	

		return false;
	}

	/**
	 * [获取相应id的全部记录]
	 * @param  [mix]   $id   [模板id,可以为数组]
	 * @return [array]     [description]
	 */
	public function getListById($id)
	{
		$input = app('request')->input();
		//先获取redis数据
		$wechatRedis = new Wechat();
		$data = $wechatRedis->getArr($id);
		if(empty($data[0])){
			$obj = $this->model->whereIn('id',$id)->get();
			if(empty($obj)){
				return false;
			}
			$data = $obj->toArray();
			$wechatRedis->setArr($data);
		}
		return $data;
	}

	//获取单条记录
	public function getRowById($id)
	{	
		//先获取redis数据
		$wechatRedis = new Wechat();
		$result = $wechatRedis->getArr([$id]);
		if(!$result){
			$obj = $this->model->wheres(['id'=>$id])->first();
			if(empty($obj)){
				return false;
			}
			$data = $obj->toArray();
		}else{
			$data = $result[0];
		}

		return $data;
	}

	//删除数据
	public function del($id)
	{
		$wechatRedis = new Wechat($id);
		$result = $this->model->wheres(['id'=>$id])->delete();
		if($result){
			$data['id'] = $id;
			$wechatRedis->del($id);
			return true;
		}

		return false;
		
	}
}




?>