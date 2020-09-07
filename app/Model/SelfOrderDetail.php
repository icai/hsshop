<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/3
 * Time: 17:55
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SelfOrderDetail extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='self_order_detail';
}