<?php 

namespace App\Services\Foundation;

use App\Model\RechargeLog;
use App\Services\Lib\Service;

/**
 * 订单
 */
class RechargeLogService extends Service {


	/**
     * 构造方法
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 10:32:35
     *
     * @return void
     */
    public function __construct() {
        // http请求类
        $this->request = app('request');
    }

	/**
     * 初始化 设置唯一标识和redis键名
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月16日 14:32:03
     *
     * @param  array  $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     *
     * @return this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {

        $this->initialize(new RechargeLog(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }



}
 
























?>
