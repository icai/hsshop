<?php
/**
 * 微页面模块
 */
namespace App\Lib;

use App\Module\MallModule;
use App\Module\SeckillModule;
use App\S\Market\CouponService;
use App\S\Product\ProductService;
use FileInfoService;
use WeixinService;
use App\S\Product\ProductGroupService;
use App\S\Member\MemberCardService;
use MicroPageTemplateService;
use App\S\Weixin\ShopService as StoreService;

class MicPage
{
    protected $isXCX;
    protected $wid;
    protected $isPC;

    /**
     * 初始化
     * @param $wid为店铺id, $isXCX
     * @author 陈文豪
     * @date 2017-04-27
     */
    public function to($wid, $isXCX, $isPC=0)
    {
        $this->isXCX = $isXCX;
        $this->wid   = $wid;
        $this->isPC  = $isPC;
        return $this;
    }

    /**
     * 查询商品
     * @param 微页面数据
     * @author 陈文豪
     * @date 2017-04-27
     * @todo 删除的sql语法要加上
     */
    public function productData($item)
    {
        if (empty($item['products_id']) || $item['products_id'] == '[]') {
            return false;
        }
        $productData = (new ProductService())->getListById($item['products_id']);
        if (empty($productData)) {
            return false;
        }

        $tmpData    = $this->dataToPage($productData);
        //todo 后续不做兼容后可去除
        $pageData = [];
        foreach ($tmpData as $v) {
            $pageData[$v['id']] = $v;
        }
        foreach ($item['products_id'] as $pid) {
            if (!empty($pageData[$pid])) {
                $item['goods'][] = $pageData[$pid];
            } else {
                $errGood['productId'] = $pid;
                $errGood['msg']       = '数值不存在';
                $item['error'][]      = $errGood;
            }
        }
        return $item;
    }

    /**
     * 查询商品
     * @param 处理查询数据
     * @author 陈文豪
     * @date 2017-04-27
     */
    public function dataToPage($data)
    {
        $products = [];
        //$goodsID  = [];

        if (empty($data)) {
            return [];
        }

        foreach($data as $item)
        {
            if ($item['status'] == -1 || $item['stock'] <= 0 || $item['status'] == 0) {
                continue;
            }
            $productData          = [];
            $productData['id']    = $item['id'];
            $productData['name']  = $item['title'];
            $productData['price'] = $item['price'];

            //Herry 添加原价字段
            $productData['oprice'] = $item['oprice'];

            //Herry 微页面返回面议字段
            $productData['is_price_negotiable'] = $item['is_price_negotiable'];
            if ($productData['is_price_negotiable'] == 1) {
                $productData['price'] = '面议';
                $productData['oprice'] = 0.00;
            }

            $productData['thumbnail']  = $item['img'];
            $productData['stock']      = $item['stock'];
            //商品介绍
            $productData['productDes'] = $item['summary'];
            $productData['is_hexiao']  = $item['is_hexiao'];

            $productData['url']='/shop/product/detail/'.$this->wid.'/'.$item['id'];
            ($this->isXCX == 1) && ($productData['url'] = '/xcx/product/detail/'.$item['id']);
            // 返回商品是否正在出售中
            $productData['is_selling'] = 1;
            if ($item['sale_time_flag'] == 2 && $item['sale_time'] > date('Y-m-d H:i:s')) {
                $productData['is_selling'] = 0;
            }

            //商品id
            $products[] = $productData;
        }
        $returnData['data']['products'] = $products;
        return $products;
    }

    /**
     * 查询优惠券
     * @param
     * @author 陈文豪
     * @date 2018年09月04日
     */
    public function couponData($item)
    {
        if (empty($item['coupons_id']) || $item['coupons_id'] == '[]') {
            return false;
        }
        $couponData = (new CouponService())->getListById($item['coupons_id']);
        if (empty($couponData)) {
            return false;
        }
        $pageData = $this->couponDataToPage($couponData);

        foreach ($item['coupons_id'] as $cid) {
            if (!empty($pageData[$cid])) {
                $item['couponList'][] = $pageData[$cid];
            } else {
                $err['couponId'] = $cid;
                $err['msg']      = '数值不存在';
                $item['error'][] = $err;
            }
        }
        return $item;
    }

