<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/1/8
 * Time: 10:19
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class LiRedPacket extends Model
{
    protected $table = 'li_red_packet_log';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}