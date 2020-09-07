<?php
namespace App\S;
use App\S\Order\OrderLogService;
use App\S\Order\OrderService;
use App\Lib\Redis\Order;
use App\Services\CashLogService;

class RelationService extends S{


	/**
	* @author wuxiaoping 
    * @version 2017.07.06
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
	public function orderLogRelation($action=[1],$dateTime = [0, -1],$data=[])
	{	
		$wid = session('wid');
		//获取order统计相关数据
		$orderLogService = new OrderLogService();
		$list = $orderLogService->getLogListByWhere($action,$dateTime,$data);

		// 数据处理
		$orderData = $return = [];
		$return['total_num']    = $list[0]['total']; //总记录数 
		$return['per_page']     = $list[0]['per_page']; //每页显示条数
		$return['current_page'] = $list[0]['current_page']; //当前页
		$return['last_page']    = $list[0]['last_page']; //当前页
		$return['pageHtml']     = $list[1];  // 分页显示
        $income = 0;  // 总收入
        //$income = $orderLogService->statistical($action,$dateTime,$data);
        if(!empty($list[0]['data'])){
            $result = array_filter($list[0]['data']);
            $orderData = [];
            foreach($result as $val){
                if($val['order']){
                    $orderData = json_decode($val['order'],true);
                }
                $val['order'] = $orderData;

                if($val['action'] == 2){
                    if($val['order']){
                        $val['pay_price'] = $val['order']['pay_price'] == 0 ? '0.00' : '+'.$val['order']['pay_price'];
                        $val['pay_class'] = $val['order']['pay_price'] == 0 ? '' : 'green_f04';
                    }else{
                        $val['pay_price'] = '0.00';
                        $val['pay_class'] = '';                    
                    }
                }else if($val['action'] == 8){
                    if($val['order']){
                        $val['pay_price'] = $val['order']['pay_price'] == 0 ? '0.00' : $val['order']['pay_price'];
                        $val['pay_class'] = $val['order']['pay_price'] == 0 ? '' : 'red_f00';
                    }else{
                        $val['pay_price'] = '0.00';
                        $val['pay_class'] = '';  
                    }
                    
                }
                $return['datas'][] = $val;
            }
        }
        $return['total_income'] = $income;
		
		return $return;

	}

	

}


?>