<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-3
 * Time: 下午1:44
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileClassify extends  Model
{

    use SoftDeletes;

    /**
     * @desc  表名
     */
    protected $table = 'file_classify';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}