    /**
     * 处理优惠券样式
     * @param
     * @author 陈文豪
     * @date 2018年09月04日
     */
    public function couponDataToPage($data)
    {
        $coupons = [];
        if (empty($data)) {
            return [];
        }

        foreach($data as $item) {
            $couponData         = [];
            $couponData['id']   = $item['id'];
            $couponData['name'] = $item['title'];

            $couponData['limit_desc'] = '不限制';
            ($item['limit_amount'] > 0) && ($couponData['limit_desc'] = '满' . $item['limit_amount'] . '可用');


            $couponData['amount'] = $item['amount'];
            $couponData['url']    = '/shop/activity/couponDetail/' . $this->wid . '/' . $item['id'];
            ($this->isXCX == 1) && $couponData['url'] = '/pages/member/couponDetail/couponDetail?id='.$item['id'];

            //微页面返回优惠券失效或不可用类型,0:可领,1:已失效,2:已过期,3:已领完,4:会员等级不满足,5:已领取(本人领完限额) Herry 20180514
            //目前微页面只简单判断前3种,涉及到当前mid判断在优惠券领取时会判断 微页面不做判断 Herry 20180514
            $couponData['type'] = 0;
            if ($item['invalid_at']) {
                $couponData['type'] = 1;
            } elseif ($item['expire_type'] == 0 && $item['end_at'] <= date('Y-m-d H:i:s')) {
                $couponData['type'] = 2;
            } elseif ($item['left'] < 1) {
                $couponData['type'] = 3;
            }

            //优惠券明细
            $coupons[$item['id']] = $couponData;
        }
        return $coupons;
    }

    /**
     * 处理图片广告
     * @param
     * @author 陈文豪
     * @date 2018年09月04日
     * @todo wid
     */
    public function imageAdData($item)
    {
        if (empty($item['images']) || $item['images'] == '[]') {
            return false;
        }
        $img_id_arr = [];
        foreach ($item['images'] as $v) {
            if(empty($v['image_id'])) continue;
            $img_id_arr[] = $v['image_id'];
        }
        $imageAdData = FileInfoService::getListById($img_id_arr);
        if (empty($imageAdData)) {
            return false;
        }
        $pageData = $this->imageAdDataToPage($imageAdData);

        foreach ($item['images'] as $k => $v) {
            if (empty($v['image_id'])) continue;
            if (!empty($pageData[$v['image_id']])) {
                $item['images'][$k]['FileInfo'] = $pageData[$v['image_id']];
            } else {
                $err['imageId']  = $v['image_id'];
                $err['msg']      = '数值不存在';
                $item['error'][] = $err;
            }
        }
        return $item;
    }

    /**
     * 处理图片广告样式
     * @param
     * @author 陈文豪
     * @date 2018年09月04日
     */
    public function imageAdDataToPage($data)
    {
        $imageAds = [];
        if (empty($data)) {
            return [];
        }
        foreach($data as $item) {
            $imageAd = [];
            $imageAd['id']     = $item['id'];
            $imageAd['path']   = $item['path'];
            $imageAd['s_path'] = $item['s_path'];
            $imageAd['m_path'] = $item['m_path'];
            $imageAd['l_path'] = $item['l_path'];

            //图片明细
            $imageAds[$item['id']]=$imageAd;
        }
        return $imageAds;
    }

    /**
     * 处理进入店铺
     * @param
     * @author 陈文豪
     * @date 2018年09月05日
     * @todo  WeixinService.getStoreInfo  全部替换成这个方法
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function storeData($item)
    {
        if (empty($item['id'])) {
            return false;
        }
        //$storeData = WeixinService::init('id', $item['id'])->where(['id' => $item['id']])->getInfo();
        $storeData = (new StoreService())->getRowById($item['id']);
        $pageData = $this->storeDataToPage($storeData);

        if (!empty($pageData)) {
            $item['url']        = $pageData['url'];
            $item['store_name'] = $pageData['shop_name'];
        } else {
            $err['storeId'] = $item['id'];
            $err['code']    = $pageData['errCode']??-100;
            $err['msg']     = $pageData['errMsg']??'数值不存在';
            $item['error']  = $err;
        }
        return $item;
    }

    /**
     * 处理进入店铺样式
     * @param
     * @author 陈文豪
     * @date 2018年09月05日
     * @todo  WeixinService.getStoreInfo  全部替换成这个方法
     */
    public function storeDataToPage($data)
    {
        $storeData = [];
        if (empty($data)) {
            return [];
        }
        $storeData['id']            = $data['id'];
        $storeData['shop_name']     = $data['shop_name'];
        $storeData['logo']          = $data['logo'];
        $storeData['is_distribute'] = $data['is_distribute'];
        $storeData['is_open_weath'] = $data['is_open_weath'] ?? 0;
        $storeData['url']           = config('app.url') . 'shop/index/' . $this->wid;

        ($this->isXCX == 1) && ($storeData['url'] = config('app.url') . 'pages/index/index');
        return $storeData;
    }



