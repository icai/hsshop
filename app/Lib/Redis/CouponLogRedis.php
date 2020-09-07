<?php
namespace App\Lib\Redis;

/**
 * 营销活动-优惠券领取记录
 * @author 许立 2018年09月13日
 */
class CouponLogRedis extends RedisInterface
{
    protected $prefixKey = 'coupon_log';
    protected $timeOut = 86400;

    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年09月13日
     */
    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}