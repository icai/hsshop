<?php
/**
 * 商品接口类
 * Created by PhpStorm.
 * User: Herry
 * Date: 2017/3/23
 * Time: 15:59
 */

namespace App\Http\Controllers\shop;


use App\Events\NewUserEvent;
use App\Http\Controllers\Controller;
use App\Lib\Redis\NewUserFlagRedis;
use App\Lib\Redis\RedisClient;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\DistributeGrade;
use App\Model\DistributeTemplate;
use App\Model\Favorite;
use App\Model\Member;
use App\Module\DiscountModule;
use App\Module\DistributeModule;
use App\S\ShareEvent\ShareEventService;
use App\Module\FavoriteModule;
use App\S\Product\H5ComponentTempleteUseService;
use App\S\WXXCX\WXXCXConfigService;
use App\Services\DistributeTemplateService;
use App\S\File\FileInfoService;
use App\Model\Product;
use App\Module\SeckillModule;
use App\S\Product\ProductImgService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductWholesaleService;
use App\S\Product\ProductPropsToValuesService;
use App\Module\ProductModule;
use App\Services\FreightService;
use App\Services\ProductEvaluatePraiseService;
use App\S\Product\ProductTemplateService;
use App\Services\Shop\CartService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use ProductEvaluateDetailService;
use ProductEvaluateService;
use ProductService;
use Validator;
use WeixinService;
use MallModule as ProductStoreService;
use MicroPageNoticeService as ProductMicroPageNoticeService;
use App\S\Product\ProductGroupService;
use Bi;
use OrderDetailService;
use App\S\PublicShareService;
use MicroPageService as StoreMicroPageService;
use App\S\Customer\KefuService;
use App\S\Store\StoreService;
use QrCode;
use App\S\Weixin\ShopService;

