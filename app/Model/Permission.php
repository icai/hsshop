<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-6
 * Time: 下午9:49
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use  SoftDeletes;

    /**
     * @desc  表名
     */
    protected $table = 'permission';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


}