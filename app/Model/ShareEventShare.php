<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShareEventShare extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='share_event_share';

    /**
     * 日期属性
     */
    public $timestamps = false;
}