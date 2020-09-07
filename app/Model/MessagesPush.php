<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/10/11
 * Time: 11:14
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class MessagesPush extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='message_push';

    protected $dates = ['created_at','update_at'];

}