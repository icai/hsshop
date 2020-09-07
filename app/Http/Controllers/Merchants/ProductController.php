<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Lib\ExcelHandler;
use App\Lib\Redis\BiToOnline as BiToOnlineRedis;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\DistributeTemplate;
use App\Model\Favorite;
use App\Model\Product;
use App\Model\Weixin;
use App\Module\CommonModule;
use App\Module\FavoriteModule;
use App\Module\GroupsRuleModule;
use App\Module\PermissionModule;
use App\Module\ProductModule;
use App\S\Foundation\FileService;
use App\S\Market\SeckillService;
use App\S\Member\MemberCardService;
use App\S\Product\CategoryService as ProductCategoryService;
use App\S\Product\H5ComponentTempleteUseService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductPropsService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductPropValuesService;
use App\S\Product\ProductService;
use App\S\Product\ProductTemplateService;
use App\S\ShareEvent\ShareEventService;
use App\S\Weixin\ShopService;
use App\S\WXXCX\WXXCXConfigService;
use App\Services\FreightService;
use App\Services\ProductQrDiscountService;
use Illuminate\Http\Request;
use MallModule as ProductStoreService;
use ProductService as PProductService;
use QrCode;
use QrCodeService;
use Redirect;
use Redisx;
use Session;
use Validator;
use WeixinService;

//use App\Services\MemberCardService;
//处理自定义模板中的商品信息使用到


class ProductController extends Controller
{
    /**
     * 构造函数
     */
    public function __construct(ProductService $productService, ProductCategoryService $productCategoryService, ProductGroupService $productGroupService, ProductQrDiscountService $productQrDiscountService, H5ComponentTempleteUseService $h5ComponentTempleteUseService, FreightService $freightService, MemberCardService $memberCardService)
    {
        $this->leftNav = 'product';
        $this->productService = $productService;
        $this->productCategoryService = $productCategoryService;
        $this->productGroupService = $productGroupService;
        $this->productQrDiscountService = $productQrDiscountService;
        $this->h5componentTempleteUseService = $h5ComponentTempleteUseService;
        $this->freightService = $freightService;
        $this->memberCardService = $memberCardService;
    }

    /**
     * 商品库
     * @param Request $request
     * @param int $status 出售状态: 出售中 1 已售罄 -2 仓库中 0
     * @param FavoriteModule $favoriteModule 收藏module 许立 2018年09月05日
     * @return view
     * @author 黄东 2017年3月7日
     * @update 许立 2018年6月26日 修改获取用户地址
     * @update 吴晓平 2018年08月23日 处理各分类商品显示的分组信息
     * @update 许立 2018年09月05日 返回商品收藏量
     * @update 许立 2018年09月17日 已售罄列表不返回下架的（java搜索接口需要同步修改 @何书哲）
     * @update 许立 2018年09月18日 已售罄列表只返回库存为0且上架状态的商品
     * @update 张永辉 2018年10月23日 返回验证用户是否有权限创建商品
     * @update 何书哲 2019年03月11日 title参数不为空，查询满足条件的商品名称和商品编码的商品
     */
    public function index(Request $request, FavoriteModule $favoriteModule, PermissionModule $permissionModule, $status = 1)
    {
        $fuzzyWhere = []; //分词查询
        $biData = $productid = [];
        $params = $request->input();               # 获取请求参数
        #由于 redis 分页尚未完善 这里强制 添加一个 无关参数 以便访问
        $wid = session('wid');
        if (!$wid) {
            error('您没有权限查看，请先登录');
        }
        //查询条件
        $where = ['wid' => $wid];
        $fuzzyWhere['wid'] = $wid;
        #-------------------1 标签页状态筛选 -----------------------------
        #出售状态 出售中 1 已售罄 -2 仓库中 0
        if ($status == 0) {
            $where['status'] = 0;
            $fuzzyWhere['status'] = 0;
        } else if ($status == -2) {
            $where['stock'] = array('<=', 0);
            $where['status'] = 1;
            $fuzzyWhere['status'] = -2;
        } else {
            $where['status'] = 1;
            $where['stock'] = array('>', 0);
            $fuzzyWhere['status'] = 1;
        }

        //检索条件
        //title参数不为空，查询满足条件的商品名称和商品编码的商品(有规格商品对应ds_product_sku表code，无规格商品对应ds_product表goods_no)
        if (!empty($params['title'])) {

            $productTitleIds = $this->productService->getProductIdsByTitle($wid, $params['title']);

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
        } else {
            if (isset($params['group_id']) && $params['group_id'] == '') {
                $where['group_id'] = "";
            }
        }

        //排序
        $orderBy = $order = '';
        if (!empty($params['orderby'])) {
            $orderBy = $params['orderby'];
            $order = $params['order'];
            $fuzzyWhere['orderBy'] = $params['orderby'];
            $fuzzyWhere['order'] = $params['order'];
        }
        //add by zhangyh 20170524
        if ($request->input('tag') == 1) {
            $slidebar = 'distributionGoods';
            $fuzzyWhere['tag'] = 1;
            $weixin = Weixin::find(session('wid'))->toArray();
            if ($weixin['is_distribute'] == 1) {
                $where['is_distribution'] = 1;
                $fuzzyWhere['isDistribution'] = 1;
            } else {
                $where['distribute_template_id'] = -1;
                $fuzzyWhere['isDistribution'] = 0;
            }
        } else if ($request->input('tag') == 2) {
            $slidebar = 'discountGoods';
            $fuzzyWhere['tag'] = 2;
            $where['is_discount'] = 1;
            //$fuzzyWhere['is_discount'] = 1;
        } else if ($request->input('tag') == 3) {
            $slidebar = 'pointGoods';
            $fuzzyWhere['tag'] = 3;
            $where['is_point'] = 1;
            //$fuzzyWhere['is_point'] = 1;
        } else if ($request->input('tag') == 4) {
            $slidebar = 'camGoods';
            $fuzzyWhere['tag'] = 4;
            $where['cam_id'] = ['<>', 0];
            //$fuzzyWhere['cam_id'] = ['<>',0];
        } else {
            $slidebar = 'index';
            $fuzzyWhere['tag'] = 0;
        }
        if (!empty($params['title']) && config('app.fuzzy_search_url')) {
            // 商品分词查询
            $fuzzyWhere['keyword'] = $params['title'];
            if ($page = ($request->input('page') ?? 1)) {
                $fuzzyWhere['pageNum'] = $page;
            }
            $fuzzyWhere['pageSize'] = 15;

            $res = jsonCurl(config('app.fuzzy_search_url'), $fuzzyWhere);
            if ($res['code'] == 100) {

                if (!empty($res['data'])) {
                    $productid = array_column($res['data']['list'], 'id');
                    $redis = new BiToOnlineRedis();
                    $biData = $redis->getPageBi($productid, $wid, 2);
                }
                $list['data'] = $this->productService->getListById($productid);
                foreach ($list['data'] as $k => $v) {
                    $list['data'][$k]['detail_url'] = config('app.url') . 'shop/product/detail/' . $wid . '/' . $v['id'];
                }
                $pageHtml = $this->productService->getPageHtml($request, $res['data']);
            } else {
                error('系统维护中，请联系客服');
            }
        } else {
            // 正常查询
            // 获取列表 使用重构的redis方法
            list($list, $pageHtml) = $this->productService->listWithPage($where, $orderBy, $order);
            // 获取商品详情页地址
            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['detail_url'] = config('app.url') . 'shop/product/detail/' . $wid . '/' . $v['id'];
            }
            if (!empty($list)) {
                foreach ($list['data'] as $k => $v) {
                    $productid[] = $v['id'];
                }
                $redis = new BiToOnlineRedis();
                $biData = $redis->getPageBi($productid, $wid, 2);
            }
        }

