<?php
namespace App\S\Wechat;
use App\S\S;

class WeixinNotifyTplService Extends S{

	//设置model
	public function __construct()
	{
		parent::__construct('WeixinNotifyTpl');
	}

	/**
	 * [根据主键id，获取单条微信图文数据]
	 * @param  [int]   $id      [主键id]
	 * @return [type]  $data    [微信图文数据]
     *
     * @update 梅杰 2019年09月24日 09:31:06 修改查询方式
	 */
	public function getRowByAppId($appid, $status,$type=1)
	{
        $data = [];
        $obj = $this->model->where(['appid' => $appid, 'status' => $status, 'type' => $type])->first();
        if ($obj) {
            $data = $obj->toArray();
        }
        return $data;
	}

    public function add($data)
    {
    	return $this->model->insertGetId($data);
    }

	/**
	 * [编辑单条微信图文]
	 * @param  [type]  $id   [要更新对应的主键id]
	 * @param  [array] $data [要更新键值对数据]
	 * @return [int]         [影响的条数]
	 */
    public function updateRowById($id,$data)
    {
    	$returnData = ['errcode' => 0, 'errmsg' => ''];
    	if(empty($id)){
    		$returnData['errcode'] = -1;
    		$returnData['errmsg'] = 'id不能为空';
    		return $returnData;
    	}

    	if(empty($data)){
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }

    	$row = $this->model->wheres(['id' => $id])->update($data);
        return $row;
    }

}
?>