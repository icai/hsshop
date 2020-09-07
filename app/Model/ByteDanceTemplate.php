<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @desc 字节小程序关联表模型
 * @date 2019年9月20日11:17:30
 * Class Account
 * @package App\Model
 */
class ByteDanceTemplate extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'byte_dance_template';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}
