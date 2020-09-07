<?php
/**
 * DIY components validator module
 *
 * @author mafanding
 */

namespace App\Module;

use Validator, Log;
use Illuminate\Support\Str;
use App\Lib\BLogger;

class DiyComponentValidatorModule
{
    /**
     * error no
     *
     * @var int
     */
    protected $errno;

    /**
     * error message
     *
     * @var string
     */
    protected $errmsg;

    public function __construct()
    {
        $this->errno = 0;
        $this->errmsg = '';
    }

    public function validateRichText($inputs)
    {
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isString($inputs, 'content') && $this->isString($inputs, 'bgcolor') && $this->isBool($inputs, 'initShow', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateGoods($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumeric($inputs, 'listStyle') && $this->isNumeric($inputs, 'cardStyle') && $this->isNumeric($inputs, 'btnStyle')
            && $this->isBool($inputs, 'showSell') && $this->isBool($inputs, 'goodName') && $this->isBool($inputs, 'goodInfo')
            && $this->isBool($inputs, 'priceShow') && $this->isBool($inputs, 'nodate') && $this->isArray($inputs, 'products_id', true)
            && $this->isNumeric($inputs, 'products_id.*', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateImageAd($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumeric($inputs, 'advsListStyle') && $this->isNumeric($inputs, 'advSize') && $this->isArray($inputs, 'images')
            && $this->isNumeric($inputs, 'images.*.user_id', true) && $this->isNumeric($inputs, 'images.*.file_info_id', true)
            && $this->isNumeric($inputs, 'images.*.file_classify_id', true) && $this->isNumeric($inputs, 'images.*.weixin_id', true)
            && $this->isNumeric($inputs, 'images.*.index', true) && $this->isNumeric($inputs, 'images.*.image_id', true)
            && $this->isString($inputs, 'images.*.created_at', true) && $this->isString($inputs, 'images.*.updated_at', true)
            && $this->isString($inputs, 'images.*.linkName', true) && $this->isString($inputs, 'images.*.linkUrl', true)
            && $this->isString($inputs, 'images.*.title', true) && $this->isBool($inputs, 'images.*.isShow', true)
            && $this->isBool($inputs, 'images.*.chooseLink', true) && $this->isBool($inputs, 'images.*.dropDown', true)
            && $this->isBool($inputs, 'images.*.pageCurrent', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateTitle($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isString($inputs, 'titleName') && $this->isString($inputs, 'subTitle') && $this->isString($inputs, 'bgColor')
            && $this->isString($inputs, 'linkName') && $this->isString($inputs, 'linkUrl') && $this->isString($inputs, 'author')
            && $this->isString($inputs, 'wlinkTitle') && $this->isString($inputs, 'wlinkUrl') && $this->isString($inputs, 'linkTitle', true)
            && $this->isString($inputs, 'date') && $this->isNumeric($inputs, 'titleStyle') && $this->isNumeric($inputs, 'showPosition')
            && $this->isNumeric($inputs, 'wlinkUrlChoose') && $this->isBool($inputs, 'addLink') && $this->isBool($inputs, 'chooseLink')
            && $this->isBool($inputs, 'dropDown')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateStore($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumeric($inputs, 'id') && $this->isString($inputs, 'store_name') && $this->isString($inputs, 'url')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateCoupon($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumeric($inputs, 'show') && $this->isArray($inputs, 'couponList') && $this->isArray($inputs, 'coupons_id', true)
            && $this->isNumeric($inputs, 'coupons_id.*', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateNotice($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isString($inputs, 'content') && $this->isString($inputs, 'placeholder')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateSearch($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isString($inputs, 'bgColor')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateGoodslist($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isString($inputs, 'groupName') && $this->isNumericOrEmpty($inputs, 'group_id', true) && $this->isNumeric($inputs, 'listStyle')
            && $this->isNumeric($inputs, 'cardStyle') && $this->isNumeric($inputs, 'btnStyle') && $this->isNumeric($inputs, 'showNum')
            && $this->isBool($inputs, 'showSell') && $this->isBool($inputs, 'goodName') && $this->isBool($inputs, 'goodInfo')
            && $this->isBool($inputs, 'priceShow') && $this->isBool($inputs, 'nodate')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateModel($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumericOrEmpty($inputs, 'id', true) && $this->isString($inputs, 'modelName')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateGoodGroup($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumeric($inputs, 'group_type') && $this->isArray($inputs, 'left_nav', true) && $this->isArray($inputs, 'top_nav', true)
            && $this->isString($inputs, 'left_nav.*.name', true) && $this->isString($inputs, 'left_nav.*.created_at', true)
            && $this->isNumeric($inputs, 'left_nav.*.id', true) && $this->isNumeric($inputs, 'left_nav.*.num', true)
            && $this->isString($inputs, 'top_nav.*.name', true) && $this->isString($inputs, 'top_nav.*.created_at', true)
            && $this->isNumeric($inputs, 'top_nav.*.id', true) && $this->isNumeric($inputs, 'top_nav.*.num', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateImageLink($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isArray($inputs, 'images') && $this->isString($inputs, 'images.*.linkTitle')
            && $this->isString($inputs, 'images.*.linkName') && $this->isString($inputs, 'images.*.linkUrl')
            && $this->isString($inputs, 'images.*.thumbnail') && $this->isBoolOrEmpty($inputs, 'images.*.chooseLink')
            && $this->isBool($inputs, 'images.*.dropDown') && $this->isNumericOrEmpty($inputs, 'images.*.image_id', true)
            && $this->isNumericOrEmpty($inputs, 'images.*.link_type', true) && $this->isNumericOrEmpty($inputs, 'images.*.link_id', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateTextlink($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isArray($inputs, 'textlink') && $this->isString($inputs, 'textlink.*.titleName')
            && $this->isString($inputs, 'textlink.*.linkName') && $this->isString($inputs, 'textlink.*.linkUrl')
            && $this->isBool($inputs, 'textlink.*.dropDown') && $this->isBool($inputs, 'textlink.*.chooseLink', true)
            && $this->isNumeric($inputs, 'textlink.*.link_type', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateBingbing($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isBool($inputs, 'chooseLink') && $this->isBool($inputs, 'dropDown') && $this->isString($inputs, 'linkName')
            && $this->isString($inputs, 'linkUrl') && $this->isString($inputs, 'bg_image') && $this->isArray($inputs, 'lists')
            && $this->isString($inputs, 'lists.*.title', true) && $this->isString($inputs, 'lists.*.linkName', true)
            && $this->isString($inputs, 'lists.*.linkUrl', true) && $this->isString($inputs, 'lists.*.icon', true)
            && $this->isString($inputs, 'lists.*.desc', true) && $this->isString($inputs, 'lists.*.bg_image', true)
            && $this->isString($inputs, 'lists.*.tag', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateMarketingActive($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isArray($inputs, 'content') && $this->isString($inputs, 'content.*.invalidate_at', true)
            && $this->isString($inputs, 'content.*.activeName') && $this->isString($inputs, 'content.*.activeType')
            && $this->isString($inputs, 'content.*.start_at') && $this->isString($inputs, 'content.*.end_at')
            && $this->isString($inputs, 'content.*.now_at', true) && $this->isString($inputs, 'content.*.timeDay')
            && $this->isNumeric($inputs, 'content.*.id') && $this->isNumeric($inputs, 'content.*.limit_num')
            && $this->isBoolOrNumeric($inputs, 'content.*.productStatus')) {
            return true;
        } else {
            return false;
        }
    }

    public function validateImageTextModel($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }

        if ($this->isNumeric($inputs, 'width') && $this->isArray($inputs, 'lists') && $this->isArray($inputs, 'slideLists')
            && $this->isNumeric($inputs, 'slideLists.*.id', true) && $this->isNumeric($inputs, 'slideLists.*.wid', true)
            && $this->isNumeric($inputs, 'slideLists.*.parent_id', true) && $this->isNumeric($inputs, 'slideLists.*.type', true)
            && $this->isNumeric($inputs, 'slideLists.*.show_cover_pic', true) && $this->isString($inputs, 'slideLists.*.title', true)
            && $this->isString($inputs, 'slideLists.*.cover', true) && $this->isString($inputs, 'slideLists.*.author', true)
            && $this->isString($inputs, 'slideLists.*.digest', true) && $this->isString($inputs, 'slideLists.*.content', true)
            && $this->isString($inputs, 'slideLists.*.content_source_url', true) && $this->isString($inputs, 'slideLists.*.content_source_title', true)
            && $this->isString($inputs, 'slideLists.*.created_at', true) && $this->isString($inputs, 'slideLists.*.updated_at', true)
            && $this->isString($inputs, 'slideLists.*.url', true) && $this->isBool($inputs, 'slideLists.*.operation', true)
            && $this->isString($inputs, 'lists.*.title', true) && $this->isNumeric($inputs, 'lists.*.lists.*.id', true)
            && $this->isNumeric($inputs, 'lists.*.lists.*.wid', true) && $this->isNumeric($inputs, 'lists.*.lists.*.parent_id', true)
            && $this->isNumeric($inputs, 'lists.*.lists.*.type', true) && $this->isNumeric($inputs, 'lists.*.lists.*.show_cover_pic', true)
            && $this->isString($inputs, 'lists.*.lists.*.title', true) && $this->isString($inputs, 'lists.*.lists.*.cover', true)
            && $this->isString($inputs, 'lists.*.lists.*.author', true) && $this->isString($inputs, 'lists.*.lists.*.digest', true)
            && $this->isString($inputs, 'lists.*.lists.*.content', true) && $this->isString($inputs, 'lists.*.lists.*.content_source_url', true)
            && $this->isString($inputs, 'lists.*.lists.*.content_source_title', true) && $this->isString($inputs, 'lists.*.lists.*.created_at', true)
            && $this->isString($inputs, 'lists.*.lists.*.updated_at', true) && $this->isString($inputs, 'lists.*.lists.*.url', true)
            && $this->isBool($inputs, 'lists.*.lists.*.operation', true)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateMobile($inputs)
    {
        $errmsg = '';
        $commonResult = $this->commonValidate($inputs);
        if (!$commonResult) {
            return false;
        }
        if ((empty($inputs['mobileStyle']) || $this->isNumeric($inputs, 'mobileStyle')) && $this->isArray($inputs, 'lists')
            && $this->isNumeric($inputs, 'lists.*.mobile') && $this->isBool($inputs, 'lists.*.close')) {
            return true;
        }
        return false;
    }

    protected function commonValidate($inputs)
    {
        $v = Validator::make($inputs, [
            'type' => 'required|string',
            'showRight' => 'required|boolean',
            'cardRight' => 'required|numeric',
            'is_add_content' => 'sometimes|required|boolean',
        ], [
            'type.required' => '组件类型不能为空',
            'type.string' => '组件类型必须是字符串',
            'showRight.required' => '右侧展示不能为空',
            'showRight.boolean' => '右侧展示必须是布尔值',
            'cardRight.required' => '右侧卡片不能为空',
            'cardRight.numeric' => '右侧卡片必须是数字',
            'is_add_content.required' => '是否增加内容不能为空',
            'is_add_content.boolean' => '是否增加内容必须是布尔值',
        ]);

        if ($v->fails()) {
            $this->errno = 1;
            $this->errmsg = $v->messages()->first();
            return false;
        }

        if (!isset($inputs['editing']) && ($inputs['editing'] !== 'editing' || $inputs['editing'] !== '')) {
            $this->errno = 1;
            $this->errmsg = '组件选中为空或值错误';
            return false;
        }

        return true;
    }

    /**
     * traversal each component and validate
     *
     * @param string|arrray $inputs
     * @return bool
     */
    public function validateComponents($inputs)
    {
        if (!config('app.enable_diy_component_validate')) {
            return true;
        }
        try {
            if (is_null($inputs)) {
                return true;
            }
            if (!is_array($inputs)) {
                $inputs = json_decode($inputs, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->errno = 1;
                    $this->errmsg = '非法的json字符串';
                    return false;
                }

                if (is_string($inputs)) {
                    $inputs = json_decode($inputs, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $this->errno = 1;
                        $this->errmsg = '非法的json字符串';
                        return false;
                    }
                }
            }

            foreach ($inputs as $input) {
                if (!isset($input['type'])) {
                    return ['errno' => 1, 'errmsg' => $input['type'] . '非法类型'];
                    $this->errno = 1;
                    $this->errmsg = $input['type'] . '非法类型';
                    return false;
                }
                $method = 'validate' . Str::studly($input['type']);
                if (method_exists($this, $method)) {
                    $return = $this->$method($input);
                    if (!$return) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error($e->getLine() . '[' . $e->getMessage() . ':' . json_last_error_msg() . ']');
        }
    }

    protected function isBool($in, $index, $whenPresent = false)
    {
        return $this->is('is_bool', $in, $index, $whenPresent);
    }

    protected function isBoolOrEmpty($in, $index, $whenPresent = false)
    {
        return $this->is('is_bool||is_empty', $in, $index, $whenPresent);
    }

    protected function isBoolOrNumeric($in, $index, $whenPresent = false)
    {
        return $this->is('is_bool||is_numeric', $in, $index, $whenPresent);
    }

    protected function isString($in, $index, $whenPresent = false)
    {
        return $this->is('is_string', $in, $index, $whenPresent);
    }

    protected function isArray($in, $index, $whenPresent = false)
    {
        return $this->is('is_array', $in, $index, $whenPresent);
    }

    protected function isNumeric($in, $index, $whenPresent = false)
    {
        return $this->is('is_numeric', $in, $index, $whenPresent);
    }

    protected function isNumericOrEmpty($in, $index, $whenPresent = false)
    {
        return $this->is('is_numeric||is_empty', $in, $index, $whenPresent);
    }

    /**
     * @param string $method method name
     * @param array $in source data
     * @param string $index the keys which need to validate
     * @param bool $whenPresent only validate when the key present
     * @return bool
     */
    protected function is($method, $in, $index, $whenPresent = false)
    {
        $parts = explode('.', $index);
        $pre = $in;

        while (count($parts) > 1) {
            $part = array_shift($parts);
            if ($part === '*') {
                if (!is_array($pre)) {
                    $this->errno = 1;
                    $this->errmsg = $index . '不存在';
                    return false;
                }
                foreach ($pre as $v) {
                    if (!$this->is($method, $v, implode('.', $parts), $whenPresent)) {
                        return false;
                    }
                }
                return true;
            } else {
                if (isset($pre[$part]) || $whenPresent) {
                    $pre = $pre[$part];
                } else {
                    $this->errno = 1;
                    $this->errmsg = $index . '不存在';
                    return false;
                }
            }
        }

        $end = end($parts);
        $methods = explode('||', $method);
        if ($end === '*') {
            if (is_array($pre)) {
                foreach ($pre as $v) {
                    $result = false;
                    foreach ($methods as $vm) {
                        $vResult = $vm($v);
                        $result = $result || $vResult;
                    }
                    if (!$result) {
                        $this->errno = 1;
                        $this->errmsg = $index . '类型不对';
                        return false;
                    }
                }
                return true;
            }
        } else {
            if ((!isset($pre[$end]) && $whenPresent)) {
                return true;
            } else {
                if (isset($pre[$end])) {
                    $result = false;
                    foreach ($methods as $v) {
                        $vResult = $v($pre[$end]);
                        $result = $result || $vResult;
                    }
                    if ($result) {
                        return true;
                    } else {
                        $this->errno = 1;
                        $this->errmsg = $index . '类型不对';
                        return false;
                    }
                } else {
                    $this->errno = 1;
                    $this->errmsg = $index . '不存在';
                    return false;
                }
            }
        }
    }

    /**
     * return error no
     *
     * @return int
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * return error message
     *
     * @return string
     */
    public function getErrmsg()
    {
        return $this->errmsg;
    }

    /***
     * todo  过滤表情
     * @param $str
     * @return mixed
     * @author jonzhang
     * @date 2017-11-21
     */
    function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }

    /**
     * todo 处理微页面模块数据
     * @param string $jsonData
     * @return array
     * @author jonzhang
     * @date 2017-11-24
     */
    public function processModel($jsonData = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        if (empty($jsonData)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '数据为空';
            return $returnData;
        }

        if (is_string($jsonData)) {
            $jsonData = json_decode($jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = 'json数据有问题';
                return $returnData;
            }
        }
        //未遍历 富文本[type=rich_text]，标题[type=title]，公告[type=notice]，商品搜索[type=search]，图片导航[type=image_link]，文本导航[type=textlink]
        foreach ($jsonData as &$item) {
            //商品 已经okay
            if ($item['type'] == 'goods') {
                if (isset($item['goods'])) {
                    $item['goods'] = [];
                }
                if (isset($item['thGoods'])) {
                    $item['thGoods'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            }//优惠券 已okay
            else if ($item['type'] == 'coupon') {
                if (isset($item['couponList'])) {
                    $item['couponList'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            }//图片广告 //注意测试 测试okay
            else if ($item['type'] == 'image_ad') {
                if (isset($item['images'])) {
                    foreach ($item['images'] as &$imageItem) {
                        if (isset($imageItem['FileInfo'])) {
                            $imageItem['FileInfo'] = [];
                        }
                    }
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            }//店铺信息
            else if ($item['type'] == 'store') {
                if (isset($item['url'])) {
                    $item['url'] = '';
                }
                if (isset($item['store_name'])) {
                    $item['store_name'] = '';
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            } //自定义模块 //未做完
            else if ($item['type'] == 'model') {
                if (isset($item['modelName'])) {
                    $item['modelName'] = '';
                }
                if (isset($item['template_data'])) {
                    $item['template_data'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            } //商品列表
            else if ($item['type'] == 'goodslist') {
                if (isset($item['goods'])) {
                    $item['goods'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            } //商品分组 //待测试 测试okay
            else if ($item['type'] == 'good_group') {
                if (isset($item['group_type']) && $item['group_type'] == 1) {
                    if (!empty($item['left_nav'])) {
                        foreach ($item['left_nav'] as &$goodItem) {
                            $goodItem['goods'] = [];
                            if (isset($goodItem['error'])) {
                                unset($goodItem['error']);
                            }
                        }
                    }
                } else if (isset($item['group_type']) && $item['group_type'] == 2) {
                    if (!empty($item['top_nav'])) {
                        foreach ($item['top_nav'] as &$goodItem) {
                            $goodItem['goods'] = [];
                            if (isset($goodItem['error'])) {
                                unset($goodItem['error']);
                            }
                        }
                    }
                }
            }//秒杀模块
            else if ($item['type'] == 'marketing_active') {
                if (isset($item['content']) && !empty($item['content'])) {
                    foreach ($item['content'] as &$activeItem) {
                        if (isset($activeItem['product'])) {
                            $activeItem['product'] = [];
                        }
                        if (isset($activeItem['sku'])) {
                            $activeItem['sku'] = [];
                        }
                        if (isset($activeItem['invalidate_at'])) {
                            $activeItem['invalidate_at'] = [];
                        }
                        if (isset($activeItem['now_at'])) {
                            $activeItem['now_at'] = [];
                        }
                    }
                }
            } //商品分组详情
            else if ($item['type'] == 'goods_group') {
                if (isset($item['goods'])) {
                    $item['goods'] = [];
                }
                if (isset($item['thGoods'])) {
                    $item['thGoods'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            }//会员卡
            else if ($item['type'] == 'card') {
                if (isset($item['cardList'])) {
                    $item['cardList'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            } //拼团分类
            else if ($item['type'] == 'spell_title') {
                if (isset($item['pages'])) {
                    $item['pages'] = [];
                }
                if (isset($item['default']['data'])) {
                    $item['default']['data'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            } //拼团商品
            else if ($item['type'] == 'spell_goods') {
                if (isset($item['groups'])) {
                    $item['groups'] = [];
                }
                if (isset($item['error'])) {
                    unset($item['error']);
                }
            }//视频
            else if ($item['type'] == 'video') {
                if (isset($item['videoItem'])) {
                    $item['videoItem'] = [];
                }
            }
        }

        if (is_array($jsonData)) {
            $jsonData = json_encode($jsonData);
        }
        $returnData['data'] = $jsonData;
        return $returnData;
    }

    /***
     * todo 过滤数据[输入]
     * @param string $jsonData
     * @author jonzhang
     * @date 2017-11-24
     */
    public function filterDataForIn($jsonData = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        if (empty($jsonData)) {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = '数据为空';
            return $returnData;
        }
        if (!get_magic_quotes_gpc()) {
            $jsonData = addslashes($jsonData);
        }
        return $returnData['data'] = $jsonData;
    }

    /***
     * todo 过滤数据[输出]
     * @param string $jsonData
     * @author jonzhang
     * @date 2017-11-24
     */
    public function filterDataForOut($jsonData = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        if (empty($jsonData)) {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = '数据为空';
            return $returnData;
        }
        if (!get_magic_quotes_gpc()) {
            $jsonData = stripslashes($jsonData);
        }
        return $returnData['data'] = $jsonData;
    }

    /**
     * todo 检查微页面每个模块数据是否都存在
     * @param string $jsonData
     * @author jonzhang
     * @date 2017-11-24
     * @update 许立 2018年08月28日 商品分组类型增加列表样式字段
     * @update 何书哲 2020年03月12日 添加直播组件类型
     */
    public function checkModel($jsonData = '', $isCheck = true)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];

        // false 不做数据效验
        if (!$isCheck) {
            $returnData['data'] = $jsonData;
            return $returnData;
        }

        if (empty($jsonData)) {
            $returnData['errCode'] = -101;
            $returnData['errMsg'] = '数据为空';
            return $returnData;
        }

        // 字符串转化为数组
        if (is_string($jsonData)) {
            $jsonData = json_decode($jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $returnData['errCode'] = -104;
                $returnData['errMsg'] = 'json数据有问题';
                return $returnData;
            }
        } else {
            // $jsonData不是数组了,报异常信息
            if (!is_array($jsonData)) {
                $returnData['errCode'] = -105;
                $returnData['errMsg'] = '数据不符合要求';
                return $returnData;
            }
        }
        try {
            // 遍历数组 *****不需要的数据不能够赋值为空，微页面模板/店铺模板中有的地方会赋值*****
            foreach ($jsonData as &$item) {
                $str = "";
                // 富文本 需要过滤输入内容 okay
                if (isset($item['type']) && $item['type'] == 'rich_text') {
                    // 富文本所有元素
                    $rtKeys = array_keys($item);
                    // 富文本必须元素
                    $rtTargetKeys = ['showRight', 'cardRight', 'content', 'type', 'editing', 'bgcolor', 'initShow', 'is_add_content'];
                    foreach ($rtKeys as $rtItem) {
                        if (in_array($rtItem, $rtTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$rtItem])) {
                            unset($item[$rtItem]);
                            continue;
                        }
                        is_array($item[$rtItem]) && ($str .= $rtItem . ":" . json_encode($item[$rtItem]) . "\t");
                        is_string($item[$rtItem]) && ($str .= $rtItem . ":" . json_encode($item[$rtItem]) . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('rich_text more item' . $str . 'type:rich_text:' . json_encode($item));
                    }
                }
                // 商品 okay
                else if (isset($item['type']) && $item['type'] == 'goods') {
                    // 商品所有元素
                    $gKeys = array_keys($item);
                    // 商品必须元素
                    $gTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'listStyle', 'cardStyle', 'showSell', 'btnStyle', 'goodName',
                        'goodInfo', 'priceShow', 'nodate', 'goods', 'thGoods', 'products_id', 'is_add_content', 'error', 'tiled'];
                    foreach ($gKeys as $gItem) {
                        if (in_array($gItem, $gTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$gItem])) {
                            unset($item[$gItem]);
                            continue;
                        }
                        is_array($item[$gItem]) && ($str .= $gItem . ":" . json_encode($item[$gItem]) . "\t");
                        is_string($item[$gItem]) && ($str .= $gItem . ":" . $item[$gItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('goods more item' . $str . 'type:goods:' . json_encode($item));
                    }
                    if (!empty($item['goods'])) {
                        $item['goods'] = [];
                    }
                    if (!empty($item['thGoods'])) {
                        $item['thGoods'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 图片广告
                else if (isset($item['type']) && $item['type'] == 'image_ad') {
                    // image_ad 模板数据ds_template_market使用到 不能够对此模块数据， $item['images']进行赋值[]
                    // 图片广告所有元素
                    $iaKeys = array_keys($item);
                    // 图片广告必须元素
                    $iaTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'advsListStyle', 'advSize', 'images', 'content', 'is_add_content', 'error', 'resize_image'];
                    foreach ($iaKeys as $iaItem) {
                        if (in_array($iaItem, $iaTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$iaItem])) {
                            unset($item[$iaItem]);
                            continue;
                        }
                        is_array($item[$iaItem]) && ($str .= $iaItem . ":" . json_encode($item[$iaItem]) . "\t");
                        is_string($item[$iaItem]) && ($str .= $iaItem . ":" . $item[$iaItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('image_ad more item' . $str . 'type:image_ad:' . json_encode($item));
                    }
                }
                // 标题 okay
                else if (isset($item['type']) && $item['type'] == 'title') {
                    // title 模板数据ds_template_market使用到 不能够对此模块数据， titleName进行赋值''
                    // 标题所有元素
                    $tKeys = array_keys($item);
                    // 标题必须元素
                    $tTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'titleName', 'titleStyle', 'subTitle', 'showPosition', 'bgColor', 'addLink',
                        'chooseLink', 'dropDown', 'linkName', 'linkUrl', 'date', 'author', 'wlinkTitle', 'wlinkUrlChoose', 'wlinkUrl', 'is_add_content', 'linkTitle'];
                    foreach ($tKeys as $tItem) {
                        if (in_array($tItem, $tTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$tItem])) {
                            unset($item[$tItem]);
                            continue;
                        }
                        is_array($item[$tItem]) && ($str .= $tItem . ":" . json_encode($item[$tItem]) . "\t");
                        is_string($item[$tItem]) && ($str .= $tItem . ":" . $item[$tItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('title more item' . $str . ' type:title:' . json_encode($item));
                    }
                }
                // 进入店铺 okay
                else if (isset($item['type']) && $item['type'] == 'store') {
                    // 进入店铺所有元素
                    $sKeys = array_keys($item);
                    // 进入店铺必须元素
                    $sTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'store_name', 'id', 'url', 'is_add_content', 'error'];
                    foreach ($sKeys as $sItem) {
                        if (in_array($sItem, $sTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$sItem])) {
                            unset($item[$sItem]);
                            continue;
                        }
                        is_array($item[$sItem]) && ($str .= $sItem . ":" . json_encode($item[$sItem]) . "\t");
                        is_string($item[$sItem]) && ($str .= $sItem . ":" . $item[$sItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('store more item' . $str . ' type:store:' . json_encode($item));
                    }
                    if (!empty($item['store_name'])) {
                        $item['store_name'] = '';
                    }
                    if (!empty($item['url'])) {
                        $item['url'] = '';
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 优惠券 okay
                else if (isset($item['type']) && $item['type'] == 'coupon') {
                    // 优惠券所有元素
                    $cKeys = array_keys($item);
                    // 优惠券必须元素
                    $cTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'show', 'couponList', 'coupons_id', 'is_add_content', 'error', 'couponStyle', 'couponColor'];
                    foreach ($cKeys as $cItem) {
                        if (in_array($cItem, $cTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$cItem])) {
                            unset($item[$cItem]);
                            continue;
                        }
                        is_array($item[$cItem]) && ($str .= $cItem . ":" . json_encode($item[$cItem]) . "\t");
                        is_string($item[$cItem]) && ($str .= $cItem . ":" . $item[$cItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('coupon more item' . $str . ' type:coupon:' . json_encode($item));
                    }
                    if (!empty($item['couponList'])) {
                        $item['couponList'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 公告
                else if (isset($item['type']) && $item['type'] == 'notice') {
                    // 优惠券所有元素
                    $nKeys = array_keys($item);
                    // 优惠券必须元素
                    $nTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'content', 'is_add_content', 'placeholder', 'txtBg', 'colorBg'];
                    foreach ($nKeys as $nItem) {
                        if (in_array($nItem, $nTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$nItem])) {
                            unset($item[$nItem]);
                            continue;
                        }
                        is_array($item[$nItem]) && ($str .= $nItem . ":" . json_encode($item[$nItem]) . "\t");
                        is_string($item[$nItem]) && ($str .= $nItem . ":" . $item[$nItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('notice more item' . $str . ' type:notice:' . json_encode($item));
                    }
                }
                // 商品搜索 okay
                else if (isset($item['type']) && $item['type'] == 'search') {
                    // 商品搜索所有元素
                    $sKeys = array_keys($item);
                    // 商品搜索必须元素
                    $sTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'bgColor', 'is_add_content', 'searchName', 'searchStyle'];
                    foreach ($sKeys as $sItem) {
                        if (in_array($sItem, $sTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$sItem])) {
                            unset($item[$sItem]);
                            continue;
                        }
                        is_array($item[$sItem]) && ($str .= $sItem . ":" . json_encode($item[$sItem]) . "\t");
                        is_string($item[$sItem]) && ($str .= $sItem . ":" . $item[$sItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('search more item' . $str . ' type:search:' . json_encode($item));
                    }
                }
                // 商品列表
                else if (isset($item['type']) && $item['type'] == 'goodslist') {
                    // 商品列表所有元素
                    $glKeys = array_keys($item);
                    // 商品列表必须元素
                    $glTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'groupName', 'group_id', 'listStyle', 'cardStyle', 'showSell', 'btnStyle',
                        'goodName', 'goodInfo', 'priceShow', 'nodate', 'showNum', 'goods', 'thGoods', 'is_add_content', 'error', 'tiled'];
                    foreach ($glKeys as $glItem) {
                        if (in_array($glItem, $glTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$glItem])) {
                            unset($item[$glItem]);
                            continue;
                        }
                        is_array($item[$glItem]) && ($str .= $glItem . ":" . json_encode($item[$glItem]) . "\t");
                        is_string($item[$glItem]) && ($str .= $glItem . ":" . $item[$glItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('goodslist more item' . $str . ' type:goodslist:' . json_encode($item));
                    }
                    if (!empty($item['goods'])) {
                        $item['goods'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 自定义模块
                else if (isset($item['type']) && $item['type'] == 'model') {
                    // 自定义模块所有元素
                    $mKeys = array_keys($item);
                    // 自定义模块必须元素
                    $mTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'modelName', 'id', 'template_data', 'is_add_content', 'error'];
                    foreach ($mKeys as $mItem) {
                        if (in_array($mItem, $mTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$mItem])) {
                            unset($item[$mItem]);
                            continue;
                        }
                        is_array($item[$mItem]) && ($str .= $mItem . ":" . json_encode($item[$mItem]) . "\t");
                        is_string($item[$mItem]) && ($str .= $mItem . ":" . $item[$mItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('model more item' . $str . ' type:model:' . json_encode($item));
                    }
                    if (!empty($item['modelName'])) {
                        $item['modelName'] = '';
                    }
                    if (!empty($item['template_data'])) {
                        $item['template_data'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 商品分组
                else if (isset($item['type']) && $item['type'] == 'good_group') {
                    // 商品分组所有元素
                    $ggKeys = array_keys($item);
                    // 商品分组必须元素
                    $ggTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'left_nav', 'top_nav', 'group_type', 'width', 'show_type', 'isall', 'show_num', 'is_add_content', 'listStyle'];
                    foreach ($ggKeys as $ggItem) {
                        if (in_array($ggItem, $ggTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$ggItem])) {
                            unset($item[$ggItem]);
                            continue;
                        }
                        is_array($item[$ggItem]) && ($str .= $ggItem . ":" . json_encode($item[$ggItem]) . "\t");
                        is_string($item[$ggItem]) && ($str .= $ggItem . ":" . $item[$ggItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('good_group more item' . $str . ' type:good_group:' . json_encode($item));
                    }
                    // 遍历item['left_nav']  item['top_nav']
                    if (isset($item['group_type']) && $item['group_type'] == 1) {
                        if (isset($item['left_nav'])) {
                            foreach ($item['left_nav'] as &$navItem) {
                                if (isset($navItem['goods'])) {
                                    $navItem['goods'] = [];
                                }
                                if (isset($navItem['error'])) {
                                    unset($navItem['error']);
                                }
                            }
                        }
                        if (isset($item['top_nav'])) {
                            $item['top_nav'] = [];
                        }
                    } else if (isset($item['group_type']) && $item['group_type'] == 2) {
                        // 对top_nav中的goods中商品进行处理
                        if (isset($item['top_nav'])) {
                            foreach ($item['top_nav'] as &$navItem) {
                                if (isset($navItem['goods'])) {
                                    $navItem['goods'] = [];
                                }
                                if (isset($navItem['error'])) {
                                    unset($navItem['error']);
                                }
                            }
                        }
                        // left_nav赋值为[]
                        if (isset($item['left_nav'])) {
                            $item['left_nav'] = [];
                        }
                    }
                }
                // 图片导航
                else if (isset($item['type']) && $item['type'] == 'image_link') {
                    // 此模块 ds_template_market数据表 模块数据使用到 item['images']不能够赋值[]
                    // 图片导航所有元素
                    $ilKeys = array_keys($item);
                    // 图片导航必须元素
                    $ilTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'images', 'is_add_content'];
                    foreach ($ilKeys as $ilItem) {
                        if (in_array($ilItem, $ilTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$ilItem])) {
                            unset($item[$ilItem]);
                            continue;
                        }
                        is_array($item[$ilItem]) && ($str .= $ilItem . ":" . json_encode($item[$ilItem]) . "\t");
                        is_string($item[$ilItem]) && ($str .= $ilItem . ":" . $item[$ilItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('image_link more item' . $str . ' type:image_link:' . json_encode($item));
                    }
                    // images 是个数组内部元素[linkTitle,linkName,linkUrl,chooseLink,dropDown,thumbnail,image_id,link_typelink_id,]
                }
                // 文本导航
                else if (isset($item['type']) && $item['type'] == 'textlink') {
                    // 此模块 ds_template_market数据表 模块数据使用到 item['textlink']不能够赋值[]
                    // 文本导航所有元素
                    $tlKeys = array_keys($item);
                    // 文本导航必须元素
                    $tlTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'textlink', 'is_add_content'];
                    foreach ($tlKeys as $tlItem) {
                        if (in_array($tlItem, $tlTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$tlItem])) {
                            unset($item[$tlItem]);
                            continue;
                        }
                        is_array($item[$tlItem]) && ($str .= $tlItem . ":" . json_encode($item[$tlItem]) . "\t");
                        is_string($item[$tlItem]) && ($str .= $tlItem . ":" . $item[$tlItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('textlink more item' . $str . ' type:textlink:' . json_encode($item));
                    }
                    // textlink 数组内部元素[titleName,dropDown,linkName,linkUrl,link_id,chooseLink]
                }
                // 营销活动
                else if (isset($item['type']) && $item['type'] == 'marketing_active') {
                    // 营销活动所有元素
                    $maKeys = array_keys($item);
                    // 营销活动必须元素
                    // $maTargetKeys没有使用error
                    $maTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'addActiveShow', 'content', 'is_add_content', 'error'];
                    foreach ($maKeys as $maItem) {
                        if (in_array($maItem, $maTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$maItem])) {
                            unset($item[$maItem]);
                            continue;
                        }
                        is_array($item[$maItem]) && ($str .= $maItem . ":" . json_encode($item[$maItem]) . "\t");
                        is_string($item[$maItem]) && ($str .= $maItem . ":" . $item[$maItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('marketing_active more item' . $str . ' type:marketing_active:' . json_encode($item));
                    }
                    if (!empty($item['content'])) {
                        foreach ($item['content'] as &$contentItem) {
                            if (isset($contentItem['product'])) {
                                $contentItem['product'] = [];
                            }
                            if (isset($contentItem['sku'])) {
                                $contentItem['sku'] = [];
                            }
                            if (isset($contentItem['invalidate_at'])) {
                                $contentItem['invalidate_at'] = "1970-01-01 00:00:00";
                            }
                            if (isset($contentItem['now_at'])) {
                                $contentItem['now_at'] = "1970-01-01 00:00:00";
                            }
                            if (isset($contentItem['error'])) {
                                unset($contentItem['error']);
                            }
                        }
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 会员卡
                else if (isset($item['type']) && $item['type'] == 'card') {
                    // 会员卡所有元素
                    $cKeys = array_keys($item);
                    // 会员卡必须元素
                    $cTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'cardList', 'card_ids', 'is_add_content', 'error'];
                    foreach ($cKeys as $cItem) {
                        if (in_array($cItem, $cTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$cItem])) {
                            unset($item[$cItem]);
                            continue;
                        }
                        is_array($item[$cItem]) && ($str .= $cItem . ":" . json_encode($item[$cItem]) . "\t");
                        is_string($item[$cItem]) && ($str .= $cItem . ":" . $item[$cItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('card more item' . $str . ' type:card:' . json_encode($item));
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 拼团商品
                else if (isset($item['type']) && $item['type'] == 'spell_goods') {
                    // 拼团商品所有元素
                    $sgKeys = array_keys($item);
                    // 拼团商品必须元素
                    $sgTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'groups', 'groups_id', 'style', 'is_add_content', 'error'];
                    foreach ($sgKeys as $sgItem) {
                        if (in_array($sgItem, $sgTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$sgItem])) {
                            unset($item[$sgItem]);
                            continue;
                        }
                        is_array($item[$sgItem]) && ($str .= $sgItem . ":" . json_encode($item[$sgItem]) . "\t");
                        is_string($item[$sgItem]) && ($str .= $sgItem . ":" . $item[$sgItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('spell_goods more item' . $str . ' type:spell_goods:' . json_encode($item));
                    }
                    if (!empty($item['groups'])) {
                        $item['groups'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 视频
                else if (isset($item['type']) && $item['type'] == 'video') {
                    // 视频所有元素
                    $vKeys = array_keys($item);
                    // 视频必须元素 error暂且没有
                    $vTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'id', 'videoItem', 'is_add_content', 'error'];
                    foreach ($vKeys as $vItem) {
                        if (in_array($vItem, $vTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$vItem])) {
                            unset($item[$vItem]);
                            continue;
                        }
                        is_array($item[$vItem]) && ($str .= $vItem . ":" . json_encode($item[$vItem]) . "\t");
                        is_string($item[$vItem]) && ($str .= $vItem . ":" . $item[$vItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('video more item' . $str . ' type:video:' . json_encode($item));
                    }
                    if (!empty($item['videoItem'])) {
                        $item['videoItem'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 微官网模板2
                else if (isset($item['type']) && $item['type'] == 'imageTextModel') {
                    continue;
                    // 后面暂且不需要过滤
                    // 微官网模板2所有元素
                    $itmKeys = array_keys($item);
                    // 微官网模板2必须元素
                    $itmTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'slideLists', 'lists', 'width', 'is_add_content'];
                    foreach ($itmKeys as $itmItem) {
                        if (in_array($itmItem, $itmTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$itmItem])) {
                            unset($item[$itmItem]);
                            continue;
                        }
                        is_array($item[$itmItem]) && ($str .= $itmItem . ":" . json_encode($item[$itmItem]) . "\t");
                        is_string($item[$itmItem]) && ($str .= $itmItem . ":" . $item[$itmItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('imageTextModel more item' . $str . ' type:imageTextModel:' . json_encode($item));
                    }
                    if (isset($item['lists'])) {
                        // 待完成
                    } else if (isset($item['slideLists'])) {
                        // 待完成
                    }
                }
                // 微官网模板
                else if (isset($item['type']) && $item['type'] == 'bingbing') {
                    continue;
                    // 后面的方法不会执行
                    // 微官网模板所有元素
                    $bbKeys = array_keys($item);
                    // 微官网模板必须元素
                    $bbTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'chooseLink', 'dropDown', 'linkName', 'linkUrl', 'bg_image', 'lists', 'is_add_content'];
                    foreach ($bbKeys as $bbItem) {
                        if (in_array($bbItem, $bbTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$bbItem])) {
                            unset($item[$bbItem]);
                            continue;
                        }
                        is_array($item[$bbItem]) && ($str .= $bbItem . ":" . json_encode($item[$bbItem]) . "\t");
                        is_string($item[$bbItem]) && ($str .= $bbItem . ":" . $item[$bbItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('bingbing more item' . $str . ' type:bingbing:' . json_encode($item));
                    }
                    if (isset($item['lists'])) {
                        // 暂未完成
                        $bbiTargetKeys = ['title', 'linkName', 'linkUrl', 'icon', 'desc', 'bg_image', 'tag'];
                    }
                }
                // 美妆小店模板
                else if (isset($item['type']) && $item['type'] == 'header') {
                    continue;
                    // 美妆小店模板
                    $hKeys = array_keys($item);
                    // 美妆小店模板
                    $hTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'store_name', 'logo', 'bg_image', 'bg_color', 'order_link'];
                    foreach ($hKeys as $hItem) {
                        if (in_array($hItem, $hTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$hItem])) {
                            unset($item[$hItem]);
                            continue;
                        }
                        is_array($item[$hItem]) && ($str .= $hItem . ":" . json_encode($item[$hItem]) . "\t");
                        is_string($item[$hItem]) && ($str .= $hItem . ":" . $item[$hItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('header more item' . $str . ' type:header:' . json_encode($item));
                    }
                }
                // 享立减商品
                else if (isset($item['type']) && $item['type'] == 'share_goods') {
                    // 享立减商品所有元素
                    $shareKeys = array_keys($item);
                    $shareTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'listStyle', 'cardStyle', 'btnStyle', 'goodName', 'goodInfo',
                        'priceShow', 'nodate', 'activitys', 'thGoods', 'activity_id', 'is_add_content', 'error'];
                    foreach ($shareKeys as $shareItem) {
                        if (in_array($shareItem, $shareTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$shareItem])) {
                            unset($item[$shareItem]);
                            continue;
                        }
                        is_array($item[$shareItem]) && ($str .= $shareItem . ":" . json_encode($item[$shareItem]) . "\t");
                        is_string($item[$shareItem]) && ($str .= $shareItem . ":" . $item[$shareItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('share_goods more item' . $str . ' type:share_goods:' . json_encode($item));
                    }
                    if (!empty($item['activitys'])) {
                        $item['activitys'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 魔方
                else if (isset($item['type']) && $item['type'] == 'cube') {
                    continue;
                }
                // 享立减2
                else if (isset($item['type']) && $item['type'] == 'li_goods') {
                    // 享立减商品所有元素
                    $shareKeys = array_keys($item);
                    $shareTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'listStyle', 'cardStyle', 'btnStyle', 'goodName', 'goodInfo',
                        'priceShow', 'nodate', 'activitys', 'thGoods', 'activity_id', 'is_add_content', 'error'];
                    foreach ($shareKeys as $shareItem) {
                        if (in_array($shareItem, $shareTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$shareItem])) {
                            unset($item[$shareItem]);
                            continue;
                        }
                        is_array($item[$shareItem]) && ($str .= $shareItem . ":" . json_encode($item[$shareItem]) . "\t");
                        is_string($item[$shareItem]) && ($str .= $shareItem . ":" . $item[$shareItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('li_goods more item' . $str . ' type:li_goods:' . json_encode($item));
                    }
                    if (!empty($item['activitys'])) {
                        $item['activitys'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 拨打电话
                else if (isset($item['type']) && $item['type'] == 'mobile') {
                    // 拨打电话所有元素
                    $mobileKeys = array_keys($item);
                    $mobileTargetKeys = ['showRight', 'cardRight', 'type', 'editing', 'icon', 'title', 'lists', 'is_add_content', 'error', 'mobileStyle'];
                    foreach ($mobileKeys as $mobileItem) {
                        if (in_array($mobileItem, $mobileTargetKeys)) {
                            continue;
                        }
                        is_array($item[$mobileItem]) && ($str .= $mobileItem . ":" . json_encode($item[$mobileItem]) . "\t");
                        is_string($item[$mobileItem]) && ($str .= $mobileItem . ":" . $item[$mobileItem] . "\t");
                        unset($item[$mobileItem]);
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('mobile more item' . $str . ' type:mobile:' . json_encode($item));
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 留言板
                else if (isset($item['type']) && $item['type'] == 'research') {
                    // 留言板所有元素
                    $rKeys = array_keys($item);
                    // 留言板必须元素
                    $nTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'resList', 'is_add_content', 'error'];
                    foreach ($rKeys as $nItem) {
                        if (in_array($nItem, $nTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$nItem])) {
                            unset($item[$nItem]);
                            continue;
                        }
                        is_array($item[$nItem]) && ($str .= $nItem . ":" . json_encode($item[$nItem]) . "\t");
                        is_string($item[$nItem]) && ($str .= $nItem . ":" . $item[$nItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('research more item' . $str . ' type:research:' . json_encode($item));
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 秒杀列表
                else if (isset($item['type']) && $item['type'] == 'seckill_list') {
                    // 秒杀列表所有元素
                    $secKeys = array_keys($item);
                    // 秒杀列表必须元素
                    $nTargetKeys = ['listStyle', 'showRight', 'showTitle', 'cardRight', 'editing', 'type', 'showTimer', 'hideOut', 'hideEnd', 'remanent', 'remanentStyle',
                        'showBtn', 'btnStyle', 'killList', 'headImage', 'thSecGoods', 'seckillIds', 'is_add_content', 'seckillList', 'error'];
                    foreach ($secKeys as $nItem) {
                        if (in_array($nItem, $nTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$nItem])) {
                            unset($item[$nItem]);
                            continue;
                        }
                        is_array($item[$nItem]) && ($str .= $nItem . ":" . json_encode($item[$nItem]) . "\t");
                        is_string($item[$nItem]) && ($str .= $nItem . ":" . $item[$nItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('seckill_list more item' . $str . ' type:seckill_list:' . json_encode($item));
                    }
                    if (empty($item['seckillList'])) {
                        $item['seckillList'] = [];
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 预约
                else if (isset($item['type']) && $item['type'] == 'researchAppoint') {
                    // 预约所有元素
                    $raKeys = array_keys($item);
                    // 预约必须元素
                    $nTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'resList', 'is_add_content', 'error'];
                    foreach ($raKeys as $nItem) {
                        if (in_array($nItem, $nTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$nItem])) {
                            unset($item[$nItem]);
                            continue;
                        }
                        is_array($item[$nItem]) && ($str .= $nItem . ":" . json_encode($item[$nItem]) . "\t");
                        is_string($item[$nItem]) && ($str .= $nItem . ":" . $item[$nItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('researchAppoint more item' . $str . ' type:researchAppoint:' . json_encode($item));
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 商品分组页
                else if (isset($item['type']) && $item['type'] == 'group_page') {
                    // 商品分组所有元素
                    $raKeys = array_keys($item);
                    // 预约必须元素
                    $nTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'classifyList', 'is_add_content', 'error'];
                    foreach ($raKeys as $nItem) {
                        if (in_array($nItem, $nTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$nItem])) {
                            unset($item[$nItem]);
                            continue;
                        }
                        is_array($item[$nItem]) && ($str .= $nItem . ":" . json_encode($item[$nItem]) . "\t");
                        is_string($item[$nItem]) && ($str .= $nItem . ":" . $item[$nItem] . "\t");
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('group_page more item' . $str . ' type:group_page:' . json_encode($item));
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 直播
                else if (isset($item['type']) && $item['type'] == 'live') {
                    // 直播所有元素
                    $raKeys = array_keys($item);
                    // 直播必须元素
                    $nTargetKeys = ['showRight', 'cardRight', 'editing', 'type', 'title', 'show_title', 'img', 'room', 'is_add_content', 'error'];
                    foreach ($raKeys as $nItem) {
                        if (in_array($nItem, $nTargetKeys)) {
                            continue;
                        }
                        if (empty($item[$nItem])) {
                            unset($item[$nItem]);
                            continue;
                        }
                        if (is_array($item[$nItem])) {
                            $str .= $nItem . ":" . json_encode($item[$nItem]) . "\t";
                        } elseif (is_string($item[$nItem])) {
                            $str .= $nItem . ":" . $item[$nItem] . "\t";
                        }
                    }
                    if (strlen($str) > 0) {
                        BLogger::getLogger('info')->info('live more item' . $str . ' type:live:' . json_encode($item));
                    }
                    if (isset($item['error'])) {
                        unset($item['error']);
                    }
                }
                // 其他元素
                else {
                    BLogger::getLogger('info')->info('type:' . $item['type'] . ':' . json_encode($item));
                }
            }
            // 数据不符合要求
            if ($returnData['errCode'] != 0) {
                return $returnData;
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -102;
            $returnData['errMsg'] = $ex->getMessage();
            return $returnData;
        }
        // 数组转化为字符串
        if (is_array($jsonData)) {
            $jsonData = json_encode($jsonData);
        }
        // 数据表中存放该数据的类型为LongText ，一般MediumText 就够用了 输入数据限制就使用MediumText做限制
        if (strlen($jsonData) > 16777215 - 1) {
            $returnData['errCode'] = -103;
            $returnData['errMsg'] = '数据过长';
            return $returnData;
        }
        $returnData['data'] = $jsonData;
        return $returnData;
    }

}

