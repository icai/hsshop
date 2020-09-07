<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LiteappHistory extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'liteapp_history';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at'];
}
