<?php 

namespace App\Services;

use App\Model\WeixinMaterialWechat;

/**
 * 微信图文
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月15日 19:23:26
 */
class WeixinMaterialWechatService extends Service {
    /**
     * 初始化
     * 
     * 1、设置唯一标识名称，用于redis键名组装
     * 2、设置唯一标识的值，用于redis键名组装
     * 3、设置忽略限制强制走redis的查询条件
     * 4、设置主键名称
     * 
     * @param  string $uniqueKey    [唯一标识名称]
     * @param  string $uniqueValue  [唯一标识的值]
     * @param  array  $ignore       [忽略限制强制走redis的查询条件]
     * @param  string $idKey        [主键名称]
     * @return $this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $ignore = [], $idKey = 'id' ) {
        
        $this->initialize(new WeixinMaterialWechat(), $uniqueKey, $uniqueValue, $idKey, $ignore);

        return $this;
    }
}
