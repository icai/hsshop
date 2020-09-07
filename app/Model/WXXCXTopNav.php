<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/12/27
 * Time: 17:17
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXTopNav extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='wxxcx_top_nav';
}