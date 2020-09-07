<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/8/30
 * Time: 9:48
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class BatchDeliveryLog extends Model
{
    public $table = 'batch_delivery_log';

    /**
     * 日期属性
     */
    protected $dates = ['created_at'];

}