<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/4/8
 * Time: 11:10
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CommendDetail extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='commend_detail';
}