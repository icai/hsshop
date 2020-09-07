<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/2
 * Time: 14:27
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * Class MicroPageTypeRelation
 * @package App\Model
 * @author jonzhang guo.jun.zhang@163.com
 * @date 2017-05-03
 */
class MicroPageTypeRelation extends Model
{
    use SoftDeletes;
    protected $table='micro_page_type_relation';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    //ds_micro_page_type_relation表与ds_micro_page_info表建立表链接关系
    public function belongsToMicroPage()
    {
        return $this->belongsTo('App\Model\MicroPage','micro_page_id','id')->select(Schema::getColumnListing('micro_page_info'));
    }
   	//ds_micro_page_type_relation表与ds_micro_page_type表建立表链接关系
    public function belongsToMicroPageType()
    {
    	return $this->belongsTo('App\Model\MicroPageType','micro_page_type_id','id')->select(Schema::getColumnListing('micro_page_type'));
    }

}