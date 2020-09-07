<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/15
 * Time: 18:55
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXPaymentLog extends Model
{
    protected $table='wxxcx_payment_log';
    protected $dates = ['created_at', 'updated_at'];
}