<?php
namespace App\S\Vote;
use App\S\S;
use App\Lib\Redis\EnrollInfoRedis;
use DB;

class EnrollInfoService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('EnrollInfo');
		$this->redis = new EnrollInfoRedis();
	}

	/**
     * [获取全部数据信息]
     * @return [array]  $list  [banner数据]
     */
    public function getAllList($wid,$whereData = [],$orderBy='',$is_page=true)
    {
    	$where['wid'] = $wid;
    	if($whereData){
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
	    			case 'name':
	    				if ($value) {
	    					$where['_string'] = ' enroll_info REGEXP ' ."'" .$value . "'";
	    				}
	    				break;
	    			case 'mobile':
	    				if ($value) {
	    					$where['_string'] = ' enroll_info REGEXP ' ."'" .$value . "'";
	    				}
	    				break;
	    			case 'vote_id':
	    				$where['vote_id'] = $value;
	    				break;
	    			case 'keyword':
	    				if ($value) {
	    					if (is_numeric($value)) {
	    						$where['id'] = $value;
	    					}else{
	    						$where['_string'] = ' enroll_info REGEXP ' ."'" .$value . "'";
	    					}
	    				}
	    				break;
	    			default:
	    				break;
	    		}
    		}	
    	}
    	$order = $orderBy ?? 'created_at';
    	if ($is_page) {
    		$list = $this->getListWithPage($where,$order,'DESC',6);
    	}else{
    		$list = $this->getList($where);
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
	 * 字段vote_num自增1
	 * @param  [int]  $id [主键id]
	 * @param  [array] $where [description]
	 * @return [type]        [description]
	 */
	public function increment($id,$where)
	{
		if ($this->model->wheres($where)->increment('vote_num')) {
			$this->redis->incr($id,'vote_num',1);
			return true;
		}

		return false;
		
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
			$obj = $this->model->wheres(['id' => $id])->first();
			if ($obj) {
				$data = $obj->toArray();
				$data['id'] = $id;
				$this->redis->add($data);
			}
			
		}
		return $data;

	}

	/**
	 * 根据相关条件获取对应的列表信息
	 * @param array $condition 条件数组
	 * @return [type] [description]
	 */
	public function getListByWhereData($wid,$condition,$orderBy='created_at')
	{
		$where['wid'] = $wid;
		foreach ($condition as $key => $value) {
			switch ($key) {
				case 'vote_id':
					$where['vote_id'] = $value;
					break;
				case 'id':
					$where['id'] = $value;
					break;
				case 'name':
					$where['_string'] = ' JSON_CONTAINS(name,' . $value . ') ';
    				break;
    			case 'mid':
					$where['mid'] = $value;
					break;
				default:
					# code...
					break;
			}
		}
		$idArr = $data = [];
		$obj = $this->model->wheres($where)->select('id')->order($orderBy . ' DESC')->get();

		if (!$obj) {
			return [];
		}

		$data = $obj->toArray();
		//获取id数组
		foreach ($data as $key => $value) {
			$idArr[] = $value['id'];
		}
		return $this->getListById($idArr);
	}

	/**
	 * 计算获取冠军的投票数
	 * @return [int] [返回距离最高票数还差几票]
	 */
	public function getMaximum($wid,$vote_id,$mid)
	{
		$allList = $this->getAllList($wid,['vote_id' => $vote_id],'',false);
		$maximum = $currentNums = $leftNums = 0;
		if ($allList) {
			foreach ($allList as $key => $value) {
				//当前的票数
				if ($value['mid'] == $mid) {
					$currentNums = $value['vote_num'];
				}

				$voteTickets[] = $value['vote_num'];
			}

			arsort($voteTickets);
			$maximum = array_shift($voteTickets);
			$leftNums = ($maximum-$currentNums) + 1;
		}

		return $leftNums;
		
	}


    /**
     * 批量更新
     * @param $ids
     * @param $data
     * @return bool
     * @author 张永辉
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redisUpData = [];
            foreach ($ids as $val){
                $redisUpData[] = array_merge($data,['id'=>$val]);
            }
            return $this->redis->updateArr($redisUpData);
        }else{
            return false;
        }
    }



}