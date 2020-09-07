<?php
namespace App\S\Customer;
use App\S\S;
use App\Lib\Redis\KefuRedis;

class KefuService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('Kefu');
		$this->redis = new KefuRedis();
	}

	/**
     * [获取qq客服列表并进行分页]
     * @author WuXiaoPing
     * @date 2017-09-27
     * @return [array]  $list  [列表数据]
     */
    public function getAllList($param=[],$pageSize=15, $type = 1)
    {
    	$wid = session('wid');
    	$where['wid'] = $wid;
    	if(!empty($param)){
    		foreach ($param as $key => $value) {
    			switch ($key) {
    				case 'qq':
    					if($value){
    						$where['qq'] = ['like','%'.$value.'%'];
    					}
    					break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
    	if($type == 1){
    		$where['qq'] = ['<>',''];
    	}else{
    		$where['telphone'] = ['<>',''];
    	}
		$list = $this->getListWithPage($where,'created_at','DESC',$pageSize);
    	
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

	public function del($id)
	{
		$rs = $this->model->wheres(['id' => $id])->delete();
		if($rs){
			$this->redis->del($id);
			return true;
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
			$data = $this->model->wheres(['id' => $id])->first()->toArray();
			$this->redis->setRow($id,$data);
		}
		return $data;

	}

	/**
	 * 根据条件获取单条记录
	 * @param  array  $where [查询条件]
	 * @return [type]        [description]
	 */
	public function getRowByCondition($condition = [])
	{
		$data = [];
		$where['wid'] = session('wid');
		if ($where) {
			foreach ($condition as $key => $value) {
				switch ($key) {
					case 'qq':
						if($value){
							$where['qq'] = $value;
						}
						break;
					default:
						# code...
						break;
				}
			}
		}

		$obj = $this->model->wheres($where)->first();
		if (empty($obj)) {
			return [];
		}
		$data = $obj->toArray();
		return $this->getRowById($data['id']);	
	}

}