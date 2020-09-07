<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * 消息模板文件
 * 
 */
class WeixinNotifyTpl extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_notify_tpl';

    public $timestamps = false;
    
    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    //protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
