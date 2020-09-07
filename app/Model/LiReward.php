<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiReward extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='li_reward';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}
