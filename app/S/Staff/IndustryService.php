<?php
namespace App\S\Staff;
use App\S\S;
use App\Lib\Redis\IndustryRedis;

class IndustryService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('Industry');
		$this->redis = new IndustryRedis();
	}

	/**
     * [获取全部的banner图列表并进行分页]
     * @param  [int]    $status   [状态]
     * 1-启用 0-禁用
     * @author WuXiaoPing
     * @date 2017-08-21
     * @return [array]  $list  [banner数据]
     * @update 吴晓平  2019年05月22日 加入标识条件 (sign)
     */
    public function getAllList($is_page = true,$whereData = [], $sign = false)
    {
    	$where = [];
    	if(is_array($whereData) && $whereData){
    		$where['id'] = ['in',$whereData];
    	}
    	if ($sign || $sign === 0) {
    		$where['sign'] = $sign;
    	}
    	if($is_page){
    		$list = $this->getListWithPage($where,'created_at','DESC');
    	}else{
    		$list = $this->getList($where,'','','sort','DESC');
    	}
        
        return $list;

    }

	/**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-08-21
     */
    public function getListById($idArr = [])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
       
        $result = $this->redis->getArr($idArr);

        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }

        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

	//添加数据
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}

	/**
	 * 处理编辑
	 * @param  [int] $id   [主键id]
	 * @param  [array] $data [要更新的数组数据]
	 * @return [type]       [description]
	 */
	public function update($id,$data)
	{
		$rs = $this->model->wheres(['id' => $id])->update($data);
		if($rs){
			$this->redis->updateHashRow($id,$data);
			return true;
		}

		return false;
	}

	/**
	 * 多条数据更新(根据ids)
	 * @author 吴晓平 [wuxiaoping1559@dingtalk.com] at 2019年05月22日
	 * @param  array  $ids  [id数组]
	 * @param  array  $data [更新的数组数据]
	 * @return bool 是否成功
	 */
	public function updateHaveMany($ids, $data)
	{
		if ($this->model->whereIn('id', $ids)->update($data)) {
			foreach ($ids as $id) {
				$this->redis->updateHashRow($id, $data);
			}
			return true;
		}
		return false;
	}

	public function del($id)
	{
		$rs = $this->model->wheres(['id' => $id])->delete();
		if($rs){
			return $this->redis->del($id);
		}

		return false;
	}

	/**
	 * [getRowById description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getRowById($id)
	{
		if(empty($id)){
			error('数据异常');
		}
		$data = [];
		$data = $this->redis->getRow($id);
		if(empty($data)){
			$obj = $this->model->wheres(['id' => $id])->first();
			($obj) && ($data = $obj->toArray());
			$this->redis->setRow($id,$data);
		}
		return $data;

	}
}