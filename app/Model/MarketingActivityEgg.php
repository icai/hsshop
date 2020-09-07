<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 砸金蛋活动主表
 * Class MarketingActivityEgg
 * @package App\Model
 */
class MarketingActivityEgg extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'marketing_activity_egg';
    
    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    
    public function getMember()
    {
        return $this->hasOne('App\Model\EggMember','egg_id');
    }

    public function prize_info()
    {
        return $this->hasMany('App\Model\EggPrize','eggId');
    }

}