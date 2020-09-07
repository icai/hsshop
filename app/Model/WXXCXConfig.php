<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/9
 * Time: 17:33
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXConfig extends Model
{
    protected $table='wxxcx_config';
    protected $dates = ['created_at', 'updated_at'];
}