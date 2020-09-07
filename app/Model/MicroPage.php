<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 微页面模型
 * @author jonzhang
 * @version 2017年03月07日 
 */
class MicroPage extends Model
{
     use SoftDeletes;
	 protected $table='micro_page_info';
     protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
