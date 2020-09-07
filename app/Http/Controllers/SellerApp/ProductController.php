<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/3/29
 * Time: 20:04
 */

namespace App\Http\Controllers\SellerApp;


use App\Http\Controllers\Controller;

use App\Model\Weixin;

use App\Model\DistributeTemplate;
use App\Model\Product;
use App\Module\EvaluateModule;
use App\Module\GroupsRuleModule;
use App\Module\ProductModule;
use App\S\Foundation\FileService;
use App\S\Market\SeckillService;
use App\S\Member\MemberCardService;
use App\S\Product\CategoryService as ProductCategoryService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductPropsService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductPropValuesService;
use App\S\Product\ProductService;
use App\Services\FreightService;
use App\S\Product\H5ComponentTempleteUseService;
use App\Services\Marketing\Exception;
use App\Services\ProductQrDiscountService;
use App\S\Product\ProductTemplateService;
use Illuminate\Http\Request;
use App\Lib\Redis\BiToOnline as BiToOnlineRedis;
use App\Services\WeixinBusinessService;
use App\Module\ShareEventModule;
use App\S\Product\ProductImgService;
use Redirect;
use Redisx;
use Session;
use MallModule as ProductStoreService;
use Validator;
use WeixinService;
use ProductService as PProductService;
use QrCode;
use QrCodeService;
use ProductEvaluateService;
use DB;
use App\S\Weixin\ShopService;

class ProductController extends Controller
{

    public function __construct(ProductService $productService,ProductCategoryService $productCategoryService,ProductGroupService $productGroupService,ProductQrDiscountService $productQrDiscountService,H5ComponentTempleteUseService $h5ComponentTempleteUseService,FreightService $freightService,MemberCardService $memberCardService) {
        $this->leftNav = 'product';
        $this->productService = $productService;
        $this->productCategoryService = $productCategoryService;
        $this->productGroupService = $productGroupService;
        $this->productQrDiscountService = $productQrDiscountService;
        $this->h5componentTempleteUseService = $h5ComponentTempleteUseService;
        $this->freightService = $freightService;
        $this->memberCardService = $memberCardService;
    }

    public function index ( Request $request)
    {
        $params = $request->input('parameter');               # 获取请求参数
        $page = $params['page']??1;
        $request->offsetSet('page',$page);
        $tokenData = $request->input('_tokenData');
        #由于 redis 分页尚未完善 这里强制 添加一个 无关参数 以便访问
        $wid = $tokenData['wid'] ?? 0;
        if(!$wid){
            apperror('您没有权限查看，请先登录');
        }
        //查询条件
        $status = $params['status']??1;
        $where = ['wid' => $wid];
        #-------------------1 标签页状态筛选 -----------------------------
        #出售状态 出售中 1 已售罄 -2 仓库中 0
        if ($status == 0) {
            $where['status'] = 0;
        } else if ($status == -2) {
            $where['stock'] = array('<=', 0);
            $where['status'] = array('>=', 0);
        } else {
            $where['status'] = 1;
            $where['stock'] = array('>', 0);
        }

        if (!empty($params['group_id']) && $params['group_id']<0 ){
            unset($params['group_id']);
        }

        //检索条件
        if (!empty($params['title'])) {
            $where['title'] = ['like', '%' . $params['title'] . '%'];
        }
        if (!empty($params['group_id'])) {
            $params['group_id']   = addslashes(strip_tags($params['group_id'] ));
            $where['_string'] = ' FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
        }

        //排序
        $orderBy = $order = '';
        if (!empty($params['orderby'])) {
            $orderBy = $params['orderby'];
            $order = $params['order'];
        }
        //add by zhangyh 20170524
        if ($request->input('tag')==1){
            $slidebar='distributionGoods';
            $weixin = Weixin::find(session('wid'))->toArray();
            if ($weixin['is_distribute']==1){
                $where['is_distribution'] = 1;
            }else{
                $where['distribute_template_id'] = -1;
            }
        }else{
            $slidebar='index';
        }

        //获取列表 使用重构的redis方法
        list($list, $pageHtml) = $this->productService->listWithPage($where, $orderBy, $order);

        //获取商品详情页地址
        foreach ($list['data'] as $k => $v) {
            //$list['data'][$k]['detail_url'] = urlencrypt($v['id'], '/product/detail');
            $list['data'][$k]['detail_url'] = config('app.url') . 'shop/product/detail/' . $wid . '/' . $v['id'];
        }
        $groups = $this->productGroupService->listWithoutPage(['wid' => $wid]);//dd($groups);

        //获取店铺会员卡列表
//        list($memberCards) = $this->memberCardService->init('wid', $wid)->getList();
        list($memberCards) = $this->memberCardService->getListPage(['wid'=>$wid], $orderBy = '', $order = '',$pagesize = '');

        $biData = $productid = [];
        if (!empty($list)) {
            foreach ($list['data'] as $k => $v) {
                $productid[] = $v['id'];
            }
            $redis = new BiToOnlineRedis();
            $biData = $redis->getPageBi($productid, $wid, 2);
        }
        $data['is_last'] = $list['last_page']>$list['current_page'] ? 0 : 1;
        $data['product'] = [];
        foreach ($list['data']  as $val){
            $data['product'][] = [
                'id'            => $val['id'],
                'title'         => $val['title'],
                'price'         => $val['price'],
                'img'           => $val['img'],
                'oprice'        => $val['oprice'],
                'cost_price'    => $val['cost_price'],
                'status'        => $val['status'],
                'stock'         => $val['stock'],
                'sold_num'      => $val['sold_num'],
            ];
        }
        $data['groups'] = [];
        foreach ($groups[0]['data'] as $val){
            $data['groups'][] = [
                'id'        => $val['id'],
                'group_sn' => $val['group_sn'],
                'title'     => $val['title'],
            ];
        }

        $tmp =  [
            'id'        => -1,
            'group_sn' => '3232321',
            'title'     => '* 最新商品',
        ];
        array_unshift($data['groups'], $tmp);
        appsuccess('查询成功',$data);
    }


