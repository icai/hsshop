<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 订单打单模型类
 * @create 何书哲 2018年6月26日
 */
class OrderLogistics extends Model {

    /**
     * 关联到模型的数据表
     */
    protected $table='order_logistics';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}