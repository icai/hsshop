<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class MessageTemplate extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='message_template';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function messageRecord()
    {
        return $this->hasMany('App\Model\MessageTemplateLog', 'message_template_id')->select(Schema::getColumnListing('message_send_log'));
    }

}
