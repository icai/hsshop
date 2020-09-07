<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/13
 * Time: 10:14
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXMicroPage extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='wxxcx_micro_page';
}