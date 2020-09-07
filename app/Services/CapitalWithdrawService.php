<?php
namespace App\Services;

use App\Model\CapitalWithdraw;
use Validator;

/**
 * 订单
 */
class CapitalWithdrawService extends Service
{
    /**
     * 构造方法
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 10:32:35
     *
     * @return void
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id', 'uid', 'wid', 'water_no', 'account_type', 'bank_id', 'money', 'status', 'withdraw_no', 'remark', 'handle_at', 'create_at'];

        /* 设置闭包标识 */
        //$this->closure('capital');
        // 所有关联关系
        $this->withAll = ['orderDetail','orderLog'];
    }

    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {
        $this->initialize(new CapitalWithdraw(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }
}