    /**
     *
     * @param $item
     * @return bool
     * @update: 梅杰 2018年9月10号 优化 默认商品组显示20个
     */
    public function goodGroupData($item)
    {
        if (empty($item['left_nav']) && empty($item['top_nav'])) {
            return false;
        }
        $groupList = $item['group_type'] == 1 ? $item['left_nav'] : $item['top_nav'];

        //定义存放商品分组的变量[存放处理后的商品分组信息]
        $afterGroupList = [];
        $productService = new ProductService();
        //最热 最新分组 特殊处理 Herry 20171013
        $productGroupService = new ProductGroupService();
        $flag = $this->isXCX;
        foreach($groupList as $group) {
            $group['goods'] = [];//定义存放商品的数组
            $where          = [];

            if ($this->isPC) {
                $afterGroupList[] = $group;
                continue;
            }
            $groupDetail = $productGroupService->getDetail($group['id']);
            if (empty($groupDetail)) {
                $err['groupID']   = $group['id'];
                $err['code']      = -100;
                $err['msg']       = '数值不存在';
                $group['error']   = $err;
                $afterGroupList[] = $group;
                continue;
            }
            if (isset($groupDetail['first_priority'])) {
                $where['first_priority'] = $groupDetail['first_priority'];
            }
            if (isset($groupDetail['second_priority'])) {
                $where['second_priority'] = $groupDetail['second_priority'];
            }

            $where['groupMax'] = $group['num'] ?? 0;
            ($flag == 1) && ($where['groupMax'] = $item['show_num'] ?? 20);
            $where['limitNum'] = 1;

            $where['groupAll']  = $item['isall'] ?? 2;
            switch ($groupDetail['is_default']) {
                case 1:
                    $where['groupType'] = 'NEW';
                    break;
                case 2:
                    $where['groupType'] = 'HOT';
                    break;
                default:
                    $where['groupID']   = $group['id'];
                    break;
            }

            $productData = $productService->getProductList($this->wid, $where, $flag);
            if ($productData['errCode'] == 0 && !empty($productData['data'])) {
                $group['goods'] = $productData['data']['products'];   //查询出来的商品存放到goods中
            } else {
                $errGroup['groupID'] = $group['id'];
                $errGroup['code']    = $productData['errCode']??-100;
                $errGroup['msg']     = $productData['errMsg']??'数值不存在';
                $group['error']      = $errGroup;
            }
            $afterGroupList[] = $group;
        }
        ($item['group_type'] == 1) && ($item['left_nav'] = $afterGroupList);
        ($item['group_type'] == 2) && ($item['top_nav'] = $afterGroupList);
        return $item;
    }


    /**
     * 会员卡处理
     * @param $item
     * @return bool
     * @author 何书哲 2018年9月6日
     * @update 吴晓平 2019年10月29日 15:44:59 添加微页面会员卡增加自定义封面图字段（card_img）
     */
    public function cardData($item) {
        if (empty($item['card_ids']) || $item['card_ids'] == '[]') {
            return false;
        }
        $cardData = (new MemberCardService())->getListById($item['card_ids']);
        if (empty($cardData)) {
            return false;
        }
        foreach ($cardData as $card) {
            $cardData[$card['id']] = $card;
        }
        $return = [];
        foreach ($item['cardList'] as $value) {
            if (!empty($cardData[$value['id']])) {
                $return[] = [
                    'id'   => $value['id'],
                    'name' => $value['name'],
                    'url'  => '/shop/member/detail/' . $cardData[$value['id']]['wid'] . '/' . $value['id'],
                    'img'  => $value['img'] ?? '',
                    'card_img' => $value['card_img'] ?? ''
                ];
            } else {
                $err['cardId']   = $value['id'];
                $err['msg']      = '数值不存在';
                $item['error'][] = $err;
            }
        }
        if ($return) {
            $item['cardList'] = $return;
        }
        return $item;
    }

    /**
     * 商品分组详情页处理
     * @param $item
     * @return mixed
     * @author 何书哲 2018年9月6日
     * @update 何书哲 2019年03月14日 如果是默认分组则去掉分组过滤，全部显示
     */
    public function goodsGroupData($item) {
        $item['goods']=[];
        if (empty($item['is_default'])) {
            $where['groupID'] = $item['id'];
        }
        $where['first_priority'] = $item['first_priority'];
        $where['second_priority'] = $item['second_priority'];
        $productData = (new ProductService())->getProductList($this->wid, $where, $this->isXCX);
        if ($productData['errCode'] == 0 && !empty($productData['data'])) {
            $item['goods'] = $productData['data']['products'];
            //拆分成3条数据一组
            $list = [];
            $len = intval(ceil(count($productData['data']['products']) / 3));
            for ($i = 0; $i < $len; $i++) {
                $list[] = array_slice($productData['data']['products'], $i * 3, 3);
            }
            $item['thGoods'] = $list;
        } else {
            $errGoodsList['goodsListID'] = $item['id'];
            $errGoodsList['code'] = $productData['errCode']??-100;
            $errGoodsList['msg'] = $productData['errMsg']??'数值不存在';
            $item['error'] = $errGoodsList;
        }
        return $item;
    }

