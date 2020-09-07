<?php 

namespace App\Services\Order;

use App\Model\OrderLog;
use App\Services\Lib\Service;

/**
 * 订单操作记录
 */
class OrderLogService extends Service {

    public $status = [
        1  => '买家创建订单',
        2  => '买家付款',
        3  => '商家发货',
        4  => '买家确认收货',
        5  => '买家评价',
        6  => '买家取消订单',
        7  => '买家申请退款',
        8  => '商家同意退款',
        9  => '商家拒绝退款',
        10 => '买家取消退款',
        11 => '系统自动确认收货',
        12 => '商家关闭交易',
        13 => '延期收货',
        14 => '系统关闭订单',
        15 => '订单改价',
        16 => '商家取消订单',
        17 => '修改发货地址'
    ];
    /**
     * 初始化 设置唯一标识和redis键名
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月16日 14:32:03
     * 
     * @param  array  $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     * 
     * @return this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        $this->initialize(new OrderLog(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    /**
     * 订单操作统计
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月8日 11:09:59
     * 
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
     */
    public function statistical( $action = [1], $dateTime = [0, -1], $dateUnit = 'day') {
        /* 结束时间小于开始时间则将结束时间置为当前时间 */
        if ( !isset($dateTime[1]) || $dateTime[1] < $dateTime[0] ) {
            $dateTime[1] = mktime(0,0,0,date('m'),date('d'),date('Y'));
        }
        /* 查询条件 */
        $where['wid']    = session('wid');
        $where['action'] = array('in', $action);
        /* 解析日期单位 获取截取字符串开始和结束位置 */
        switch ( $dateUnit ) {
            case 'day':
                $end = 10;
                break;
            case 'month':
                $end = 7;
                break;
            case 'year':
                $end = 4;
                break;
            case 'hour':
                $end = 13;
                break;
            case 'minute':
                $end = 16;
                break;
            case 'second':
                $end = 19;
                break;
            default:
                $end = 19;
                break;
        }
        $where['updated_at'] = array('between', [date('Y-m-d H:i:s',$dateTime[0]), date('Y-m-d H:i:s',$dateTime[1])] );
        /* 添加查询条件 */
        $this->whereAdd($where, true);
        /* 查询数据 */
        $list = OrderLog::select(['oid', 'action', 'updated_at'])->wheres($this->where)->with('order')->order('updated_at DESC')->get()->toArray();
        /* 数据处理 */
        $return = [];
        $income = 0;
        foreach ($list as $value) {
            /* 按单位分组 */
            $return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['datas'][] = $value;
            /* 单位内数据总数 */
            $return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['countUnit'] = isset($return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['countUnit']) ? $return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['countUnit'] + 1 : 1;
            /* 单位内订单支付金额 */
            $return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['payPriceUnit'] = isset($return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['payPriceUnit']) ? $return[$value['action']]['list'][substr($value['updated_at'],0, $end)]['payPriceUnit'] + $value['order']['pay_price'] : 0.00;
            /* 数据总计 */
            $return[$value['action']]['countTotal'] = isset($return[$value['action']]['countTotal']) ? $return[$value['action']]['countTotal'] + 1 : 1;

            $return[$value['action']]['payPriceTotal'] = isset($return[$value['action']]['payPriceTotal']) ? $return[$value['action']]['payPriceTotal'] + $value['order']['pay_price'] : 0.00;
             /* 订单支付金额总计 */
            $income += $value['order']['pay_price'];
            $return['income'] = $income;
        }
        
        return $return;
    }

    /**
     * 获取订单日志的所有状态
     * @author 吴晓平 <2018年08月20日>
     * @return [type] [description]
     */
    public function getAllStatus()
    {
        return $this->status;
    }
}
