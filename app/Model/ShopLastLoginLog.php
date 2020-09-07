<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2019/8/19
 * Time: 11:09
 * Desc：店铺最后访问时间记录
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ShopLastLoginLog extends Model
{

    /**
     * 数据表名
     * @var string
     */
    protected $table = "shop_login_log";

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = 'dc';

}