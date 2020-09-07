<?php 

namespace App\Services;

use App\Model\WeixinTemplate;

/**
 * 省市区
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月2日 15:57:29
 */
class WeixinTemplateService extends Service {
    /**
     * 初始化 设置唯一标识和redis键名
     * 
     * @param  array  $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     * 
     * @return this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {

        $this->initialize(new WeixinTemplate(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }
}
