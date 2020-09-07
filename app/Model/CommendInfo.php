<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/4/3
 * Time: 15:04
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CommendInfo extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='commend_info';
}