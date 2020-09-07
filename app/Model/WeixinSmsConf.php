<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/9/25
 * Time: 15:13
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WeixinSmsConf extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='weixin_sms_conf';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];
}