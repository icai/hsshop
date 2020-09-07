<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/3/8
 * Time: 13:48
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXConfigRecord extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='wxxcx_config_record';
}