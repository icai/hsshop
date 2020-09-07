<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/30
 * Time: 13:57
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class AliappConfigOperateLog extends Model
{
    protected $table='aliapp_config_operate_log';
    protected $dates = ['created_at', 'updated_at'];
}