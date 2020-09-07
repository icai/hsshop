<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/1/18
 * Time: 15:23
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXTemplate extends Model
{
    protected $table='wxxcx_template';
    protected $dates = ['created_at', 'updated_at'];
}