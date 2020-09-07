<?php
namespace App\S\Cam;
use App\Lib\Redis\CDKeyRedis;
use App\S\S;
use App\Lib\Redis\CamListRedis;
use DB;

class CamListService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('CamList');
		$this->redis = new CamListRedis();
	}

	/**
	 * 获取全部预约列表信息
	 * @param  string $orderBy [description]
	 * @return [type]          [description]
     * @update 2018年8月6日 增加查询字段
	 */
    public function getAllList($whereData=[],$orderBy='',$is_page=true,$pageSize=20)
    {

        $where = [];
    	if ($whereData) {
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
                    case 'cam_id':
                        $where['cam_id'] = $value;
                        break;
                    case 'mid':
                        $where['mid'] = $value;
                        break;
                    case 'is_send':
                        $where['is_send'] = $value;
                        break;
                    case 'oid':
                        $where['oid']=$value;
                        break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
    	if ($is_page) {
    		$list = $this->getListWithPage($where,'','',$pageSize);
    	}else {
    		$list = $this->getList($where);
    	}
        return $list;

    }

    /**
     * 统计库存量
     * @author 吴晓平 <2018.08.06>
     * @param  [type] $camId [卡密活动id]
     * @return [type]        [description]
     */
    public function countStock($camId)
    {
    	$sendTotal = $this->sendTotal($camId);
    	$leftTotal = $this->leftStock($camId);
    	$total = $sendTotal + $leftTotal;

    	return ['sendTotal' => $sendTotal,'leftTotal' => $leftTotal,'total' => $total];
    }

    /**
     * 剩余库存数
     * @param  [type] $camId [description]
     * @return [type]        [description]
     */
    public function leftStock($camId)
    {
    	$leftTotal = $this->model->where(['cam_id' => $camId,'is_send' => 0])->count();
    	return $leftTotal;
    }

    /**
     * 已发送卡密
     * @author 吴晓平 <2018年08月08日>
     * @return [type] [description]
     */
    public function sendTotal($camId)
    {
    	$sendTotal = $this->model->where(['cam_id' => $camId,'is_send' => 1])->count();
    	return $sendTotal;
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
        	$mysqlData = [];
            $obj = $this->model->whereIn('id',$redisId)->get();
            if ($obj) {
            	$mysqlData = $obj->load('getMember')->toArray();
            }
            $mysqlData = array_column($mysqlData, null,'id');
            foreach ($mysqlData as $key => &$value) {
            	if(isset($value['getMember']) && $value['getMember']) {
            		$value['getMember'] = json_encode($value['getMember'],JSON_UNESCAPED_UNICODE);
            	}
            }
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
	 * 批量删除数据
	 * @param  [type] $ids [description]
	 * @return [type]      [description]
	 */
	public function delBatch($ids)
	{
		$where['id'] = ['in',$ids];
		$rs = $this->model->wheres($where)->delete();
		if ($rs) {
			$this->redis->batchDel($ids);
			return true;
		}
	}

	/**
	 * 获取单条数据
	 * @param  [int] $id   [主键id]
	 * @return [type]     [description]
	 */
	public function getRowById($id)
	{
		if(empty($id)){
			return [];
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

	/**
	 * 批量插入数据
	 * @author 吴晓平 2018年08月06日
	 * @param  array $arr 要插入的二维数组数据
	 * @return [type]      [description]
	 */
	public function insertBatch($arr)
	{
		return DB::table($this->model->getTable())->insert($arr);
	}


    /**
     * 将卡密库存放入redis
     * @param $cam_id 活动id
     * @author: 梅杰 2018年8月7号
     */
	public function addList($cam_id)
    {
        $data = $this->model->where(['cam_id'=>$cam_id,'is_send'=> 0])->get(['id'])->toArray();
        $redis = new CDKeyRedis();
        return   $data && $redis->del($cam_id) !== false && $redis->push($cam_id,$data);
    }


    /**
     * 获取卡密
     * @param $cam_id 活动id
     * @return bool|array
     * @author: 梅杰 2018年8月7号
     */
     public function getCdKey($cam_id,$num = 1)
    {
        $redis = new CDKeyRedis();
        $stock = $redis->getLen($cam_id);
        //有库存
        if ($stock >= $num) {
            return $redis->pop($cam_id,$num);
        }
        //库存不足
        if ($stock && $stock == $this->leftStock($cam_id)) {
            return false;
        }

        if ($this->addList($cam_id)) {
            return $redis->pop($cam_id,$num);
        }
        return false;
    }



}