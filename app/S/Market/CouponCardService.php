<?php

namespace App\S\Market;

use App\S\S;
use Validator;

/**
 * 优惠券卡券
 * @author 许立 2018年09月12日
 */
class CouponCardService extends S
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年09月12日
     */
    public function __construct()
    {
        parent::__construct('WeixinCoupon');
    }

    /**
     * 表单字段验证
     * @param array $input 传递进来的表单字段数组
     * @return bool
     * @author 许立 2018年09月12日
     */
    public function verify($input)
    {
        $rules = [
            'weixin_color'  => 'required',
            'weixin_title' => 'required|max:9',
            'weixin_subtitle' => 'required|max:18',
        ];
        // 定义错误消息
        $messages = [
            'weixin_color.required'    => '卡券颜色不能为空',
            'weixin_title.required'    => '请填写卡券标题',
            'weixin_title.max'         => '卡券标题最多填写9个字',
            'weixin_subtitle.required' => '请填写卡券副标题',
            'weixin_subtitle.max'      => '卡券副标题最多填写18个字'
        ];
        // 执行验证
        $validator = Validator::make($input, $rules, $messages);
        $validator->fails() && error($validator->errors()->first());
        return true;
    }

    /**
     * 根据优惠券id获取卡券
     * @param int $couponId 优惠券id
     * @return object
     * @author 许立 2018年09月12日
     */
    public function getRowByCouponId($couponId)
    {
        return $this->model->where('coupon_id', $couponId)->first();
    }
}