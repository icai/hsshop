<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/12
 * Time: 11:53
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SelfPayLog extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='self_pay_log';
}