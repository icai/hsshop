<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/1/19
 * Time: 15:56
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class FormIds extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'form_ids';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}