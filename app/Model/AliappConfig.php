<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AliappConfig extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='aliapp_config';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}
