<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/10/13
 * Time: 14:24
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WXXCXComponentTicket extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='wxxcx_component_verify_ticket';
}