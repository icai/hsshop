<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 小票打印机模型
 * Class DeliveryPrinter
 * @package App\Model
 * @author 何书哲 2018年11月14日
 */

class DeliveryPrinter extends Model
{
    use  SoftDeletes;

    protected $table='delivery_printer';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
