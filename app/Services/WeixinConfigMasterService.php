<?php 

namespace App\Services;

use App\Model\WeixinConfigMaster;
use Validator;

/**
 * 订单操作记录
 */
class WeixinConfigMasterService extends Service {
    /**
     * 构造方法
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 10:32:35
     * 
     * @return void
     */
    public function __construct() {
        /* http请求类 */
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

        $this->initialize(new WeixinConfigMaster(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    /**
     * 订单字段验证
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月7日 15:44:15
     * 
     * @param  array $verifyField   [需要验证的字段数组，默认只验证主键]
     * @param  array $type          [接收参数的形式：1只接收指定数组参数；2只接收指定数组之外的参数；3接收所有数据库字段参数；其余表示接收全部参数]
     * 
     * @return array 请求参数
     */
    public function verify( $verifyField = ['id'], $type = '1', $inputField = ['id'] ) {
        /* 接收数据 */
        switch ( strval($type) ) {
            case '1':
                $input = $this->request->only($inputField);
                break;
            case '2':
                $input = $this->request->except($inputField);
                break;
            case '3':
                $input = $this->request->only($this->field);
                break;
            default:
                $input = $this->request->input();
                break;
        }

        if ( $this->request->isMethod('post') ) {
            /* 获取验证数据和提示消息 */
            $rules       = [];
            $messages    = [];
            foreach ($verifyField as $value) {
                switch ( $value ) {
                    // 店铺id
                    case 'wid':
                        $rules['wid'] = 'required';
                        $messages['id.required'] = '店铺异常';
                        break;
                    // 页面标题后是否加统一后缀标识
                    case 'is_title':
                        $rules['is_title'] = 'filledIn:1';
                        $messages['is_title.filled_in'] = '参数错误:is_title';
                        break;
                    // 页面标题后是否加统一后缀文字
                    case 'title':
                        $rules['title'] = 'required_with:is_title|max:10';
                        $messages['title.required_with'] = '请填写统一后缀文字';
                        $messages['title.max'] = '统一后缀文字最多填写10个字';
                        break;
                    // 使用“店铺名称”作为页面标题
                    case 'is_shopname':
                        $rules['is_shopname'] = 'filledIn:1';
                        $messages['is_shopname.filled_in'] = '参数错误:is_shopname';
                        break;
                    // 是否开启购物车
                    case 'is_cart':
                        $rules['is_cart'] = 'filledIn:1';
                        $messages['is_cart.filled_in'] = '参数错误:is_cart';
                        break;
                    // 购物车图标样式
                    case 'cart_icon':
                        $rules['cart_icon'] = 'required_with:is_cart|in:1,2,3,4';
                        $messages['cart_icon.required_with'] = '请选择图标样式';
                        $messages['cart_icon.in'] = '参数错误:cart_icon';
                        break;
                    // 销量及成交记录
                    case 'is_record':
                        $rules['is_record'] = 'required|in:0,1';
                        $messages['is_record.required'] = '请设置销量及成交记录';
                        $messages['is_record.in'] = '参数错误:is_record';
                        break;
                    // 商品评价
                    case 'is_comment':
                        $rules['is_comment'] = 'required|in:0,1,2';
                        $messages['is_comment.required'] = '请设置商品评价';
                        $messages['is_comment.in'] = '参数错误:is_comment';
                        break;
                    // 更多商品推荐
                    case 'is_more':
                        $rules['is_more'] = 'required|in:0,1';
                        $messages['is_more.required'] = '请设置更多商品推荐';
                        $messages['is_more.in'] = '参数错误:is_more';
                        break;
                    // 列表显示售罄商品
                    case 'is_sellout':
                        $rules['is_sellout'] = 'required|in:0,1';
                        $messages['is_sellout.required'] = '请设置列表显示售罄商品';
                        $messages['is_sellout.in'] = '参数错误:is_sellout';
                        break;
                    // 联系商家/在线客服
                    case 'is_service':
                        $rules['is_service'] = 'required|in:0,1';
                        $messages['is_service.required'] = '请设置联系商家/在线客服';
                        $messages['is_service.in'] = '参数错误:is_service';
                        break;
                    // 会搜买家版收录
                    case 'is_included':
                        $rules['is_included'] = 'required|in:0,1';
                        $messages['is_included.required'] = '请设置会搜买家版收录';
                        $messages['is_included.in'] = '参数错误:is_included';
                        break;
                    // 店铺顶部导航
                    case 'is_nav':
                        $rules['is_nav'] = 'required|in:0,1';
                        $messages['is_nav.required'] = '请设置店铺顶部导航';
                        $messages['is_nav.in'] = '参数错误:is_nav';
                        break;
                    // 营业状态
                    case 'is_business':
                        $rules['is_business'] = 'required|in:0,1';
                        $messages['is_business.required'] = '请设置营业状态';
                        $messages['is_business.in'] = '参数错误:is_business';
                        break;
                    // 营业时间 是否全天营业
                    case 'is_all_day':
                        $rules['is_all_day'] = 'required_with:is_business|in:0,1';
                        $messages['is_all_day.required_with'] = '请设置营业时间';
                        $messages['is_all_day.in'] = '参数错误:is_all_day';
                        break;
                    // 营业开始时间
                    case 'business_start':
                        $rules['business_start'] = 'required_if_and:is_business,1,is_all_day,0|date_format:Y-m-d H:i:s';
                        $messages['business_start.required_if_and'] = '请设置营业开始时间';
                        $messages['business_start.date_format'] = '营业开始时间格式不正确';
                        break;
                    // 营业结束时间
                    case 'business_end':
                        $rules['business_end'] = 'required_if_and:is_business,1,is_all_day,0|date_format:Y-m-d H:i:s|after:business_start';
                        $messages['business_end.required_if_and'] = '请设置营业结束时间';
                        $messages['business_end.date_format'] = '营业结束时间格式不正确';
                        $messages['business_end.after'] = '营业结束时间不能小于营业开始时间';
                        break;
                    // 自动开业
                    case 'is_auto':
                        $rules['is_auto'] = 'required_if:is_business,0|in:0,1';
                        $messages['is_auto.required_if'] = '请设置自动开业';
                        $messages['is_auto.in'] = '参数错误:is_auto';
                        break;
                    // 自动开业时间
                    case 'auto_time':
                        $rules['auto_time'] = 'required_if_and:is_business,0,is_auto,1|date_format:Y-m-d H:i:s';
                        $messages['auto_time.required_if_and'] = '请设置自动开业时间';
                        $messages['auto_time.date_format'] = '自动开业时间格式不正确';
                        break;
                    // 店铺底部logo
                    case 'footer_logo':
                        $rules['footer_logo'] = 'required_if:is_footer_logo,1';
                        $messages['footer_logo.required_if'] = '请上传店铺底部logo';
                        break;
                    default:
                        # code...
                        break;
                }
            }

            /* 调用验证器执行验证方法 */
            $validator = Validator::make($input, $rules, $messages);

            /* 验证不通过则提示错误信息 */
            if ( $validator->fails() ) {
                error( $validator->errors()->first() );
            }
        }

        return $input;
    }
}
