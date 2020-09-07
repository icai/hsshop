<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/30
 * Time: 15:19
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ALiPay extends Model
{
    protected $table = "weixin_alipay_payment";
    protected $dates = ['created_at', 'updated_at'];
}