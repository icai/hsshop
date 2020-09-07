<?php
namespace App\S\Order;
use App\S\S;
use App\Lib\Redis\HexiaoLogRedis;

class HexiaoLogService extends S{


	//定义模型类
	public function __construct()
	{
		parent::__construct('HexiaoLog');
	}

	public function getAllList($wid,$whereData = [],$is_page = true)
	{
		$where = [];
        $where['wid'] = $wid;
        if($whereData){
            foreach ($whereData as $key => $item) {
                switch($key){
                    case 'status':
                        if(!empty($item)){
                            $where['status'] = $item;
                        }
                        break;
                    case 'field':
                        if(!empty($whereData['searchVal'])){
                            if($item == 'telphone'){
                                $where['telphone'] = $whereData['searchVal'];
                            }else if($item == 'code'){
                                $where['code'] = $whereData['searchVal'];
                            }else if($item == 'orderNo'){
                                $where['order_sn'] = $whereData['searchVal'];
                            }else if($item == 'name'){
                                $where['name'] = $whereData['searchVal'];
                            }
                        }
                        break;
                    case 'start_time':
                        if(!empty($item) && !empty($whereData['end_time'])){
                            $where['created_at'] = ['between',[$item,$whereData['end_time']]];
                        }else if(empty($item) && !empty($whereData['end_time'])){
                            $where['created_at'] = ['<',$whereData['end_time']];
                        }else if(!empty($item) && empty($whereData['end_time'])){
                            $where['created_at'] = ['>',$item];
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        //dd($where);
        //是否分页
        $list = [];
        if($is_page){
            $list = $this->getListWithPage($where);
        }else{
            $list = $this->getList($where,'','','id','ASC');
        }
        
        return $list;
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
		$HexiaoLogRedis = new HexiaoLogRedis();
		$redisData = $mysqlData = [];
        $redisId = [];

        $result = $HexiaoLogRedis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');

            $HexiaoLogRedis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
	}

	//添加数据
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}

	/**
	 * 创建核销码
	 * @return [type] [description]
	 */
	public function createCode()
	{
		$code = uniqid();
        $code = str_shuffle($code);
        $code = 'hx_'.substr($code,0,8);
		return $code;
	}

	/**
	 * 根据订单的id更新状态
	 * @param  [array] $orderId [订单的数组id]
	 * @param  [array] $data    [要更新的数组数据]
	 * @return [type]          [description]
	 */
	public function saveByOid($orderId,$data)
	{
		$returnData = ['errcode' => 0, 'errmsg' => ''];
    	if(empty($orderId)){
            $returnData['errcode'] = -1;
            $returnData['errmsg']  = '订单id不能为空';
    		return $returnData;
    	}

    	if(empty($data)){
            $returnData['errcode'] = -2;
            $returnData['errmsg']  = '更新的数据不能为空';
            return $returnData;
        }
        //保存更新redis数据
        $orderData = [];
        if($rs = $this->model->wheres(['oid' => $orderId])->update($data)){

            $orderData = $this->model->wheres(['oid' => $orderId])->get()->toArray();
            //更新redis操作
            (new HexiaoLogRedis())->setArr($orderData);
        }else{
            $returnData['errcode'] = -3;
            $returnData['errmsg']  = '订单更新失败';
            return $returnData;
        }

        return $returnData;
	}

    /**
     * 根据订单id获取数据列表
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function getListByOid($orderId)
    {
        if(empty($orderId)){
            error('订单不存在');
        }
        $data = [];
        $hexiaoLogRedis = new HexiaoLogRedis();
        $data = $hexiaoLogRedis->getRow($orderId);
        if(empty($data)){
            $obj = $this->model->wheres(['oid' => $orderId])->get();
            if($obj){
                $data = $obj->toArray();
                $hexiaoLogRedis->updateHashRow($orderId,$data);
            }
        }
        return $data; 
    }




}



?>