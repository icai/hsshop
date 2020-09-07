<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/13
 * Time: 17:33
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SelfFeeOperateLog extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='self_fee_operate_log';
}