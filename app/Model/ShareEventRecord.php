<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/12/6
 * Time: 14:54
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ShareEventRecord extends Model
{
    protected $table = 'share_event_record';

    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

    protected function getDateFormat()
    {
        return 'U';
    }

}