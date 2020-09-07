<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentTicket extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='component_ticket';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}
