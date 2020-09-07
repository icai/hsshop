<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/7
 * Time: 9:18
 */

namespace App\Lib\Order;


class OrderCommon
{
    /**
     * todo 生成订单号
     * @return string 返回订单号
     * @author jonzhang
     * @date 2017-06-07
     */
    public function createOrderNumber()
    {
        //生成订单id
        $date = date('YmdHi');
        //按照年月日时分生成订单号[17060709495214] 每分钟生成9999个订单号 14位
        $order_id = substr($date, 2, -1) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return $order_id;
    }
    /**
     * todo 生成服务订单号
     * @return string 返回订单号
     * @author 张国军  2018年07月04日
     */
    public function createServiceOrderNumber()
    {
        //生成订单id
        $date = date('YmdHi');
        //按照年月日时分生成订单号[201806070949521] 每分钟生成999个订单号  15位
        $order_id = $date.str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        return $order_id;
    }

    /***
     * todo 申请发票时生成编号
     * @return string
     * @author 张国军 2018年07月05日
     */
    public function createInvoiceRequestNumber()
    {
        //生成订单id
        $date = date('YmdHi');
        //按照年月日时分生成订单号[170607094911] 每分钟生成99个订单号 12位
        $order_id = substr($date, 2) . str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
        return $order_id;
    }
}