        $groups = $this->productGroupService->listWithoutPage(['wid' => $wid]);

        //获取店铺会员卡列表
        list($memberCards) = $this->memberCardService->getListPage(['wid' => $wid], $orderBy = '', $order = '', $pagesize = '');

        //判断当前店铺是否授权了小程序 Herry
        $lite_app_is_authorized = 0;
        $lite_app_config = (new WXXCXConfigService())->getRow($wid);
        if (empty($lite_app_config['errCode']) && !empty($lite_app_config['data'])) {
            $lite_app_is_authorized = 1;
        }

        // 许立 2018年6月26日 商品列表获取商品分组列表
        $list['data'] = (new ProductGroupService())->listWithGroupNames($list['data']);

        // 收藏量
        $list['data'] = $favoriteModule->handleListFavoriteCount($list['data'], Favorite::FAVORITE_TYPE_PRODUCT);
        //是否可以创建商品
        $isCreate = 1;
        if (session('role_id') == '10' && !$permissionModule->checkPermission(session('role_id'), session('wid'), 'create_product')) {
            $isCreate = 0;
        }
        return view('merchants.product.index', array(
            'title' => '商品库',
            'leftNav' => $this->leftNav,
            'list' => $list['data'] ?? [],
            'pageHtml' => $pageHtml,
            'groups' => $groups[0]['data'],
            //'pageLinks' => $products['pageLinks'],
            'slidebar' => $slidebar,
            'status' => $status,
            'wid' => $wid,
            'memberCardNum' => $memberCards['total'],
            'biData' => $biData,
            'lite_app_is_authorized' => $lite_app_is_authorized,
            'isCreate' => $isCreate,
        ));
    }

    /*
     * @todo: 删除单个商品
     * @params：id integer 商品id
     * @return：status integer 0|1
     */
    public function productDel(Request $request)
    {
        error('该接口已关闭');

        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $res = $this->productService->productDel($params, $wid);
            if ($res) {
                success('删除成功');
            } else {
                error('删除失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /**
     * 批量删除商品
     * @param Request $request 参数类
     * @author 许立 2017年05月26日
     * @update  梅杰 20180717 如果存在参与享立减商品不能删除
     * @update 许立 2018年08月10日 如果存在未发货卡密商品不能删除
     */
    public function batchDel(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();

        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $isDel = (new GroupsRuleModule())->isDelProduct($params['ids'], 'del');
            if (!$isDel) {
                error('存在未开始或正在进行中的团购商品，暂时不能删除');
            }

            if ((new SeckillService())->isProductSeckilling($params['ids'])) {
                error('存在未开始或正在进行中的秒杀商品，暂时不能删除');
            }
            if ((new ShareEventService())->isShareProduct($params['ids'])) {
                error('存在未开始或正在进行中的享立减商品，暂时不能删除');
            }

            // 未发货卡密订单
            if ((new ProductModule())->isCamNotDelivery($params['ids'])) {
                error('存在未发货卡密商品订单，暂时不能删除');
            }

            $res = $this->productService->batchDel($params);
            if ($res) {
                success('删除成功');
            } else {
                error('删除失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /**
     * 批量上下架商品
     * @param Request $request 参数类
     * @author 许立 2017年05月26日
     * @update 许立 2018年08月10日 如果存在未发货卡密商品不能操作
     */
    public function productOnOffSale(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {

            $isDel = (new GroupsRuleModule())->isDelProduct($params['ids'], 'del');
            if (!$isDel) {
                error('存在未开始或正在进行中的团购商品，暂时不能下架');
            }

            if ((new SeckillService())->isProductSeckilling($params['ids'])) {
                error('存在未开始或正在进行中的秒杀商品，暂时不能下架');
            }

            if ((new ShareEventService())->isShareProduct($params['ids'])) {
                error('存在未开始或正在进行中的享立减商品，暂时不能下架');
            }

            // 未发货卡密订单
            if (!$params['status'] && (new ProductModule())->isCamNotDelivery($params['ids'])) {
                error('存在未发货卡密商品订单，暂时不能删除');
            }

            $res = $this->productService->productOnOffSale($params, $wid);
            $str = $params['status'] ? '上架' : '下架';
            if ($res) {
                success($str . '成功');
            } else {
                error($str . '失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /*
     * @todo: 批量修改商品分组接口
     * @params：ids array 商品修改分组
     * @params: group_idx array 分组id集合
     * @params: idx  array  分组商品id 集合
     * @return：status integer 0|1
     */
    public function productModGroup(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $res = $this->productService->productModGroup($params, $wid);
            if ($res) {
                success('分组修改成功');
            } else {
                error('分组修改失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /*
     * @todo:  批量是否参与会员折扣接口
     * @params：ids array 商品 id 集合
     * @params: is_discount integer 0|1
     * @return：status integer 0|1
     */
    public function productModDiscount(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $res = $this->productService->productModDiscount($params, $wid);
            if ($res) {
                success('修改成功');
            } else {
                error('修改失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /*
     * 批量设置运费模板
     * @params：ids array 商品 id 集合
     * @params: is_discount integer 0|1
     * @return：status integer 0|1
     */
    public function setFreight(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $res = $this->productService->setFreight($params, $wid);
            if ($res) {
                success('修改成功');
            } else {
                error('修改失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /*
     * @todo: 查询所有分组接口
     * @params： null
     * @return: 返回所有分组
     */
    public function getAllGroup(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        //查询所有分组 过滤默认分组
        $groups = $this->productGroupService->listWithoutPage(['wid' => $wid, 'is_default' => 0]);
        success('查询分组成功', '', $groups[0]['data']);
    }

    /*
     * @todo: 查询所有商品类目接口
     * @params： null
     * @return: 返回所有类目
     */
    public function getAllCategory(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        # 查询所有分组

        $list = $this->productCategoryService->getAll();

        //处理二级
        $newList = [];
        foreach ($list as $v) {
            $v['sub'] = [];
            $newList[$v['id']] = $v;
        }

        foreach ($newList as $k => $v) {
            if ($v['parent_id']) {
                $newList[$v['parent_id']]['sub'][] = $v;
                unset($newList[$k]);
            }
        }

        //根据sort排序 sort要求唯一
        $return = [];
        foreach ($newList as $v) {
            $return[$v['listorder']] = $v;
        }
        ksort($return);

        success('查询分类成功', '', array_values($return));
    }

    /*
     * @todo: 添加商品
     * @params： 新建商品
     * @return: 返回是否创建成功
     */
    public function addProduct(Request $request, ProductService $productService)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        try {
            if ($request->isMethod('post')) {
                $product_id = $productService->setProduct($params['data'], $wid);
                if ($product_id) {
                    success('商品添加成功', '', ['id' => $product_id, 'wid' => $wid]);
                } else {
                    throw new \Exception('商品添加失败');
                }
            } else {
                throw new \Exception('非法操作');
            }
        } catch (\Exception $e) {
            error($e->getMessage());
        }
    }

    /**
     * 编辑商品
     * @param Request $request 参数类
     * @param ProductService $productService 商品类
     * @return int $pid 商品id
     * @author 许立 2018年07月18日 判断是否是商家自己编辑
     * @update 许立 2018年09月20日 享立减活动去掉分销字段限制
     * @author update 吴晓平 2018年08月22日 取消限制分销商品不能参加享立减活动
     * @update 吴晓平 2018年08月22日 取消限制分销商品不能参加享立减活动
     * @update 许立 2018年09月20日 享立减活动去掉分销字段限制
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 梅杰 2018年11月07日 已过期享立减修改商品bug
     */
    public function setProduct(Request $request, ProductService $productService, ShopService $shopService, $pid = 0)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            return myerror('您没有权限操作！');
        }
        $store = [];
        /*$storeInfo=WeixinService::getStoreInfo(session('wid'));
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById(session('wid'));
        if (!empty($storeInfo)) {
            $store = $storeInfo;
        }
        $shareEventService = new ShareEventService();
        if ($request->isMethod('post')) {
            // 参加享立减的商品价格不能够改为面议
            if (isset($params['data']['is_price_negotiable']) && $params['data']['is_price_negotiable'] == 1) {
                //true表示该商品正在参加享立减活动
                if ($shareEventService->isShareProduct([$params['data']['id']])) {
                    error('该商品正在参加享立减活动，价格不能够更改为面议');
                }
            }

            $shareEventData = $shareEventService->getListWithCondition(['status' => 0, 'type' => 0, 'product_id' => $params['data']['id']]);
            if (!empty($shareEventData)) {
                $num = 0;
                $itemPrice = 0;
                foreach ($shareEventData as $item) {
                    //逐减金额不能够大于商品最低价
                    if ($item['unit_amount'] > ($params['data']['price']) * 100) {
                        $num = -1;
                        $itemPrice = sprintf('%.2f', $item['unit_amount'] / 100);
                        break;
                    }//保底价不能够大于商品的最低价
                    if ($item['lower_price'] >= ($params['data']['price']) * 100) {
                        $itemPrice = sprintf('%.2f', $item['lower_price'] / 100);
                        $num = -2;
                        break;
                    }//保底价+逐减金额 不能够大于商品的最低价
                    if (intval($item['lower_price']) + intval($item['unit_amount']) > intval(($params['data']['price']) * 100)) {
                        $num = -3;
                        $itemPrice = sprintf('%.2f', (intval($item['lower_price']) + intval($item['unit_amount'])) / 100);
                        break;
                    }
                }
                if ($num == -1) {
                    error('该商品已添加享立减活动，商品最低价低于了逐减金额:' . $itemPrice);
                } else if ($num == -2) {
                    error('该商品已添加享立减活动，商品最低价低于了保底价:' . $itemPrice);
                } else if ($num == -3) {
                    error('该商品已添加享立减活动，商品最低价低于保底价与逐减金额之和:' . $itemPrice);
                }
            }
            //end
            $product_id = $productService->setProduct($params['data'], $wid);
            if ($product_id) {
                return mysuccess('商品编辑成功', '', ['id' => $product_id, 'wid' => $wid]);
            } else {
                return myerror('商品编辑失败');
            }
        } else {
            $product = Product::select(['id', 'is_distribution', 'distribute_template_id', 'wid'])->find($pid);
            if ($product) {
                $product = $product->toArray();
            } else {
                error('商品不存在');
            }

            // 判断是否是商家自己编辑
            if ($product['wid'] != $wid) {
                error('无权操作别人的商品');
            }

            $template = [];
            if ($store['is_distribute'] == 1) {
                if ($product['distribute_template_id'] != 0) {
                    $template = DistributeTemplate::find($product['distribute_template_id']);
                    if ($template) {
                        $template = $template->toArray();
                    } else {
                        $template = DistributeTemplate::where('wid', session('wid'))->orderBy('is_default', 'desc')->first()->toArray();
                    }
                } else {
                    $template = DistributeTemplate::where('wid', session('wid'))->orderBy('is_default', 'desc')->first()->toArray();
                }
            }
            //获取该店铺是否有会员卡，有则开启会员折扣勾选  add mj
            $card = (new MemberCardService)->getListPage(['wid' => $wid, 'state' => 1]);


            //商品是否正在参与秒杀活动 或者 拼团活动 或者 享立减活动中
            $is_in_activity = 0;
            if (!empty($pid)) {
                if ((new SeckillService())->isProductSeckilling([$pid])
                    || !(new GroupsRuleModule())->isDelProduct([$pid], 'edit')
                    || $shareEventService->isShareProduct([$pid])) {
                    $is_in_activity = 1;
                }
            }
            return view('merchants.product.create', array(
                'title' => '发布编辑商品',
                'leftNav' => $this->leftNav,
                'slidebar' => 'index',
                'bodyClass' => ' ng-controller=myCtrl ng-app=myApp',
                'id' => $params['data']['id'] ?? 0,
                'backUri' => $request->header()['referer'][0] ?? '',
                'store' => json_encode($store),
                'template' => json_encode($template),
                'product' => json_encode($product),
                'wid' => $wid,
                'card' => $card[0]['total'] ?? 0,
                'productId' => $pid ?? 0,
                'is_in_activity' => $is_in_activity,
                //'shopKind'  =>json_encode($shopKind)
            ));
        }
    }

    /*
     * @todo: 查询单个商品
     */
    public function getProduct(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        $product = $this->productService->getProduct($params, $wid);
        $product['content'] = ProductModule::addProductContentHost($product['content']);
        success('商品查询成功！', '', $product);
    }

    /*
     * @todo: 添加或修改优惠折扣
     */
    public function setQrDiscount(Request $request)
    {
        $wid = session('wid'); #商品id
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $flag = $this->productQrDiscountService->setQrDiscount($params, $wid);
            if ($flag) {
                success('设置扫码优惠成功');
            } else {
                error('设置扫码优惠失败');
            }
        } else {
            error('非法操作！');
        }
    }

    /*
     * @todo: 查询优惠折扣
     */
    public function getQrDiscount(Request $request)
    {
        $wid = session('wid'); #商品id
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        $qrlist = $this->productQrDiscountService->getQrDiscount($params, $wid);
        success('查询扫码优惠成功', '', $qrlist);
    }

    /*
     * @todo:查询商品模板
     */
    public function getProductTemplete(Request $request)
    {
        $wid = session('wid'); #商品id
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        $qrlist = $this->h5componentTempleteUseService->getTempletes($params, $wid, false);
        $qrlist = isset($qrlist['list']) && isset($qrlist['list']['data']) ? $qrlist['list']['data'] : $qrlist['list'];
        success('查询商品模板成功', '', $qrlist);
    }

    /*
     * @todo: 修改商品 对应的商品模板
     */
    public function modProductTemplete(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $res = $this->productService->productModTpl($params, $wid);
            if ($res) {
                success('修改模板成功');
            } else {
                error('修改模板失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /**
     * 商品会员价获取/设置
     * @param Request $request
     * @update 许立 2019年11月22日 13:41:44 增加非会员是否显示会员价参数
     */
    public function propMemPrice(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        $propValueService = new ProductPropsToValuesService();
        if ($request->isMethod('post')) {
            $rules = array(
                'productId' => 'required|integer',
                'vip_discount_way' => 'required|in:0,1,2',
                'prop_values' => 'required',
                'is_show_vip_price' => 'required|in:0,1',
            );
            $messages = array(
                'productId.required' => '商品id不存在',
                'productId.integer' => '商品id必须为整数',
                'vip_discount_way.required' => '优惠方式不存在',
                'vip_discount_way.in' => '优惠方式不正确',
                'prop_values.required' => '优惠内容不能为空',
                'is_show_vip_price.required' => '非会员是否显示会员价参数不存在',
                'is_show_vip_price.in' => '非会员是否显示会员价参数不正确',
            );
            $validator = Validator::make($params, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $res = $propValueService->setPropMemLevel($params, $wid);
            if ($res['status'] == 1) {
                success('会员价设置成功');
            } else {
                error('会员价设置失败');
            }
        } else { //查询对应的会员卡及属性
            $rules = array(
                'product_id' => 'required|integer',
                'title' => 'required',
            );
            $messages = array(
                'product_id.required' => '商品id不存在',
                'product_id.integer' => '商品id必须为整数',
                'title.required' => '商品名称不存在',
            );
            $validator = Validator::make($params, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $data = $propValueService->getPropMemLevel($params, $wid);
            if ($data['status'] != 1) {
                error($data['message']);
            }
            success('查询属性等级成功', '', $data['data']);
        }
    }

    /*
     * 发布编辑商品
     * @return [type] [description]
     * @update 张永辉 2018年10月23日 基础版本最多创建20个
     */
    public function create(Request $request, ShopService $shopService, PermissionModule $permissionModule)
    {
        $store = [];
        /*$storeInfo=WeixinService::getStoreInfo(session('wid'));
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById(session('wid'));
        if (!empty($storeInfo)) {
            $store = $storeInfo;
        }
        //获取分销模板
        $template = [];
        if ($store['is_distribute'] == 1) {
            $template = DistributeTemplate::where('wid', session('wid'))->orderBy('is_default', 'desc')->first();
            if ($template) {
                # code...
                $template = $template->toArray();
            }
        }


        //获取该店铺是否有会员卡，有则开启会员折扣勾选  add mj
        $card = (new MemberCardService)->getListPage(['wid' => session('wid'), 'state' => 1]);

        if (session('role_id') == '10') {
            $permissionModule->checkPermission(session('role_id'), session('wid'), 'create_product') || $permissionModule->returnInfo('商品数量已超过上限，请联系客服升级处理');
        }

        return view('merchants.product.create', array(
            'title' => '发布编辑商品',
            'leftNav' => $this->leftNav,
            'slidebar' => 'index',
            'bodyClass' => ' ng-controller=myCtrl ng-app=myApp',
            'backUri' => $request->header()['referer'][0] ?? '',
            'store' => json_encode($store),
            'template' => json_encode($template),
            'wid' => session('wid'),
            'card' => $card[0]['total'] ?? 0,
            'is_in_activity' => 0,
            //'shopKind' =>json_encode($shopKind),
        ));
    }

    /*
     * 商品分组
     * @return [type] [description]
     */
    public function productGroup(Request $request)
    {
        $params = $request->input();               # 获取请求参数
        #由于 redis 分页尚未完善 这里强制 添加一个 无关参数 以便访问
        $wid = session('wid');
        if (!$wid) {
            error('您没有权限查看，请先登录');
        }

        //查询条件
        $where = ['wid' => $wid];

        //列表
        list($groups, $pageHtml) = $this->productGroupService->listWithPage($where);

        $groupList = $groups['data'];
        if (!empty($groupList)) {
            foreach ($groupList as $key => $group) {
                $where = ['wid' => $wid];
                if ($group['is_default'] == 0 || $group['is_default'] == 3 || $group['is_default'] == 4) {
                    //非默认分组
                    $group['id'] = addslashes(strip_tags($group['id']));
                    $where['_string'] = ' find_in_set(' . $group['id'] . ', group_id) ';
                }
                //商品分组数 只统计上架商品
                $where['status'] = 1;
                $groupList[$key]['goods_num'] = Product::wheres($where)->count();
            }
        }

        return view('merchants.product.productGroup', array(
            'title' => '商品分组',
            'leftNav' => $this->leftNav,
            'list' => $groupList,
            'pageHtml' => $pageHtml,
            'slidebar' => 'productGroup'
        ));
    }

    /*
     * @todo: 删除商品分组
     * @params : id  integer  商品分组id
     * @return : 删除成功 或 者 删除失败
     */
    public function delGroup(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $res = $this->productGroupService->delGroup($params, $wid);
            if ($res) {
                success('删除成功');
            } else {
                error('删除失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /*
     * @todo: 新建编辑商品分组
     * @params: title string 分组标题
                show_tag_title  integer 页面上是否显示分组名称 1 是 0 否
                first_priority  integer  第一优先级
                second_priority integer  第二优先级
                default_config_json string 默认配置
                introduce string 分组简介
     * @return : 创建成功或失败
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function createGroup(Request $request, ShopService $shopService)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        if ($request->isMethod('post')) {
            $params['data'] = ProductModule::delProductContentHost($params['data']);
            $flag = $this->productGroupService->setGroup($params, $wid);
            if ($flag) {
                success('商品分组操作成功', URL('/merchants/product/productGroup'));
            } else {
                error('商品分组操作失败');
            }
        } else {
            $store = [];
            //$storeInfo = WeixinService::getStageShop($wid);
            $storeInfo = $shopService->getRowById($wid);
            if (!empty($storeInfo)) {
                $store['id'] = $storeInfo['id'];
                $store['shop_name'] = $storeInfo['shop_name'];
                $store['url'] = config('app.url') . 'shop/index/' . $store['id'];
            }
            return view('merchants.product.createGroup', array(
                'title' => '新建编辑商品分组',
                'leftNav' => $this->leftNav,
                'slidebar' => 'productGroup',
                'store' => json_encode($store)
            ));
        }
    }

    /*
     * @todo: 查询单个分组
     * @params: id integer 分组id
     * @return: 分组 数据
     */
    public function getGroup(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        $group = $this->productGroupService->getGroup($params, $wid);

        if (count($group['tpl']['data'])) {
            //商品分组
            $group['tpl']['data'][0]['id'] = $params['id'];
        }

        $tpl = ProductStoreService::processTemplateData($wid, json_encode($group['tpl']['data']));
        $group['tpl']['data'] = json_decode($tpl, true);

        $group = json_decode(ProductModule::addProductContentHost(json_encode($group)), true);//add by zhangyh

        success('查询分组成功', '', $group);
    }

    /*
     * @todo: 显示商品页面模板列表
     * @params：type string product 查询商品模板
     * @return 模板视图及对应的列表
     */
    public function goodsTemplate(Request $request, ProductTemplateService $productTemplateService)
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时', '/auth/login');
        }
        $productTemplateList = [];
        $result = $productTemplateService->getListByConditionWithPage(['wid' => $wid]);
        if (!empty($result[0]['data'])) {
            $productTemplateList = $result[0]['data'];
        }
        $pageHtml = $result[1];
        return view('merchants.product.goodsTemplate', array(
            'title' => '页面模板',
            'leftNav' => $this->leftNav,
            'list' => $productTemplateList,
            'pageHtml' => $pageHtml,
            'slidebar' => 'goodsTemplate'
        ));
    }

    /**
     * 获取商品模板页列表(商品发布第三步使用)
     */
    public function getGoodsTemplates(ProductTemplateService $productTemplateService)
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('session过期');
        }
        $result = $productTemplateService->getListByCondition(['wid' => $wid]);
        if ($result['errCode'] == 0) {
            success('', '', $result['data']);
        } else {
            error($result['errMsg']);
        }

    }

    /*
     * @todo:删除单个商品页面模板
     * @params： id integer 单个商品页面id
     * @return： 删除成功或失败
     */
    public function delProductTpl(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        $params['type'] = 'product'; #删除与产品页面模板 相关的模板
        if ($request->isMethod('post')) {
            $res = $this->h5componentTempleteUseService->delTpl($params, $wid);
            if ($res) {
                success('删除成功');
            } else {
                error('删除失败');
            }
        } else {
            error('来源错误！');
        }
    }

    /*
     * @todo: 新建编辑商品页模版
     * @params:
     * @return [type] [description]
     */
    public function createTemplate(Request $request, ProductTemplateService $productTemplateService, ShopService $shopService, $id = 0)
    {
        $wid = session('wid');
        if (empty($wid)) {
            error('登录超时', '/auth/login');
        }
        $data = [];
        $pageTitle = '新建模版';
        if (!empty($id)) {
            $result = $productTemplateService->getRowById($id);
            if ($result['errCode'] == 0 && !empty($result['data'])) {
                $productTemplate = $result['data'];
                if (!empty($productTemplate)) {
                    $data['id'] = $productTemplate['id'];
                    $data['title'] = $productTemplate['template_name'];
                    $data['style'] = $productTemplate['template_style'];
                    //对商品信息进行处理 [通过商品ID找商品对应的商品信息]
                    $productTemplateData = $productTemplate['product_template_info'];
                    if (!empty($productTemplateData)) {
                        $productTemplateData = ProductStoreService::processTemplateData($wid, $productTemplateData, 1);
                    }
                    $data['template_info'] = $productTemplateData;
                    $data['template_info'] = ProductModule::addProductContentHost($data['template_info']);  //add by zhangyh
                }
            }
            $pageTitle = '编辑模版';
        }
        $store = [];
        /*$storeInfo=WeixinService::getStoreInfo($wid);
        if(!empty($storeInfo['data'])) {
            $store=$storeInfo['data'];
        }*/
        $storeInfo = $shopService->getRowById(session('wid'));
        if (!empty($storeInfo)) {
            $store = $storeInfo;
        }
        return view('merchants.product.createTemplate', array(
            'title' => '新建编辑商品页模版',
            'leftNav' => $this->leftNav,
            'slidebar' => 'goodsTemplate',
            'bodyClass' => ' ng-controller=myCtrl ng-app=myApp',
            'product_template' => json_encode($data),
            'store' => json_encode($store),
            'page_title' => $pageTitle,
            'wid' => $wid
        ));
    }

    /*
     * @todo: 查询所有运费模板
     */
    public function getFreights(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
        list($list) = $this->freightService->init('wid', $wid)->getList(false);
        success('查询运费模板列表成功', '', $list['data'] ?? []);
    }

    /*
     * @todo: 查询所有会员卡列表
     */
    public function getMemberCards(Request $request)
    {
        $wid = session('wid');
        $params = $request->all();
        if (!$wid) {
            error('您没有权限操作！');
        }
//        list($list) = $this->memberCardService->init('wid', $wid)->getList(false);
        $list['data'] = $this->memberCardService->getListByWid($wid);
        success('查询会员等级列表成功', '', $list['data'] ?? []);
    }

    /**
     * 商品导入 - 外部商品导入
     * @return [type] [description]
     */
    public function importGoods(Request $request)
    {
        return view('merchants.product.importGoods', array(
            'title' => '商品导入 - 外部商品导入',
            'leftNav' => $this->leftNav,
            'slidebar' => 'importGoods'
        ));
    }

    /*
     * 商品导入 - 导入商品素材
     * @return [type] [description]
     */
    public function importMaterial(Request $request)
    {
        $wid = session('wid');
        if (!$wid) {
            error('来源错误');
        }
        $params = $request->all();
        if ($request->method() == 'POST') {
            //导入到类目
            /*$categoryId = $request->input('cid');
            if (empty($categoryId)) {
                error('请选择类目');
            }*/
            $filepath = $_FILES['upload']['tmp_name'];
            if ($filepath) {
                //获取扩展名
                $extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
                //最终入库数据列表数组
                $new_data = [];
                if ($extension == 'csv') {
                    //csv文件
                    if (($handle = FileService::fopen_utf8($filepath)) === FALSE) {
                        error('文件打开失败');
                    }
                    $title_arr = array();#存储标题栏
                    $data_arr = array(); #存储数据栏
                    $is_title_flag = false;
                    $return_title = ['宝贝名称', '宝贝价格', '宝贝数量', '宝贝描述', '新图片', '商家编码', '外部链接'];
                    while ($data = fgetcsv($handle, null, "\t")) {
                        $temp_data = $data;
                        if ($is_title_flag) { #标题找到后才往数组里面塞数据
                            $data_arr[] = $temp_data;
                        }
                        if ($temp_data[0] == '宝贝名称') {
                            $title_arr = $temp_data; #塞入整个标题组
                            $is_title_flag = true;
                        }
                        unset($data);
                        unset($temp_data);
                    }
                    fclose($handle);
                    $new_data = $this->productService->taobao_to_weshop($title_arr, $data_arr, $return_title);
                } else if ($extension == 'xls' || $extension == 'xlsx') {
                    //@todo excel文件
                    $objPHPExcel = \PHPExcel_IOFactory::load($_FILES["upload"]["tmp_name"]);
                    $sheet = $objPHPExcel->getSheet(0);
                    //初始列表数据
                    $data = $sheet->toArray();
                    //除去标题行
                    unset($data[0]);
                    $new_data = $data;
                }
                $import_data = array(
                    'fields' => ['title', 'price', 'stock', 'introduce', 'img', 'goods_no', 'out_buy_link'],
                    'data' => $new_data
                );
                $flag = $this->productService->addTaobao($import_data, $wid, uniqid());
                if (!$flag) {
                    error('导入失败！');
                }
            } else {
                error('文件上传失败！');
            }
            return \Illuminate\Support\Facades\Redirect::to('merchants/product/importMaterial');
        } else {
            //组装where条件
            $where['wid'] = $wid;
            $where['status'] = 2;
            //商品标题筛选
            $title = isset($params['title']) && trim($params['title']) ? trim($params['title']) : '';
            if ($title) {
                $where['title'] = array('like', "%{$params['title']}%");
                //$where['title'] = $title;
            }
            //$products = $this->productService->init('wid', session('wid'))->where($where)->getList();
            $products = $this->productService->listWithPage($where);
            //获取商品详情页地址
            foreach ($products[0]['data'] as $k => $v) {
                $products[0]['data'][$k]['detail_url'] = urlencrypt($v['id'], '/product/detail');
            }
            return view('merchants.product.importMaterial', array(
                'title' => '商品导入 - 导入商品素材',
                'list' => $products[0]['data'],
                'pageLinks' => $products[1],
                'leftNav' => $this->leftNav,
                'slidebar' => 'importGoods',
                //'categories'=> $this->productCategoryService->getAll(),
                'groups' => $this->productGroupService->listWithoutPage(['wid' => $wid, 'is_default' => 0])
            ));
        }
    }

    /*
     * 商品导入 - 导入淘宝商品素材
     */
    public function importTaobao(Request $request)
    {
        if ($request->method() == 'POST') {
            //导入到分组
            $groupID = $request->input('gid');

            //执行解压解析文件等操作
            $this->productService->parseTaobaoZip(0, $groupID, session('wid'));
            return redirect('/merchants/product/importMaterial');
        }
    }

    /*
     * 商品导入 - 导入阿里巴巴商品素材
     */
    public function importAli(Request $request)
    {
        if ($request->method() == 'POST') {


            //导入到分组
            $groupID = $request->input('gid');

            //执行解压解析文件等操作
            $this->productService->parseAliZip(0, $groupID, session('wid'));
            return redirect('/merchants/product/importMaterial');
        }
    }

    /*
     * 商品导入 - 导入阿凡提商品素材
     */
    public function importAfanti(Request $request)
    {
        $datas = $request->all();
        $uploadFile = $datas['upload_afanti'];
        if ($uploadFile->getSize() <= 0 && !$uploadFile->isReadable()) {
            error('文件有问题');
        }
        $this->productService->processAfantiXls($uploadFile->getPathname(), 0, session('wid'));
        return redirect('/merchants/product/importMaterial');
    }

    /*
     * 商品导入 - 导入小程序商品素材
     */
    public function importXCX(Request $request)
    {
        $datas = $request->all();
        $uploadFile = $datas['upload_xcx'];
        if ($uploadFile->getSize() <= 0 && !$uploadFile->isReadable()) {
            error('文件有问题');
        }
        $this->productService->processXCXXls($uploadFile->getPathname(), 0, session('wid'));
        return redirect('/merchants/product/importMaterial');
    }

    /**
     * @description：商品导入 - 导入会搜云新零售系统的商品素材
     * @param Request $request
     * @param ExcelHandler $excel
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \App\Exceptions\CommonException
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月26日 16:05:54
     */
    public function importCard(Request $request, ExcelHandler $excel)
    {
        $file = $request->upload_aiCard;
        if (empty($file)) {
            error('请选择正确的文件');
        }
        // 对导入的文件进行本地保存
        $result = $excel->saveExcelFile($file, 'import/excel', rand(1, 100));
        if (!$result) {
            error('上传的文件格式不正确，只支持.xls .xlsx .csv格式的文件');
        }
        // 根据导入文件的绝对路径进行数据读取
        $importData = $excel->import($result['path']);
        array_shift($importData);
        $this->productService->processCardXls(session('wid'), $importData);
        return redirect('/merchants/product/importMaterial');
    }

    /**
     * 分销商品
     * @return [type] [description]
     */
    public function distributionGoods(Request $request, $status = 1)
    {

        $params = $request->input();               # 获取请求参数
        #由于 redis 分页尚未完善 这里强制 添加一个 无关参数 以便访问
        $wid = session('wid');
        if (!$wid) {
            error('您没有权限查看，请先登录');
        }
        //查询条件
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

        //检索条件
        if (!empty($params['title'])) {
            $where['title'] = ['like', '%' . $params['title'] . '%'];
        }
        if (!empty($params['group_id'])) {
            $params['group_id'] = addslashes(strip_tags($params['group_id']));
            $where['_string'] = ' FIND_IN_SET(' . $params['group_id'] . ',group_id) ';
        }

        //排序
        $orderBy = $order = '';
        if (!empty($params['orderby'])) {
            $orderBy = $params['orderby'];
            $order = $params['order'];
        }

        //获取列表 使用重构的redis方法
        list($list, $pageHtml) = $this->productService->listWithPage($where, $orderBy, $order);

        //获取商品详情页地址
        foreach ($list['data'] as $k => $v) {
            //$list['data'][$k]['detail_url'] = urlencrypt($v['id'], '/product/detail');
            $list['data'][$k]['detail_url'] = config('app.url') . 'shop/product/detail/' . $wid . '/' . $v['id'];
        }
        list($groups) = $this->productGroupService->listWithPage($where, $orderBy, $order);

        //获取店铺会员卡列表
        list($memberCards) = $this->memberCardService->getListByWid($wid);
        return view('merchants.product.index', array(
            'title' => '分销商品',
            'leftNav' => $this->leftNav,
            'list' => $list['data'] ?? [],
            'pageHtml' => $pageHtml,
            'groups' => $groups['data'] ?? [],
            //'pageLinks' => $products['pageLinks'],
            'slidebar' => 'distributionGoods',
            'status' => $status,
            'wid' => $wid,
            'memberCardNum' => $memberCards['total']
        ));
    }

    /*
     * @todo: 编辑时导入淘宝商品部分信息
     * @params：url string 获取地址栏信息
     * @return : 可能jsonp 跳转
     */
    public function getTbGoods(Request $request)
    {
        $wid = session('wid');
        if (!$wid) {
            error('来源错误1');
        }
        $params = $request->all();
        $params = $this->productService->getTb($params, $wid);
        dd($params);
    }

    /**
     * todo 添加商品模板信息
     * @param Request $request
     * @param ProductTemplateservice $productTemplateservice
     * @return array
     * @author jonzhang
     * @date 2017-07-28
     */
    public function insertProductTemplate(Request $request, ProductTemplateService $productTemplateService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $name = $request->input('name');
        $style = $request->input('style');
        $templateData = $request->input('data');

        $templateData = ProductModule::delProductContentHost($templateData);  //add zhangyh
        $data = [];
        $errMsg = '';
        if (empty($name)) {
            $errMsg .= '模板名称为空';
        } else {
            $data['template_name'] = $name;
        }
        if (empty($style)) {
            $errMsg .= '模板样式为空';
        } else {
            $data['template_style'] = $style;
        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        $wid = session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = 'session丢失';
            return $returnData;
        }
        $data['wid'] = $wid;
        if (empty($templateData) || $templateData == '[]') {
            $data['product_template_info'] = null;
        } else {
            if (is_array($templateData)) {
                $data['product_template_info'] = json_encode($templateData);
            } else {
                //验证数据是否为标准的json字符串
                $validateData = json_decode($templateData, true);
                if (empty($validateData)) {
                    $returnData['errCode'] = -3;
                    $returnData['errMsg'] = '数据格式不符合要求';
                    return $returnData;
                }
                $data['product_template_info'] = $templateData;
            }
        }
        $result = $productTemplateService->insertData($data);
        if ($result['errCode'] != 0) {
            return $result;
        }
        return $returnData;
    }

    /**
     * todo 更改商品模板信息
     * @param Request $request
     * @param ProductTemplateservice $productTemplateservice
     * @return array
     * @author jonzhang
     * @date 2017-07-28
     */
    public function updateProductTemplate(Request $request, ProductTemplateService $productTemplateService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $id = $request->input('id');
        $name = $request->input('name');
        $style = $request->input('style');
        $templateData = $request->input('data');
        $templateData = ProductModule::delProductContentHost($templateData);  //add zhangyh
        $data = [];
        $errMsg = '';
        if (empty($id)) {
            $errMsg .= 'id为空';
        }
        if (empty($name)) {
            $errMsg .= '模板名称为空';
        } else {
            $data['template_name'] = $name;
        }
        if (empty($style)) {
            $errMsg .= '模板样式为空';
        } else {
            $data['template_style'] = $style;
        }
        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }
        if (empty($templateData)) {
            $data['product_template_info'] = null;
        } else {
            if (is_array($templateData)) {
                $data['product_template_info'] = json_encode($templateData);
            } else {
                $validateData = json_decode($templateData, true);
                if (empty($validateData)) {
                    $returnData['errCode'] = -3;
                    $returnData['errMsg'] = '数据格式不符合要求';
                    return $returnData;
                }
                $data['product_template_info'] = $templateData;
            }
        }
        $wid = session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = 'session丢失';
            return $returnData;
        }
        $result = $productTemplateService->updateData($id, $data);
        if ($result['errCode'] != 0) {
            return $result;
        }
        return $returnData;
    }

    /**
     * todo 删除商品模板信息
     * @param Request $request
     * @param ProductTemplateservice $productTemplateservie
     * @return array
     * @author jonzhang
     * @date 2017-07-28
     */
    public function deleteProductTemplate(Request $request, ProductTemplateService $productTemplateService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        $id = $request->input('id');
        if (empty($id)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '接收数据为空';
            return $returnData;
        }
        $wid = session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = 'session丢失';
            return $returnData;
        }
        $result = $productTemplateService->delete($id);
        if ($result['errCode'] != 0) {
            return $result;
        }
        return $returnData;
    }

    /**
     * todo 查询某个店铺下的商品模板信息
     * @param Request $request
     * @param ProductTemplateservice $productTemplateservice
     * @author jonzhang
     * @date 2017-07-28
     */
    public function selectProductTemplate(Request $request, ProductTemplateService $productTemplateService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $wid = session('wid');
        if (empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'session丢失';
            return $returnData;
        }
        return $productTemplateService->getListByCondition(['wid' => $wid]);
    }

    public function copy(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            return myerror('商品ID为空');
        }

        if ($this->productService->copy($id, session('wid'))) {
            return mysuccess('复制商品成功');
        } else {
            return myerror('复制商品失败');
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170602
     * @desc
     */
    public function getTemplate(Request $request)
    {
        $res = DistributeTemplate::where('wid', session('wid'))->orderBy('is_default', 'desc')->first();
        if ($res) {
            return mysuccess('查询成功', '', $res->toArray());
        } else {
            return myerror('未设置分销模板');
        }
    }

    /**
     * 设置商品属性
     */
    public function set(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            $data = $request->input('data');
            if (empty($id) || empty($data)) {
                error('参数不完整');
            }
            if ($this->productService->update($id, $data)) {
                success('设置成功');
            } else {
                error('设置失败');
            }
        }
    }

    /*
     * 商品详情预览页
     */
    public function commodityPreview()
    {
        return view('merchants.product.commodityPreview', array(
            'title' => '商品预览',
            'leftNav' => $this->leftNav,
            'slidebar' => 'commodityPreview'
        ));
    }

    /**
     * 商品属性列表
     */
    public function propList()
    {
        $where = [];
        $where['_closure'] = function ($query) {
            $query->where('wid', 0)->orWhere('wid', session('wid'));
        };

        list($list) = (new ProductPropsService())->listWithoutPage($where, 'id', 'asc');
        success('', '', $list['data']);
    }

    /**
     * 添加商品属性
     */
    public function addProp(Request $request)
    {
        if ($request->isMethod('post')) {
            $title = $request->input('title');
            if (empty($title)) {
                error('参数不能为空');
            }
            $id = (new ProductPropsService())->add(session('wid'), $title);
            success('', '', $id);
        }
    }

    /**
     * 商品属性值列表
     * @param int $propID 属性id
     * @param ProductPropValuesService $productPropValuesService 属性值类
     * @param ProductPropsToValuesService $productPropsToValuesService 属性和属性值关联类
     * @return json
     * @author 许立 2018年7月3日
     */
    public function propValues($propID, ProductPropValuesService $productPropValuesService, ProductPropsToValuesService $productPropsToValuesService)
    {
        if (empty($propID)) {
            error('参数不完整');
        }

        // 获取列表
        list($list) = $productPropsToValuesService->listWithoutPage(['wid' => session('wid'), 'prop_id' => $propID]);

        // 获取属性值标题
        $ids = array_column($list['data'], 'value_id');
        //$prop_value_service = new ProductPropValuesService();
        list($list) = $productPropValuesService->listWithoutPage(['id' => ['in', array_unique($ids)]]);

        // 属性值列表去重
        $list['data'] = $productPropValuesService->removeRepeatValue($list['data']);

        success('', '', $list['data']);
    }

    /**
     * 添加商品属性值
     */
    public function addPropValue(Request $request)
    {
        if ($request->isMethod('post')) {
            $title = $request->input('title');
            if (empty($title)) {
                error('参数不能为空');
            }
            $id = (new ProductPropValuesService())->addValue($title);
            success('', '', $id);
        }
    }

    /**
     * 编辑商品属性值
     */
    public function editPropValue(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('id');
            $title = $request->input('title');
            if (empty($id) || empty($title)) {
                error('参数不能为空');
            }
            (new ProductPropValuesService())->editValue($id, $title);
        }
    }

    /**
     * todo 修改商品对应的模板
     * @param Request $request
     * @author jonzhang
     * @date 2017-07-31
     */
    public function updateGoodsTpl(Request $request)
    {
        $templateID = $request->input('tpl_id');
        $ids = $request->input('ids');
        if (empty($templateID) || empty($ids)) {
            error('参数为空');
        }
        if (!is_array($ids)) {
            error('ids数据不符合要求');
        }
        foreach ($ids as $id) {
            PProductService::update($id, ['templete_use_id' => $templateID]);
        }
        success('成功');
    }

    /**
     * 获取规格列表
     */
    public function getSku(Request $request)
    {
        if ($request->isMethod('post')) {
            $pid = $request->input('pid');
            if (empty($pid)) {
                error('参数不能为空');
            }

            success('', '', (new ProductPropsToValuesService())->getSkuList($pid));
        }

    }

    /**
     * 获取商品详情地址二维码图片
     */
    public function getQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = config('app.url') . 'shop/product/detail/' . $wid . '/' . $id;

        success('', '', QrCode::size(160)->generate(URL($url)));
    }

    /**
     * 获取商品详情地址二维码图片
     */
    public function downloadQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = config('app.url') . 'shop/product/detail/' . $wid . '/' . $id;
        //$code = QrCode::size(160)->generate(URL($url));
        $code = QrCodeService::create($url, '', 200);

        return response()->download($code, time() . '.png');
    }

    /**
     * 获取商品详情地址小程序二维码图片
     */
    public function getLiteAppQRCode(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id');

        if (empty($id)) {
            error('参数不合法');
        }

        $url = 'pages/main/pages/product/product_detail/product_detail?id=' . $id;

        success('', '', (new ThirdPlatform())->getXCXQRCode($wid, 200, $url));
    }

    /**
     * 获取商品详情地址小程序二维码图片
     * @update 许立 2018年08月28日 商品详情小程序二维码下载
     */
    public function downloadLiteAppQRCode(Request $request)
    {
        $id = $request->input('id');
        if (empty($id)) {
            error('参数不合法');
        }
        (new CommonModule())->qrCodeDownload(session('wid'), 'pages/main/pages/product/product_detail/product_detail?id=' . $id, 1);
    }


    /*
     * @desc 商品列表页数据导出 .xls格式
     * @付国维   20180108
     * */
    public function exportXls(Request $request)
    {
        $input = $request->input();
        if (!$input) {
            error('请选中导出的数据');
        }
        $wid = session('wid');
        if (!$wid) {
            error('您没有权限导出，请先登录');
        }

        //查询条件
        $where['wid'] = $wid;

        if ($input['all'] == 1) {
            //转化为数组
            $data['id'] = explode(',', $input['orderids']);
            $list = $this->productService->getProduc($data['id']);
            if ($list) {
                $this->productService->exportExcelXls($list);
            }

        } else {
            #-------------------1 标签页状态筛选 -----------------------------
            #出售状态 出售中 1 已售罄 -2 仓库中 0
            if ($input['status'] == 0) {
                $where['status'] = 0;
            } else if ($input['status'] == -2) {
                $where['stock'] = array('<=', 0);
                $where['status'] = array('>=', 0);
            } else {
                $where['status'] = 1;
                $where['stock'] = array('>', 0);
            }
            $list = $this->productService->getPro($where);
            if ($list) {
                $this->productService->exportExcelXls($list);
            }

        }

    }

    /**
     * 批量设置商品通用接口
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年11月16日
     */
    public function batchEdit(Request $request)
    {
        $input = $request->input();
        empty($input['ids']) && error('请先选择商品');
        $this->productService->batchEdit($input) ? success() : error();
    }
}
