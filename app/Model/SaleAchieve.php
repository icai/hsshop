<?php
/**
 * Created by PhpStorm.
 * User: wuxiaoping
 * Date: 2018/9/20
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SaleAchieve extends Model
{
    protected $table='saleachieve_count';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];
}