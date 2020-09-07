<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/3
 * Time: 17:29
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EggMember extends Model
{
    use SoftDeletes;
    protected $table = 'egg_member_log';

//    protected $fillable = ['wid','mid','egg_id','is_prize','prize_id'];
    
    protected $dates = ['created_at','updated_at','deleted_at'];

    public function prize()
    {
        return $this->hasOne('App\Model\EggPrize','id','prize_id');
    }
}