<?php


namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MemberFans extends Model
{
    protected $table='member_fans';
    public $timestamps=false;

    /**
     * 获取关联到提现记录
     */
    public function memberCard() {
        return $this->hasMany('App\Model\memberCard', 'id')->select(['*']);
    }
}