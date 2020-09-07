<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 订单退款
 */
class OrderRefund extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;

    /**
     * 退款中状态数组
     * @author 许立 2018年6月26日
     */
    const REFUNDING_STATUS_ARRAY = [1,2,3,6,7,10];

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_refund';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
