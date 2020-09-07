<?php
/**
 * 商品模块
 */

namespace App\Module;

use App\Jobs\CreateShareCard;
use App\Lib\Redis\RedisClient;
use App\Model\ProductEvaluateDetail;
use App\S\File\FileInfoService;
use App\S\Member\MemberService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductImgService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductWholesaleService;
use App\S\Product\RemarkService;
use App\Services\FreightService;
use App\S\Product\ProductTemplateService;
use App\Services\Shop\CartService;
use ProductService;
use MallModule as ProductStoreService;
use Upyun\Config;
use Upyun\Upyun;
use WeixinService;
use MemberCardRecordService;
use ProductMicroPageNoticeService;
use ProductEvaluateService;
use DB;
use OrderDetailService;
use App\S\Product\ProductSkuService;
use App\S\Weixin\ShopService;
use Storage;
use QrCodeService;
use QrCode;

class ProductModule
{
    /**
     * 商品详情页详细数据
     * @param int $pid 商品id
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @param bool $isLiteapp 是否是小程序
     * @return array
     * @author 许立 2018年07月12日
     * @update 许立 2018年07月12日 返回商品是否有会员折扣, 返回预售时间戳和当前时间戳
     * @update 许立 2018年07月12日 预售时间和服务器当前时间返回前端所需格式
     * @update 许立 2018年07月17日 默认运费字符串处理
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年11月20日 评论返回商家回复
     * @update 张永辉 2019年8月20日 当企业关闭财富眼是不显示佣金相关信息
     * @update 许立 2019年11月22日 16:48:42 非会员如果设置显示会员价 返回最大力度折扣价
     * @update 许立 2019年11月28日 17:06:59 价格区间使用波浪号 否则前端有些地方计算金额有问题
     */
    public function getDetail($pid, $wid, $mid, $isLiteapp = false)
    {
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            if ($isLiteapp) {
                xcxerror('商品不存在');
            } else {
                error('商品不存在');
            }
        }

        //对商品模板数据进行处理 add by jonzhang 2017-07-24
        if (!empty($product['content'])) {
            $product['content'] = ProductStoreService::processTemplateData($wid, $product['content']);

            //详情替换图片路径 20180502修复 使用绝对路径 不然小程序图片无法显示 Herry
            $product['content'] = ProductModule::addProductContentHost($product['content']);

            //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
            if ($isLiteapp) {
                $product['content'] = dealWithXCXProductContent($product['content']);
            } else {
                $product['content'] = dealWithProductContent($wid, $product['content']);
            }
        }

        if ($product['status'] == 0 || $product['status'] == -1) {
            if ($isLiteapp) {
                xcxerror('商品已下架，或已删除');
            } else {
                error('商品已下架，或已删除');
            }
        }

        //获取商品图片
        $product['productImg'] = (new ProductImgService())->getListByProduct($pid);

        //获取要求留言列表
        $product['productMsg'] = (new ProductMsgService())->getListByProduct($pid);

        //获取批发设置
        $product['wholesale_array'] = $product['wholesale_flag'] ? (new ProductWholesaleService())->getListByProduct($pid) : [];

        //商品的价格
        $product['showPrice'] = $product['price'];

        // 是否显示会员折扣标签
        $product['is_vip'] = 0;
        $sku               = [];
        $product['bestCardPrice'] = 0;
        if (!$product['wholesale_flag']) {
            if ($product['sku_flag']) {
                // 判断规格价格是否有折扣
                $data = $this->handleSkuDiscountPrice($pid, $mid);
                $sku  = $data['data'];
                if ($data['is_vip']) {
                    $product['is_vip'] = 1;
                }
            } else {
                $new_price_data       = ProductService::reSetNoSkuPrice($product, $mid, $wid);
                $product['showPrice'] = $new_price_data['price'];
                $product['bestCardPrice'] = $new_price_data['bestCardPrice'];
                if ($product['bestCardPrice'] && $product['showPrice'] <= $product['bestCardPrice']) {
                    $product['bestCardPrice'] = 0;
                }
                if ($new_price_data['is_vip']) {
                    $product['is_vip'] = 1;
                }
            }
        }

        $max = $product['price'];
        if ($product['sku_flag']) {
            !$sku && $sku = (new ProductPropsToValuesService())->getSkuList($pid);
            $tmp = [];
            $bestCardPriceArray = [];
            if (!empty($sku['stocks'])) {
                foreach ($sku['stocks'] as $val) {
                    $tmp[] = $val['price'];
                    if (!empty($val['bestCardPrice'])) {
                        $bestCardPriceArray[] = $val['bestCardPrice'];
                    }
                }
            } else {
                if ($isLiteapp) {
                    xcxerror('商品规格错误');
                } else {
                    error('商品规格错误');
                }
            }
            sort($tmp);
            $max = $tmp[0];
            $min = end($tmp);
            //价格没有区间 则只显示一个价格
            $product['showPrice'] = ($max == $min) ? $max : ($max . '～' . $min);

            // 非会员显示会员折扣逻辑
            $product['bestCardPrice'] = 0;
            if ($bestCardPriceArray) {
                $bestMax = max($bestCardPriceArray);
                $bestMin = min($bestCardPriceArray);
                //价格没有区间 则只显示一个价格
                $product['bestCardPrice'] = ($bestMax == $bestMin) ? $bestMax : ($bestMin . '～' . $bestMax);
                // max存的是最小原价。。。 min是最大原价。。。。
                if ($max <= $bestMin || $min <= $bestMax) {
                    $product['bestCardPrice'] = 0;
                }
            }
        }

