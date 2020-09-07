<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 客户/会员
 */
class Member extends Model
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
    protected $table = 'member';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    /**
     * 获取关联到提现记录
     */
    public function shipCard() {
        return $this->hasMany('App\Model\memberCard', 'id')->select(['*']);
        //return $this->belongsTo('App\Model\MemberCardRecord','member_id','card_id');
    }

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月24日 08:34:29
     */
    public function merchant()
    {
        return $this->belongsTo(Weixin::class, 'wid');
    }
}
