<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityWheel extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='activity_wheel';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
