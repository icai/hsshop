<?php
namespace App\Lib\Redis;

/**
 * 营销活动-优惠券
 * @author 许立 2018年09月11日
 */
class CouponRedis extends RedisInterface
{
    protected $prefixKey = 'coupon';
    protected $timeOut = 86400;

    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年09月11日
     */
    public function __construct($key = "")
    {
        parent::__construct($key);
    }
}