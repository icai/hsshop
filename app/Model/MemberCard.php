<?php


namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberCard extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;
    
    protected $withAll = 'member_card_record';

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'member_card';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    //protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
    /**
     * 获取关联到提现记录
     */
    public function shipMember() {
        //return $this->hasMany('App\Model\memberCard', 'id')->select(['*']);
        return $this->belongsToMany('App\Model\Member', 'member_card_record', 'card_id', 'member_id');
    }
}