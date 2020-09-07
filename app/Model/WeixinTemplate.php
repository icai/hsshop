<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WeixinTemplate extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_template';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