    /**
     * 商品详情
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function detail ( Request $request,ShopService $shopService){
        $input = $request->input('parameter');
        $tokenData = $request->input('_tokenData');
        $wid = $tokenData['wid'];
        if(!$wid){
            apperror('您没有权限查看，请先登录');
        }
        $rules = array(
            'product_id' => 'required|integer',
        );
        $messages = array(
            'product_id.required' => '商品id不能为空',
            'product_id.integer' => '商品id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $product = $this->productService->getDetail($input['product_id']);
        if (!$product) {
            apperror('商品不存在');
        }
        if ($product['content']) {
            $product['content'] = ProductStoreService::processTemplateData($wid, $product['content']);

            $product['content'] = ProductModule::addProductContentHost($product['content']);

            //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
            $product['content'] = dealWithProductContent($wid, $product['content']);
        }
        if ($product['status'] == 0 || $product['status'] ==-1){
            apperror('商品已下架，或已删除');
        }
        //详情替换图片路径
        $product['introduce'] = str_replace('<img src="ueditor', '<img src="' . config('app.source_img_url') . 'ueditor', $product['introduce']);
        //获取商品图片
        $product['productImg'] = (new ProductImgService())->getListByProduct($input['product_id']);
        //获取要求留言列表
        $product['productMsg'] = (new ProductMsgService())->getListByProduct($input['product_id']);
        //商品的价格
        $product['showPrice'] = $product['price'];
        $sku = [];
        $max = $product['price'];
        if ($product['sku_flag']){
            $sku = (new ProductPropsToValuesService())->getSkuList($input['product_id']);
            $tmp = [];
            if (!empty($sku['stocks'])){
                foreach ($sku['stocks'] as $val)
                {
                    $tmp[] = $val['price'];
                }
            }else{
                apperror('商品规格错误');
            }
            sort($tmp);
            $max = $tmp[0];
            $min = end($tmp);
            //价格没有区间 则只显示一个价格
            $product['showPrice'] = $max == $min ? $max : $max.'～'.$min;
        }
        //获取店铺信息
        /*$shopData = WeixinService::init('wid',$wid)->model->find($wid);
        if ($shopData){
            $shopData = $shopData->load('weixinConfigMaster')->toArray();
        }else{
            apperror('店铺不存在，或已删除');
        }*/
        $shopData = $shopService->getRowById($wid,['weixinConfigMaster']);
        if (empty($shopData)) {
            apperror('店铺不存在，或已删除');
        }
        $shopData['weixinConfigMaster'] = json_decode($shopData['weixinConfigMaster'],true);
        //更多商品
        $moreProduct = [];
        if (!$shopData['weixinConfigMaster']['is_more']) {
            //随机取出店铺最多10个商品
            list($data) = (new ProductService())->listWithPage(['wid' => $wid, 'status' => 1], '', '', 100);
            if ($data['total']>10){
                $randNum = array_rand($data['data'],10);
                foreach ($randNum as $value)
                {
                    $moreProduct[] = $data['data'][$value];
                }
            }else{
                $moreProduct = $data['data'];
            }
        }
        //默认运费计算
        $defaultFreight = 0.00;
        if ($product['freight_type'] == 1) {
            //统一运费
            $defaultFreight = $product['freight_price'];
        } elseif($product['freight_type'] == 2) {
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
        $template = [];
        $productTemplateService = new ProductTemplateService();
        $productTemplateData = $productTemplateService->getRowById($product['templete_use_id']);
        if ($productTemplateData['errCode'] == 0 && !empty($productTemplateData['data'])) {
            $template = $productTemplateData['data'];
        }
        //获取商品评价
        $evaluate_num = ProductEvaluateService::init('wid', $wid)->getEvaluateNumByPid($input['product_id']);
        $evaluate_data = (new EvaluateModule())->getProductEvaluate($input['product_id']);
        foreach ($evaluate_data as &$val) {
            $tmp = [
                'nickname' => $val['nickname'],
                'headimgurl' => $val['headimgurl'],
                'content' => $val['content'],
                'created_at' => $val['created_at'],
                'spes' => $val['spes'],
                'img' => isset($val['img']) ? array_map( function ($v) { return imgUrl().$v; }, array_column($val['img'], 'path')) : []
            ];
            $val = $tmp;
        }
        
        $res = [
            'shop_name' => $shopData['shop_name'],
            'logo' => $shopData['logo'] ? imgUrl().$shopData['logo'] : imgUrl().'hsshop/image/static/huisouyun_120.png',
            'product_img' => array_map( function ($v) { return imgUrl().$v; }, array_column($product['productImg'], 'img')),
            'title' => $product['title'],
            'show_price' => $product['showPrice'],
            'default_feight' => $defaultFreight,
            'sold_num' => $product['sold_num'],
            'stock' => $product['stock'],
            'evaluate_num' => $evaluate_num,
            'evaluate_data' => $evaluate_data,
            'content' => $product['content'],
            'api_host' => imgUrl(),
        ];
        $res['content'] = ProductModule::addProductContentHost($res['content']);

        appsuccess('商品详情获取成功', $res);
    }

