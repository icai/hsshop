<?php 

namespace App\Services\Foundation;

use App\Model\Recharge;
use App\Services\Lib\Service;
use DB;
use PaymentService;
use RechargeLogService;

/**
 * 订单
 */
class RechargeService extends Service {

	public $withAll = ['rechargeLog'];
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

        $this->initialize(new Recharge(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }


    /**
     * [创建订单]
     * @author <[吴晓平]>
     * @param  [int] $wid              [店铺id]
     * @param  [int] $mid              [用户id]
     * @param  [array] $rechargeDatas       [充值订单数据]
     * @param  [array] $rechargeLogDatas    [充值日志数据]
     * @return [array] $orderDatas       [处理后的订单数据]
     */
    public function createOrder($wid,$mid,$rechargeDatas,$rechargeLogDatas)
    {
    	$returnData = [];
        //事务处理，如果失败则会自动回滚
        $idsArr = DB::transaction(function() use ($rechargeDatas,$rechargeLogDatas){
        	//定义返回插入成功后的所有表的主键id
        	$idsArr = [];

            $recharge_id = $this::init()->addD($rechargeDatas,false);
            $rechargeLogDatas['recharge_id'] = $recharge_id;
            $rechargeLog_id = RechargeLogService::init()->addD($rechargeLogDatas,false);

            $idsArr['recharge_id'] = $recharge_id;
            $idsArr['rechargeLog_id'] = $rechargeLog_id;

            return $idsArr;
        });

        //事务成功后的操作
        $rechargeDatas['id'] = $idsArr['recharge_id'];
        $rechargeLogDatas['id'] = $idsArr['rechargeLog_id'];
        $rechargeLogDatas['recharge_id'] = $idsArr['recharge_id'];

        $rechargeDatas['rechargeLog'] = $rechargeLogDatas;
        $returnData[] = $rechargeDatas;
        $this::init('mid',$mid)->addR($rechargeDatas,false);
        $this::init('wid',$wid)->addR($rechargeDatas,false);

        return $rechargeDatas;
    }

}
 
























?>
