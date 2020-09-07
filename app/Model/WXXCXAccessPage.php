<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/10/26
 * Time: 15:15
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXAccessPage extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='wxxcx_access_page';
}