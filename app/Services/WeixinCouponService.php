<?php

namespace App\Services;

use App\Model\WeixinCoupon;
use Validator;

class WeixinCouponService extends Service
{
    public function __construct()
    {
        $this->request = app('request');
        //设置所有字段
        $this->field = [
            'id', 'coupon_id', 'title', 'color', 'subtitle', 'service_phone','card_id', 'created_at', 'updated_at', 'deleted_at'
        ];
    }

    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {

        $this->initialize(new WeixinCoupon(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    /**
     * 参数验证
     */
    public function verify()
    {
        $rawInput = $this->request->input();
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
        $validator = Validator::make($rawInput, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }
        return true;
    }
}