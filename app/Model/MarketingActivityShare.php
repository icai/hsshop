<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MarketingActivityShare extends Model
{
    protected $table = 'marketing_activity_share';
    protected $primaryKey = 'activity_id';
    public $timestamps = false;

}