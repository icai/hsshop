<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/10/27
 * Time: 14:01
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WeixinCustomService extends Model
{
    protected $table = 'weixin_custom_service';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}