<?php
/*商家主营类目模型*/
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeixinBusiness extends Model
{
    use SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_business';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    public function category()
    {
        return $this->hasMany('App\Model\WeixinBusiness','pid','id')->select();
    }

    /**
     * 关联店铺信息
     * @return [type] [description]
     */
    public function shop()
    {
        return $this->hasMany(Weixin::class,'business_id')->select(['weixin.id','business_id','shop_name','logo']);
    }

    /**
     * 定义关系（一级分类下是否含有二级分类）
     * @return boolean [description]
     */
    public function hasManyChilds()
    {
        return $this->hasMany($this,'pid','id')->orderBy('sort','desc')->orderBy('id','desc');
    }

    /**
     * 分类下有多少案例
     * @return boolean [description]
     */
    public function hasManyCase()
    {
        return $this->hasMany(WeixinCase::class,'business_id','id');
    }
}
