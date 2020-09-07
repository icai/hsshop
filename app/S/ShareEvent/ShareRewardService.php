<?php
namespace App\S\ShareEvent;
use App\S\S;
use App\Lib\Redis\ShareRewardRedis;

class ShareRewardService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('ShareReward');
		$this->redis = new ShareRewardRedis();
	}

	/**
     * [获取案例列表并进行分页]
     * @param  [int]    $status   [状态]
     * 1-启用 0-禁用
     * @author WuXiaoPing
     * @date 2017-08-21
     * @return [array]  $list  [banner数据]
     */
    public function getAllList($wid,$orderBy='',$is_page=true)
    {
    	$where['wid'] = $wid;
    	$order = $orderBy ?? 'created_at';
    	if ($is_page) {
    		$list = $this->getListWithPage($where,$order,'DESC');
    	}else{
    		$list = $this->getlist($where);
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
	 * 获取红包助减金额
	 * @return [type] [description]
	 */
	public function getReduceSum($wid)
	{
		$list = $this->getAllList($wid);
		$reduceSum = 0;
		if ($list[0]['data']) {
			$data = $list[0]['data'];
			if ($data[0]['is_open'] == 1) {
				if ($data[0]['type'] == 0) {
					$num       = $data[0]['fixed_money'];
				}else {
					$num = $this->getRate($data[0]['minimum'],$data[0]['maximum']);
				}
				$reduceSum = sprintf("%.2f", $num);
			}
		}
		return $reduceSum;
	}

	/**
	 * 随机领取红包规则
	 * @param  [int] $min [随机最小值]
	 * @param  [int] $max [随机最大值]
	 * @return [type]      [description]
	 */
	public function getRate($min,$max){
    	$result = [];
    	for($i=1;$i<=100;$i++){
    		if ($i == 1) {
	    		$start = ($min+($max-$min)*($i/100)) - 0.01;
	    		$end = $min+($max-$min)*($i/100);
	    		$val = 44.5;
	    	}else {
	    		if ($i == 2) {
	    			$val = 40;
	    		}else if ($i >= 3 && $i <= 5){
	    			$val = 2;
	    		}else if ($i >= 6 && $i <= 10){
	    			$val = 1;
	    		}else if ($i >= 11 && $i <= 15){
	    			$val = 0.5;
	    		}else if ($i >= 16 && $i <= 25){
	    			$val = 0.1;
	    		}else if ($i >= 26 && $i <= 50){
	    			$val = 0.02;
	    		}else {
	    			$val = 0.01;
	    		}
	    		$start = $min+($max-$min)*(($i-1)/100);
	    		$end = $min+($max-$min)*($i/100);
	    	}

	    	$result[$start.'-'.$end] = $val;
    	}

    	$items = '';    
	    //概率数组的总概率精度   
	    $proSum = array_sum($result);    
	    //概率数组循环   
	    foreach ($result as $key => $proCur) {
	    	if ($proSum > 1) {
	    	   	$randNum = mt_rand(1, $proSum);   
		        if ($randNum <= $proCur) {   
		            $items = $key;   
		            break;   
		        } else {   
		            $proSum -= $proCur;   
		        }  
    	    }          
	    }   
	    unset ($result); 
	    $rangeArr = [];
	    if ($items) {
	    	$rangeArr = explode('-',$items); 
	    	$keys = array_rand($rangeArr); 
	    	$reduceSum = $rangeArr[$keys];
	    }else {
	    	$reduceSum = ($min+($max-$min)*0.01) - 0.01;
	    }

	    return $reduceSum;
    }

}