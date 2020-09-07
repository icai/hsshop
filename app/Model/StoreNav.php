<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/17
 * Time: 16:44
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;


/**
 * todo 店铺导航实体类
 * Class StoreNav
 * @package App\Model
 * @author jonzhang guo.jun.zhang
 * @date 2017-03-17
 */
class StoreNav extends Model
{
    protected $table='store_nav';
    protected $dates = ['created_at', 'updated_at'];
}