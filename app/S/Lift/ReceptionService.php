<?php
namespace App\S\Lift;
use App\S\S;
use App\Lib\Redis\ReceptionRedis;
use App\S\Foundation\RegionService;
use ProductService;

class ReceptionService extends S{

	protected $redis;

	public function __construct()
	{
		parent::__construct('Reception');
		$this->redis = new ReceptionRedis();
	}

	/**
	 * 获取全部列表信息
	 * @param  string $orderBy [description]
	 * @return [type]          [description]
	 */
    public function getAllList($wid,$whereData=[],$orderBy='',$is_page=true,$pageSize=15)
    {
    	$where['wid'] = $wid;
    	$order = $orderBy ?? 'created_at';
    	if ($whereData) {
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
    				case 'title':
    					$where['title'] = ['like','%'.$value.'%'];
    					break;
                    case 'city_id':
                        $where['city_id'] = $value;
                        break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
    	if ($is_page) {
    		$list = $this->getListWithPage($where,$order,'DESC',$pageSize);
    	}else {
    		$list = $this->getList($where,'','',$order,'DESC');
    	}
		
        return $list;
    }

    /**
     * [统计店铺自提点的数目]
     * @author 吴晓平 <2018年07月20日>
     * @param  int  $wid [店铺id]
     * @return [type]            [description]
     */
    public function countList($wid=0)
    {
        $where['wid'] = $wid;
        return $this->model->where($where)->count();
    }

    /**
     * 根据自提列表距离远近返回数据
     * @param  [array]  $from  [起点坐标(经纬度),例如:array(118.012951,36.810024)] 
     * @param  [int]  $wid       [店铺id]
     * @param  [array]  $whereData [查询条件]
     * @param  string  $orderBy   [排序字段]
     * @param  boolean $is_page   [是否分页]
     * @return [type]             [description]
     */
    public function getListSort($from,$wid,$whereData=[],$orderBy='',$is_page=false)
    {
        if (empty($from) || !is_array($from)) {
            error('数据异常');
        }
        $list = $this->getAllList($wid,$whereData,$orderBy,$is_page);
        $range = [];
        foreach ($list as $key => &$value) {
            $distance = get_distance($from,[$value['longitude'],$value['latitude']]);
            $range[] = $distance;
            $value['distance'] = $distance.'km';
        }
        array_multisort($range,SORT_ASC,$list);
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

	/**
	 * 处理自提列表信息
	 * @param  [int] $wid   [店铺id]
	 * @param  [array] $where [条件数组]
     * @param  [array] $from  定位所在位置数组 经纬度 例：[120.227008,30.274044]
	 * @return [type]        [description]
	 */
	public function dealZitiList($wid,$where=[],$from=[])
	{
		$returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
		$list = $this->getAllList($wid,$where,'',false);
        if ($list) {
            $range = [];
            foreach ($list as $key => &$value) {
                if ($from) {
                    $distance = get_distance($from,[$value['longitude'],$value['latitude']]); //计算距离
                    $range[] = $distance;
                    $value['distance'] = $distance;
                }
                $temp[] = $value['province_id'];
                $temp[] = $value['city_id'];
                $temp[] = $value['area_id'];
            }
            //对自提列表信息按离目标位置的距离排序（由近到远）
            if (!empty($range)) {
                array_multisort($range,SORT_ASC,$list);
            }
            $regionService = new RegionService();
            $region = $regionService->getListById($temp);

            $tmpAddr = [];
            foreach ($region as $val){
                $tmpAddr[$val['id']] = $val['title'];
            }
            foreach ($list as $key=>$item){
                $citys[$key]['id'] = $item['city_id'];
                $citys[$key]['city'] = $tmpAddr[$item['city_id']];
                $data[$key]['id']        = $item['id'];
                $data[$key]['title']     = $item['title'];
                $data[$key]['province']  = $tmpAddr[$item['province_id']];
                $data[$key]['city']      = $tmpAddr[$item['city_id']];
                $data[$key]['area']      = $tmpAddr[$item['area_id']];
                $data[$key]['address']   = $item['address'];
                $data[$key]['distance']  = isset($item['distance']) && $item['distance'] ? $item['distance'] : 0;
            }
            $citys = $this->array_unique_city($citys); //去重相同的城市
            $returnData['data']['datas'] = $data;
            $returnData['data']['citys'] = $citys;
        }else {
            $returnData['errCode'] = -2;
            $returnData['errMsg']  = '该店铺未有自提点信息，请联系商家';
            return $returnData;
        }

        return $returnData;
	}

    /**
     * 去重城市名称显示
     * @param  [type] $citys [description]
     * @return [type]        [description]
     */
    public function array_unique_city($citys){  
        foreach ($citys as $k => $v){  
            $v =join(",",$v);    //降维 
            $temp[$k] =$v;      //保留原来的键值 
        }  
        $temp =array_unique($temp);    //去重：去掉重复的字符串   
        foreach ($temp as $k => $v){  
            $a = explode(",",$v);   //拆分后的重组 
            $arr_after[$k]['id'] = $a[0];
            $arr_after[$k]['city'] = $a[1];
        } 
        return $arr_after;  
    } 

    /**
     * 判断购物车中的商品是否可以一块下单，或者只能选择配送或自提
     * @author wuxiaoping 2018.05.22
     * @param  array  $cartProducts [购物车中商品信息]
     * @return boolean               [description]
     * @update 吴晓平 2018年07月19日 自提二期数据优化修改
     */
    public function isZitiProduct($cartProducts)
    {
        /**
         * 后台每个商品添加时都会设置物流，所以店铺每个商品都是可以发送物流
         * status状态说明：status==0 表示购物车一块下单的商品只能选择默认的物流；status==1 表示购物车中的商品可以全部自提或发送物流
         * status==2 表示购物车中商品部分可以自提，或都全部发送物流；status==3 表示购物车中商品部分只能自提
         */
        $returnData = ['status' => 0, 'hint' => '', 'data' => []];
        if (empty($cartProducts)) {
           $returnData['status'] = -1;
           $returnData['hint'] = '购物车没有商品';
           return $returnData;
        }
        $goodsNum = count($cartProducts);
        $zitiProducts = $mixProducts = $logisticsProducts = [] ;

        if ($goodsNum == 1) { //购物车中只有一种商品（数量可以有多个）
            $detail = ProductService::getDetail($cartProducts[0]['product_id']);
            if ($detail['is_hexiao'] == 1 && $detail['is_logistics'] == 1) { //商品开启了自提,并且配送方式选择物流
                $returnData['status'] = 1; 
                $data['ziti']       = $cartProducts;
                $data['Logistics']  = $cartProducts;
                $data['all']        = $cartProducts;
                $returnData['data'] = $data;
            }else if($detail['is_hexiao'] == 1 && $detail['is_logistics'] == 0) { //商品开启了自提,并且配送方式选择无需物流
                $returnData['status'] = 3; 
                $data['ziti']       = $cartProducts;
                $data['Logistics']  = [];
                $data['all']        = $cartProducts;
                $returnData['data'] = $data;
            }else if($detail['is_hexiao'] == 0 && $detail['is_logistics'] == 1) { //商品未开启了自提,并且配送方式只能选择物流
                $data['ziti']       = [];
                $data['Logistics']  = $cartProducts;
                $data['all']        = $cartProducts;
                $returnData['data'] = $data;
            }else {
                $returnData['status'] = -2;
                $returnData['hint'] = '物流数据异常(不是自提商品未选择物流)';
                return $returnData;
            }
        }else {
            //分别取出自提，物流的购物车商品数
            foreach ($cartProducts as $key => $value) {
                $detail = ProductService::getDetail($value['product_id']);
                if ($detail['is_hexiao'] == 1) {
                    if ($detail['is_logistics'] == 0) { //开启自提,配送方式选择无需物流(纯自提)
                        $zitiProducts[] = $value; // (纯自提)
                    }else {
                        $mixProducts[] = $value;  // (可自提或物流)
                    }
                }else {
                    if ($detail['is_logistics'] == 0) {
                        $returnData['status'] = -2;
                        $returnData['hint'] = '物流数据异常(不是自提商品未选择物流)';
                        return $returnData;
                    }
                    $logisticsProducts[] = $value; // (纯物流)
                }   
            }
 
            //判断购物车中的商品是否可以既可以自提也可发物流
            if (!empty($mixProducts)) {
                $returnData['status'] = 2; 
                $data['newestZiti']   = $zitiProducts;
                $data['newestLogis']  = $logisticsProducts;
                $data['ziti']         = array_merge($zitiProducts,$mixProducts);
                $data['Logistics']    = array_merge($logisticsProducts,$mixProducts);
                $data['all']          = $cartProducts;
                $returnData['data']   = $data;
            }else {
                //判断购物车中的商品是否可以一起下单
                if ($zitiProducts && $goodsNum == count($zitiProducts)) {
                    $returnData['status'] = 3; // 全部商品只能选择自提方式
                    $data['ziti']       = $zitiProducts;
                    $data['Logistics']  = [];
                    $data['all']        = $cartProducts;
                    $returnData['data'] = $data;
                }else if ($logisticsProducts && $goodsNum == count($logisticsProducts)) {
                    $data['ziti']       = [];
                    $data['Logistics']  = $logisticsProducts;
                    $data['all']        = $cartProducts;
                    $returnData['data'] = $data;
                }else {
                    $returnData['status'] = 2; //表示多商品既可以自提也可发物流
                    //$diffData = $this->get_diff_array_by_filter($cartProducts,$zitiProducts);
                    foreach ($logisticsProducts as &$value) {
                        $value['reason'] = '当前商品不支持自提';
                    }

                    foreach ($zitiProducts as $key => &$value) {
                        $value['reason'] = '当前商品不支持物流';
                    }
                    $data['newestZiti']  = $zitiProducts;
                    $data['newestLogis'] = $logisticsProducts;
                    $data['ziti']       = $zitiProducts;
                    $data['Logistics']  = $logisticsProducts;
                    $data['all']        = $cartProducts;
                    $returnData['data'] = $data;
                }
            } 
        }
        return $returnData;
    }

    /**
     * 比较两个二维数据，并取出差集
     * @author wuxiaoping 2018.05.22
     * @param  [array] $arr1 [数组1]
     * @param  [array] $arr2 [数组2]
     * @return [type]       [description]
     */
    public function get_diff_array_by_filter($arr1,$arr2){
        try{
            return array_filter($arr1,function($v) use ($arr2){
                return !in_array($v,$arr2);
            });
        }catch (\Exception $exception){
            return $arr1;
        }
    }

	/**
	 * 显示时间范围设置
	 * @param  [array] $zitiTimes [时间范围数组]
	 * @return [type]            [description]
	 */
	public function getZitiDates($zitiTimes,$date='')
	{
		//定义固定显示的选择时间范围
        $timeArr = [
            '09:00-09:30','09:30-10:00','10:00-10:30','10:30-11:00','11:00-11:30','11:30-12:00','12:00-12:30',
            '12:30-13:00','13:00-13:30','13:30-14:00','14:00-14:30','14:30-15:00','15:00-15:30','15:30-16:00',
            '16:00-16:30','16:30-17:00','17:00-17:30','17:30-18:00','18:00-18:30','18:30-19:00','19:00-19:30',
            '19:30-20:00','20:00-20:30','20:30-21:00','21:00-21:30','21:30-22:00','22:00-22:30','22:30-23:00',
            '23:00-23:30','23:30-24:00'
        ];
        $days = '';
        $spaceSec = $returnTimes = [];
        $currentTime = date('H:i',time()); //当前的时间点
        foreach ($zitiTimes as $key => $value) {
            $days .= join(',',$value['days']).',';
            $spaceSec = strtotime($value['endTime'])-strtotime($value['startTime']);
            $spaceNum = ($spaceSec/3600)/0.5;
            //表示后台设置的时间间隔为一小时
            if ($spaceNum == 1) {
                $setTimes[] = $value['startTime'].'-'.$value['endTime'];
            }else {
                for ($i=1;$i<=$spaceNum;$i++) {
                    $setTimes[] = date('H:i',(strtotime($value['startTime'])+($i-1)*30*60)).'-'.date('H:i',strtotime($value['startTime'])+$i*30*60);
                }
            }
        }
        /***选择时间范围头部导航设置***/
        $days = substr($days, 0,-1);
        $days = explode(',',$days);
        //定义一个星期数组
        $weekData = [1 => '周一', 2 => '周二', 3 => '周三', 4 => '周四', 5 => '周五', 6 => '周六', 7 => '周日'];
        $restDays = $this->get_diff_array_by_filter(array_keys($weekData),$days);
        $returnTimes = [];
        for ($i=0; $i<5 ; $i++) { 
            switch ($i) {
                case 0:
                    $returnDates[$i]['status'] = 0;
                    $week = date('N',time());
                    if (in_array($week,$days)) {
                        $returnDates[$i]['status'] = 1;
                    }
                    $returnDates[$i]['ex'] = '今天';
                    $returnDates[$i]['date'] = date('m-d',time());
                    if ($returnDates[$i]['status'] == 1) {
                        foreach ($timeArr as $key => $time) {
                            $cutTimes = explode('-',$time);
                            if (strtotime($cutTimes[0]) > strtotime($currentTime)) {
                                if (in_array($time,$setTimes)) {
                                    $returnTimes[$key]['time'] = $time;
                                    $returnTimes[$key]['status'] = 1;
                                }else {
                                    $returnTimes[$key]['time'] = $time;
                                    $returnTimes[$key]['status'] = 0;
                                }
                            }else {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 0;
                            }
                            
                        }
                    }
                    $returnDates[$i]['timeLimit'] = $returnTimes;
                    break;

                case 1:
                    $returnDates[$i]['status'] = 0;
                    $week = date('N',strtotime('+1 day'));
                    if (in_array($week,$days)) {
                        $returnDates[$i]['status'] = 1;
                    }
                    $returnDates[$i]['ex'] = '明天';
                    $returnDates[$i]['date'] = date('m-d',strtotime('+1 day'));
                    if ($returnDates[$i]['status'] == 1) {
                        foreach ($timeArr as $key => $time) {
                            $cutTimes = explode('-',$time);
                            if (in_array($time,$setTimes)) {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 1;
                            }else {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 0;
                            }
                        }
                    }
                    $returnDates[$i]['timeLimit'] = $returnTimes;
                    break;
                case 2:
                    $returnDates[$i]['status'] = 0;
                    $week = date('N',strtotime('+2 day'));
                    if (in_array($week,$days)) {
                        $returnDates[$i]['status'] = 1;
                    }
                    $returnDates[$i]['ex'] = '后天';
                    $returnDates[$i]['date'] = date('m-d',strtotime('+2 day'));
                    if ($returnDates[$i]['status'] == 1) {
                        foreach ($timeArr as $key => $time) {
                            $cutTimes = explode('-',$time);
                            if (in_array($time,$setTimes)) {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 1;
                            }else {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 0;
                            }
                        }
                    }
                    $returnDates[$i]['timeLimit'] = $returnTimes;
                    break;
                case 3:
                    $returnDates[$i]['status'] = 0;
                    $week = date('N',strtotime('+3 day'));
                    if (in_array($week,$days)) {
                        $returnDates[$i]['status'] = 1;
                    }
                    $returnDates[$i]['ex'] = $weekData[date('N',strtotime('+3 day'))];
                    $returnDates[$i]['date'] = date('m-d',strtotime('+3 day'));
                    if ($returnDates[$i]['status'] == 1) {
                        foreach ($timeArr as $key => $time) {
                            $cutTimes = explode('-',$time);
                            if (in_array($time,$setTimes)) {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 1;
                            }else {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 0;
                            }
                        }
                    }
                    $returnDates[$i]['timeLimit'] = $returnTimes;
                    break;
                case 4:
                    $returnDates[$i]['status'] = 0;
                    $week = date('N',strtotime('+4 day'));
                    if (in_array($week,$days)) {
                        $returnDates[$i]['status'] = 1;
                    }
                    $returnDates[$i]['ex'] = $weekData[date('N',strtotime('+4 day'))];
                    $returnDates[$i]['date'] = date('m-d',strtotime('+4 day'));
                    if ($returnDates[$i]['status'] == 1) {
                        foreach ($timeArr as $key => $time) {
                            $cutTimes = explode('-',$time);
                            if (in_array($time,$setTimes)) {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 1;
                            }else {
                                $returnTimes[$key]['time'] = $time;
                                $returnTimes[$key]['status'] = 0;
                            }
                        }
                    }
                    $returnDates[$i]['timeLimit'] = $returnTimes;
                    break;
                
                default:
                    break;
            }
        }
        if ($date) {
            $specifyDate['status'] = 0;
            $week = date('N',strtotime($date));
            if (in_array($week,$days)) {
                $specifyDate['status'] = 1;
            }
            $specifyDate['ex'] = $weekData[$week];
            $specifyDate['date'] = date('m-d',strtotime($date));
            if ($specifyDate['status'] == 1) {
                foreach ($timeArr as $key => $time) {
                    $cutTimes = explode('-',$time);
                    if (in_array($time,$setTimes)) {
                        $returnTimes[$key]['time'] = $time;
                        $returnTimes[$key]['status'] = 1;
                    }else {
                        $returnTimes[$key]['time'] = $time;
                        $returnTimes[$key]['status'] = 0;
                    }
                }
            }
            $specifyDate['timeLimit'] = $returnTimes;
        }
        $returnDate['data']['dates']    = $returnDates;   // 返回选择头部导航日期选择限制 
        $returnDate['data']['restDays'] = array_values($restDays);      // 返回一周时间，周几是休息
        $returnDate['data']['specifyDate'] = isset($specifyDate) && !empty($specifyDate) ? $specifyDate : [];
        return $returnDate;
	}

}