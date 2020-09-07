<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/8
 * Time: 14:44
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WXXCXUser extends Model
{
    protected $table='wxxcx_user';
    protected $dates = ['created_at', 'updated_at'];
}