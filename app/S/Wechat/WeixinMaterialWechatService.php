<?php
namespace App\S\Wechat;
use App\S\S;
use App\Lib\Redis\WeixinMaterial;

class WeixinMaterialWechatService Extends S{

	//设置model
	public function __construct()
	{
		parent::__construct('WeixinMaterialWechat');
	}

	/**
	 * [获取某个店铺下的全部微信图文数据，可以进行分页]
	 * @param  [int]    $wid   [店铺id]
	 * @param  [array]  $whereData [条件关联数组]
	 * @param  $is_page 是否进行分页
	 * @return [array]  $list  [微信图文数据]
	 */
	public function getAllList($wid,$whereData = [],$is_page = true)
	{	
		$where = [];
		if(!empty($whereData)){
			$where = ($where + $whereData);
		}
		$where['wid'] = $wid;
		if($is_page){
			$list = $this->getListWithPage($where);
		}else{
			$list = $this->getList($where);
		}
		
		return $list;
	}

	/**
	 * [根据主键id，获取单条微信图文数据]
	 * @param  [int]   $id      [主键id]
	 * @return [type]  $data    [微信图文数据]
	 */
	public function getRowById($id)
	{	
		$data = [];
		$obj = $this->model->wheres(['id' => $id])->first();
		if($obj){
			$data = $obj->toArray();
		}

		return $data;
	}

	/**
	 * [根据上级id获取全部的二级数据]
	 * @param  [int]   $pid [上级的主键id(parent_id)]
	 * @return [array] $data      [多图文数据]
	 */
	public function getChildList($pid)
	{
		$data = [];
		$obj = $this->model->wheres(['parent_id' => $pid])->get();
		if($obj){
			$data = $obj->toArray();
		}

		return $data;

	}
	/**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-07-25
     */
	public function getListById($idArr = [])
	{
		$wechatRedis = new WeixinMaterial();
		$redisData = $mysqlData = [];
        $redisId = [];
       
        $result = $wechatRedis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $wechatRedis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
	}


	/**
     * 数组转树形结构
     * 
     * @param  array   $list  [数据数组]
     * @param  string  $pk    [主键字段名称]
     * @param  string  $pid   [父级字段名称]
     * @param  string  $child [子级下标]
     * @param  integer $root  [顶级id]
     * @return array          [处理后的数组]
     */
    public function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = [];

        if ( is_array($list) && count($list) ) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $value) {
                $refer[$value[$pk]] =& $list[$key];
                $refer[$value[$pk]][$child] = [];
            }
            foreach ($list as $key => $value) {
                // 判断是否存在parent
                $parentId = $value[$pid];
                if ( $root == $parentId ) {
                    $tree[] =& $list[$key];
                } else {
                    if ( isset( $refer[$parentId] ) ) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }

        return $tree;
    }


    /**
     * 添加单条微信单图文
     * @param [int] 插入数据库的主键id值
     */
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
    public function update($id,$data)
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
    	$wechatRedis = new WeixinMaterial();

		//redis永远不更新的bug fixed by Herry
		/*if(!$row){
        	$result = $this->getRowById($id);
        	$wechatRedis->updateHashRow($id, $result);
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }*/

		$result = $this->getRowById($id);
		$wechatRedis->updateHashRow($id, $result);

        return $returnData;
    }

    /**
     * 根据图文id删除信息
     * @param  [int] $id [description]
     * @return [type]     [description]
     */
    public function del($id)
    {
    	$ids = [];
    	$obj = $this->model->select('id')->wheres(['parent_id' => $id])->get();
    	if($obj){
    		$arr = $obj->toArray();
    		foreach($arr as $val){
    			$ids[] = $val['id'];
    		}
    	}
    	$ids[] = $id;
    	$rs = $this->model->wheres(['id' => $id])
    		->orWhere(function($query) use ($id){
    			$query->wheres(['parent_id' => $id]);
    		})->delete();
    	if($rs){
    		$wechatRedis = new WeixinMaterial();
    		foreach($ids as $id){
    			$wechatRedis->del($id);
    		}
    		return true;	
    	}
    	return false;
    }
	//手动分页ajax请求
	public function getListPage($where = [], $orderBy = '', $order = '',$page = 1 ,$pageSize = 1)
	{
		$skip = ($page  - 1)*$pageSize;
		return $this->getList($where, $skip, $pageSize, $orderBy, $order);
	}

    /**
     * 批量删除
     * @author 吴晓平 [wuxiaoping1559@dingtalk.com] at 2019年08月13日
     * @param  [type] $ids [主键数组id]
     * @return boolean
     */
    public function deleteArr($ids)
    {
        $wechatRedis = new WeixinMaterial();
        if ($wechatRedis->deleteArr($ids)) {
            return $this->model->whereIn('id', $ids)->delete();
        }
        return false;

    }

}

 ?>