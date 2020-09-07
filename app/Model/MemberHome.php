<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/10
 * Time: 10:50
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

/**
 * 实体类 Memberhome
 * @author jonzhang guo.jun.zhang@163.com
 * @date 2017-03-10
 */
class MemberHome extends Model
{
    protected $table='member_home';
    protected $dates = ['created_at', 'updated_at'];
}