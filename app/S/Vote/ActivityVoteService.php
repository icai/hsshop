<?php
namespace App\S\Vote;
use App\S\S;
use App\Lib\Redis\ActivityVoteRedis;

class ActivityVoteService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('ActivityVote');
		$this->redis = new ActivityVoteRedis();
	}

	/**
     * [获取全部数据信息]
     * @return [array]  $list  [banner数据]
     */
    public function getAllList($wid,$whereData = [],$orderBy='')
    {
    	$where['wid'] = $wid;
    	if($whereData){
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
	    			case 'status':
	    				if ($value == 1) { //未开始
	    					$where['start_time'] = ['>',time()];
	    				}else if ($value == 2) { //进行中
	    					$where['start_time'] = ['<=',time()];
	    					$where['end_time']   = ['>=',time()];
	    				}else if ($value == 3) { //已结束
	    					$where['end_time'] = ['<',time()];
	    				}
	    				break;
	    			
	    			default:
	    				break;
	    		}
    		}	
    	}
    	
    	$order = $orderBy ?? 'created_at';
		$list = $this->getListWithPage($where,'created_at','DESC');
    	
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
			$data['id'] = $id;
			$this->redis->updateRow($data);
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
			$obj = $this->model->wheres(['id' => $id])->first();
			if ($obj) {
				$data = $obj->toArray();
				$data['id'] = $id;
				$this->redis->add($data);
			}
		}
		return $data;

	}

}