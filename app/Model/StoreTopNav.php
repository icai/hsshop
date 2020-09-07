<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/5/17
 * Time: 9:34
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class StoreTopNav extends Model
{
    //禁止使用  craeted_at 和 updated_at
    public $timestamps = false;
    protected $table='store_top_nav';
}