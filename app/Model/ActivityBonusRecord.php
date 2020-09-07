<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 调查活动
 * @author 许立 2018年7月5日
 */
class ActivityBonusRecord extends Model
{
    /**
     * 用户关闭弹窗
     */
    const RECORD_STATUS_CLOSE = 0;

    /**
     * 用户拆红包
     */
    const RECORD_STATUS_UNPACK = 1;

    /**
     * 软删除
     */
    use SoftDeletes;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'activity_bonus_record';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
