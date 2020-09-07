<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9
 * Time: 11:15
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MicroPageNotice
 * 公告模型
 * @package App\Model
 */
class MicroPageNotice extends Model
{
    protected $table='micro_page_notice';
    //public  $timestamps=false;
    protected $dates = ['created_at', 'updated_at'];
}