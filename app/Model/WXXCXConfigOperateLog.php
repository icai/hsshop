<?php
/**
 * Created by PhpStorm.
 * User: 何书哲
 * Date: 2020/3/11
 * Time: 16:39
 * Desc: 小程序操作日志记录模型
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WXXCXConfigOperateLog extends Model
{
    /**
     * @var string 数据表属性
     */
    protected $table = 'wxxcx_config_operate_log';

    /**
     * @var bool 日期属性
     */
    public $timestamps = false;
}