    /**
     * 商品规格
     * @param Request $request
     */
    public function getSkusByProductId( Request $request){
        $input = $request->input('parameter');
        $tokenData = $request->input('_tokenData');
        $wid = $tokenData['wid'];
        if(!$wid){
            apperror('您没有权限查看，请先登录');
        }
        $rules = array(
            'product_id' => 'required|integer',
        );
        $messages = array(
            'product_id.required' => '商品id不能为空',
            'product_id.integer' => '商品id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $res = (new ProductPropsToValuesService())->getSkuList($input['product_id']);
        appsuccess('获取商品属性成功', $res);
    }

    /**
     * 商品分享
     * @param Request $request
     */
    public function getProductShare( Request $request){
        $input = $request->input('parameter');
        $tokenData = $request->input('_tokenData');
        $wid = $tokenData['wid'];
        if(!$wid){
            apperror('您没有权限分享，请先登录');
        }
        $rules = array(
            'product_id' => 'required|integer',
        );
        $messages = array(
            'product_id.required' => '商品id不能为空',
            'product_id.integer' => '商品id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $product = (new ProductService())->getRowById($input['product_id']);
        if (!$product) {
            apperror('该商品不存在');
        }
        appsuccess('获取商品分享成功', [
            'share_title' => $product['share_title'] ? $product['share_title'] : $product['title'],
            'share_desc' => $product['share_desc'] ? $product['share_desc'] : '我有一件好宝贝分享给你',
            'share_logo' => $product['share_img'] ? imgUrl($product['share_img']) : imgUrl($product['img']),
            'url' => config('app.url').'shop/product/detail/'.$product['wid'].'/'.$product['id']
        ]);
    }




}