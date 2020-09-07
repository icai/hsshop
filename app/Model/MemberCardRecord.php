<?php


namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberCardRecord extends Model
{
    protected $table='member_card_record';
    public $timestamps=false;
    use  SoftDeletes;
    /**
     * 获取关联到提现记录
     */
    public function memberCardRecode() {
        return $this->hasMany('App\Model\memberCard', 'id')->select(['*']);
    }
    
    //关联到member表
    public function member()
    {
        return $this->belongsTo('App\Model\Member', 'member_id', 'id');
    }
    
    //关联到member_card表
    public function memberCard()
    {
        return $this->belongsTo('App\Model\MemberCard', 'card_id', 'id');
    }
    
    
}