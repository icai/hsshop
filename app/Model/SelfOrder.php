<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/7/3
 * Time: 17:53
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SelfOrder extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='self_order';
}