    /**
     * 秒杀列表
     * @param $item
     * @return bool
     * @author 何书哲 2018年9月6日
     */
    public function seckillListData($item) {
        $seckillModule = new SeckillModule();
        $item['seckillList'] = [];
        if (empty($item['seckillIds']) || $item['seckillIds'] == '[]') {
            return false;
        }
        foreach ($item['seckillIds'] as $id) {
            $seckill = $seckillModule->getSeckillInfo($id);
            if ($seckill['errCode'] == 0 && !empty($seckill['data'])) {
                $item['seckillList'][] = $seckill['data'];
            } else {
                $errMarket['seckillID'] = $id;
                $errMarket['code'] = $seckill['errCode']??-100;
                $errMarket['msg'] = $seckill['errMsg']??'数值不存在';
                $item['error'] = $errMarket;
            }
        }
        return $item;
    }

    /**
     * 秒杀模板
     * @param $item
     * @return bool
     * @author 何书哲 2018年9月6日
     * @update 何书哲 2018年9月13日 修改秒杀取不到值报错的bug
     */
    public function marketingActiveData($item) {
        $seckillModule = new SeckillModule();
        if (empty($item['content'][0]['id'])) {
            return false;
        }
        $seckillData = $seckillModule->getSeckillInfo($item['content'][0]['id']);
        if ($seckillData['errCode'] == 0 && !empty($seckillData['data'])) {
            $item['content'][0]['product'] = $seckillData['data']['product'];
            $item['content'][0]['sku'] = $seckillData['data']['sku'];
            $item['content'][0]['invalidate_at'] = $seckillData['data']['seckill']['invalidate_at'];
            $item['content'][0]['now_at'] = date('Y-m-d H:i:s');

            //秒杀tag返回 Herry 20171017
            $item['content'][0]['product']['seckill_tag'] = $seckillData['data']['seckill']['tag'];
        }
        else
        {
            //add by jonzhang 2017-12-20
            $errMarket['seckillID'] = $item['content'][0]['id'];
            $errMarket['code'] = $seckillData['errCode']??-100;
            $errMarket['msg'] = $seckillData['errMsg']??'数值不存在';
            $item['content']=[];
            $item['error'] = $errMarket;
        }
        return $item;
    }

    /**
     * 商品列表
     * @param $item
     * @return bool
     * @author 何书哲 2018年9月7日
     */
    public function goodsListData($item) {
        if (empty($item['group_id'])) {
            return false;
        }
        //获取某个分组下的商品信息
        $item['goods']=[];
        $where = [];
        if ($this->isPC) {
            return $item;
        }
        $where['groupID'] = $item['group_id'];
        $where['groupMax'] = $item['showNum'] ?? 0;
        $where['limitNum'] = 1;
        isset($item['first_priority']) && ($where['first_priority'] = $item['first_priority']);
        isset($item['second_priority']) && ($where['second_priority'] = $item['second_priority']);
        $productData = (new ProductService())->getProductList($this->wid, $where, $this->isXCX);
        if ($productData['errCode'] != 0 || empty($productData['data'])) {
            $errGoodsList['goodsListID'] = $item['group_id'];
            $errGoodsList['code'] = $productData['errCode']??-100;
            $errGoodsList['msg'] = $productData['errMsg']??'数值不存在';
            $item['error'] = $errGoodsList;
            return $item;
        }
        $item['goods'] = $productData['data']['products'];
        return $item;
    }

    /**
     *  自定义模板
     * @param $item
     * @return bool
     * @author 何书哲 2018年9月7日
     */
    public function modelData($item) {
        if (empty($item['id'])) {
            return false;
        }
        $item['modelName'] = '';
        $item['template_data'] = [];
        $customTemplate = MicroPageTemplateService::getRowById($item['id']);
        if ($customTemplate['errCode'] != 0 || empty($customTemplate['data'])) {//异常信息
            $errModel['modelID'] = $item['id'];
            $errModel['code'] = $customTemplate['errCode']??-100;
            $errModel['msg'] = $customTemplate['errMsg']??'数值不存在';
            $item['error'] = $errModel;
            return $item;
        }
        $item['modelName'] = $customTemplate['data']['template_name'];
        !$this->isPC && $customTemplate['data']['template_info']
        && ($customTemplate['data']['template_info'] = (new MallModule())->processTemplateData($this->wid, $customTemplate['data']['template_info']))
        && ($item['template_data'] = json_decode($customTemplate['data']['template_info'], true));
        return $item;
    }



}