class ProductController extends Controller
{
    /**
     * 商品列表
     */
    public function index(CartService $cartService, $wid = 0)
    {
        //判断登录
        $mid = session('mid');
        $cartNum = 0;
        if (!empty($mid)) {
            $cartNum = $cartService->cartNum($mid, $wid);
        }

        return view('shop.product.index', array(
            'title' => '商品列表',
            'wid' => $wid,
            'cartNum' => $cartNum,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }

    /**
     * 分页获取商品列表数据
     * @update 何书哲 2018年09月18日 已售罄列表不返回下架的
     * @update 许立 2018年09月19日 已售罄列表只返回库存为0且上架状态的商品
     * @update 何书哲 2019年03月11日 title参数不为空，查询满足条件的商品名称和商品编码的商品
     */
    public function list(Request $request, $wid)
    {
        $fuzzyWhere = [];
        $params = $request->input();
        // 判断店铺合法性
        if (empty(intval($wid))) {
            error('请选择店铺');
        }
        // 查询条件
        $where = ['wid' => $wid];
        $fuzzyWhere['wid'] = $wid;
        // 出售状态 出售中 1 已售罄 -2 仓库中 0
        if (isset($params['status']) && $params['status'] === 0) {
            $where['status'] = 0;
            $fuzzyWhere['status'] = 0;
        } else if (isset($params['status']) && $params['status'] == -2) {
            $where['stock'] = array('<=', 0);
            $where['status'] = 1;
            $fuzzyWhere['status'] = -2;
        } else {
            $where['status'] = 1;
            $where['stock'] = array('>', 0);
            $fuzzyWhere['status'] = 1;
        }

        // 检索条件
        // title参数不为空，查询满足条件的商品名称和商品编码的商品
        if (!empty($params['title'])) {

            $productTitleIds = (new \App\S\Product\ProductService())->getProductIdsByTitle($wid, $params['title']);

            if (isset($where['_string'])) {
                if ($productTitleIds) {
                    $where['_string'] .= " ADD (title LIKE '%" . $params['title'] . "%' OR id IN (" . implode(',', $productTitleIds) . "))";
                } else {
                    $where['_string'] .= " ADD (title LIKE '%" . $params['title'] . "%')";
                }
            } else {
                if ($productTitleIds) {
                    $where['_string'] = " (title LIKE '%" . $params['title'] . "%' OR id IN (" . implode(',', $productTitleIds) . "))";
                } else {
                    $where['_string'] = " (title LIKE '%" . $params['title'] . "%')";
                }
            }

        }

        if (!empty($params['group_id'])) {
            $params['group_id'] = addslashes(strip_tags($params['group_id']));

            if (isset($where['_string'])) {
                $where['_string'] .= ' AND FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
            } else {
                $where['_string'] = ' FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
            }

            $fuzzyWhere['groupId'] = $params['group_id'];
        }
        if (!empty($params['distribute_grade_id'])) {
            $data = DistributeGrade::select(['id', 'pids'])->find($params['distribute_grade_id']);
            if (!empty($data->pids)) {
                if (isset($where['_string'])) {
                    $where['_string'] .= " ADD id IN (" . $data->pids . ")";
                } else {
                    $where['_string'] = " id IN (" . $data->pids . ")";
                }
            }
        }

        // 获取列表
        if (!empty($params['title']) && config('app.fuzzy_search_url')) {
            // 商品分词查询
            $fuzzyWhere['keyword'] = $params['title'];
            if ($page = ($request->input('page') ?? 1)) {
                $fuzzyWhere['pageNum'] = $page;
            }
            $fuzzyWhere['pageSize'] = 15;
            $res = jsonCurl(config('app.fuzzy_search_url'), $fuzzyWhere);
            if ($res['code'] == 100) {
                $list['data'] = array_merge([], ProductService::getListById(array_column($res['data']['list'], 'id')));
            } else {
                error('系统维护中，请联系客服');
            }
        } else {
            // 正常查询
            list($list) = ProductService::listWithPage($where);
        }

        return mysuccess('', '', $list);
    }

    /**
     * 商品简易详情 弃用2017年08月22日
     */
    public function simpleDetail($wid, $pid)
    {
        if (empty($wid) || empty($pid)) {
            error('参数错误');
        }
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            error('商品不存在');
        }


        return mysuccess('', '', $product);
    }

    /**
     * 商品详情
     * @param CartService $cartService 购物车类
     * @param int $wid 店铺id
     * @param int $pid 商品id
     * @param Request $request 参数类
     * @author 许立 2017年03月27日
     * @update 张永辉 2018年07月13日 商品详情图片域名添加
     * @update 许立 2018年07月16日 预售时间返回时间戳
     * @update 许立 2018年07月17日 默认运费字符串处理
     * @update 张永辉 2018年8月20日 满减活动优惠
     * @update 许立 2018年09月21日 商品删除或下架判断逻辑修复
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年09月29日 特殊商品不展示更多商品推荐
     * @update 许立 2018年11月20日 评论返回商家回复
     * @update 许立 2019年11月22日 16:48:42 非会员如果设置显示会员价 返回最大力度折扣价
     * @update 许立 2019年11月28日 17:06:59 价格区间使用波浪号 否则前端有些地方计算金额有问题
     */
    public function detail(CartService $cartService, $wid, $pid, Request $request, ShopService $shopService, DistributeModule $distributeModule)
    {
        if (empty($wid) || empty($pid)) {
            error('参数错误');
        }
        $product = ProductService::getDetail($pid);
        if (empty($product)) {
            error('商品不存在');
        }

        //对商品模板数据进行处理 add by jonzhang 2017-07-24
        if (!empty($product['content'])) {
            $product['content'] = ProductStoreService::processTemplateData($wid, $product['content']);

            //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
            $product['content'] = dealWithProductContent($wid, $product['content']);
        }

        if ($product['status'] == 0 || $product['status'] == -1) {
            error('商品已下架，或已删除');
        }

        //商品的价格
        $product['showPrice'] = $product['price'];
        $product['is_vip'] = 0;

        //商品规格重构
        $sku = [];
        $mid = session('mid');
        if ($product['sku_flag']) {
            //商品规格重构
            $data = (new ProductModule())->handleSkuDiscountPrice($pid, $mid);
            $sku = $data['data'];
            if ($data['is_vip'] == 1) {
                $product['is_vip'] = 1;
            }
        }

        // 处理未开启规格价格
        $product['bestCardPrice'] = 0;
        if ($product['sku_flag'] == 0 && !$product['wholesale_flag']) {
            $reData = ProductService::reSetNoSkuPrice($product, $mid, session('wid'));
            $product['showPrice'] = $reData['price'];
            $product['bestCardPrice'] = $reData['bestCardPrice'];
            if ($product['bestCardPrice'] && $product['showPrice'] <= $product['bestCardPrice']) {
                $product['bestCardPrice'] = 0;
            }
            if ($reData['is_vip'] == 1) {
                $product['is_vip'] = 1;
            }

        }

        //开启规格，重构价格
        if ($product['sku_flag'] == 1) {
            $tmp = [];
            $bestCardPriceArray = [];
            if (!empty($sku['stocks'])) {
                foreach ($sku['stocks'] as $k => $val) {
                    $tmp[] = $val['price'];
                    $bestCardPriceArray[] = $val['bestCardPrice'] ?? 0;
                }
            } else {
                error('商品规格错误');
            }
            sort($tmp);
            $max = $tmp[0];
            $min = end($tmp);
            // 价格没有区间 则只显示一个价格
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
            error('店铺不存在，或已删除');
        }*/
        $shopData = $shopService->getRowById($wid, ['weixinConfigMaster']);
        if ($shopData) {
            $shopData['weixinConfigMaster'] = json_decode($shopData['weixinConfigMaster'], true);
        } else {
            error('店铺不存在，或已删除');
        }


        $moreProduct = [];
        if (!in_array($pid, ['92866', '100535']) && !$shopData['weixinConfigMaster']['is_more']) {
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

        //获取商品评价
        list($evaluate) = ProductEvaluateService::init('wid', session('wid'))->where(['pid' => $pid])->getList();
        list($data['good']) = ProductEvaluateService::init('wid', session('wid'))->where(['pid' => $pid, 'status' => 1])->getList();
        list($data['middle']) = ProductEvaluateService::init('wid', session('wid'))->where(['pid' => $pid, 'status' => 2])->getList();
        list($data['bad']) = ProductEvaluateService::init('wid', session('wid'))->where(['pid' => $pid, 'status' => 3])->getList();

        // 评论获取商家回复
        $productModule = new ProductModule();
        $evaluate['data'] = $productModule->handleCommentReply($evaluate['data']);
        $data['good']['data'] = $productModule->handleCommentReply($data['good']['data']);
        $data['middle']['data'] = $productModule->handleCommentReply($data['middle']['data']);
        $data['bad']['data'] = $productModule->handleCommentReply($data['bad']['data']);

        //统计评价信息 只取当前店铺的评论 Herry 20171206
        $res = ProductEvaluateService::init('wid', session('wid'))->model->select(DB::raw('count(*) as number,status'))->where(['wid' => session('wid'), 'pid' => $pid])->groupBy('status')->get()->toArray();
        $number = [
            'all' => 0,
            'good' => 0,
            'middle' => 0,
            'bad' => 0,
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
        $cartNum = $cartService->cartNum($mid, $wid);
        //是否显示分享佣金信息
        $member = Member::find(session('mid'));
        if (!$member) {
            \Log::info('ProductController的' . __LINE__ . '行错误，用户不存在,用户mid是' . session('mid'));
            error('用户不存在');
        }
        $member = $member->toArray();

        $distribute = [];
        $rate = $rateSec = 0;
        if ($shopData['is_open_weath'] == 1 && $shopData['is_distribute'] == 1 && ($shopData['distribute_grade'] == 0 || ($shopData['distribute_grade'] == 1 && $member['is_distribute'] == 1)) && $product['distribute_template_id'] != 0) {
            $distribute['tag'] = 1;
            $res = DistributeTemplate::find($product['distribute_template_id']);
            if (!$res) {
                return myerror('分销模板不存在');
            }
            $res = $res->load('gradeTemplate')->toArray();
            $res = $distributeModule->handDistribute([$res]);
            $res = array_pop($res);
            $res = $res[$member['distribute_grade_id']];
            $rate = [$res['zero'], $res['one'], $res['sec'], $res['three']];
            sort($rate);
            $rate = $res['one'];
            $rateSec = $res['sec'];
            if ($product['sku_flag']) {
                $distribute['price'] = ($min * $rate) / 100;
            } else {
                $distribute['price'] = $product['price'] * $rate / 100;
            }
            $distribute['price'] = round($distribute['price'], 2);

        }

        //详情替换图片路径
        $product['introduce'] = str_replace('<img src="ueditor', '<img src="' . config('app.source_img_url') . 'ueditor', $product['introduce']);

        //获取商品图片
        $product['productImg'] = (new ProductImgService())->getListByProduct($pid);

        //获取要求留言列表
        $product['productMsg'] = (new ProductMsgService())->getListByProduct($pid);

        //获取批发设置
        $product['wholesale_array'] = [];
        if ($product['wholesale_flag']) {
            $product['wholesale_array'] = (new ProductWholesaleService())->getListByProduct($pid);
            //设置批发价 则显示原价
            $product['is_vip'] = 0;
        }

        //获取模板详情
        //add by jonzhang 2017-07-28
        $template = '';
        $goodTemplateStyle = -2;
        if ($product['templete_use_id'] == -1 || $product['templete_use_id'] == -2) {
            $goodTemplateStyle = $product['templete_use_id'];
        } else if (!empty($product['templete_use_id']) && $product['templete_use_id'] != -1 && $product['templete_use_id'] != -2) {
            $productTemplateService = new ProductTemplateService();
            $productTemplateData = $productTemplateService->getRowById($product['templete_use_id']);
            if ($productTemplateData['errCode'] == 0 && !empty($productTemplateData['data'])) {
                $goodTemplateData = $productTemplateData['data'];
                $goodTemplateStyle = '-' . $goodTemplateData['template_style'];
                if ($goodTemplateData['template_style'] == 1 && !empty($goodTemplateData['product_template_info'])) {
                    $template = ProductStoreService::processTemplateData($wid, $goodTemplateData['product_template_info']);
                }
            }
        }
        //分享数据设置
        $shareData = [];
        $shareData['share_title'] = $product['share_title'] ? $product['share_title'] : $product['title'];
        $shareData['share_desc'] = $product['share_desc'] ? str_replace(PHP_EOL, '', $product['share_desc']) : $product['introduce'];
        $shareData['share_img'] = $product['share_img'] ? imgUrl() . $product['share_img'] : imgUrl() . $product['img'];
        //add by wuxiaoping 2017.08.30
        if (empty($product['share_title']) && empty($product['share_desc']) && empty($product['share_img'])) {
            $shareData = (new PublicShareService())->publicShareSet($wid);
        }

        //add by jonzhang 2017-07-24
        $microPageNotice = ProductMicroPageNoticeService::getNoticeApplication(['wid' => $wid, 'apply_id' => 3]);

        Bi::productView($wid, session('mid'), $product['id'], 2);

        //Herry 返回用户当前商品已购买数量
        $alreadyBuy = OrderDetailService::productBuyNum($mid, $pid);

        //【获取店铺主页的客服信息，注:当店铺主页添加QQ号时,商品详情才显示客服标识 2017.10.16 wuxiaoping】
        $storeResult = StoreMicroPageService::getRowByCondition(['wid' => $wid, 'is_home' => 1]);

        //默认设置为空
        $product['qq'] = $product['weixin'] = $product['telphone'] = '';
        if ($storeResult['data']) {
            $qq = $storeResult['data']['qq'];
            if ($qq) {
                $res = (new KefuService())->getRowByCondition(['qq' => $qq]);
                if ($res) {
                    $product['qq'] = $res['qq'];
                    $product['weixin'] = $res['weixin'];
                    $product['telphone'] = $res['telphone'];
                }
            }

        }
        /*update by wuxiaoping 2018.06.26 微商城享立减分享获取分享者id*/
        $product['shareEventInfoImg'] = [];
        if ($activityId = $request->input('activityId', 0)) {
            $shareEventInfo = (new ShareEventService())->getOne($activityId, $wid);
            $shareEventInfo && $product['shareEventInfoImg'] = explode(',', $shareEventInfo['show_imgs']);
        }
        $shareId = $request->input('shareId') ?? $mid;
        $view = $activityId ? 'shop.shareevent.showEventDetail' : 'shop.product.detail';
        $product['content'] = ProductModule::addProductContentHost($product['content'] ?? '');
        //增加新用户统计
        if ($activityId && (new NewUserFlagRedis())->get($mid)) {
            //
            $data = [
                'page' => '/shareevent/product/showproductdetail',
                'type' => 1,
                'param_id' => $activityId,
                'register_time' => time(),
            ];
            Event::fire(new NewUserEvent($data));
        }
        // 默认运费字符串处理
        $product['freight_string'] = $productModule->getDefaultFreight($pid, $mid, session('umid'));

        // 返回预售时间戳
        $product['now_timestamp'] = time();
        $product['sale_timestamp'] = time();
        if ($product['sale_time_flag'] == 2) {
            $product['sale_timestamp'] = strtotime($product['sale_time']);
        }
        //检查生成分享卡片
        $productModule->handProductShareCard($pid, $mid, $wid, $product);

        return view($view, [
            'title' => $product['title'] ? $product['title'] : '商品详情',
            'product' => $product,
            'shop' => $shopData,
            'more' => $moreProduct,
            'evaluate' => $evaluate,
            'number' => $number,
            'data' => $data,
            'cartNum' => $cartNum,
            'distribute' => $distribute,
            'sku' => json_encode($sku) ?? '',
            'template' => $template,
            'micro_page_notice' => json_encode($microPageNotice) ?? '',
            'goodTemplateStyle' => $goodTemplateStyle,
            'shareData' => $shareData,
            'alreadyBuy' => $alreadyBuy ?: 0,
            'member' => $member ?? [],
            'activityId' => $activityId,
            'shareId' => $shareId,
            'discount' => (new DiscountModule())->getDiscountDetailByPid($pid, $wid),
            'rate' => $rate,
            'rateSec' => $rateSec,
        ]);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703311701
     * @desc 获取商品评价信息 status=1,好评，2：中评，3：差评
     * @param Request $request
     * @update 许立 2018年11月20日 评论返回商家回复
     */
    public function evaluate(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'pid' => 'required',
        );
        $message = Array(
            'pid.required' => '商品ID不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $where = [
            'pid' => $input['pid'],
        ];
        if (isset($input['status']) && $input['status']) {
            $where['status'] = $input['status'];
        }

        list($evaluate) = ProductEvaluateService::init('wid', session('wid'))->where($where)->getList();

        $evaluate['data'] = (new ProductModule())->handleCommentReply($evaluate['data']);

        success('', '', $evaluate['data']);
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201704010911
     * @desc 获取评价详情信息
     * @param $eid
     */
    public function evaluateDetail(Request $request, FileInfoService $fileInfoService, ProductEvaluatePraiseService $productEvaluatePraiseService)
    {
        $input = $request->input();
        $rule = Array(
            'eid' => 'required',
        );
        $message = Array(
            'eid.required' => '评论ID不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        if (!isset($input['page']) || $input['page'] == 1) {
            $evaluateData = ProductEvaluateService::init('wid', session('wid'))->model->find($input['eid'])->load('member')->load('orderDetail')->toArray();


            if (!$evaluateData) {
                error('评论不存在或已被删除');
            }
            list($evaluateDetailData) = ProductEvaluateDetailService::init()->where(['eid' => $evaluateData['id']])->order('id asc')->getList();
            //展示页面
            $tmp = [];
            if ($evaluateData['img']) {
                foreach (explode(',', $evaluateData['img']) as $val) {
                    $tmp[] = $fileInfoService->getRowById($val);
                }
            }
            $evaluateData['img'] = $tmp;
            //是否已点赞
            list($praise) = $productEvaluatePraiseService->init('mid', session('mid'))->where(['eid' => $input['eid']])->getList();
            if ($praise['data']) {
                $evaluateData['praise'] = 1;
            } else {
                $evaluateData['praise'] = 0;
            }
            return view('shop.product.evaluateDetail', [
                'title' => '评价详情',
                'evaluateDetailData' => $evaluateDetailData,
                'evaluate' => $evaluateData,
                'shareData' => (new PublicShareService())->publicShareSet(session('wid'))
            ]);
        } else {
            //分页数据
            list($evaluateDetailData) = ProductEvaluateDetailService::init()->where(['eid' => $input['eid']])->order('id asc')->getList();
            success('', '', $evaluateDetailData);
        }

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201704011041
     * @desc 回复评论
     * @param Request $request
     */
    public function evaluateReply(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'eid' => 'required',
            'content' => 'required',
        );
        $message = Array(
            'eid.required' => '评论ID不能为空',
            'content.required' => '评论回复内容不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $evaluateData = [
            'eid' => $input['eid'],
            'mid' => session('mid'),
            'reply_id' => isset($input['reply_id']) ? $input['reply_id'] : '',
            'content' => $input['content']
        ];
        $id = ProductEvaluateDetailService::init()->add($evaluateData, false);
        $data = ProductEvaluateDetailService::init()->model->find($id)->load('member')->load('reply')->toArray();
        success('', '', $data);

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201704011154
     * @desc 对评论进行点赞
     * @param $wid
     * @param $eid
     */
    public function evaluatePraise($wid, $eid, ProductEvaluatePraiseService $productEvaluatePraiseService)
    {
        $productEvaluatePraiseData = $productEvaluatePraiseService->init()->model->where(['mid' => session('mid'), 'eid' => $eid])->get()->toArray();
        if ($productEvaluatePraiseData) {
            error('已点赞');
        }
        ProductEvaluateService::init('wid', session('wid'))->increment($eid, 'agree_num', false);
        $productEvaluatePraiseService->init()->add(['mid' => session('mid'), 'eid' => $eid]);
    }

    /**
     * 商品列表
     */
    public function search(Request $request, $wid)
    {
        if (empty($wid)) {
            error('请先选择店铺');
        }

        return view('shop.product.search', array(
            'title' => '商品搜索页面',
            'wid' => $wid,
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }

    /**
     * 获取规格列表
     */
    public function getSku(Request $request)
    {
        if ($request->isMethod('post')) {
            //获取商品详情
            $pid = $request->input('pid');
            if (empty($pid)) {
                error('参数不能为空');
            }
            /**********************************************************/
            //add by meiJie
            $sku = (new ProductModule())->handleSkuDiscountPrice($pid, session('mid'));
            success('', '', $sku['data']);
        }
    }

    /**
     * todo 移动端 显示商品模板的详情
     * @param Request $request
     * @author jonzhang
     * @date 2017-07-31
     */
    public function showProductTemplateDetail(Request $request, $wid, $id)
    {
        if (empty($id)) {
            error('id不能为空');
        }
        //商品信息
        $product = ProductService::getDetail($id);
        if (empty($product)) {
            error('商品不存在');
        }
        //商品详情中模板信息
        $productDetailTemplate = '';
        if (!empty($product['content'])) {
            $productDetailTemplate = ProductStoreService::processTemplateData($wid, $product['content']);

            //todo 商品详情数据错误临时补丁 目前只处理VOA真丝店铺 Herry
            $productDetailTemplate = dealWithProductContent($wid, $productDetailTemplate);
        }
        //商品简介
        $productIntro = $product['summary'];
        //商品模板信息
        $productTemplate = '';
        if (!empty($product['templete_use_id']) && $product['templete_use_id'] != -1 && $product['templete_use_id'] != -2) {
            $productTemplateService = new ProductTemplateService();
            $productTemplateData = $productTemplateService->getRowById($product['templete_use_id']);
            if ($productTemplateData['errCode'] == 0 && !empty($productTemplateData['data'])) {
                $goodTemplateData = $productTemplateData['data'];
                if ($goodTemplateData['template_style'] == 2 && !empty($goodTemplateData['product_template_info'])) {
                    $productTemplate = ProductStoreService::processTemplateData($wid, $goodTemplateData['product_template_info'], 1);
                }
            }
        }
        //公告信息
        $microPageNotice = ProductMicroPageNoticeService::getNoticeApplication(['wid' => $wid, 'apply_id' => 3]);
        return view('shop.product.templateDetail', [
                'productIntro' => $productIntro,//商品简介
                'productTemplate' => $productTemplate,//商品模板
                'productDetailTemplate' => $productDetailTemplate,//商品详情中模板
                'microPageNotice' => json_encode($microPageNotice), //公告
                'wid' => $wid
            ]
        );
    }

    /*
     * 获取规格列表
     */
    public function getSeckillSku(Request $request)
    {
        if ($request->isMethod('post')) {
            $seckillID = $request->input('sid');
            if (empty($seckillID)) {
                error('参数不能为空');
            }

            //秒杀详情
            $seckillModule = new SeckillModule();
            $seckill = $seckillModule->getSeckillDetail($seckillID);

            //秒杀商品原始sku
            $sku = (new ProductPropsToValuesService())->getSkuList($seckill['seckill']['product_id']);

            //组装最终秒杀库存
            empty($seckill['sku']) && error('秒杀商品价格库存不存在');
            $seckillSku = $seckillModule->getSeckillSku($sku, $seckill['sku']);

            success('', '', $seckillSku);
        }
    }

    /**
     * 商品分组详情
     * @param Request $request 请求参数
     * @param $wid 店铺id
     * @param $id 商品分组id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 何书哲 2018年9月14日 商品分组添加店铺底部导航
     */
    public function groupDetail(Request $request, $wid, $id)
    {
        $detail = (new ProductGroupService())->getGroup(['id' => $id], $wid);
        if (count($detail['tpl']['data'])) {
            //商品分组
            $detail['tpl']['data'][0]['id'] = $id;
        }
        $tpl = ProductStoreService::processTemplateData($wid, json_encode($detail['tpl']['data']));
        $detail['tpl']['data'] = json_decode($tpl, true);
        //店铺底部导航
        $storeNavData = ProductStoreService::getStoreNavData($wid, 1);
        return view('shop.product.groupDetail', array(
            'title' => '商品分组详情页面',
            'wid' => $wid,
            'id' => $id,
            'data' => $detail,
            'footer' => $storeNavData['footer'],
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ));
    }

    /*
     * 商品分组详情预览
     */
    public function groupPreview(Request $request, $wid, $id)
    {
        $detail = (new ProductGroupService())->getGroup(['id' => $id], $wid);

        if (count($detail['tpl']['data'])) {
            //商品分组
            $detail['tpl']['data'][0]['id'] = $id;
        }

        $tpl = ProductStoreService::processTemplateData($wid, json_encode($detail['tpl']['data']));
        $detail['tpl']['data'] = json_decode($tpl, true);

        return view('shop.product.groupPreview', array(
            'title' => '商品分组详情页面',
            'wid' => $wid,
            'id' => $id,
            'data' => $detail
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171024
     * @desc商品评价页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showProductEvaluate($pid)
    {
        return view('shop.product.showProductEvaluate', array(
            'title' => '商品评价',
            'pid' => $pid,
        ));
    }


    /**
     * 商品详情预览
     * @param int $wid 店铺id
     * @param int $pid 商品id
     * @return view
     * @author 许立 2017年12月29日
     * @update 许立 2018年07月16日 预售时间返回时间戳
     * @update 许立 2018年07月23日 如果授权小程序则返回小程序商品预览二维码 否则返回公众号二维码, 小程序二维码拉伸处理
     * @update 许立 2018年07月23日 小程序二维码有图片才返回
     */
    public function preview($wid, $id)
    {
        //获取商品信息
        $product = (new ProductModule())->showDetail($id, $wid);
        //对商品的详细信息处理
        $content = [];
        if ($product['product']['content']) {
            $json = json_decode($product['product']['content'], true);
            foreach ($json as $v) {
                if (isset($v['content']) && $v['content']) {
                    $content[] = $v['content'];
                }

            }
        }
        //线下门店
        $storeService = new StoreService();
        $__storeNumber__ = $storeService->getStoreNum();
        //获取商品属性
        $sku = (new ProductPropsToValuesService())->getSkuList($id);  //dd($sku);

        // 返回预售时间戳
        $product['product']['now_timestamp'] = time();
        $product['product']['sale_timestamp'] = time();
        if ($product['product']['sale_time_flag'] == 2) {
            $product['product']['sale_timestamp'] = strtotime($product['product']['sale_time']);
        }

        // 如果授权小程序则返回小程序商品预览二维码 否则返回公众号二维码
        $qr_code_preview = QrCode::size(120)->generate(URL("/shop/product/detail/" . $wid . '/' . $id));
        $lite_app_config = (new WXXCXConfigService())->getRow($wid);
        if (empty($lite_app_config['errCode']) && !empty($lite_app_config['data'])) {
            $url = 'pages/main/pages/product/product_detail/product_detail?id=' . $id;
            $qr_code_xcx = (new ThirdPlatform())->getXCXQRCode($wid, 120, $url);
            // 小程序二维码拉伸处理
            if ($qr_code_xcx['errCode'] == 0 && $qr_code_xcx['data']) {
                $qr_code_preview = '<img width="120" src="data:image/png;base64,' . $qr_code_xcx['data'] . '"/>';
            }
        }

        return view('shop.product.preview', array(
            'product' => $product ?? [],
            'content' => $content ?? [],
            '__storeNumber__' => $__storeNumber__ ?? 0,
            'sku' => $sku ?? [],
            'qr_code_preview' => $qr_code_preview
        ));
    }

    /**
     * 获取商品组商品分页信息
     * @param Request $request
     * @param ProductModule $module
     * @author: 梅杰 2018年10月23日
     */
    public function productGroupDetail(Request $request, ProductModule $module)
    {

        if ($groupId = $request->input('group_id', 0)) {
            $wid = $request->session()->get('wid');
            $data = $module->getProductByGroupId($wid, $groupId);
            if ($data['errCode'] == 0) {
                $request->input('isNew') == 1 && !empty($data['data']['products']) && $data['data']['products'] = array_map(function ($item) {
                    $item['thumbnail'] = imgUrl() . $item['thumbnail'];
                    return $item;
                }, $data['data']['products']);
                if ($request->input('isNew') == 2 && !empty($data['data']['products'])) {
                    foreach ($data['data']['products'] as &$outItem) {
                        foreach ($outItem as &$inItem) {
                            $inItem['thumbnail'] = imgUrl() . $inItem['thumbnail'];
                        }
                    }
                }
                success('操作成功', '', $data['data']['products'] ?? []);
            }
            error('操作失败：' . $data['errMsg']);
        }
        error();
    }


    /**
     * 获取商品分享信息分享信息
     * @param $product_id 商品id
     * @author 何书哲 2018年11月06日
     */
    public function getShareData($product_id)
    {
        $result = (new ProductModule())->getShareData($product_id);
        success('操作成功', '', $result);
    }


    /**
     * 获取商品二维码
     * @author 张永辉 2018年11月13日
     * @update 张永辉 2019年8月19日 失败的情况下处理
     */
    public function getProductCard(Request $request, ProductModule $productModule, RedisClient $redisClient)
    {
        $id = $request->input('id');
        $product = ProductService::getRowById($id);
        $redisClient = $redisClient->getRedisClient();
        $res = $redisClient->get($productModule->getProductShareCardKey($id, session('mid'), $product));
        if ($res) {
            $res = config('app.cdn_img_url') . $res;
            success('操作成功', '', $res);
        }
        $res = $productModule->getShareCode($id, session('mid'), session('wid'));
        if (!$res) {
            error('数据异常');
        }
        success('操作成功', '', $res['url']);
    }


}