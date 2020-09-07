<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 调查活动
 * @author 许立 2018年7月5日
 */
class ActivityBonus extends Model
{
    /**
     * 活动进行中状态
     */
    const BONUS_STATUS_ON = 0;

    /**
     * 活动停止状态
     */
    const BONUS_STATUS_DELETE = 1;

    /**
     * 活动停止状态
     */
    const BONUS_STATUS_STOP = 2;

    /**
     * 活动显示状态-显示弹窗
     */
    const BONUS_WINDOW_STATUS_SHOW = 0;

    /**
     * 活动显示状态-有下角图标
     */
    const BONUS_WINDOW_STATUS_CORNER = 1;

    /**
     * 活动显示状态-不显示
     */
    const BONUS_WINDOW_STATUS_HIDE = 2;

    /**
     * 软删除
     */
    use SoftDeletes;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'activity_bonus';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
