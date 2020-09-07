<?php
namespace App\Services\Foundation;

use App\Module\SeckillModule;
use App\S\Cam\CamActivityService;
use App\S\Cam\CamListService;
use App\Module\DiscountModule;
use App\S\Market\CouponService;
use App\S\Market\ResearchService;
use App\S\Market\SeckillService;
use App\S\Market\SeckillSkuService;
USE App\S\Market\SignService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductSkuService;
use App\S\ShareEvent\LiEventService;
use App\S\ShareEvent\ShareEventService;
use App\S\Wechat\WeixinMaterialAdvancedService;
use App\S\Wechat\WeixinMaterialWechatService;
use Illuminate\Pagination\LengthAwarePaginator;
use MemberHomeService;
use MicroPageService;
use ProductService;
use Validator;
use WeixinService;
use MicroPageTypeService;
use App\Module\GroupsRuleModule;

/**
 * 链接到 服务类
 *
 * 类型说明：
 * 1商品；2商品分组；3微页面；4微页面分类；5店铺主页；6会员主页；7营销活动；8投票调查；9历史消息；10微信图文；11高级图文；12优惠券
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年3月22日 16:44:15
 */
class LinkToService
{
    /**
     * 请求参数
     *
     * @var array
     */
    public $input;

    /**
     * 每页数据条数
     *
     * @var integer
     */
    private $pp = 5;

    /**
     * 静态数据定义
     *
     * @var array
     */
    // private $staticDatas;
    private $staticDatas = [
        'methodList' => [
            '1'  => 'Product',
            '2'  => 'ProductGroup',
            '3'  => 'MicroPage',
            '4'  => 'MicroPageType',
            '5'  => 'Home',
            '6'  => 'MemberHome',
            '7'  => 'Cart',
            '8'  => 'Search',
            '9'  => 'AuthLink',
            '10' => 'MaterialWechat',
            '11' => 'MaterialAdvanced',
            '12' => 'WeixinCoupon',
            '13' => 'Seckill',
            '14' => 'materialGetSingle',
            '15' => 'weChatStaff',
            '16' => 'shareEvent',
            '17' => 'LiEvent',
            '18' => 'GroupList',
            '19' => 'Research', // 许立 2018年07月05日 增加调查留言活动类型
            '20' => 'Cam', // 许立 2018年08月06日 增加卡密活动类型
            '21' => 'Sign', //何书哲 2018年8月27日 增加签到活动类型
        ],
        'typeList' => [
            '1'  => '商品',
            '2'  => '商品分组',
            '3'  => '微页面',
            '4'  => '微页面分组',
            '5'  => '店铺主页',
            '6'  => '会员主页',
            '7'  => '营销活动',
            '8'  => '投票调查',
            '9'  => '历史消息',
            '10' => '微信图文',
            '11' => '高级图文',
            '12' => '优惠券',
            '13' => '秒杀',
            '14' => '图文',
            '15' => '微信客服',
            '16' => '享立减',
            '17' => '享立减2',
            '18' => '拼团商品',
            '19' => '调查活动', // 许立 2018年07月05日 增加调查留言活动类型
            '20' => '卡密活动', // 许立 2018年08月06日 增加卡密活动类型
            '21' => '签到', //何书哲 2018年8月27日 增加签到活动类型
        ],
        'searchName' => [
            '1'  => 'title',
            '2'  => 'title',
            '3'  => 'page_title',
            '4'  => 'title',
            '5'  => '',
            '6'  => '',
            '7'  => '',
            '8'  => '',
            '9'  => '',
            '10' => 'title',
            '11' => 'title',
            '12' => 'title',
            '13' => 'title',
            '14' => 'title',
            '15' => 'title',
            '16' => 'title',
            '17' => 'title',
            '18' => 'title',
            '19' => 'title', // 许立 2018年7月5日 增加调查留言活动类型
            '20' => 'title', // 许立 2018年08月06日 增加卡密活动类型
            '21' => '', //何书哲 2018年8月27日 增加签到活动类型
        ],
        'url' => [
            '1'  => '/product/detail',
            '2'  => '/group/detail',
            '3'  => '/microPage/index',
            '4'  => '/microPage/type',
            '5'  => '/index',
            '6'  => '/member/index',
            '7'  => '',
            '8'  => '',
            '9'  => '',
            '10' => '/news/detail',
            '11' => '/news/detail',
            '12' => '/activity/couponDetail',
            '13' => '/seckill/preview',
            '14' => '/news/Mdetail',
            '15' => '',
            '16' => '',
            '17' => '',
            '18' => '',
            '19' => '', // 许立 2018年7月5日 增加调查留言活动类型
            '20' => '', // 许立 2018年08月06日 增加卡密活动类型
            '21' => '', //何书哲 2018年8月27日 增加签到活动类型
        ],
    ];

