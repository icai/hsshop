<?php
namespace App\S\Order;
use App\S\S;
use App\Lib\Redis\Order;
use DB;
use App\Lib\BLogger;

class OrderService extends S{


	//定义模型类
	public function __construct()
	{
		parent::__construct('Order');
	}

	/**
	 * 
	 * @return [type] [description]
	 */
	public function getOrderListByIds($ids)
	{
		$return = [];
		$orderRedis = new Order();
		$return  =  $orderRedis->getArr($ids);
		if(empty($return[0])){
			$where['id'] = ['in',$ids];
			$obj = $this->model->wheres($where)->get();
			if($obj){
				$return = $obj->toArray();
				$orderRedis->setArr($return);
			}
		}
		return $return;
	}

	/*根据订单id获取订单详情信息*/
	public function getOrderDetail($orderId)
	{
		$return = $this->model->find($orderId);
        if (!empty($return)) {
            return $return->orderDetail()->get()->toArray();
        }
        return [];
	}

    public function orderMidDetail( $wid,$mid )
    {
        $return = $this->model->wheres([ 'wid'=>$wid, 'mid'=>$mid])->orderBy('id','desc')->limit(10)->get();
        if (!empty($return)) {
            return $return->load('orderDetail')->toArray();
        }
        return [];
    }

	/**
	 * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
	 * @author mafanding
	 */
	public function find($id)
	{
		return $this->model->find($id);
	}

	public function getRowByWhere($where = [])
	{
		$data = $this->getList($where);
		return $data[0] ?? $data;
	}

    public function getListById($ids)
    {
        $return = [];
        $orderRedis = new Order();
        $return  =  $orderRedis->getArr($ids);
        if(empty($return[0])){
            $where['id'] = ['in',$ids];
            $obj = $this->model->wheres($where)->get();
            if($obj){
                $return = $obj->toArray();
                $orderRedis->setArr($return);
            }
        }
        return $return;
    }

    /**
	 * todo 每个月的交易统计
     * @param int $wid
     * @param string $beginDate
     * @param string $endDate
     * @return array
	 * @author jonzhang
	 * @date 2017-11-09
     */
    public  function statXCXOrderTradeForMonthly($wid=0,$beginDate='',$endDate='')
    {
        $yesterday=date("Y-m", strtotime($beginDate));
        $beginDate=date("Y-m", strtotime("-1 month", strtotime($beginDate)));
        $endDate=date("Y-m", strtotime("+1 month", strtotime($beginDate)));
        $sql=" select date_format(created_at,'%Y-%m') as stat_date ,";
        $sql.=" count(1) as cnt ,";
        $sql.=" count(if (status=1 or status=2 or status=3,true,null)) as pay_cnt, ";
        $sql.=" count(distinct mid) as mid_cnt , ";
        $sql.=" count(distinct if(status=1 or status=2 or status=3,mid,null)) as pay_mid_cnt, ";
        $sql.=" sum(pay_price) as total_amount, ";
        $sql.=" sum(if(status=1 or status=2 or status=3 ,pay_price,0)) as pay_total_amount,";
        $sql.=" convert(ifnull(sum(if(status=1 or status=2 or status=3 ,pay_price,0)) / count(distinct if(status=1 or status=2 or status=3,mid,null)),0) ,decimal(10,2)) as avg_price ,";
        $sql.=" convert(ifnull(count(if (status=3,true,null))/count(if (status=1 or status=2 or status=3,true,null)),0) ,decimal(10,2)) as pay_rate, ";
        $sql.=" convert(ifnull(count(if (status=3,true,null))/ count(1),0) ,decimal(10,2))as order_rate ";
        $sql.=" from ds_order where source=1 and deleted_at is null and wid=".$wid." and created_at >='".$beginDate."' and created_at<'".$endDate."' group by stat_date;";
        //BLogger::getLogger(' month info')->info("sql:".$sql);
        $orderData=DB::select($sql);
        //BLogger::getLogger(' month info')->info("orderData:".json_encode($orderData));
        $result=['before'=>[],'yesterday'=>[]];
        if(!empty($orderData))
        {
            //$orderData为对象
            foreach($orderData as $item)
            {
                if($item->stat_date==$beginDate)
                {
                    //此处把对象转化为数组
                    $result['before']=json_decode(json_encode($item),true);
                }
                else if($item->stat_date==$yesterday)
                {
                    $result['yesterday']=json_decode(json_encode($item),true);
                }
            }
        }
        return $result;
    }

    /***
     * todo 按天分组统计数据 曲线图所需要的数据
     * @param int $wid
     * @param string $beginDate
     * @param string $endDate
     * @return array
     * @date 2017-11-09
     * @author jonzhang
     */
    public  function statXCXOrderTradeByDate($wid=0,$beginDate='',$endDate='')
    {
        $beginDate=date("Y-m-d", strtotime("-1 day", strtotime($beginDate)));
        $endDate=date("Y-m-d", strtotime("+1 day", strtotime($endDate)));
        $sql=" select date_format(created_at,'%Y-%m-%d') as stat_date ,";
        $sql.=" count(1) as cnt ,";
        $sql.=" count(if (status=1 or status=2 or status=3,true,null)) as pay_cnt, ";
        $sql.=" count(distinct mid) as mid_cnt , ";
        $sql.=" count(distinct if(status=1 or status=2 or status=3,mid,null)) as pay_mid_cnt, ";
        $sql.=" sum(pay_price) as total_amount, ";
        $sql.=" sum(if(status=1 or status=2 or status=3 ,pay_price,0)) as pay_total_amount,";
        $sql.=" convert( ifnull(sum(if(status=1 or status=2 or status=3 ,pay_price,0)) / count(distinct if(status=1 or status=2 or status=3,mid,null)),0),decimal(10,2)) as avg_price ,";
        $sql.=" convert( ifnull(count(if (status=3,true,null))/count(if (status=1 or status=2 or status=3,true,null)),0),decimal(10,2)) as pay_rate, ";
        $sql.=" convert( ifnull(count(if (status=3,true,null))/ count(1),0),decimal(10,2)) as order_rate ";
        $sql.=" from ds_order where source=1 and deleted_at is null and wid=".$wid." and created_at >'".$beginDate."' and created_at<'".$endDate."' group by stat_date;";
        //BLogger::getLogger('day info')->info("sql:".$sql);
        $orderData=DB::select($sql);
        //BLogger::getLogger('day info')->info("orderData:".json_encode($orderData));
        $result=[];
        if(!empty($orderData))
        {
			//此处把对象转化为数组
			$result=json_decode(json_encode($orderData),true);
        }
        return $result;
    }

    /**
     * Author: MeiJay
     * @param $oid
     * @param array $columns
     * @return array
     */
    public function getOrderDetailByOid($oid,$columns = ['*'])
    {
        $return = $this->model->select($columns)->find($oid);
        if (!empty($return)) {
            return $return->load('orderDetail')->toArray();
        }
        return [];
    }

    /**
     * 获取用户的消费信息
     * @param $mid
     * @return mixed
     * @author: 梅杰 2018年9月21日
     */
    public function getMemberOrderInfo($mid)
    {
        return $this->model->select([DB::raw('COUNT(id) as num'),DB::raw('SUM(pay_price) as amount')])->where(['mid' => $mid])
            ->where('status','>',0)->where('status','<',4)->first()->toArray();
    }
}
?>
