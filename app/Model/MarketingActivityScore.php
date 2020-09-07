<?php
/**
 * Created by PhpStorm.
 * User: meijie
 * Date: 2017/8/2
 * Time: 9:09
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class MarketingActivityScore extends Model
{
    protected $table = 'marketing_activity_score';

    protected $fillable = ['per_score','wid','title',"amount_score",'score_group','start_at','end_at','left','total'];

    protected $dates = ['created_at','updated_at'];
}