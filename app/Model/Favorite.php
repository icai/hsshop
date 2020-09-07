<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 收藏model
 * @author 许立 2018年09月04日
 */
class Favorite extends Model
{
    /**
     * 收藏类型-商品
     */
    const FAVORITE_TYPE_PRODUCT = 0;

    /**
     * 收藏类型-秒杀
     */
    const FAVORITE_TYPE_SECKILL = 1;

    /**
     * 收藏类型-拼团
     */
    const FAVORITE_TYPE_GROUP = 2;

    /**
     * 收藏类型-享立减
     */
    const FAVORITE_TYPE_SHARE = 3;

    /**
     * 状态-正常
     */
    const FAVORITE_STATUS_NORMAL = 0;

    /**
     * 状态-删除
     */
    const FAVORITE_STATUS_DELETE = 1;

    /**
     * 收藏有效性-有效的
     */
    const FAVORITE_VALID = 'VALID';

    /**
     * 收藏有效性-已失效
     */
    const FAVORITE_INVALID = 'INVALID';

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'favorite';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
