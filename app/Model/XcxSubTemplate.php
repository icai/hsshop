<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
* 小程序订阅消息模板model
*
 * @author 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月04日 20:47:35
*/
class XcxSubTemplate extends Model
{

    /**
     * @var string 定义表名
     */
    protected $table = 'xcx_sub_template';

    /**
     * 白名单字段
     * @var array
     */
    protected $fillable = [
        'wid', 'title_id', 'template_id', 'scene'
    ];


}
