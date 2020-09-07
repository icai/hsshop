<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingActivityRuleEgg extends Model
{
    use SoftDeletes;
    
    protected $table = 'marketing_activity_rule_egg';
    public $timestamps = false;

}