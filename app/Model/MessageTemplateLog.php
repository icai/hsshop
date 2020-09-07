<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageTemplateLog extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='message_send_log';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
