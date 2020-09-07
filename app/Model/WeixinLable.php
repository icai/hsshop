<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeixinLable extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='weixin_lable';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