    /**
     * 域名
     *
     * @var string
     */
    public $domain;

    /**
     * 获取静态数据
     *
     * @return array
     */
    public function getStaticDatas()
    {
        // if ( empty($this->staticDatas) ) {
        //     dd(1);
        //     // $this->staticDatas = SC::M('Foundation.LinkToService');
        //     dd($this->staticDatas);
        // }

        return $this->staticDatas;
    }

    /**
     * 获取主域名
     *
     * @return string
     */
    public function getDomain()
    {
        if ( empty($this->domain) ) {
            $this->domain = config('app.url');
        }

        return $this->domain;
    }

    /**
     * 获取展示数据
     *
     * @return [mixed] [请求类型对应的数据]
     */
    public function getDatas()
    {
        // 接收参数
        if ( empty($this->input) ) {
            empty($this->input) && $this->input = app('request')->only(['type', 'page', 'wid', 'title']);
            // 默认返回第一页数据
            empty($this->input['page']) && $this->input['page'] = 1;
        }
        // 定义验证规则
        $rules = [
            'wid'  => 'required|exists:weixin,id',
            'page' => ['regex:/^\d+$/'],
            'type' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21',
        ];
        // 定义错误消息
        $messages = [
            'wid.required'  => '参数缺失',
            'wid.exists'    => '店铺异常',
            'page.regex'    => '分页异常',
            'type.required' => '类型缺失',
            'type.in'       => '类型异常',
        ];
        // 执行验证
        $validator = Validator::make($this->input, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // 根据类型返回对应数据
        $method = 'get' . $this->staticDatas['methodList'][$this->input['type']];
        return $this->$method();
    }

    /**
     * 将所选数据解析成对应的微商城访问链接地址
     *
     * @param  array  $datas    [数据，应包含主键/wid/type，可包含operation/paramList]
     * 示例说明：
     * $datas = [
     *     'wid' => 42, // 店铺id
     *     'type' => 1, // 跟LinkToService中的type对应，需要在调用该函数前自行处理好
     *     '主键名' => 主键值, // 数据id，默认为主键名为id
     *     'operation' => true, // 是否对id进行加密，默认加密，非必须
     *     'paramList' => ['type' => 1], // url参数，默认为空，非必须
     * ]
     * @param  string $urlField [url键名]
     * @param  string $idKey    [主键名]
     * @return string [微商城访问链接地址]
     */
    public function parseUrl($datas = [], $urlField = 'url', $idKey = 'id')
    {
        // 数据验证
        if ( empty($datas) || !is_array($datas) ) {
            return $datas;
        }
        // 数据处理
        if ( is_array(current($datas)) ) {
            foreach ($datas as $key => $value) {
                $datas[$key] = $this->parseUrl($value,$urlField, $idKey );
            }
        } else {

            // 定义验证规则
            $rules = [
                'wid'  => 'required|exists:weixin,id',
                $idKey => 'required',
                'type' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21',
            ];
            // 定义错误消息
            $messages = [
                'wid.required'       => '参数缺失',
                'wid.exists'         => '店铺异常',
                $idKey . '.required' => '主键缺失',
                'type.required'      => '类型缺失',
                'type.in'            => '类型异常',
            ];
            // 执行验证
            $validator = Validator::make($datas, $rules, $messages);
            // 理论上放到else中即可 为了确保数据结构一致性 故放到判断外面 可根据实际情况进行调整
            $datas['operation'] = $datas['operation'] ?? false;
            $datas['paramList'] = $datas['paramList'] ?? [];
            // 构建url
            if ( $validator->fails() ) {
                $datas[$urlField] = '';
            } else {
                $datas[$urlField] = urlencrypt($datas['id'], $this->staticDatas['url'][$datas['type']], $datas['operation'], $datas['paramList']);
            }
        }

        return $datas;
    }

    /**
     * 构建搜索条件数组
     *
     * @return array [搜索条件数组]
     */
    public function search()
    {
        $where = [];
        if ( !empty(trim($this->input['title'])) ) {
            $where[$this->staticDatas['searchName'][$this->input['type']]] = ['like', '%' . $this->input['title'] . '%'];
        }
        return $where;
    }

    /**
     * 设置请求参数
     *
     * @param array $input [请求参数数组]
     */
    public function setInput( $input = [] ) {
        $this->input = $input;

        return $this;
    }

    public function url()
    {

    }

    /**
     * 获取商品列表
     *
     * @return [array] [数据和分页]
     * @update 吴晓平 2018年07月26日  过滤掉纯自提的商品选择
     * @update 许立 2018年08月09日 是否过滤卡密商品
     * @update 吴晓平 2018年08月21日 取消限制分销商品不能添加享立减活动
     */
    public function getProduct()
    {
        $where = $this->search();

        //update by 吴晓平 注释掉分销商品限制 
        /*$is_distribution=app('request')->input('is_distribution');
        if(isset($is_distribution))
        {
            $where['is_distribution']=app('request')->input('is_distribution');
        }*/
        $where['wid'] = $this->input['wid'];
        //上架且有库存商品
        $where['status'] = 1;
        $where['stock'] = ['>', 0];

        //add by 吴晓平 过滤掉纯自提的商品选择
        $is_filterHexiao = app('request')->input('filter_hexiao', 0);
        !empty($is_filterHexiao) && $where['is_hexiao'] = 0;
        
        //ids商品过滤 
        $ids = app('request')->input('ids');
        !empty($ids) && $where['id'] = ['in', explode(',', $ids)];

        //是否过滤掉面议商品 20171227
        $is_filter = app('request')->input('filter_negotiable', 0);
        !empty($is_filter) && $where['is_price_negotiable'] = 0;

        // 是否过滤卡密商品
        $is_filter = app('request')->input('filter_cam', 0);
        !empty($is_filter) && $where['cam_id'] = 0;

        //过滤掉满减活动商品
//        $flag = app('request')->input('flag','');
//        if ($flag == 1){
//            $result = (new DiscountModule())->getPids($this->input['wid']);
//            if ($result['errCode'] == -1){
//                return [];
//            }
//            $pids = $result['data'];
//            $where['id'] = ['not in', $pids];
//        }

        return call_user_func(function($datas) {
            foreach ($datas[0]['data'] as $key => $value) {
                $datas[0]['data'][$key]['url'] = urlencrypt($value['id'], $this->staticDatas['url'][1],false);

                //面议商品的价格字段返回面议两个字 原价字段返回0 20171229
                if ($value['is_price_negotiable'] == 1) {
                    $datas[0]['data'][$key]['price'] = '面议';
                    $datas[0]['data'][$key]['oprice'] = 0.00;
                }
            }
            return $datas;
//        }, ProductService::init('wid', $this->input['wid'])->where($where)->perPage($this->pp)->getList());
        }, ProductService::listWithPage($where, '', '', $this->pp));
    }

    /**
     * 获取商品分组列表
     *
     * @return [array] [数据和分页]
     */
    public function getProductGroup()
    {
        $where = $this->search();
        $where['wid'] = session('wid');

        $productGroupService = new ProductGroupService();
        return call_user_func(function($datas) {
            foreach ($datas[0]['data'] as $key => $value) {
                $datas[0]['data'][$key]['url'] = urlencrypt($value['id'], $this->staticDatas['url'][2],false);
            }
            return $datas;
        }, $productGroupService->listWithPage($where, '', '', 8));
    }

    /**
     * 获取微页面列表
     *
     * @return [array] [数据和分页]
     */
    public function getMicroPage()
    {
        $result=[];
        $whereData=[];
        $wid=session('wid');
        if(empty($wid))
        {
            return $result;
        }
        $title=$this->input['title'];
        if(!empty($title))
        {
            $whereData['page_title'] = $title;
        }
        $whereData['wid']=$wid;
        $list=MicroPageService::getListByConditionWithPage($whereData,'created_at','desc',5);
        if(!empty($list[0]['data']))
        {
            foreach($list[0]['data'] as $microPage)
            {
                $microPage['url']=config('app.url').'shop/microPage/index/'.$wid.'/'.$microPage['id'];
                if ($microPage['is_home'] == 1) {
                    $microPage['url']=config('app.url').'shop/index/'.$wid;
                    $microPage['page_title'] = '[主页]（'.$microPage['page_title'].'）';
                }
                
                $result[]=$microPage;

            }
            unset($list[0]['data']);
            $list[0]['data']=$result;
            unset($result);
            $result[]=$list[0];
        }
        return $result;
    }

    /**
     * 获取微页面分类列表
     *
     * @return [array] [数据和分页]
     */
    public function getMicroPageType()
    {
        $result=[];
        $wid=session('wid');
        if(empty($wid))
        {
            return $result;
        }
        $title=trim($this->input['title']);
        if(!empty($title))
        {
            $whereData['title'] =$title;
        }
        $whereData['wid']=$wid;
        $microPageData=MicroPageTypeService::getListByConditionWithPage($whereData,'created_at','desc',5);
        if(!empty($microPageData[0]['data']))
        {
            foreach($microPageData[0]['data'] as $item)
            {
                $item['url']=config('app.url').'#';
                $item['type_template_info']=json_decode($item['type_template_info'], true);
                $result[]=$item;
            }
            unset($microPageData[0]['data']);
            $microPageData[0]['data']=$result;
            unset($result);
            $result[]=$microPageData[0];
        }
        return $result;
    }

    /**
     * 获取店铺主页数据
     *
     * @return [array] [数据]
     */
    public function getHome()
    {
        $wid=$this->input['wid'];
        $info=[];
        $store = MicroPageService::getRowByCondition(['wid'=>$wid,'is_home' => 1]);
        if($store['errCode']==0&&!empty($store['data']))
        {
            $info=$store['data'];
            $info['url']=config('app.url').'shop/index/'.$wid;
        }
        return $info;
    }

    /**
     * 获取会员主页数据
     *
     * @return [array] [数据]
     */
    public function getMemberHome()
    {
        $result = MemberHomeService::getRow($this->input['wid']);
        $datas =  [];
        if($result['errCode']==0&&!empty($result['data'])) {
            $datas=$result['data'];
            if (isset($result['data']['custom_info'])) {
                $datas['custom_info'] = json_decode($result['data']['custom_info'], true);
            }
        }
        //$datas['url'] = urlencrypt(0, $this->staticDatas['url'][6], false);
        $datas['url']=config('app.url').'shop/member/index/'.$this->input['wid'];

        return $datas;
    }

    /**
     * 获取微信图文数据
     *
     * @return [array] [数据]
     */
    public function getMaterialWechat()
    {
        $where = $this->search();
        // 查询微信单条图文列表
        $request = app('request');
        $weixinMaterialWechatService = new WeixinMaterialWechatService();
        list($list) = $weixinMaterialWechatService->getAllList($this->input['wid'],$where);
        $list = $list['data'] ?? [];
        foreach ($list as $key => $value) {
            $list[$key]['url'] = urlencrypt($value['id'], $this->staticDatas['url'][10]);
        }
        // 转为树形结构
        $list = $weixinMaterialWechatService->listToTree($list);

        // 自定义分页
        $page = $request->input('page', 1);
        $list = new LengthAwarePaginator(array_slice($list, ( $page - 1 ) * $this->pp, $this->pp), count($list), $this->pp, $page, [
            'path' => $request->url(),
        ]);
        $pageHtml = $list->links();
        $list = $list->toArray();

        return [$list, $pageHtml];
    }

    /**
     * 获取高级图文数据
     *
     * @return [array] [数据]
     */
    public function getMaterialAdvanced()
    {
        $where = $this->search();
        // 查询高级单条图文列表
        $request = app('request');
        $weixinMaterialAdvancedService = new WeixinMaterialAdvancedService();
        list($list) = $weixinMaterialAdvancedService->getAllList($this->input['wid'],$where);
        $list = $list['data'] ?? [];
        foreach ($list as $key => $value) {
            $list[$key]['url'] = $value['href'];
        }
        // 转为树形结构
        $list = $weixinMaterialAdvancedService->listToTree($list);

        // 自定义分页
        $page = $request->input('page', 1);
        $list = new LengthAwarePaginator(array_slice($list, ( $page - 1 ) * $this->pp, $this->pp), count($list), $this->pp, $page, [
            'path' => $request->url(),
        ]);
        $pageHtml = $list->links();
        $list = $list->toArray();
        return [$list, $pageHtml];
    }

    /**
     * 获取店铺优惠券列表
     *
     * @return [array] [数据和分页]
     */
    public function getWeixinCoupon()
    {
        $where = $this->search();
        // 有效优惠券条件
        //$now   = date('Y-m-d H:i:s');
        $where = array_merge($where, [
            'wid'      => $this->input['wid'],
            'left'     => ['>', 0],
            //'start_at' => ['<', $now],
            //'end_at'   => ['>', $now],
            '_string'  => 'invalid_at IS NULL',
            //查询未删除列表
            'status' => 0
        ]);

        $where['_closure'] = function ($query) {
            $query->where(function($query){
                $query->where('expire_type',0)->where('end_at','>',date('Y-m-d H:i:s'));
            })->orWhere(function ($query){
                $query->where('expire_type','>',0);
            });
        };

        return call_user_func(function($datas) {
            foreach ($datas[0]['data'] as $key => $value) {
                $datas[0]['data'][$key]['url'] = urlencrypt($value['id'], $this->staticDatas['url'][3]);
            }
            return $datas;
        }, (new CouponService())->listWithPage($where, '', '', $this->pp));
    }

    /**
     * 获取秒杀活动列表
     * @return array
     * @author 许立 2017年07月25日
     * @update 许立 2018年08月31日 增加总库存总销量等字段
     */
    public function getSeckill()
    {
        $where = $this->search();
        $where['wid'] = $this->input['wid'];
        //适用平台类型 0:全部,1:微商城,2:小程序
        $type = app('request')->input('platform') ?? 0;
        if ($type == 1) {
            $where['type'] = ['in', [0, 1]];
        } elseif ($type == 2) {
            $where['type'] = ['in', [0, 2]];
        }

        //未失效且未结束
        $where['end_at'] = ['>', date('Y-m-d H:i:s')];
        $where['invalidate_at'] = '0000-00-00 00:00:00';
        return call_user_func(function($datas) {
            // 返回所需字段
            $return = [];
            $seckillModule = new SeckillModule();
            foreach ($datas[0]['data'] as $key => $value) {
                $product = ProductService::getDetail($value['product_id']);
                $datas[0]['data'][$key]['product'] = $product;
                $sku = (new SeckillSkuService())->getListBySeckillID($value['id']);

                //获取商品原价或sku原价
                $skuService = new ProductSkuService();
                foreach ($sku as $k => $v) {
                    if ($v['sku_id']) {
                        //有sku
                        $row = $skuService->getRowById($v['sku_id']);
                        $v['seckill_oprice'] = $row['price'];
                    } else {
                        //无规格
                        $v['seckill_oprice'] = $product['price'];
                    }
                    $sku[$k] = $v;
                }
                $datas[0]['data'][$key]['sku'] = $sku;

                $datas[0]['data'][$key]['url'] = imgUrl() . 'shop' . $this->staticDatas['url'][13] . '/' . $value['id'];

                //返回服务器当前时间
                $datas[0]['data'][$key]['now_at'] = date('Y-m-d H:i:s');

                // 总库存
                $datas[0]['data'][$key]['seckill_stock'] = array_sum(array_column($sku, 'seckill_stock'));
                // 价格信息
                $datas[0]['data'][$key]['seckill_oprice'] = $sku[0]['seckill_oprice'];
                $datas[0]['data'][$key]['seckill_price'] = $sku[0]['seckill_price'];
                $datas[0]['data'][$key]['seckill_discount_price'] = sprintf('%.2f', $sku[0]['seckill_oprice'] - $sku[0]['seckill_price']);
                $priceArr = explode('.', $sku[0]['seckill_price']);
                $datas[0]['data'][$key]['seckill_price_dollar'] = $priceArr[0];
                $datas[0]['data'][$key]['seckill_price_cent'] = $priceArr[1];
                // 获取总销量
                $datas[0]['data'][$key]['seckill_sold_num'] = $seckillModule->getSoldNumBySeckillId($value['id']);
            }
            return $datas;
        }, (new SeckillService())->listWithPage($where, '', '', $this->pp));
    }


    public function getWeChatStaff()
    {
        return ['id'=> 'w','type'=> 15,'page_title'=>'微信客服'];
    }

    public function getShareEvent()
    {
        $where = $this->search();
        $where['wid'] = $this->input['wid'];
        $where['status'] = 0;
        $where['type']   = 0;
        //add by jonzhang 剔除过期的享立减活动
        $where['endTime']=['>',time()];
        $data = (new ShareEventService)->list($where,$orderBy = '', $order = '',$pageSize=6);

        if($data[0]['data']){
            foreach ($data[0]['data'] as &$datum){
                $datum['created_time'] = date('Y-m-d H:i:s',$datum['created_time']);
                //获取商品图
                $productData = ProductService::getDetail($datum['product_id']);
                $datum['img'] = $productData['img'];
            }
        }

        return $data;
    }

    public function getLiEvent()
    {
        $where = $this->search();
        $where['wid'] = $this->input['wid'];
        $where['status'] = 0;
        $where['type']   = 0;
        //add 筛选未过期活动
        $where['end_time']   = ['>',time()];
        $data = (new LiEventService())->list($where,'','',6);

        if($data[0]['data']){
            foreach ($data[0]['data'] as &$datum){
                $datum['created_time'] = date('Y-m-d H:i:s',$datum['created_time']);
                //获取商品图
                $productData = ProductService::getDetail($datum['product_id']);
                $datum['img'] = $productData['img'];
            }
        }
        return $data;
    }

    /**
     * 未出生的功能
     *
     * @return array 空数组
     */
    public function getunborn()
    {
        return [];
    }

    /**
     * 获取拼团商品
     * @return [type] [description]
     */
    public function getGroupList()
    {
        $ruleModule = new GroupsRuleModule();
        $wid = $this->input['wid'];
        $data = $ruleModule->getGroupsRuleList($wid,4);
        if (isset($data[0]['data']) && $data[0]['data']) {
            foreach ($data[0]['data'] as $key => &$value) {
                $value['page_title'] = $value['title'];
            }
        }

        return $data;
    }

    /**
     * 获取调查留言活动列表
     * @return array
     * @author 许立 2018年07月05日
     * @update 许立 2018年07月11日 只返回未失效且进行中的活动
     * @update 许立 2018年08月22日 区分活动类型 0:在线报名,1:预约,2:投票
     * @update 许立 2018年08月23日 修复活动按分类过滤
     */
    public function getResearch()
    {
        $where = $this->search();
        $where['wid'] = $this->input['wid'];
        // 未失效且进行中的活动
        $now = date('Y-m-d H:i:s');
        $where['start_at'] = ['<=', $now];
        $where['end_at'] = ['>', $now];
        $where['invalidate_at'] = '0000-00-00 00:00:00';

        // 区分活动类型
        $type = app('request')->input('activity_type');
        $type === null || $where['type'] = (int)$type;

        return call_user_func(function($datas) {
            return $datas;
        }, (new ResearchService())->listWithPage($where, '', '', $this->pp));
    }

    /**
     * 获取卡密活动列表
     * @return array
     * @author 许立 2018年08月06日
     * @update 许立 2018年08月08日 获取卡密库存
     */
    public function getCam()
    {
        $where = $this->search();
        // 未失效且进行中的活动
        $now = date('Y-m-d H:i:s');
        $where['begin_time'] = ['<=', $now];
        $where['end_time'] = ['>', $now];
        $where['invalid'] = 0;
        return call_user_func(function($data) {
            $camListService = new CamListService();
            foreach ($data[0]['data'] as $k => $v) {
                // 获取卡密库存
                $data[0]['data'][$k]['stock'] = $camListService->countStock($v['id'])['leftTotal'];
            }
            return $data;
        }, (new CamActivityService())->getAllList($this->input['wid'], $where));
    }

    /**
     * 获取签到
     * @return array
     * @author 何书哲 2018年8月27日
     */
    public function getSign()
    {
        $data = ['page_title'=>'签到', 'url'=>config('app.url').'shop/point/sign/'.$this->input['wid']];
        $res = (new SignService())->getRow($this->input['wid']);
        if ($res['errCode'] == 0) {
            $res['data']['template_data'] = json_decode($res['data']['template_data'], true);
            $data = array_merge($data, $res['data']);
        }
        return $data;
    }

}
