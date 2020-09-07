<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 外卖订单配置模型
 * Class DeliveryConfig
 * @package App\Model
 * @author 何书哲 2018年11月14日
 */

class DeliveryConfig extends Model
{
    use  SoftDeletes;

    protected $table='delivery_config';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
