<?php
namespace App\S\WXXCX;
use App\S\S;
use App\Lib\Redis\XCXStatisticsLogRedis;

class XCXStatisticsLogService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('XCXStatisticsLog');
		$this->redis = new XCXStatisticsLogRedis();
	}

	/**
	 * 获取全部统计log日志
	 * @param  string $orderBy [description]
	 * @return [type]          [description]
	 */
    public function getAllList($whereData=[],$is_page=true,$pageSize=15)
    {
    	$where = [];
    	if ($whereData) {
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
    				case 'status':
    					if ($value) {
    						$where['status'] = $value;
    					}
    					break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
    	if ($is_page) {
    		$list = $this->getListWithPage($where,'id','DESC',$pageSize);
    	}else {
    		$list = $this->getList($where);
    	}
		
        return $list;
    }

    /**
     * 获取统计失败列表数据
     * @param  array  $whereData [数组条件]
     * @return [type]            [description]
     */
    public function getAllErrorList($whereData=[])
    {
    	$where['status'] = 0;
        $where['log'] = ['like','%system error%'];
    	$where['start_date'] = ['between',[date('Ymd',strtotime('-7 days')),date('Ymd',time())]];
    	if ($whereData) {
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
    				case 'wid':
    					$where['wid'] = $value;
    					break;
    				case 'start_date':
    					$where['start_date'] = $value;
    					break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
    	$list = $this->model->wheres($where)->groupBy('start_date')->orderBy('start_date','desc')->get()->toArray();
    	return $list;
    }

    /**
     * 验证是否已存在数据
     * @author 吴晓平 <2018年07月06日>
     * @param  array  $where [数组条件]
     * @return boolean        [description]
     */
    public function isHaveData($where=[])
    {
    	$id = 0;
    	$obj = $this->model->where($where)->first();
    	if ($obj) {
    		$rs = $obj->toArray();
    		$id = $rs['id'];
    	}

    	return $id;
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
		$whereData['wid']        = $data['wid'];
		$whereData['start_date'] = $data['start_date'];
		$whereData['status']     = $data['status'];
		$rs = $this->isHaveData($whereData);
		if ($rs) {
			return $this->update($rs,$data);
		}else {
			return $this->model->insertGetId($data);
		}
		
	}

	/**
	 * 处理编辑
	 * @param  [int] $id   [主键id]
	 * @param  [array] $data [要更新的数组数据]
	 * @return [type]       [description]
	 */
	public function update($id,$data)
	{
		$rs = $this->model->where(['id' => $id])->update($data);
		if($rs){
			$this->redis->updateHashRow($id,$data);
			return true;
		}

		return false;
	}

	/**
	 * 删除数据
	 * @param  [int] $id   [主键id]
	 * @return [type]     [description]
	 */
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
	 * 获取单条数据
	 * @param  [int] $id   [主键id]
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
			if ($obj) {
				$data = $obj->toArray();
				$this->redis->setRow($id,$data);
			}		
		}
		return $data;

	}

}