<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 回复关键词
 * 
 * @author 黄东 406764368@qq.com
 * @version  2017年3月15日 17:43:20
 */
class WeixinReplyContent extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;
    
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_reply_content';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联到主表
     * 
     * @return Builder
     */
    public function weixinReplyRule() {
        return $this->belongsTo('App\Model\WeixinReplyRule', 'rule_id')->select(Schema::getColumnListing('weixin_reply_rule'));
    }
}