        //获取店铺信息
        /*$shopData = WeixinService::init('wid',$wid)->model->find($wid);
        if ($shopData){
            $shopData = $shopData->load('weixinConfigMaster')->toArray();
        }else{
            if ($isLiteapp) {
                xcxerror('店铺不存在，或已删除');
            } else {
                error('店铺不存在，或已删除');
            }
        }*/
        $shopService = new ShopService();
        $shopData    = $shopService->getRowById($wid, ['weixinConfigMaster']);
        if ($shopData) {
            $shopData['weixinConfigMaster'] = json_decode($shopData['weixinConfigMaster'], true);
        } else {
            if ($isLiteapp) {
                xcxerror('店铺不存在，或已删除');
            } else {
                error('店铺不存在，或已删除');
            }
        }

        //更多商品
        $moreProduct = [];
        if (!$shopData['weixinConfigMaster']['is_more']) {
            //随机取出店铺最多10个商品
            list($data) = ProductService::listWithPage(['wid' => $wid, 'status' => 1], '', '', 100);

            if ($data['total'] > 10) {
                $randNum = array_rand($data['data'], 10);
                foreach ($randNum as $value) {
                    $moreProduct[] = $data['data'][$value];
                }
            } else {
                $moreProduct = $data['data'];
            }
        }
        $distribute = '';
        $ratio = $ratioSec = 0;
        if ($mid && $shopData['is_open_weath'] == 1 && $shopData['is_distribute'] == 1 && $product['distribute_template_id'] != 0 && $product['is_distribution'] != 0) {
            $res        = (new DistributeModule())->getProductDistributePrice($shopData, $max, $mid, $product['distribute_template_id']); //add by zhangyh 20171220
            $distribute = $res[0];
            $ratio      = $res[1];
            $ratioSec   = $res[2];
            if (empty($distribute) && empty($ratio) && empty($ratioSec)) {
                $product['is_distribution'] = 0;
            }
        } else {
            $product['is_distribution'] = 0;
        }

        //默认运费计算
        $defaultFreight = 0.00;
        if ($product['freight_type'] == 1) {
            //统一运费
            $defaultFreight = $product['freight_price'];
        } elseif ($product['freight_type'] == 2) {
            //获取运费模板中的默认地区运费
            $freightTpl = (new FreightService())->init('wid', $wid)->getInfo($product['freight_id']);
            if (!empty($freightTpl)) {
                $rule = json_decode($freightTpl['delivery_rule'], true);
                foreach ($rule as $v) {
                    if (count($v['regions']) == 1 && $v['regions'][0] == 0) {
                        //默认配置规则
                        $defaultFreight = $v['first_fee'];
                        break;
                    }
                }
            }
        }

        //商品模板
        $template               = [];
        $productTemplateService = new ProductTemplateService();
        $productTemplateData    = $productTemplateService->getRowById($product['templete_use_id']);
        if ($productTemplateData['errCode'] == 0 && !empty($productTemplateData['data'])) {
            $template = $productTemplateData['data'];
        }

