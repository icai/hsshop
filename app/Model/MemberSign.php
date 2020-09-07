<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MemberSign extends Model
{
    protected $table = 'member_sign';
    public $timestamps = false;
    
    
    public function signAwards() {
        return $this->hasMany('App\Model\MarketingActivityExtra', 'activity_id');
    }
}