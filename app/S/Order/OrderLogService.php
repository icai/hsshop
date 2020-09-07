<?php
namespace App\S\Order;
use App\S\S;
use App\Lib\Redis\Order;

class OrderLogService extends S{

    protected $icomeData; //统计数组

	//定义使用的model类
	public function __construct()
	{
        parent::__construct('OrderLog');
	}

	/**
	* @author Mr.Wu
    * @version 2017.07.05
    * @param  array   $action   [要统计的操作标识数组]
    	操作：1买家创建订单；2买家付款；3商家发货；4买家确认收货；5买家评价；6买家取消订单；7买家申请退款；8商家同意退款；9商家拒绝退款；10买家取消退款；11系统自动确认收货；12商家关闭交易；13 延期收货 14系统关闭订单 15订单改价 16商家取消订单

     * 订单操作统计 OrderLog表只能统计 订单入账与订单退款：action分别对应2，8;
     * @param  integer $dateTime [时间数组：[统计日期开始时间戳,统计日期结束时间戳]]
     * 默认值：
     * [0, -1] 代表从时间戳为0今日凌晨0点的数据统计
     * 自定义时间戳示例：
     * [1486310400, 1486915200] 代表从2017-02-06 00:00:00到2017-02-13 00:00:00之间的数据统计
     * 
     * @return array [结果数组]
     */
	public function getLogListByWhere($action = [1], $dateTime = [0, -1],$input=[])
	{
		/* 结束时间小于开始时间则将结束时间置为当前时间 */
        if ( !isset($dateTime[1]) || $dateTime[1] < $dateTime[0] ) {
            $dateTime[1] = mktime(0,0,0,date('m'),date('d'),date('Y'));
        }
        $wid = session('wid');
        $where['wid']        = $wid;
        $where['action']     = array('in', $action);
        $where['updated_at'] = array('between', [date('Y-m-d H:i:s',$dateTime[0]), date('Y-m-d H:i:s',$dateTime[1])] ); 
        if ($input) {
            foreach ($input as $key => $value) {
                switch ($key) {
                    case 'oid':
                        $where['oid'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        $orderBy             = 'updated_at';
        $order               = 'DESC';
        $pageSize            = 20;
        $list = $this->getListWithPage( $where, $orderBy, $order, $pageSize );

        return $list;
	}

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-07-18
     */
    public function getListById($idArr=[])
    {
        //先获取redis数据
        $orderRedis = new order();
        $data = $orderRedis->getArr($idArr);

        if(empty($data[0])){
            //redis数据为空，从数据库读取数据
            $obj = $this->model->with('order')->whereIn('id',$idArr)->get();
            if(empty($obj)){
                return [];
            }

            $data = $obj->toArray();
            //把关联order表的数据设置为json保存到redis
            foreach($data as &$val){
                if($val['order']){
                    $val['order'] = json_encode($val['order']);
                }
            }
            $orderRedis->setArr($data);
        }
        return $data;;
    }

    /**
     * 增加条件
     * @param  [array] $input [查询条件数组]
     * @return [type]        [description]
     */
    public function buildWhere($input)
    {
        /* 查询条件数组 */
        $where = [];

        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                if ( empty($value) ) {
                    continue;
                }
                switch ( $key ) {
                    /* 订单号 */
                    case 'order_sn':
                        $obj = D('Order')->select('id')->wheres(['oid' => $value])->first();
                        if($obj){
                            $orderInfo = $obj->toArray();
                            $orderId = $orderInfo['id'];
                            $where['oid'] = $orderId;
                        }else{
                            $where['oid'] = 0;
                        } 
                        break;
                    default:
                        // to do somethings
                        break;
                }
            }
        }
        return $where;
    }

    /**
     * 按时间统计收入金额
     * @author wuxiaoping 2017.09.27
     * @param  array   $action   [要统计的操作标识数组]
     * 1买家创建订单；2买家付款；3商家发货；4买家确认收货；5买家评价；6买家取消订单；7买家申请退款；8商家同意退款；9商家拒绝退款；10买家取消退款；11系统自动确认收货；12商家关闭交易；
     * @param  integer $dateTime [时间数组：[统计日期开始时间戳,统计日期结束时间戳]]
     * @param  string  $dateUnit  [统计日期单位]
     * 'day'    => 按天统计
     * 'month'  => 按月统计
     * 'year'   => 按年计
     * 'week'   => 按周统计【暂不支持】
     * 'hour'   => 按小时统计
     * 'minute' => 按小时统计
     * 'second' => 按小时统计
     * @param  array $dateTime [时间数组：[统计日期开始时间戳,统计日期结束时间戳]]
     * 默认值：
     * [0, -1] 代表从时间戳为0今日凌晨0点的数据统计
     * 自定义时间戳示例：
     * [1486310400, 1486915200] 代表从2017-02-06 00:00:00到2017-02-13 00:00:00之间的数据统计
     * 
     * @return array [统计数据结果数组]
     * @return [type] [description]
     */
    public function statistical($action = [1],$dateTime = [0, -1],$input=[])
    {
        /* 结束时间小于开始时间则将结束时间置为当前时间 */
        if ( !isset($dateTime[1]) || $dateTime[1] < $dateTime[0] ) {
            $dateTime[1] = time();
        }
        $wid = session('wid');
        $totalIcome = $icome = $expend = 0;
        $where['wid']        = $wid; 
        $where['action']     = array('in', $action);;
        $where['updated_at'] = array('between', [date('Y-m-d H:i:s',$dateTime[0]), date('Y-m-d H:i:s',$dateTime[1])] );
        if ($input) {
            foreach ($input as $key => $value) {
                switch ($key) {
                    case 'oid':
                        $where['oid'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        $data = [];
        //分段统计订单收入
        $result = $this->model->select(['oid','action'])->with('order')->wheres($where)->chunk(100, function ($obj) use($icome,$expend,$totalIcome,&$data){
            $orderLogData = $obj->toArray();
            foreach($orderLogData as $val){
                if($val['action'] == 2){
                    $icome += $val['order']['pay_price'];
                }else if($val['action'] == 8){
                    $expend += $val['order']['pay_price'];
                }
            }
            $totalIcome = $icome-$expend;
            $data[] = $totalIcome;
        });

        //总收入
        $totalIcome = (array_sum($data));
        return $totalIcome;        

    }

}




?>