        //获取商品评价
        list($evaluate) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid])->getList();
        list($data['good']) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid, 'status' => 1])->getList();
        list($data['middle']) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid, 'status' => 2])->getList();
        list($data['bad']) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid, 'status' => 3])->getList();

        // 评论获取商家回复
        $evaluate['data'] = $this->handleCommentReply($evaluate['data']);
        $data['good']['data'] = $this->handleCommentReply($data['good']['data']);
        $data['middle']['data'] = $this->handleCommentReply($data['middle']['data']);
        $data['bad']['data'] = $this->handleCommentReply($data['bad']['data']);

        //商品评价图片
        if ($evaluate['data']) {
            $evaluate['data'] = $this->getCommentImgArr($evaluate['data']);
        }
        if ($data['good']['data']) {
            $data['good']['data'] = $this->getCommentImgArr($data['good']['data']);
        }
        if ($data['middle']['data']) {
            $data['middle']['data'] = $this->getCommentImgArr($data['middle']['data']);
        }
        if ($data['bad']['data']) {
            $data['bad']['data'] = $this->getCommentImgArr($data['bad']['data']);
        }

        //统计评价信息 只取当前店铺的评论 Herry 20171206
        $res    = ProductEvaluateService::init('wid', $wid)->model->select(DB::raw('count(*) as number,status'))->where(['wid' => $wid, 'pid' => $pid])->groupBy('status')->get()->toArray();
        $number = [
            'all'    => 0,
            'good'   => 0,
            'middle' => 0,
            'bad'    => 0,
        ];
        if ($res) {
            foreach ($res as $val) {
                switch ($val['status']) {
                    case 1:
                        $number['good'] = $val['number'];
                        break;
                    case 2:
                        $number['middle'] = $val['number'];
                        break;
                    case 3:
                        $number['bad'] = $val['number'];
                        break;
                }
                $number['all'] = $val['number'] + $number['all'];
            }
        }

        //Herry 返回用户当前商品已购买数量
        $alreadyBuy = OrderDetailService::productBuyNum($mid, $pid);

        // 预售时间和服务器当前时间 格式:Y/m/d H:i:s
        $product['now_time_new']  = date('Y/m/d H:i:s');
        $product['sale_time_new'] = $product['sale_time_flag'] == 2 ? date('Y/m/d H:i:s', strtotime($product['sale_time'])) : $product['sale_time'];

        // 默认运费字符串处理
        $umid                      = (new MemberService())->getRowById($mid)['umid'] ?? 0;
        $product['freight_string'] = $this->getDefaultFreight($pid, $mid, $umid);

        return [
            'product'           => $product,
            'shop'              => $shopData,
            'more'              => $moreProduct,
            'evaluate'          => $evaluate,
            'number'            => $number,
            'cartNum'           => (new CartService())->cartNum($mid, $wid),
            'distribute'        => $distribute,
            'sku'               => json_encode($sku)??'',
            'template'          => $template,
            'defaultFreight'    => $defaultFreight,
            //'micro_page_notice' => json_encode(ProductMicroPageNoticeService::getNoticeApplication(['wid'=>$wid,'apply_id'=>3])) ?? '',
            'micro_page_notice' => '',
            'alreadyBuy'        => $alreadyBuy ?: 0,
            'ratio'             => $ratio,
            'ratioSec'         => $ratioSec,
        ];
    }

    /**
     * 获取商品评价图片
     */
    public function getCommentImgArr($list)
    {
        if (empty($list)) {
            return [];
        }

        //获取评价图片ID
        $imgIDArr = [];
        foreach ($list as $v) {
            if ($v['img']) {
                $arr      = explode(',', $v['img']);
                $imgIDArr = array_merge($imgIDArr, $arr);
            }
        }
        $imgIDArr = array_unique($imgIDArr);

        //获取图片信息
        $imgs = (new FileInfoService())->getListById($imgIDArr);

        //组装imgs
        $newImgs = [];
        foreach ($imgs as $img) {
            $newImgs[$img['id']] = $img['path'];
        }

        //返回路径
        foreach ($list as $k => $v) {
            $list[$k]['imgPathArr'] = [];
            if ($v['img']) {
                $imgIDs = explode(',', $v['img']);
                foreach ($imgIDs as $id) {
                    !empty($newImgs[$id]) && $list[$k]['imgPathArr'][] = $newImgs[$id];
                }

            }
        }

        return $list;
    }

    /**
     * todo 展现推荐商品的信息
     * @param $wid
     * @return array
     * @author jonzhang
     * @date 2017-11-22
     * @modify 张国军 2018年08月15日 面议商品显示面议
     */
    public function showRecommendProducts($wid, $isXCX = 0, $productId = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '店铺ID为空';
            return $returnData;
        }
        //获取最新商品信息
        $normalResult = ProductService::listWithPage(['wid' => $wid, 'status' => 1, 'stock' => ['>', 0]], 'created_at', 'desc', 5);

        if (!empty($normalResult[0]['data'])) {
            $i      = 0;
            $normal = [];
            foreach ($normalResult[0]['data'] as $item) {
                $productData = [];
                //商品推荐，剔除当前商品 add by jonzhang 2018-05-14
                if ($item['id'] != $productId) {
                    $productData['id']     = $item['id'];
                    $productData['name']   = $item['title'];
                    $productData['price']  = $item['price'];
                    $productData['oprice'] = $item['oprice'];
                    //add by 张国军 面议商品
                    $productData['is_price_negotiable'] = $item['is_price_negotiable'];
                    if ($productData['is_price_negotiable'] == 1) {
                        $productData['price']  = '面议';
                        $productData['oprice'] = 0.00;
                    }
                    $productData['thumbnail'] = $item['img'];
                    $productData['stock']     = $item['stock'];
                    //商品介绍
                    $productData['productDes'] = $item['summary'];
                    if ($isXCX) {
                        $productData['url'] = '/xcx/product/detail/' . $item['id'];
                    } else {
                        $productData['url'] = '/shop/product/detail/' . $wid . '/' . $item['id'];
                    }
                    //普通商品推荐，只显示四条数据
                    if ($i < 4) {
                        $normal[] = $productData;
                    }
                    $i++;
                }
            }
            $cnt = count($normal);
            //只显示0,2,4数目的商品推荐
            if ($cnt % 2 != 0) {
                unset($normal[$cnt - 1]);
            }
            $returnData['data']['normal'][] = $normal;
        }
        return $returnData;
    }


    /**
     * todo 获取商品详情供享立减使用
     * @author jonzhang
     * @date 2017-12-06
     */
    public function getProductByShareEvent($productId = 0, $skuId = 0, $isSku = true)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($productId)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg']  = "商品id为空";
            return $returnData;
        }

        $productData = ProductService::getDetail($productId);
        if (isset($productData['status']) && $productData['status'] != 1) {
            $returnData['errCode'] = -2;
            $returnData['errMsg']  = "该商品已下架";
            return $returnData;
        }

        if (!empty($productData['content'])) {
            $productData['content'] = ProductStoreService::processTemplateData($productData['wid'], $productData['content']);
        }

        $productData['productImg'] = [];
        //商品图片
        $productImgData = (new ProductImgService())->getListByProduct($productId);
        foreach ($productImgData as $item) {
            $productData['productImg'][] = $item['img'];
        }

        if (!empty($productData['sku_flag']) && $isSku) {
            if (empty($skuId)) {
                $returnData['errCode'] = -3;
                $returnData['errMsg']  = '商品规格id不能为空';
                return $returnData;
            }
            $propService = new ProductSkuService();
            $skuData     = $propService->getSkuDetail($skuId);
            if (empty($skuData)) {
                $returnData['errCode'] = -3;
                $returnData['errMsg']  = '商品规格不存在';
                return $returnData;
            }
            //如果商品有规格 则商品的库存,价格,图片为规格里面的库存,价格,图片
            $productData['stock'] = $skuData['stock_num']??0;
            $productData['price'] = $skuData['price']??0.00;
            $spec                 = "";
            if (!empty($skuData['k1'])) {
                $spec .= $skuData['k1'] . ":" . $skuData['v1'];
            }
            if (!empty($skuData['k2'])) {
                $spec .= "  " . $skuData['k2'] . ":" . $skuData['v2'];
            }
            $productData['product_spec'] = $spec;
            if (!empty($skuData['img'])) {
                $productData['img'] = $skuData['img'];
            }
        }
        $returnData['data'] = $productData;
        return $returnData;
    }


    /**
     * 商品详情预览
     * @param int $pid 商品id
     * @param int $wid 店铺id
     * @param bool $isLiteapp 是否是小程序
     * @return array
     * @author 付国维 2017年12月29日
     * @update 许立   2018年07月17日 预览的运费显示修改
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function showDetail($pid, $wid, $isLiteapp = false)
    {
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            if ($isLiteapp) {
                xcxerror('商品不存在');
            } else {
                error('商品不存在');
            }
        }

        //对商品模板数据进行处理 add by jonzhang 2017-07-24
        if (!empty($product['content'])) {
            $product['content'] = ProductStoreService::processTemplateData($wid, $product['content']);

            $product['content'] = ProductModule::addProductContentHost($product['content']);

            //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
            if ($isLiteapp) {
                $product['content'] = dealWithXCXProductContent($product['content']);
            } else {
                $product['content'] = dealWithProductContent($wid, $product['content']);
            }
        }

        if ($product['status'] == 0 || $product['status'] == -1) {
            if ($isLiteapp) {
                xcxerror('商品已下架，或已删除');
            } else {
                error('商品已下架，或已删除');
            }
        }

        //详情替换图片路径
        $product['introduce'] = str_replace('<img src="ueditor', '<img src="' . config('app.source_img_url') . 'ueditor', $product['introduce']);

        //获取商品图片
        $product['productImg'] = (new ProductImgService())->getListByProduct($pid);

        //获取要求留言列表
        $product['productMsg'] = (new ProductMsgService())->getListByProduct($pid);

        //商品的价格
        $product['showPrice'] = $product['price'];

        $sku = [];
        $max = $product['price'];  //add by zhangyh 20171220
        if ($product['sku_flag']) {
            $sku = (new ProductPropsToValuesService())->getSkuList($pid);
            $tmp = [];
            if (!empty($sku['stocks'])) {
                foreach ($sku['stocks'] as $val) {
                    $tmp[] = $val['price'];
                }
            } else {
                if ($isLiteapp) {
                    xcxerror('商品规格错误');
                } else {
                    error('商品规格错误');
                }
            }
            sort($tmp);
            $max = $tmp[0];
            $min = end($tmp);
            //价格没有区间 则只显示一个价格
            $product['showPrice'] = $max == $min ? $max : $max . '～' . $min;
        }

        //获取店铺信息
        /*$shopData = WeixinService::init('wid',$wid)->model->find($wid);
        if ($shopData){
            $shopData = $shopData->load('weixinConfigMaster')->toArray();
        }else{
            if ($isLiteapp) {
                xcxerror('店铺不存在，或已删除');
            } else {
                error('店铺不存在，或已删除');
            }
        }*/
        $shopService = new ShopService();
        $shopData    = $shopService->getRowById($wid, ['weixinConfigMaster']);
        if ($shopData) {
            $shopData['weixinConfigMaster'] = json_decode($shopData['weixinConfigMaster'], true);
        } else {
            if ($isLiteapp) {
                xcxerror('店铺不存在，或已删除');
            } else {
                error('店铺不存在，或已删除');
            }
        }

        //更多商品
        $moreProduct = [];
        if (!$shopData['weixinConfigMaster']['is_more']) {
            //随机取出店铺最多10个商品
            list($data) = ProductService::listWithPage(['wid' => $wid, 'status' => 1], '', '', 100);

            if ($data['total'] > 10) {
                $randNum = array_rand($data['data'], 10);
                foreach ($randNum as $value) {
                    $moreProduct[] = $data['data'][$value];
                }
            } else {
                $moreProduct = $data['data'];
            }
        }

        //商品模板
        $template               = [];
        $productTemplateService = new ProductTemplateService();
        $productTemplateData    = $productTemplateService->getRowById($product['templete_use_id']);
        if ($productTemplateData['errCode'] == 0 && !empty($productTemplateData['data'])) {
            $template = $productTemplateData['data'];
        }

        //获取商品评价
        list($evaluate) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid])->getList();
        list($data['good']) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid, 'status' => 1])->getList();
        list($data['middle']) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid, 'status' => 2])->getList();
        list($data['bad']) = ProductEvaluateService::init('wid', $wid)->where(['pid' => $pid, 'status' => 3])->getList();

        //商品评价图片
        if ($evaluate['data']) {
            $evaluate['data'] = $this->getCommentImgArr($evaluate['data']);
        }
        if ($data['good']['data']) {
            $data['good']['data'] = $this->getCommentImgArr($data['good']['data']);
        }
        if ($data['middle']['data']) {
            $data['middle']['data'] = $this->getCommentImgArr($data['middle']['data']);
        }
        if ($data['bad']['data']) {
            $data['bad']['data'] = $this->getCommentImgArr($data['bad']['data']);
        }

        //统计评价信息 只取当前店铺的评论 Herry 20171206
        $res    = ProductEvaluateService::init('wid', $wid)->model->select(DB::raw('count(*) as number,status'))->where(['wid' => $wid, 'pid' => $pid])->groupBy('status')->get()->toArray();
        $number = [
            'all'    => 0,
            'good'   => 0,
            'middle' => 0,
            'bad'    => 0,
        ];
        if ($res) {
            foreach ($res as $val) {
                switch ($val['status']) {
                    case 1:
                        $number['good'] = $val['number'];
                        break;
                    case 2:
                        $number['middle'] = $val['number'];
                        break;
                    case 3:
                        $number['bad'] = $val['number'];
                        break;
                }
                $number['all'] = $val['number'] + $number['all'];
            }
        }


        return [
            'product'           => $product,
            'shop'              => $shopData,
            'more'              => $moreProduct,
            'evaluate'          => $evaluate,
            'number'            => $number,
            'sku'               => json_encode($sku)??'',
            'template'          => $template,
            'micro_page_notice' => '',
        ];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180118
     * @desc 获取商品留言编号
     */
    public function getIdentifier()
    {
        $identifier = "R" . date("YmdHis");
        for ($i = 0; $i < 6; $i++) {
            $identifier = $identifier . rand(0, 9);
        }
        $res = (new RemarkService())->getList(['remark_no' => $identifier]);
        if ($res) {
            return $this->getIdentifier();
        } else {
            return $identifier;
        }
    }

    /**
     * 根据购买件数获取批发商品单价
     */
    public function getWholesalePrice($product_id, $count)
    {
        //商品详情
        $product = ProductService::getDetail($product_id);
        if (empty($product)) {
            return 0;
        }

        //批发设置
        $wholesale_array = (new ProductWholesaleService())->getListByProduct($product_id);
        if ($wholesale_array) {
            //判断当前购买数所属区间
            foreach ($wholesale_array as $v) {
                if ($v['min'] <= $count && $count <= $v['max']) {
                    return $v['price'];
                }
            }

            //如果不在任何区间 目前只处理: 大于最大设置数量 则返回最大数量区间价格
            $max       = $wholesale_array[0]['max'];
            $max_price = $wholesale_array[0]['price'];
            foreach ($wholesale_array as $v) {
                if ($v['max'] > $max) {
                    $max       = $v['max'];
                    $max_price = $v['price'];
                }
            }
            if ($count > $max) {
                return $max_price;
            }
        }

        return $product['price'];
    }

    /**
     * 验证批发设置数据
     * @param $array array 批发设置数据
     * @return array
     * @author 许立 2018年08月16日
     * @update 许立 2018年08月16日 判断是否填写完整数据
     */
    public static function verifyWholesaleData($array)
    {
        foreach ($array as &$v) {
            // 判断是否填写完整数据
            if (empty($v['min']) || empty($v['max']) || empty($v['price'])) {
                return [
                    'code'  => 1,
                    'error' => '请填写完整的批发价设置'
                ];
            }

            //格式
            $v['min']   = (int)$v['min'];
            $v['max']   = (int)$v['max'];
            $v['price'] = sprintf('%.2f', $v['price']);

            //正数
            if ($v['min'] <= 0 || $v['max'] <= 0 || $v['price'] <= 0) {
                return [
                    'code'  => 1,
                    'error' => '批发价设置的值必须是数字且大于0'
                ];
            }

            //起止件数
            if ($v['min'] >= $v['max']) {
                return [
                    'code'  => 2,
                    'error' => '批发价设置的起始件数必须小于最大件数'
                ];
            }
        }

        //第一个元素与剩余元素一一比较是否有交集
        $first = $array[0];
        unset($array[0]);
        foreach ($array as $v) {
            if (($v['min'] <= $first['min'] && $first['min'] <= $v['max']) || ($v['min'] <= $first['max'] && $first['max'] <= $v['max'])) {
                //比如：[[2,6],[7,9],[1,3]] 就有重叠
                return [
                    'code'  => 3,
                    'error' => '批发价设置的起止件数不能重叠'
                ];
            }
        }

        return [
            'code'  => 0,
            'error' => ''
        ];
    }

    /**
     * 重构商品价格
     * 如果设置了会员价格则以会员价格为准
     * 没有则以会员卡享有的折扣为准
     * 上述条件不满足则以原价为准
     * author: meijie
     * @param $pid 商品Id
     * @return array
     * @update 许立 2019年11月22日 16:48:42 非会员如果设置显示会员价 返回最大力度折扣价
     */
    public function handleSkuDiscountPrice($pid, $mid, $is_raw_sku = false)
    {
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            return [
                'is_vip' => 0,
                'data'   => []
            ];
        }

        $is_vip = 0;
        $sku    = (new ProductPropsToValuesService())->getSkuList($pid);

        if (!$is_raw_sku) {
            //获取该用户的会员卡信息
            $default_card = MemberCardRecordService::useCard($mid, $product['wid']);
            if (isset($default_card['data']['info']['card_id'])) {
                $cardInfo = $default_card['data'];
                $card_id  = $cardInfo['info']['card_id'];
                foreach ($sku['stocks'] as $k => $v) {
                    $sku['stocks'][$k]['bestCardPrice'] = 0;
                    $tempPrice = $v['price'];
                    if ($cardInfo['info']['isDiscount'] == 1 && $product['is_discount']) {
                        $is_vip                     = 1;
                        $sku['stocks'][$k]['price'] = $tempPrice * $cardInfo['info']['discount'] * 0.1;
                    }

                    $temp = json_decode($v['vip_card_price_json'], 1);
                    if ($is_vip) {
                        // 是会员且商品设置参与会员折扣
                        if ($v['vip_discount_way'] == 1 && isset($temp[$card_id]) && $temp[$card_id] != 0) {
                            $is_vip = 1;
                            //获取当前所用的会员卡
                            $sku['stocks'][$k]['price'] = $tempPrice - $temp[$card_id];
                        }
                        if ($v['vip_discount_way'] == 2 && isset($temp[$card_id]) && $temp[$card_id] != 0) {
                            $is_vip = 1;
                            //获取当前所用的会员卡
                            $sku['stocks'][$k]['price'] = $temp[$card_id];
                        }
                        $sku['stocks'][$k]['price'] = sprintf('%.2f', $sku['stocks'][$k]['price']);
                    }

                }
            }
        }

        if (!$is_vip && $product['is_discount'] && $product['is_show_vip_price']) {
            foreach ($sku['stocks'] as $k => $v) {
                $sku['stocks'][$k]['bestCardPrice'] = $v['price'];
                $tempPrice = $v['price'];
                $temp = json_decode($v['vip_card_price_json'], 1);
                // 最大减少价格
                $maxCardPrice = $temp ? max($temp) : 0;
                $priceToArray = [];
                if ($temp) {
                    foreach ($temp as $value) {
                        if ($value) {
                            $priceToArray[] = $value;
                        }
                    }
                }
                // 最小减少到的价格 不考虑减价到0元的情况
                $minCardPrice = $priceToArray ? min($priceToArray) : 0;
                // 否则 如果商品参与折扣 返回最大会员折扣价 非会员且商家设置了展示会员折扣 展示折扣
                if ($v['vip_discount_way'] == 1) {
                    $sku['stocks'][$k]['bestCardPrice'] = $tempPrice - $maxCardPrice;
                } elseif ($v['vip_discount_way'] == 2 && $minCardPrice) {
                    $sku['stocks'][$k]['bestCardPrice'] = $minCardPrice;
                }
            }
        }

        return [
            'is_vip' => $is_vip,
            'data'   => $sku
        ];
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180611
     * @desc 为商品详情图片视频添加域名
     * @param $content
     * @update 张永辉 2018年7月13 替换域名修改
     */
    public static function addProductContentHost($content, $tag = '')
    {
        $cdnImgUrl = ltrim(config('app.cdn_img_url'), '/');
        $imgUrl    = ltrim(config('app.source_img_url'), '/');

        $content = str_replace('src="/ueditor', 'src="' . $cdnImgUrl . '/ueditor', $content);
        $content = str_replace('src="/hsshop/ueditor', 'src="' . $cdnImgUrl . '/hsshop/ueditor', $content);
        $content = str_replace('src="/hsshop/ueditor', 'src="' . $cdnImgUrl . '/hsshop/ueditor', $content);
        $content = str_replace('src="/hsshop', 'src="' . $imgUrl . '/hsshop', $content);

        $content = str_replace('src=\"\/ueditor', 'src=\"' . $cdnImgUrl . '\/ueditor', $content);
        $content = str_replace('src=\"\/hsshop\/ueditor', 'src=\"' . $cdnImgUrl . '\/hsshop\/ueditor', $content);
        $content = str_replace('src=\"\/hsshop\/ueditor', 'src=\"' . $cdnImgUrl . '\/hsshop\/ueditor', $content);
        $content = str_replace('src=\"\/hsshop', 'src=\"' . $imgUrl . '\/hsshop', $content);

        return $content;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180611
     * @desc 删除商品详情图片域名
     * @param $content
     */
    public static function delProductContentHost($content, $tag = '')
    {
        $cdnImgUrl = config('app.cdn_img_url');
        $imgUrl    = config('app.source_img_url');

        $content = str_replace('<img src="' . $cdnImgUrl . 'ueditor', '<img src="/ueditor', $content);
        $content = str_replace('<img src="' . $cdnImgUrl . 'hsshop', '<img src="/hsshop', $content);
        $content = str_replace('<video src="' . $cdnImgUrl . 'hsshop', '<video src="/hsshop', $content);
        $content = str_replace('<img src="' . $imgUrl . 'hsshop', '<img src="/hsshop', $content);

        $content = str_replace('<img src=\"' . $cdnImgUrl . 'ueditor', '<img src=\"\/ueditor', $content);
        $content = str_replace('<img src=\"' . $cdnImgUrl . 'hsshop', '<img src=\"\/hsshop', $content);
        $content = str_replace('<video src=\"' . $cdnImgUrl . 'hsshop', '<video src=\"\/hsshop', $content);
        $content = str_replace('<img src=\"' . $imgUrl . 'hsshop', '<img src=\"\/hsshop', $content);


        return $content;
    }

    /**
     * 获取商品默认的运费字符串(可能是区间字符串)
     * @param int $product_id 商品id
     * @return string
     * @author 许立 2018年07月17日
     * @update 许立 2018年07月17日 如果只有一个规格 显示该规格的运费
     * @update 许立 2018年07月24日 多规格按重量返回运费0(只是显示 下单会计算真实运费)
     * @update 许立 2018年08月06日 运费模板计费 但是模板id为0的情况处理
     */
    public function getDefaultFreight($product_id, $mid, $umid)
    {
        // 判断商品是否存在
        $product = ProductService::getDetail($product_id);
        if (empty($product)) {
            return '0.00';
        }

        // 判断是否设置运费模板
        if ($product['freight_type'] == 1) {
            return $product['freight_price'];
        }

        // 判断是否是多规格
        $order_module = new OrderModule();
        $default_buy  = [
            [
                'product_id' => $product_id,
                'prop_id'    => 0,
                'num'        => 1
            ]
        ];
        if ($product['sku_flag']) {
            // 先获取所有规格
            $sku_list = (new ProductSkuService())->getListByProductID($product_id);
            // 运费模板
            $freightTemplate = (new FreightService())->init('wid', $product['wid'])->getInfo($product['freight_id']);
            // 运费模板计费 但是模板id为0的情况处理
            if ($freightTemplate && $freightTemplate['billing_type'] == 0 && $sku_list) {
                // 默认计算购买一件商品的运费
                $default_buy[0]['prop_id'] = $sku_list[0]['id'];
                return $order_module->getFreightByCartIDArr([], $product['wid'], $mid, $umid, 0, $default_buy);
            }
        } else {
            // 默认计算购买一件商品的运费
            return $order_module->getFreightByCartIDArr([], $product['wid'], $mid, $umid, 0, $default_buy);
        }

        return '0.00';
    }


    /**
     * 享立减图片广告补丁处理
     * @param $content
     * @return mixed|string
     * @author 张永辉
     */
    public function dealAdImg($content)
    {
        $content = json_decode($content);
        $content = json_decode(json_encode($content), true);
        foreach ($content as &$val) {
            if (!empty($val['type']) && $val['type'] == 'image_ad') {

                foreach ($val['images'] as $key => &$item) {
                    $item['FileInfo']['path']   = imgUrl() . $item['FileInfo']['path']??'';
                    $item['FileInfo']['s_path'] = imgUrl() . $item['FileInfo']['s_path']??'';
                    $item['FileInfo']['m_path'] = imgUrl() . $item['FileInfo']['m_path']??'';
                    $item['FileInfo']['l_path'] = imgUrl() . $item['FileInfo']['l_path']??'';
                }
            }
        }
        $content = json_encode($content);
        return $content;
    }

    /**
     * 判断商品是否有未发货的卡密订单
     * @param array $productId 商品id数组
     * @return bool
     * @author 许立 2018年08月10日
     */
    public function isCamNotDelivery($productIds)
    {
        // 获取卡密商品
        if (empty($productIds)) {
            return false;
        }
        $products    = ProductService::getListById($productIds);
        $camProducts = [];
        foreach ($products as $product) {
            $product['cam_id'] && $camProducts[] = $product['id'];
        }
        // 卡密且未发货订单
        return !!OrderDetailService::init()
            ->model
            ->whereIn('product_id', $camProducts)
            ->where('is_delivery', 0)
            ->get()
            ->toArray();
    }


    /**
     * 根据商品分组获取商品数据
     * @param $wid
     * @param $groupId
     * @param $isXCX
     * @return mixed
     * @author: 梅杰 2018 年10月23日
     */
    public function getProductByGroupId($wid, $groupId, $isXCX = 0)
    {
        if ($groupDetail = (new ProductGroupService())->getDetail($groupId)) {

            (isset($groupDetail['first_priority'])) && ($where['first_priority'] = $groupDetail['first_priority']);
            (isset($groupDetail['second_priority'])) && ($where['second_priority'] = $groupDetail['second_priority']);
            $where['groupMax'] = $group['num'] ?? 0;
            $where['limitNum'] = 1;
            $where['groupAll'] = 2;
            switch ($groupDetail['is_default']) {
                case 1:
                    $where['groupType'] = 'NEW';
                    break;
                case 2:
                    $where['groupType'] = 'HOT';
                    break;
                default:
                    $where['groupID'] = $groupId;
                    break;
            }
            $data = ProductService::getProductListV2($wid, $where, $isXCX);
            return $data;
        }
        return ['errCode' => 0, 'errMsg' => '', 'data' => []];
    }

    /**
     * 根据商品id获取
     * @param $product_id 商品id
     * @author 何书哲 2018年11月06日
     */
    public function getShareData($product_id)
    {
        $result      = [];
        $productData = ProductService::getDetail($product_id);
        $productData && $result = [
            'share_title' => $productData['share_title'],
            'share_desc'  => $productData['share_desc'],
            'share_img'   => $productData['share_img']
        ];
        return $result;
    }


    public function getShareCode($id, $mid, $wid)
    {
        $productData = ProductService::getRowById($id);
        if (!$productData) {
            return false;
        }
        $productUrl = $this->cdnFile($productData['img']);
        $member     = $this->getHeadUrl($mid);
        // @update 张永辉 2019年8月19日 如果获取头像失败直接返回
        if (!$member){
            return false;
        }
        $url        = config('app.url') . '/shop/product/detail/' . $wid . '/' . $id.'?_pid_='.$mid;
        $qrcode     = $this->qrCode($url);
        $url        = config('app.cdn_img_url') . 'hsshop/image/static/bg02.png!';
        $url .= '/watermark/url/' . base64_encode($productUrl . "!/format/webp/sq/580") . '/align/northwest/margin/20x20';
        $url .= '/watermark/url/' . base64_encode($qrcode . "!/format/webp/fwfh/200x200") . '/align/southeast/margin/20x30';
        $url .= "/watermark/url/" . base64_encode($member['url'] . "!/format/webp/roundrect/40/fw/40") . "/align/southwest/margin/20x200";
        $url .= '/watermark/text/' . str_replace('/', '|', base64_encode($member['nickname'])) . '/font/simkai/align/southwest/margin/65x205/color/000000/size/18';
        $titles = $this->getProductTitleArray($productData['title']);
        $size   = 160;
        $i=0;
        foreach ($titles as $val) {
            if($i < 2){
                $url .= '/watermark/text/' . str_replace('/', '|', base64_encode($val)) . '/font/sc/align/southwest/margin/20x' . $size . '/color/000000/size/18';
                $size -= 27;
            }
            $i++;
        }
        $url .= '/watermark/text/' . base64_encode('￥' . $productData['price']) . '/font/sc/align/southwest/margin/20x70/color/ff0000/size/25';
        $url .= '/watermark/text/' . base64_encode('￥' . $productData['oprice']) . '/font/sc/align/southwest/margin/20x30/color/808080/size/18';
        $url .= "/watermark/url/" . base64_encode("hsshop/image/static/s002.png!/format/webp/fw/100") . "/align/southwest/margin/20x45";


       return ['url'=>$url,'product'=>$productData];

    }


    /**
     * 获取分享卡片的key
     * @author 张永辉 2018年11月26日
     */
    public function getProductShareCardKey($id,$mid,$product)
    {
        return 'product:sharecard:'.$id.':'.$mid.':'.md5($product['title'].$product['img'].$product['price'].$product['oprice']);
    }


    public function getProductTitleArray($title)
    {
        $oldchar = array(" ", "　", "\t", "\n", "\r");
        $newchar = array("", "", "", "", "");
        $title   = str_replace($oldchar, $newchar, $title);
        $titles  = $this->left($title, 20);
//        show_debug($titles);
        return $titles;
    }


    function left($str, $len, $charset = "utf-8")
    {
        if (!is_numeric($len) or $len <= 0) {
            return [];
        }
        $sLen = strlen($str);
        if ($len >= $sLen) {
            return [$str];
        }
        if (strtolower($charset) == "utf-8") {
            $len_step = 3; //如果是utf-8编码，则中文字符长度为3
        } else {
            $len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
        }

        //执行截取操作
        $len_i = 0;
        //初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
        $substr_len = 0; //初始化应该要截取的总字节数
        $offset     = 0;
        $result     = [];
        for ($i = 0; $i < $sLen; $i++) {
            if ($len_i >= $len) {
                $result[] = substr($str, $offset, $substr_len);
                $offset += $substr_len;
                $len_i = $substr_len = 0;
            } //总截取$len个字符串后，停止循环
            //判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
            if (ord(substr($str, $i, 1)) > 0xa0) {
                $i += $len_step - 1;
                $substr_len += $len_step;
            } else { //否则，为英文字符，加1个字节
                $substr_len++;
            }
            $len_i++;
            if ($i >= $sLen - 1) {
                $result[] = substr($str, $offset, $substr_len);
            }
        }
        return $result;
    }


    /**
     *
     * @param $url 连接地址
     * @author 张永辉 2018年11月13日
     */
    public function cdnFile($url, $stream = '')
    {
        $bucket = config('app.cdn_bucket');
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $client = new Upyun($config);
        $res    = $client->has($url);
        if ($res) {
            return $url;
        }
        if (!$stream) {
            $stream = file_get_contents(imgUrl().$url);
        }
        $client->write($url, $stream);
        return $url;
    }


    /**
     * 获取摸一个人的头像
     * @author 张永辉 2018年11月13日
     * @update 张永辉2019年8月19日 用户不存在时直接返回false
     */
    public function getHeadUrl($mid)
    {
        $memberData = (new MemberService())->getRowById($mid);
        if (!$memberData['headimgurl']) {
            return false;
        }
        $url        = 'hsshop/image/headurl/' . md5($memberData['headimgurl']) . 'png';
        if (Storage::exists($url)) {
            return ['url' => $url, 'nickname' => $memberData['nickname']];
        }
        $stream = file_get_contents($memberData['headimgurl']);
        Storage::put($url, $stream);
        $url = $this->cdnFile($url, $stream);
        return ['url' => $url, 'nickname' => $memberData['nickname']];
    }

    public function qrCode($url)
    {
        $path   = 'hsshop/image/product/qrcode/' . date('Y/m') . md5($url) . '.png';
        $bucket = config('app.cdn_bucket');
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $client = new Upyun($config);
        $res    = $client->has($path);
        if ($res) {
            return $path;
        }
        $stream = Qrcode::format('png')->size(200)->margin(1)->generate($url);
        return $this->cdnFile($path, $stream);

    }

    /**
     * 获取评论的商家回复
     * @param array $comments 评论列表
     * @return array
     * @author 许立 2018年11月19日
     */
    public function handleCommentReply($comments)
    {
        if (empty($comments) || !is_array($comments)) {
            return [];
        }

        // 获取所有回复
        $commentIds = array_column($comments, 'id');
        $replies = (new ProductEvaluateDetail())
            ->whereIn('eid', $commentIds)
            ->where('mid', 0)
            ->where('reply_id', 0)
            ->get()
            ->toArray();
        $replies && $replies = array_column($replies, null, 'eid');

        // 重组回复
        foreach ($comments as $k => $comment) {
            $comments[$k]['seller_reply'] = $replies[$comment['id']]['content'] ?? '';
        }

        return $comments;
    }


    /**
     * 处理商品分享卡片
     * @author 张永辉 2018年11月26日
     */
    public function handProductShareCard($id,$mid,$wid,$product)
    {
        $redisClient = (new RedisClient())->getRedisClient();
        $res = $redisClient->get($this->getProductShareCardKey($id,$mid,$product));
        if ($res){
            return true;
        }
        $job = (new CreateShareCard($id, $mid, $wid))->onQueue('CreateShareCard');
        dispatch($job);
        return true;
    }
}