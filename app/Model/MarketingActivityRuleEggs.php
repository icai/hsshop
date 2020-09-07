<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/1
 * Time: 14:47
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingActivityRuleEggs extends Model
{
    use SoftDeletes;

    protected $table = 'marketing_activity_rule_eggs';

    protected $fillable = ['egg_id',"prize_ids",'amount','left'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
}