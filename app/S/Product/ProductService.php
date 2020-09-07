<?php

namespace App\S\Product;

use App\Jobs\importCardProduct;
use App\Jobs\ProcessImportPictureUrl;
use App\Jobs\UpdateProductImagesJob;
use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductImg as ImgRedis;
use App\Lib\Redis\ProductMsg as MsgRedis;
use App\Lib\Redis\ProductPropsToValues as PropsRedis;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Lib\Redis\ProductWholesale as WholesaleRedis;
use App\Model\Product;
use App\Model\Product as ProductModel;
use App\Model\ProductImg;
use App\Model\ProductMsg;
use App\Model\ProductProps;
use App\Model\ProductPropsToValues;
use App\Model\ProductPropValues;
use App\Model\ProductSku;
use App\Model\ProductWholesale;
use App\Module\GroupsRuleModule;
use App\Module\ProductModule;
use App\S\Cam\CamActivityService;
use App\S\Foundation\FileService;
use App\S\Lift\ReceptionService;
use App\S\Market\SeckillService;
use App\S\S;
use App\S\ShareEvent\ShareEventService;
use App\Services\FreightService;
use App\Services\MemberCardRecordService;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use MallModule as ProductStoreService;
use MicPage;
use PHPExcel;
use Redirect;
use RedisPagination;
use Redisx;
use Session;
use Validator;


class ProductService extends S
{

    public function __construct()
    {
        parent::__construct('Product');
    }

    /**
     * 获取非分页列表
     * @return array
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ProductRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 发布编辑商品
     * @param int $params 商品数据数组
     * @param int $wid 店铺id
     * @return int 商品id
     * @author 许立 2018年7月2日
     * @update 梅杰 20180717 参与营销活动的商品不能下架
     * @update 许立 2018年07月18日 判断是否是商家自己编辑
     * @update 许立 2018年07月18日 编辑商品才判断是否是商家自己编辑
	 * @update 吴晓平 2018年07月18日，增加商品可以选择配送方式（可无需物流）
     * @update 许立 2018年08月06日 编辑商品新增卡密id 不能编辑是否是卡密类型
     * @update 许立 2018年08月16日 面议字段没传报错修复
     * @update 吴晓平 2018年08月22日 当设置为卡密商品时自动分到卡密分组中，当普通商品未选择分组时自到分到未分组
     * @update 许立 2018年09月20日 享立减活动去掉分销字段限制
     * @update 何书哲 2018年11月07日 价格改为100万
     * @update 许立 2019年01月25日 价格改为1000万
     * @update 许立 2019年01月27日 编辑商品且修改过规格值 则删除老sku 新增sku
     * @update 何书哲 2019年05月27日 商品名限制75个字
     */
    public function setProduct($params, $wid)
    {
        // 验证规则
        if(empty($params)){
            error('请填写商品信息！');
        }
        $product_id = isset($params['id']) && $params['id'] ? $params['id'] : 0;

        // 商品是否存在
        if ($product_id) {
            $product = $this->getDetail($product_id);
            if (empty($product)) {
                error('商品不存在');
            }
            // 判断是否是商家自己编辑
            if ($product['wid'] != $wid) {
                error('无权操作别人的商品');
            }

            // 编辑商品 不能改变是否是卡密类型
            if (!empty($params['cam_id']) && $params['cam_id'] != $product['cam_id']) {
                error('不能编辑是否是卡密类型');
            }
        }

        $rules = array(
            'title' => 'required|between:1,75',
            'price'  => 'required|numeric',
            'cost_price' =>'numeric',
        );
        $messages = array(
            'title.required'          => '请填写商品名称',
            'title.between'           => '商品名称最多填写:max个字符',
            'price.required'          => '请填写商品价格',
            'price.numeric'           => '商品价格必须是数字',
            'cost_price.numeric'           => '成本价必须是数字'
        );
        $validator = Validator::make($params, $rules, $messages);
        if ( $validator->fails() ) {
            error($validator->errors()->first());
        }

        // 验证规格价格
        if ($params['sku']['stocks']) {
            foreach ($params['sku']['stocks'] as $v) {
                //价格必填
                if (empty($v['price'])) {
                    error('规格价格不能为空');
                }
            }
        }

        //处理商品信息
        $insert_data = [];
        $insert_data['wid'] = $wid ;
        $insert_data['category_id'] = isset($params['category_id']) ? $params['category_id'] : 0;
        $insert_data['buy_way'] = isset($params['buy_way']) ? $params['buy_way'] : 1;
        if($insert_data['buy_way'] == 2){
            $insert_data['out_buy_link'] = isset($params['out_buy_link']) ? $params['out_buy_link'] : '';
            if(!$insert_data['out_buy_link']){
                error('请填写外部购买链接！');
            }
        }
        // 前端会传多余逗号处理。。。
        $insert_data['group_id'] = isset($params['group_id']) ? trim($params['group_id'], ',') : '';
        /**add by 吴晓平 2018年08月22日 当设置为卡密商品时自动分到卡密分组中，当普通商品未选择分组时自到分到未分组**/
        /*$groupService = new ProductGroupService();
        $groupData = $groupService->getGroupIdByDefault($wid);
        if (isset($params['cam_id']) && $params['cam_id']) {
            if (!empty($groupData)) {
                $insert_data['group_id'] = $groupData[1]['id'];
            }
        }
        if (empty($insert_data['group_id'])) {
            if (!empty($groupData)) {
                $insert_data['group_id'] = $groupData[0]['id'];
            }
        }*/// end

        $insert_data['type'] = isset($params['type']) ? $params['type'] : 1;

        // 商品预售坑
        $insert_data['presell_flag'] = isset($params['presell_flag']) ? $params['presell_flag'] : 0;
        if($insert_data['presell_flag']){ # 如果是预售商品 需要填写预售类型
            $presell_delivery_type = isset($params['presell_delivery_type']) ? $params['presell_delivery_type'] : 0;
            $insert_data['presell_delivery_type'] = $presell_delivery_type;
            if($presell_delivery_type == 1){
                $insert_data['presell_delivery_time'] = isset($params['presell_delivery_time']) ? $params['presell_delivery_time'] : '';
                if(!$insert_data['presell_delivery_time']){
                    error('请选择预售发货时间！');
                }
            }else if($presell_delivery_type == 2){
                $insert_data['presell_delivery_payafter'] = isset($params['presell_delivery_payafter']) ? $params['presell_delivery_payafter'] : 0;
                if(!$insert_data['presell_delivery_payafter']){
                    error('请选择下单几天后发货！');
                }
            }else{
                error('请选择预售发货类型！');
            }
        }

        // 库存价格等重要信息
        $insert_data['stock'] = isset($params['stock']) && $params['stock'] ? $params['stock'] : 0;
        $insert_data['stock_show'] = (isset($params['stock_show']) && $params['stock_show'] == true) ? $params['stock_show'] : 0;
        $insert_data['goods_no'] = isset($params['goods_no']) ? $params['goods_no'] : '';
        $insert_data['title'] = isset($params['title']) ? $params['title'] : '';
        $insert_data['price'] = isset($params['price']) ? $params['price'] : 0.00;
        $insert_data['oprice'] = isset($params['oprice']) ? $params['oprice'] : 0.00;
        $insert_data['cost_price'] = isset($params['cost_price']) ? $params['cost_price'] : 0.00;
        $insert_data['weight'] = $params['weight'] ?? 0.000;

        // 商品价格最大10万 且最多两位小数 @产品Rudy @Herry
        //update 何书哲 2018年11月07日 价格改为100万
        if ($insert_data['price'] >= 10000000) {
            error('价格不能超过1000万');
        } elseif ($insert_data['oprice'] >= 10000000) {
            error('原价不能超过1000万');
        } elseif ($insert_data['cost_price'] >= 10000000) {
            error('成本价不能超过1000万');
        }
        $insert_data['price'] = sprintf('%.2f', $insert_data['price']);
        $insert_data['oprice'] = sprintf('%.2f', $insert_data['oprice']);
        $insert_data['cost_price'] = sprintf('%.2f', $insert_data['cost_price']);
        $insert_data['is_logistics'] = $params['is_logistics'] ?? 1; //默认物流

        //update by 吴晓平 2018年07月17日  增加了当商品选择物流时进行运费判断
        if ($insert_data['is_logistics'] == 1) {
	        #----------------------商品运费坑-----
	        $insert_data['freight_type'] = isset($params['freight_type']) ? $params['freight_type'] : 1; #运费类型 1 统一运费 2 运费模板
	        if($insert_data['freight_type'] == 1){
	            $insert_data['freight_price'] = isset($params['freight_price']) && $params['freight_price'] ? $params['freight_price'] : 0.00;
	            //选择统一运费 运费模板id重置为0 201711299
	            $insert_data['freight_id'] = 0;
	        }
	        if($insert_data['freight_type'] == 2){
	            $insert_data['freight_id'] = isset($params['freight_id']) && $params['freight_id'] ? $params['freight_id'] : 0;
	            if(!$insert_data['freight_id']){
	                error('请选择运费模板！');
	            }
	        }
        }else {
        	$insert_data['freight_id'] = 0;
        }

        #--------------------商品 购买限制 坑-----

        // 购买权限
        $insert_data['buy_permissions_flag'] = isset($params['buy_permissions_flag']) ? $params['buy_permissions_flag'] : 0;
        if($insert_data['buy_permissions_flag'] == 1){
            $insert_data['buy_permissions_level_id'] = isset($params['buy_permissions_level_id']) ? $params['buy_permissions_level_id'] : '';
            $insert_data['buy_permissions_level_id'] = $insert_data['buy_permissions_level_id'] ? implode(',', $insert_data['buy_permissions_level_id']) : '';
            if(!$insert_data['buy_permissions_level_id']){
                error('请选择指定的会员级别');
            }
        }

        // 留言
        $insert_data['note_flag'] = 0;
        $product_msgs = isset($params['noteList']) ? (is_array($params['noteList'])?$params['noteList']:json_decode($params['noteList'],true)) : array();
        if($product_msgs){
            $insert_data['note_flag'] = 1;
        }

        // 是否开启无需物流
        $insert_data['no_logistics'] = $params['no_logistics'] ?? 0;

        // 开售时间
        $insert_data['sale_time_flag'] = isset($params['sale_time_flag']) ? $params['sale_time_flag'] : 1;
        if($insert_data['sale_time_flag'] == 2){
            $insert_data['sale_time'] = isset($params['sale_time']) ? $params['sale_time'] : '';
            if(!$insert_data['sale_time']){
                error('请填写开售时间！');
            }
        }

        $insert_data['is_discount'] =$params['is_discount'] ??  0;
        $insert_data['introduce'] = isset($params['introduce']) ? $params['introduce'] : '';
        // 是否默认商品
        $insert_data['is_default'] = $params['is_default'] ?? 0;

        // 商品是否正在参与秒杀活动 或者 拼团活动 或者 享立减活动中
        $is_in_activity = 0;
        if ((new SeckillService())->isProductSeckilling([$product_id])
            || !(new GroupsRuleModule())->isDelProduct([$product_id],'edit')
            || (new ShareEventService())->isShareProduct([$product_id])) {
            $is_in_activity = 1;
        }

        if ($is_in_activity) {
            $activity_sku_flag = 0;
            if (!empty($params['sku']['props']) && !empty($params['sku']['stocks'])) {
                $activity_sku_flag = 1;
            }
        } else {
            $insert_data['sku_flag'] = 0;
            if (!empty($params['sku']['props']) && !empty($params['sku']['stocks'])) {
                $insert_data['sku_flag'] = 1;

                // 许立 2018年7月2日 商品价格取规格最低价
                $insert_data['price'] = min(array_column($params['sku']['stocks'], 'price'));
            }
        }

        // 商品图片
        $product_imgs = array();
        $imgs = isset($params['img']) ? $params['img'] : array();
        if(empty($imgs)){
            error('请上传商品图片');
        }
        // 默认取第一张 图片放入商品 表中 其他图片放入 商品图片表中
        $insert_data['img'] = $imgs[0];
        // 图片存入 商品图片 表
        if(count($imgs)){
            $product_imgs = $imgs;
        }

        // 商品状态 默认上架
        $insert_data['status'] = $params['status'] ?? 1;

        if ($is_in_activity && $insert_data['status'] == 0) {
            error('该商品正在参与拼团或者秒杀或享立减等营销活动，无法下架');
        }

        //设置分销模板
        if (isset($params['is_distribution']) && $params['is_distribution'] == 1){
            $insert_data['distribute_template_id'] = $params['distribute_template_id'];
            $insert_data['is_distribution'] = 1;
        }else{
            $insert_data['is_distribution'] = 0;
            $insert_data['distribute_template_id'] = 0;
        }

        // 商品发布第三步 详情模板编辑 保存模板 简介和模板信息
        if (!empty($params['content'])) {
            $insert_data['content'] = $params['content']['editors'] ?? '';
            $insert_data['templete_use_id'] = $params['content']['templateId'] ?? '';
            $insert_data['summary'] = $params['content']['productIntro'] ?? '';
        }

        // 吴晓平 2017年08月15日 把分享设置字段添加到数据库
        $insert_data['share_title'] = $params['share_title'] ?? '';
        $insert_data['share_desc']  = $params['share_desc'] ?? '';
        $insert_data['share_img']   = $params['share_img'] ?? '';

        // 吴晓平 2017年09月15日 添加核销
        $insert_data['is_hexiao']    = $params['is_hexiao'] ?? 0;
        $insert_data['hexiao_start'] = $params['hexiao_start'] ?? '';
        $insert_data['hexiao_end']   = $params['hexiao_end'] ?? '';

        // 吴晓平 2018年06月01日 把以前的核销功能对接自提（开启核销相当于用户可自提）
        /*if($insert_data['is_hexiao'] == 1 && (empty($insert_data['hexiao_start']) || empty($insert_data['hexiao_end']))){
            error('请设置核销时间');
        }*/
        // add by 吴晓平 2018年09月13日 限制添加自提商品（未添加自提点不允许创建自提商品）
        if ($insert_data['is_hexiao'] == 1) {
            $count = (new ReceptionService())->countList($wid);
            if (!$count) {
                error('还未设置自提点地址，暂不能添加自提商品，请稍后再试~');
            }
        }

        // 张国军 2017年11月23日 该商品是否可以使用积分 默认可以使用积分
        $insert_data['is_point']=$params['is_point']??1;

        // 需要重置重量的情况 1统一运费 2按件数运费模板
        $resetWeight = true;
        if ($insert_data['freight_id']) {
            $freight = (new FreightService())->getOne($insert_data['freight_id']);
            if (!empty($freight) && $freight['billing_type'] == 1) {
                $resetWeight = false;
            }
        }
        $resetWeight && $insert_data['weight'] = 0;

        // 销量可以编辑
        $insert_data['sold_num'] = $params['sold_num'] ?? 0;

        // 价格面议模块修改
        if (!$is_in_activity) {
            $insert_data['is_price_negotiable'] = $params['is_price_negotiable'] ?? 0;
            $insert_data['negotiable_type'] = $params['negotiable_type'] ?? 0;
            $insert_data['negotiable_value'] = $params['negotiable_value'] ?? 0;
        }

        // 是否开启批发设置
        $insert_data['wholesale_flag'] = $params['wholesale_flag'] ?? 0;
        if ($insert_data['wholesale_flag']) {
            $verify = ProductModule::verifyWholesaleData($params['wholesale_array']);
            $verify['code'] && error($verify['error']);
        }

        // 限购数量 0 表示不限购
        if (empty($params['quota'])) {
            // 不限购 则最小购买量跟总库存比较
            if (!empty($params['buy_min'])) {
                $params['buy_min'] = (int)$params['buy_min'];
                if ($params['buy_min'] > $params['stock']) {
                    error('最小购买量不能大于总库存');
                }
                $insert_data['buy_min'] = $params['buy_min'];
            }

            $insert_data['quota'] = 0;
        } else {
            $params['quota'] = (int)$params['quota'];
            if ($params['quota'] > $params['stock']) {
                error('限购数不能大于总库存');
            }
            $insert_data['quota'] = $params['quota'];

            // 最小购买量取值范围判断
            if (!empty($params['buy_min'])) {
                $params['buy_min'] = (int)$params['buy_min'];
                if ($params['buy_min'] > $params['quota']) {
                    error('最小购买量不能大于限购数');
                }
                $insert_data['buy_min'] = $params['buy_min'];
            }
        }

        $insert_data['content'] = ProductModule::delProductContentHost($insert_data['content']??'');

        // 卡密活动id
        $insert_data['cam_id'] = $params['cam_id'] ?? 0;

        //添加或修改商品主表
        //保存商品主表信息 新增只操作数据库 修改操作数据库和redis
        $edit_flag = false;
        if ($product_id) {
            // 编辑
            $edit_flag = true;
            $this->update($product_id, $insert_data);
        } else {
            // 添加
            $product_id = ProductModel::insertGetId($insert_data);
        }

        // 更新关联表
        if($product_id){
            // 规格模块 秒杀中的商品编辑时 只有修改规格（不增加规格数）前提下 才能编辑规格
            if ($is_in_activity) {
                if ($edit_flag && $product['sku_flag'] == 1 && $activity_sku_flag == 1) {
                    if (!empty($params['sku']['stocks'][0]['id'])) {
                        // 如果没有增加或减少规格(只是修改价格库存之类信息) 则直接更新对应sku
                        // 属性图片也需要修改
                        (new ProductSkuService())->updateSku($product_id, $params['sku'], $resetWeight);
                    }
                }
            } else {
                if($insert_data['sku_flag']){
                    // 有规格
                    if($edit_flag) {
                        if (!empty($params['sku']['stocks'][0]['id']) && empty($params['is_edit_prop_value'])) {
                            // 如果没有增加或减少规格(只是修改价格库存之类信息) 则直接更新对应sku
                            // 属性图片也需要修改
                            (new ProductSkuService())->updateSku($product_id, $params['sku'], $resetWeight);
                        } else {
                            // 增减规格情况
                            $this->deletePropsByProductId($product_id);
                            $this->addProps($product_id, $params['sku'], $wid);
                        }
                    } else {
                        $this->addProps($product_id, $params['sku'], $wid);
                    }
                } else {
                    // 无规格
                    if($edit_flag){
                        $this->deletePropsByProductId($product_id);
                    }
                }
            }

            // 添加到 商品图片表
            if(!empty($product_imgs)){
                if($edit_flag){
                    // 编辑图片 先删除之前图片
                    $this->deleteImgsByProductId($product_id);
                }
                $this->batchAddImg($product_imgs,$product_id,$wid,$edit_flag);
            }

            // 添加到 留言表
            if(!empty($product_msgs)){
                if($edit_flag){
                    // 先删除之前留言
                    $this->deleteMsgsByProductId($product_id);
                }
                $this->batchAddMsg($product_msgs,$product_id,$edit_flag);
            }

            // 批发价设置
            if (!empty($params['wholesale_flag'])) {
                // 编辑批发设置 先删除之前的设置
                $edit_flag && $this->deleteWholesaleByProductId($product_id);
                $this->batchAddWholesale($params['wholesale_array'], $product_id, $edit_flag);
            }
        }else{
            error('商品添加失败');
        }
        return $product_id;
    }

    /**
     * 查询商品详情
     * @param array $params 查询参数
     * @param int $wid 店铺id
     * @return array|json
     * @author 许立 2017年07月06日
     * @update 许立 2018年08月09日 获取卡密活动名称
     */
    public function getProduct($params = array(), $wid = 0)
    {
        if(empty($params) || !$wid){
            error('参数错误');
        }
        $product_id = isset($params['id']) && $params['id'] ? $params['id']: 0; #获取商品id
        if(!$product_id){
            error('商品不存在');
        }

        #1. 查询商品表
        $product = $this->getDetail($product_id);

        if(!$product){
            error('您所查询的商品不存在');
        }
        //对商品模板数据进行处理 add by jonzhang 2017-07-24
        if(!empty($product['content'])) {
            $product['content'] = ProductStoreService::processTemplateData($wid, $product['content'], 1);
        }
        unset($product['img']); #删除多余的 商品图片
        $return_data = $product;

        # 4. 查询对应的 规格表
        $return_data['sku'] = [];
        if ($product['sku_flag'] == 1) {
            $return_data['sku'] = (new ProductPropsToValuesService())->getSkuList($product_id);
        }

        # 5. 查询对应的 留言表
        $msgService = new ProductMsgService();
        $return_data['noteList'] = $msgService->getListByProduct($product_id);

        # 6. 查询对应的 图片表
        $imgService = new ProductImgService();
        $imgs = $imgService->getListByProduct($product_id);
        $return_data['imgs'] = [];
        foreach($imgs as $v){
            $return_data['imgs'][] = $v['img'];
        }

        //获取批发设置
        $return_data['wholesale_array'] = (new ProductWholesaleService())->getListByProduct($product_id);

        // 获取卡密活动名称
        $return_data['cam_title'] = '';
        if ($product['cam_id']) {
            $return_data['cam_title'] = (new CamActivityService())->getRowById($product['cam_id'])['title'] ?? '';
        }

        return $return_data;
    }

    /*
     * @todo: 单个商品删除接口 暂时关闭此接口
     */
    public function productDel($params = array(),$wid = 0){
        if(!isset($params['id']) || !$params['id'] || !is_numeric($params['id']) || !$wid){
            error('请选择商品！');
        }

        $productModel = new Product();
        $productRedis = new ProductRedis();
        //删除商品
        $productModel->where('id', $params['id'])->delete();
        $productRedis->delete($params['id']);

        //删除规格
        $this->deletePropsByProductId($params['id']);

        //删除图片
        $this->deleteImgsByProductId($params['id']);

        //删除留言
        $this->deleteMsgsByProductId($params['id']);

        return true;
    }

    /*
     * 商品批量删除接口
     */
    public function batchDel($params = array()){
        if(!isset($params['ids']) || !is_array($params['ids'])){
            error('请选择商品！');
        }

        $productModel = new Product();
        $productRedis = new ProductRedis();
        foreach ($params['ids'] as $id) {
            //状态删除 20180116 todo 是否需要删除对应的规格图片等信息？
            $this->update($id, ['status' => -1]);
            //删除商品
            /*$productModel->where('id', $id)->delete();
            $productRedis->delete($id);

            //删除规格
            $this->deletePropsByProductId($id);

            //删除图片
            $this->deleteImgsByProductId($id);

            //删除留言
            $this->deleteMsgsByProductId($id);*/
        }
        return true;
    }

    /*
     * @todo: 商品批量上下架接口
     */
    public function productOnOffSale($params = array(),$wid = ''){
        if(!isset($params['ids']) || !is_array($params['ids']) || !$wid || !isset($params['status']) || !is_numeric($params['status'])){
            error('请选择商品！');
        }
        if(!in_array($params['status'],[0,1])){
            error('参数非法，操作失败！');
        }
        $updateArr = ['status' => $params['status']];

        //更新数据库
        ProductModel::whereIn('id', $params['ids'])->update($updateArr);
        //更新redis
        $productRedis = new ProductRedis();
        foreach ($params['ids'] as $id) {
            $updateArr['id'] = $id;
            $productRedis->updateRow($updateArr);
        }

        return true;
    }

    /*
     * @todo: 多个商品 批量修改 分组
     */
    public function productModGroup($params = array(),$wid = ''){
        if(!isset($params['ids']) || !is_array($params['ids']) || !$wid){
            error('请选择商品！');
        }
        if(!isset($params['group_ids']) || !is_array($params['group_ids'])){
            error('请选择分组！');
        }
        $updateArr = ['group_id' => implode(',',$params['group_ids'])];

        //更新数据库
        ProductModel::whereIn('id', $params['ids'])->update($updateArr);
        //更新redis
        $productRedis = new ProductRedis();
        foreach ($params['ids'] as $id) {
            $updateArr['id'] = $id;
            $productRedis->updateRow($updateArr);
        }

        return true;
    }

    /*
     * @todo: 多个商品 批量修改 是否参与 会员折扣
     */
    public function productModDiscount($params = array(),$wid = ''){
        if(!isset($params['ids']) || !is_array($params['ids']) || !$wid){
            error('请选择商品！');
        }
        if(!isset($params['is_discount']) || !is_numeric($params['is_discount']) || !in_array($params['is_discount'],[0,1])){
            error('请选择是否参与会员折扣！');
        }
        $updateArr = ['is_discount' => $params['is_discount']];

        //更新数据库
        ProductModel::whereIn('id', $params['ids'])->update($updateArr);
        //更新redis
        $productRedis = new ProductRedis();
        foreach ($params['ids'] as $id) {
            $updateArr['id'] = $id;
            $productRedis->updateRow($updateArr);
        }

        return true;
    }

    /**
     * 设置运费模板
     * @param array $params
     * @param string $wid
     * @return bool
     */
    public function setFreight($params = array(),$wid = ''){
        if(!isset($params['ids']) || !is_array($params['ids']) || !$wid){
            error('请选择商品！');
        }

        $updateArr = [
            'freight_type' => $params['freight_type'] ?? 1,
            'freight_price' => $params['freight_price'] ?? 0.00,
            'freight_id' => $params['freight_id'] ?? 0
        ];

        //更新数据库
        ProductModel::whereIn('id', $params['ids'])->update($updateArr);
        //更新redis
        $productRedis = new ProductRedis();
        foreach ($params['ids'] as $id) {
            $updateArr['id'] = $id;
            $productRedis->updateRow($updateArr);
        }

        return true;
    }

    /*
     * @todo: 多个商品 批量修改 模板
     */
    public function productModTpl($params = array(),$wid = 0){
        if(!isset($params['ids']) || !is_array($params['ids']) || !$wid){
            error('请选择商品！');
        }
        if(!isset($params['tpl_id']) || !is_numeric($params['tpl_id']) || !$params['tpl_id']){
            error('请选择要使用的模板！');
        }
        #先删除数据库里面的数据
        $where['wid'] = $wid;
        $where['id'] = array('in',$params['ids']);
        $tpl_id = $params['tpl_id'];
        $flag = Product::wheres($where)->update(array('templete_use_id' => $tpl_id));
        if($flag){
            #从redis 里面删除数据
            $redisKey = 'product:wid:' . $wid; #从redis中取出 商品为店铺wid的 所有商品
            foreach($params['ids'] as $id){
                Redisx::HSET($redisKey . '|id:' . $id, 'templete_use_id',$tpl_id);
            }
            return true;
        }
        return false;
    }

    /*
     * @todo: 添加淘宝商品
     *  $params['fields'] = ['title','price','stock','introduce','img','goods_no'];
     *  $params['data'] = ['title','price','stock','intro','img','goods_no'];
     */
    public function addTaobao($params, $wid, $uniqid , $categoryId = 0, $groupID = '', $type = ''){
        if(empty($params) || !$wid){
            error('您上传的商品为空或者上传时出现错误！');
        }
        if(!isset($params['fields']) || !is_array($params['fields']) || empty($params['fields'])){
            error('上传的字段为空');
        }
        if(!isset($params['data']) || !is_array($params['data']) || empty($params['data'])){
            error('上传的数据为空');
        }
        foreach($params['data'] as $data){
            $temp_data = array();
            $imgs = array();
            foreach($params['fields'] as $key => $field){
                if($field == 'img'){
                    //获取淘宝远程图片地址
                    //$imgs = $this->getTaobaoimg($data[$key]); #正则 批处理淘宝图片
                    //csv或excel文件导入 图片设置为空
                    $imgs = [];
                    if (empty($type)) {
                        $temp_data['img'] = '';
                        continue;
                    }

                    //图片处理 解析淘宝图片地址 对应之前解压的tbi图片
                    $pics   = explode(";",trim($data[$key],';'));
                    $pics   = array_unique($pics);
                    foreach ($pics as $k => $v){
                        //正则匹配图片
                        $pattern = '';
                        if ($type == 'taobao') {
                            $pattern = '/^([\w]{32}):(.*)/i';
                        } elseif ($type == 'ali') {
                            //$pattern = '/((http|https):\/\/)?[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?/i';
                            $pattern = '/^(\w+):(.*)/i';
                        }
                        $pic_tmp_name = preg_replace($pattern, '$1', $v);

                        //保存的图片相对路径
                        $ext = '';
                        if ($type == 'taobao') {
                            $ext = '.tbi';
                        } elseif ($type == 'ali') {
                            $ext = '.ali';
                        }
                        $imgPath = 'hsshop/import/' . $uniqid . '/' .md5(strtolower($pic_tmp_name).$ext).'.jpg';
                        if ($k == 0) {
                            $temp_data[$field] = $imgPath;
                        }

                        //@todo 由于打包的淘宝和阿里巴巴数据包中只有图片字段的图片文件 没有商品详情里的图片对应的图片文件
                        //暂时不处理详情里的图片 原样保存（图片是外链）
                        //获取图片远程url 后续替换详情url为本地使用到
                        //$arr = explode('|', $v);

                        //图片坑 商品图片和商品详情图片路径不同 转化
//                        $url = $arr[1];
//                        if ($type == 'taobao') {
//                            $url = str_replace('bao/uploaded', 'imgextra', $arr[1]);
//                        }

                        //加入图片数组
                        $imgs[] = $imgPath;
                    }
                }else{
                    $temp_data[$field] = $data[$key];
                }
            }

            //默认值
            $temp_data['category_id'] = $categoryId;
            if ($groupID) {
                $temp_data['group_id'] = $groupID;
            }
            $temp_data['status'] =  2 ;
            $temp_data['wid'] =  $wid ;

            //处理详情
            $temp_data['introduce'] = preg_replace('/(?<=href=)([^>]*)(?=>)/i', 'javascript:;', $temp_data['introduce']);
//            if ($type == 'taobao') {
//                $temp_data['introduce'] = preg_replace('/(?<=href=)([^>]*)(?=>)/i', 'javascript:;', $temp_data['introduce']);
//                $temp_data['introduce'] = $this->imgToLocal($temp_data['introduce'], $imgs);
//            } elseif ($type == 'ali') {
//                $temp_data['introduce'] = '';
//            }

            //默认导入的商品外链购买
            //$temp_data['buy_way'] = 2;

            //商品详情新处理方式
            $content = [];
            $content[] = [
                'showRight' => true,
                'cardRight' => 17,
                'type'      => 'shop_detail',
                'editing'   => 'editing',
                'content'   => preg_replace('/height=\"(.+?)\"/i', 'height="auto"', $temp_data['introduce'])  //Herry 去掉图片高度属性 改成自适应
            ];

            //中文编码问题 Herry 使用JSON_UNESCAPED_UNICODE
            $temp_data['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
            $temp_data['introduce'] = '';

            //20170915 16:19 Herry 阿里巴巴价格字段更换
            if ($type == 'ali') {
                $temp_data['price'] = last(explode(':', $temp_data['price']));
            }

            //商品入库 入redis 该方法需要等service add函数返回主键才能使用
            $product_id = ProductModel::insertGetId($temp_data);
            //商品图片表
            if (!empty($imgs)) {
                //循环入库
                foreach($imgs as $img){
                    $imgData = [
                        'wid'        => $wid,
                        'product_id' => $product_id,
                        'img'        => $img
                    ];
                    //逐条入库
                    ProductImg::insertGetId($imgData);
                }
            }
            unset($temp_data);
            unset($imgs);
        }
        return true;
    }

    /*
     * @todo: 正则获取产品图片
     */
    public function getTaobaoimg($string = ''){
        $parten = '/((http[s]{0,1}|ftp):\/\/[a-zA-Z0-9\\.\\-]+\\.([a-zA-Z]{2,4})(:\\d+)?(\/[a-zA-Z0-9\\.\\-~!@#$%^&*+?:_\/=<>]*)?)|(www.[a-zA-Z0-9\\.\\-]+\\.([a-zA-Z]{2,4})(:\\d+)?(\/[a-zA-Z0-9\\.\\-~!@#$%^&*+?:_\/=<>]*)?)/i';
        preg_match_all($parten,$string,$matches);
        return $matches[0];
    }

    /*
     * @todo: 导入商品查询
     */
    public function getTaoBaoGoods($params = array(),$wid = 0){
        if(!$wid){
            error('来源错误！');
        }
        $perPage = config('database')['perPage'];  # 获取每页条数
        $fields = ['id', 'wid', 'group_id', 'img', 'stock', 'sold_num', 'goods_no', 'title', 'price', 'sort', 'status', 'uv_num', 'pv_num', 'is_distribution', 'created_at', 'updated_at'];
        $where['wid'] = $wid;
        $where['status'] = 2;
        #-------------------3 商品标题筛选 -----------------------------
        $title = isset($params['title']) && trim($params['title']) ? trim($params['title']) : '';
        if ($title) {
            $where['title'] = array('like', "%{$params['title']}%");
        }
        $query = Product::select($fields)->wheres($where);
        #-------------------4 排序 -----------------------------
        # price 价格 stock 库存 sold_num 销量 sort 序号 created_at 创建时间
        if (isset($params['orderby']) && isset($params['order']) && $params['orderby'] && $params['order']) {
            $query = $query->orderBy($params['orderby'], $params['order']);
        }
        $query = $query->paginate($perPage)->appends($params);
        $pageList = $query->links();
        $list = $query->toArray();

        return array('list'=>$list,'pageLinks'=>$pageList);
    }


    /*
     * @todo: 淘宝商品快速导入
     *        商品编辑页面使用
     */
    public function getTb($params = array(),$wid = 0){
        if(empty($params) || !$wid){
            error('来源错误1！');
        }
        # 抓取  名称  价格 图片
        #https%3a%2f%2fitem.taobao.com%2fitem.htm%3fspm%3da230r.1.14.59.nSTg3z%26id%3d530646995695%26ns%3d1%26abbucket%3d16%23detail
        #https%3a%2f%2fdetail.tmall.com%2fitem.htm%3fspm%3da221y.601495ac9f02c0ce1fb61e4f80f23a71.hunderd.3.q5tLME%26id%3d35109366716%26pvid%3d8f297d59-f1e1-4526-a53b-06db8155fcdd%26scm%3d1007.13040.32483.100200300000000%26sku_properties%3d5919063%3a6536025
        $string_url = isset($params['url']) ? urldecode($params['url']) : '';
        if(!$string_url){
            error('来源错误2');
        }
        $params = parse_url($string_url);
        parse_str($params['query'],$query);
        $id = isset($query['id']) && $query['id'] ? $query['id']: 0;
        if(!$id){
            error('您要导入的商品不存在');
        }
        $return_data = array();
        $url = "http://hws.m.taobao.com/cache/wdetail/5.0/?id=".$id;
        $content = file_get_contents($url);
        $content_ori = strip_tags($content);
        $content_arr = json_decode($content_ori,true);
        $detail = json_decode($content_arr['data']['apiStack']['0']['value'],true);
        $success_sym=$detail['ret']['0'];//成功则返回"SUCCESS::调用成功";
        if($success_sym =="SUCCESS::调用成功"){
            $return_data['title'] =$content_arr['data']['itemInfoModel']['title'];
            $price =  explode('-',$detail['data']['itemInfoModel']['priceUnits']['0']['price']);
            $return_data['price'] = $price[0];
            $return_data['oprice'] = $detail['data']['itemInfoModel']['priceUnits']['1']['price'];
            $return_data['imgs'] = $content_arr['data']['itemInfoModel']['picsPath'];
        }else{
            error('宝贝不存在');
        }
        return $return_data;
    }

    /**
     * 重构保存规格
     * @update 陈文豪  2018年08月31日 按销量排序价格上限100万
     * @update 许立 2018年10月15日 属性图片检查
     * @update 许立 2019年01月17日 价格上限100万
     * @update 许立 2019年01月25日 价格改为1000万
     */
    private function addProps($pid, $sku, $wid, $editFlag = false)
    {
        $now = date('Y-m-d H:i:s');
        //----------- 处理属性 开始 ---------------
        $propsRedis = new PropsRedis();
        if ($sku['props']) {
            foreach ($sku['props'] as $propSort => $p) {
                $propData = [];
                $propData['wid'] = $wid;
                $propData['pid'] = $pid;
                $propData['prop_id'] = $p['prop']['id'];
                $propData['prop_sort'] = $propSort;
                $propData['prop_is_img'] = $p['prop']['show_img'] ?? 0;
                foreach ($p['values'] as $valueSort => $value) {
                    // 判断属性图片
                    if ($propData['prop_is_img'] && !empty($value['img'])) {
                        $propData['value_img'] = $value['img'];
                    } else {
                        $propData['value_img'] = '';
                        $propData['prop_is_img'] = 0;
                    }

                    $propData['value_id'] = $value['id'];
                    $propData['value_sort'] = $valueSort ?? 0;
                    $propData['value_img'] = $value['img'] ?? '';

                    //入库
                    $newPropsID = ProductPropsToValues::insertGetId($propData);

                    //更新redis
                    if ($editFlag) {
                        $propData['id'] = $newPropsID;
                        $propData['created_at'] = $now;
                        $propData['updated_at'] = $now;
                        $propData['deleted_at'] = null;
                        $propsRedis->add($propData);
                    }
                }
            }
        }
        //----------- 处理属性 结束 ---------------

        //----------- 处理库存 开始 ---------------
        $skuRedis = new SkuRedis();
        if ($sku['stocks']) {
            foreach ($sku['stocks'] as $v) {
                //价格必填
                if (empty($v['price'])) {
                    error('规格价格不能为空');
                }

                $stockData = [];
                //拼接sku字段
                $stockData['sku_key'] = (!empty($v['k1_id']) && !empty($v['v1_id'])) ? ($v['k1_id'] . ':' . $v['v1_id']) : '';
                $stockData['sku_key'] .= (!empty($v['k2_id']) && !empty($v['v2_id'])) ? (';' . $v['k2_id'] . ':' . $v['v2_id']) : '';
                $stockData['sku_key'] .= (!empty($v['k3_id']) && !empty($v['v3_id'])) ? (';' . $v['k3_id'] . ':' . $v['v3_id']) : '';

                //其他字段
                $stockData['pid'] = $pid;
                // 价格上限100万
                $stockData['price'] = $v['price'] >= 10000000 ? 9999999.99 : $v['price'];
                $stockData['stock'] = $v['stock_num'];
                $stockData['code'] = $v['code'] ?? '';
                $stockData['weight'] = $v['weight'] ?? 0;
                $stockData['sold_num'] = $v['sold_num'] ?? 0; //销量可以修改 20171226
                $stockData['is_show'] = $v['is_show'] ?? 0;
                $stockData['is_show1'] = $v['is_show1'] ?? 0;
                $stockData['rowspan0'] = (empty($v['rowspan0']) || $v['rowspan0'] < 1) ? 1 : $v['rowspan0'];
                $stockData['rowspan1'] = (empty($v['rowspan1']) || $v['rowspan1'] < 1) ? 1 : $v['rowspan1'];

                //入库
                $newSkuID = ProductSku::insertGetId($stockData);

                //更新redis
                if ($editFlag) {
                    $stockData['id'] = $newSkuID;
                    $stockData['created_at'] = $now;
                    $stockData['updated_at'] = $now;
                    $stockData['deleted_at'] = null;
                    $skuRedis->add($stockData);
                }
            }
        }
        //----------- 处理库存 结束 ---------------
    }

    /**
     * 批量添加图片
     * @param array $imgs
     * @param int $product_id
     * @param int $wid
     * @return bool
     */
    public function batchAddImg($imgs = [],$product_id = 0 ,$wid = 0,$edit_flag = false){
        if(!$imgs || !$product_id || !$wid){
            error('图片上传时参数错误！');
        }

//        $imgArr = [];
//        $productImgService = new ProductImgService();
        $imgModel = new ProductImg();
        $imgRedis = new ImgRedis();
        foreach($imgs as $v){
            $imgData = [
                'wid'        => $wid,
                'product_id' => $product_id,
                'img'        => $v['img'] ?? $v,
                'status'     => 1,
                //'created_at' => date('Y-m-d H:i:s')
            ];

            //逐条入库
            $id = $imgModel->insertGetId($imgData);
            if ($edit_flag) {
                $imgData['id'] = $id;
                $imgData['created_at'] = date('Y-m-d H:i:s');
                $imgData['updated_at'] = date('Y-m-d H:i:s');
                $imgData['deleted_at'] = null;
                $imgRedis->add($imgData);
            }
        }

        return true;
    }

    /**
     * 批量添加留言
     * @param array $msgs
     * @param int $product_id
     * @param int $wid
     * @return bool
     */
    public function batchAddMsg($msgs = array(),$product_id = 0 ,$edit_flag = false){
        if(!$msgs || !$product_id){
            error('留言添加时参数错误！');
        }

//        $msgArr = [];
//        $productMsgService = new ProductMsgService();
        $msgModel = new ProductMsg();
        $msgRedis = new MsgRedis();
        foreach($msgs as $msg){
            $msgData = [
                'product_id' => $product_id,
                'title'      => $msg['title'],
                'type'       => $msg['type'],
                'multiple'   => intval($msg['multiple']),
                'required'   => intval($msg['required']),
            ];

            //逐条入库
            $id = $msgModel->insertGetId($msgData);

            if ($edit_flag) {
                $msgData['id'] = $id;
                $msgData['created_at'] = date('Y-m-d H:i:s');
                $msgData['updated_at'] = date('Y-m-d H:i:s');
                $msgData['deleted_at'] = null;
                $msgRedis->add($msgData);
            }
        }

        return true;
    }

    /**
     * 添加批发设置
     */
    public function batchAddWholesale($array, $product_id, $edit_flag = false)
    {
        $model = new ProductWholesale();
        $redis = new WholesaleRedis();
        foreach($array as $v){
            $data = [
                'product_id' => $product_id,
                'min'      => $v['min'],
                'max'       => $v['max'],
                'price'   => $v['price']
            ];

            //逐条入库
            $id = $model->insertGetId($data);

            if ($edit_flag) {
                $data['id'] = $id;
                $data['created_at'] = date('Y-m-d H:i:s');
                $redis->add($data);
            }
        }

        return true;
    }

    /**
     * 店铺创建成功后 默认新建两个默认商品分组
     * @param int $wid 店铺id
     * @return boolean
     * @update 吴晓平 2018年08月22日  添加两个默认分组 （未分组，卡密商品）
     */
    public function createDefaultGroups($wid)
    {
        //最新商品分组
        $data1[] = [
            'type'              => 'goods_group',
            'first_priority'    => 1,
            'second_priority'   => 0,
            'show_tag_title'    => 1,
            'groupTitle'        => '最新商品',
            'is_default'        => 1,
            'showRight'         => true,
            'cardRight'         => 10,
            'editing'           => 'editing',
            'listStyle'         => 3,
            'cardStyle'         => 1,
            'showSell'          => true,
            'btnStyle'          => 1,
            'goodName'          => false,
            'goodInfo'          => false,
            'priceShow'         => true,
            'nodate'            => true,
            'signInfo'          => '',
            'goods'             => [
                [
                    'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                    'name'          => '这里显示商品名称',
                    'info'          => '这里显示商品通知',
                    'price'         => '￥1',
                    'deleteBtn'     => true
                ],
                [
                    'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                    'name'          => '这里显示商品名称',
                    'info'          => '这里显示商品通知',
                    'price'         => '￥2',
                    'deleteBtn'     => true
                ]
            ],
            'thGoods'             => [
                [
                    [
                        'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                        'name'          => '这里显示商品名称',
                        'info'          => '这里显示商品通知',
                        'price'         => '￥1'
                    ],
                    [
                        'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                        'name'          => '这里显示商品名称',
                        'info'          => '这里显示商品通知',
                        'price'         => '￥2'
                    ],
                    [
                        'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                        'name'          => '这里显示商品名称',
                        'info'          => '这里显示商品通知',
                        'price'         => '￥3'
                    ]
                ]
            ]
        ];
        $group1 = [
            'data' => json_encode($data1)
        ];

        //最热商品分组
        $data2 = $data1;
        $data2[0]['first_priority'] = 3;
        //$data2[0]['second_priority'] = 0;
        $data2[0]['groupTitle'] = '最热商品';
        $data2[0]['is_default'] = 2;
        $group2 = [
            'data' => json_encode($data2)
        ];

        //未分组
        /*$data3 = $data1;
        $data3[0]['first_priority'] = 4;
        //$data2[0]['second_priority'] = 0;
        $data3[0]['groupTitle'] = '未分组';
        $data3[0]['is_default'] = 3;
        $group3 = [
            'data' => json_encode($data3)
        ];
        //卡密商品
        $data4 = $data1;
        $data4[0]['first_priority'] = 5;
        //$data2[0]['second_priority'] = 0;
        $data4[0]['groupTitle'] = '卡密商品';
        $data4[0]['is_default'] = 4;
        $group4 = [
            'data' => json_encode($data4)
        ];*/
        $groupService = new ProductGroupService();
        if ($groupService->setGroup($group1, $wid) && $groupService->setGroup($group2, $wid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 在原来的基础上新加两个默认分组
     * @author 吴晓平 <2018年08月22日>
     * @param  [int] $wid [店铺id]
     * @return [type]      [description]
     */
    public function createDefaultUnGroup($wid)
    {
        $data1[] = [
            'type'              => 'goods_group',
            'first_priority'    => 4,
            'second_priority'   => 0,
            'show_tag_title'    => 1,
            'groupTitle'        => '未分组',
            'is_default'        => 3,
            'showRight'         => true,
            'cardRight'         => 10,
            'editing'           => 'editing',
            'listStyle'         => 3,
            'cardStyle'         => 1,
            'showSell'          => true,
            'btnStyle'          => 1,
            'goodName'          => false,
            'goodInfo'          => false,
            'priceShow'         => true,
            'nodate'            => true,
            'signInfo'          => '',
            'goods'             => [
                [
                    'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                    'name'          => '这里显示商品名称',
                    'info'          => '这里显示商品通知',
                    'price'         => '￥1',
                    'deleteBtn'     => true
                ],
                [
                    'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                    'name'          => '这里显示商品名称',
                    'info'          => '这里显示商品通知',
                    'price'         => '￥2',
                    'deleteBtn'     => true
                ]
            ],
            'thGoods'             => [
                [
                    [
                        'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                        'name'          => '这里显示商品名称',
                        'info'          => '这里显示商品通知',
                        'price'         => '￥1'
                    ],
                    [
                        'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                        'name'          => '这里显示商品名称',
                        'info'          => '这里显示商品通知',
                        'price'         => '￥2'
                    ],
                    [
                        'thumbnail'     => config('app.source_url') . 'static/images/product_img_1.jpg',
                        'name'          => '这里显示商品名称',
                        'info'          => '这里显示商品通知',
                        'price'         => '￥3'
                    ]
                ]
            ]
        ];
        $group1 = [
            'data' => json_encode($data1)
        ];
        //卡密商品
        $data2 = $data1;
        $data2[0]['first_priority'] = 5;
        //$data2[0]['second_priority'] = 0;
        $data2[0]['groupTitle'] = '卡密商品';
        $data2[0]['is_default'] = 4;
        $group2 = [
            'data' => json_encode($data2)
        ];
        $groupService = new ProductGroupService();
        if ($groupService->setGroup($group1, $wid) && $groupService->setGroup($group2, $wid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 店铺创建成功后 默认新建两个默认商品
     * @param int $wid 店铺id
     * @return array $idArr
     */
    public function createDefaultProducts($wid)
    {
        $img = [
            'hsshop/image/static/product_img_1.jpg',
            'hsshop/image/static/product_img_2.jpg'
        ];

        //默认规格
        $service = new ProductPropValuesService();
        $v1_id = $service->addValue('大');
        $v2_id = $service->addValue('中');
        $v3_id = $service->addValue('小');
        $stock1 = [
            'is_show' => 1,
            'price' => 0.01,
            'spec0' => '大',
            'stock_num' => 99,
            'k1_id' => 2,
            'k1' => '尺寸',
            'v1_id' => $v1_id,
            'v1' => '大'
        ];
        $stock2 = $stock1;
        $stock2['v1_id'] = $v2_id;
        $stock2['v1'] = '中';
        $stock3 = $stock1;
        $stock3['v1_id'] = $v3_id;
        $stock3['v1'] = '小';
        $sku = [
            'props' => [
                [
                    'prop' => [
                        'id' => 2,
                        'show_img' => 0,
                        'title' => '尺寸'
                    ],
                    'values' => [
                        [
                            'id' => $v1_id,
                            'img' => ''
                        ],
                        [
                            'id' => $v2_id,
                            'img' => ''
                        ],
                        [
                            'id' => $v3_id,
                            'img' => ''
                        ]
                    ]
                ],
            ],
            'stocks' => [$stock1, $stock2, $stock3]
        ];

        $product1 = [
            'title'       => '测试商品1',
            'category_id' => 8,
            'price'       => 0.01,
            'stock'       => 99,
            'img'         => $img,
            'introduce'   => '<img src="/static/images/product_img_1.jpg"/>',
            'sku'         => ['props' => [], 'stocks' => []],
            'is_default'  => 1
        ];
        $product2 = [
            'title'       => '测试商品2',
            'category_id' => 8,
            'price'       => 0.01,
            'stock'       => 297,
            'img'         => $img,
            'introduce'   => '<img src="/static/images/product_img_2.jpg"/>',
            'prop1'       => '尺寸',
            'prop2'       => '颜色',
            'sku'     => $sku,
            'is_default'  => 1
        ];

        $idArr = [];
        $flag1 = $this->setProduct($product1, $wid);
        if ($flag1) {
            $idArr[] = $flag1;
        } else {
            return false;
        }

        $flag2 = $this->setProduct($product2, $wid);
        if ($flag2) {
            $idArr[] = $flag2;
        } else {
            return false;
        }
        return $idArr;
    }

    /**
     * 复制一个商品
     * @param $id
     */
    public function copy($id, $wid)
    {
        //获取商品信息
        $product = $this->getDetail($id);
        if (empty($product)) {
            return myerror('商品不存在');
        }

        //删除多余字段
        unset($product['id'], $product['created_at'], $product['updated_at'], $product['deleted_at']);

        //插入商品表
        //$product['created_at'] = $product['updated_at'] = date('Y-m-d H:i:s');
        //$newId = $this->init('wid', $wid)->add($product, false);
        $pid = ProductModel::insertGetId($product);

        //新增规格
        (new ProductPropsToValuesService())->copySkuByProductID($id, $pid);

        //新增图片
        $imgService = new ProductImgService();
        $imgs = $imgService->getListByProduct($id);
        if (count($imgs)) {
            $this->batchAddImg($imgs, $pid, $wid);
        }

        //新增留言
        $msgService = new ProductMsgService();
        $msgs = $msgService->getListByProduct($id);
        if (count($msgs)) {
            $this->batchAddMsg($msgs, $pid);
        }

        return true;
    }

    /**
     * 批量查询商品对应的明细[自定义微页面使用，更改请通知张国军]
     * @param int $wid 店铺id
     * @param array $data 商品数据
     * @param int $isXCX 1:小程序 0:非小程序
     * @return array
     * @author 张国军 2018年04月27日
     * @update 许立   2018年07月13日 返回是否是预售标记
     * @update 许立   2018年07月16日 返回商品是否正在出售中
     * @todo 陈文豪  2018年08月28日  此处代码太垃圾 必须重写
     * @update 陈文豪  2018年08月31日 按销量排序
     * @update 梅杰  2018年09月10日 必须按wid查找
     * @update 梅杰  2018年10月16日 商品分组最多50
     */
    public function getProductList($wid, $data = [], $isXCX = 0)
    {
        if ($isXCX == 0 || app('request')->input('isNew',0)) {
            return $this->getProductListV2($wid, $data, $isXCX = 0);
        }
        $returnData = array('errCode' => 0, 'errMsg' => '', 'data' => []);
        $errMsg = '';
        (empty($wid)) && $errMsg .= '店铺id为空';
        //status为1表示商品上架
        $whereData = [
            'wid' => $wid,
            'status' =>1
        ];
        (!empty($data['productsID'])) && ($whereData['id'] = ['in', [$data['productsID']]]);
        (!empty($data['is_default'])) && ($whereData['is_default'] = $data['is_default']);

        if (!empty($data['groupID'])) {
            $data['groupID'] = addslashes(strip_tags($data['groupID']));
            $whereData['_string'] = ' FIND_IN_SET(' . $data['groupID'] . ',group_id) ';
        }

        if (strlen($errMsg) > 0) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $errMsg;
            return $returnData;
        }

        //查询商品明细 status为1表示商品上架
        //==========最热 最新分组 特殊处理 Herry 20171013
        $max = 0;
        (!empty($data['groupAll']) && $data['groupAll'] == 1 ) && ($max = $data['groupMax']);
        if (isset($data['limitNum'])) {
            $max = $data['groupMax'];
            unset($data['limitNum']);
        }

        $orderby1 = $orderby2 = '';
        (!isset($data['first_priority'])) && ($data['first_priority'] = '');
        (!isset($data['second_priority'])) && ($data['second_priority'] = '');
        switch ($data['first_priority']) {
            case 0:
                $orderby1 = 'sort desc';
                $orderby2 = 'sold_num desc';
                ($data['second_priority'] == 1) && ($orderby2 = 'created_at desc');
                ($data['second_priority'] == 2) && ($orderby2 = 'created_at asc');
                break;
            case 1:
                $orderby1 = 'created_at desc';
                $orderby2 = 'sold_num desc';
                ($data['second_priority'] == 3) && ($orderby2 = 'sort desc');
                break;
            case 2:
                $orderby1 = 'created_at asc';
                $orderby2 = 'sold_num desc';
                ($data['second_priority'] == 3) && ($orderby2 = 'sort desc');
                break;
            case 3:
                $orderby1 = 'sold_num desc';
                $orderby2 = 'created_at asc';
                ($data['second_priority'] == 1) && ($orderby2 = 'id desc');
                ($data['second_priority'] == 2) && ($orderby2 = 'created_at desc');
                break;
            default:
                $orderby1 = 'sort desc';
                break;
        }
        (!$max || $max >400) && $max = 400;
        //==========最热 最新分组 特殊处理 Herry 20171013
        $whereData['stock'] = ['>',0];
        $productsList = $this->getGroupProductList($whereData, 0, $max, $orderby1, $orderby2);
        $returnPrd = MicPage::to($wid,$isXCX)->dataToPage($productsList);
        $returnPrd && $returnData['data']['products'] = $returnPrd;

        return $returnData;
    }


    /**
     * 分页处理商品分组
     * @param $wid
     * @param array $data
     * @param int $isXCX
     * @return array
     * @author: 梅杰 2018年10月26日
     */
    public function getProductListV2($wid, $data = [], $isXCX = 0)
    {
        $returnData = array('errCode' => 0, 'errMsg' => '', 'data' => []);

        //status为1表示商品上架
        $whereData = [
            'wid'    => $wid,
            'status' => 1,
            'stock'=> ['>',0]
        ];
        (!empty($data['productsID'])) && ($whereData['id'] = ['in', [$data['productsID']]]);
        (!empty($data['is_default'])) && ($whereData['is_default'] = $data['is_default']);

        if (!empty($data['groupID'])) {
            $data['groupID'] = addslashes(strip_tags($data['groupID']));
            $whereData['_string'] = ' FIND_IN_SET(' . $data['groupID'] . ',group_id) ';
        }

        //查询商品明细 status为1表示商品上架
        //==========最热 最新分组 特殊处理 Herry 20171013
        $max = 0;
        (!empty($data['groupAll']) && $data['groupAll'] == 1 ) && ($max = $data['groupMax']);
        if (isset($data['limitNum'])) {
            $max = $data['groupMax'];
            unset($data['limitNum']);
        }

        $orderby2 = '';
        (!isset($data['first_priority'])) && ($data['first_priority'] = '');
        (!isset($data['second_priority'])) && ($data['second_priority'] = '');
        switch ($data['first_priority']) {
            case 0:
                $orderby1 = 'sort desc';
                $orderby2 = 'sold_num desc';
                ($data['second_priority'] == 1) && ($orderby2 = 'created_at desc');
                ($data['second_priority'] == 2) && ($orderby2 = 'created_at asc');
                break;
            case 1:
                $orderby1 = 'created_at desc';
                $orderby2 = 'sold_num desc';
                ($data['second_priority'] == 3) && ($orderby2 = 'sort desc');
                break;
            case 2:
                $orderby1 = 'created_at asc';
                $orderby2 = 'sold_num desc';
                ($data['second_priority'] == 3) && ($orderby2 = 'sort desc');
                break;
            case 3:
                $orderby1 = 'sold_num desc';
                $orderby2 = 'created_at asc';
                ($data['second_priority'] == 1) && ($orderby2 = 'id desc');
                ($data['second_priority'] == 2) && ($orderby2 = 'created_at desc');
                break;
            default:
                $orderby1 = 'sort desc';
                break;
        }

        $page = app('request')->input('page',1);
        if ($max && ceil($max / 15) < $page ) {
            return $returnData;
        }
        $skip = ($page - 1) * 15 ;
        //==========最热 最新分组 特殊处理 Herry 20171013
        $productsList = $this->getGroupProductList($whereData, $skip, 15, $orderby1, $orderby2);

        //判断是否显示完了
        if ($max && ($skip + count($productsList)) > $max) {
            //切割多余的数组
            $count = $max % 15;
            $productsList = array_slice($productsList,0,$count);
        }

        $returnPrd = MicPage::to($wid,$isXCX)->dataToPage($productsList);

        if ($returnPrd && app('request')->input('isNew') == 2) {
            $list = [];
            $len = intval(ceil(count($returnPrd) / 3));
            for ($i = 0; $i < $len; $i++) {
                $list[]= array_slice($returnPrd, $i * 3, 3);
            }
            $returnPrd = $list;
        }

        $returnPrd && $returnData['data']['products'] = $returnPrd;

        return $returnData;
    }

    public function getGroupProductList($where = [], $skip = "", $perPage = "", $orderby1 = "", $orderby2 = "")
    {
        $select = $this->model->select('id')->wheres($where);
        //排序
        (!empty($orderby1)) && ($select->order($orderby1));
        (!empty($orderby2)) && ($select->order($orderby2));


        //分页
        $ids =  (empty($skip) && empty($perPage)) ?
                ($select->pluck('id')->toArray()) :
                ($select->skip($skip)->take($perPage)->pluck('id')->toArray());
        return $this->getListById($ids);
    }
    /**
     * todo 查询商品信息 [下单时使用 如果要进行更改，请通知张国军]
     * @param $wid
     * @param array $productsID
     * @return array
     * @author jonzhang
     * @date 2017-07-11
     */
    public function getProducts($productsID=[])
    {
        //定义返回数据格式
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        //参数判断
        $errMsg='';
        if(empty($productsID))
        {
            $errMsg.='商品id为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        //查询商品信息
        $productsList = $this->getList(['id'=>['in',$productsID]]);
        //定义存放商品数组,且有商品规格信息
        $products=[];
        if(!empty($productsList))
        {
            $productProps=new ProductPropsToValuesService();
            foreach($productsList as $item)
            {
                //查询商品规格信息
                $skuList=$productProps->getSkuList($item['id']);
                if(!empty($skuList['stocks']))
                {
                    $item['productProp'] =$skuList['stocks'];
                }
                $products[]=$item;
            }
        }
        $returnData['data']=$products;
        return $returnData;
    }

    /**
     * 解压解析导入的淘宝商品zip文件
     * @param $categoryId int 类目id
     */
    public function parseTaobaoZip($categoryId, $groupID, $wid)
    {
        set_time_limit(0);
        $uniqid = uniqid();

        if (substr($_FILES['upload_taobao']['name'], -4) == '.zip') {
            //重新index
            $this->renameIndex('upload_taobao');

            //解压到临时文件夹
            $tmpPath = base_path('public') . '/hsshop/import/' . $uniqid;
            $this->extractZip($tmpPath, 'upload_taobao');

            //获取解压后的所有文件
            $files = FileService::getFileList($tmpPath, true);
            foreach($files as $f){
                if(substr($f['path'], -4) == '.csv'){
                    $new_data = $this->parseTaobaoCsv($f['path']);

                    $importData = array(
                        'fields' => ['title', 'price', 'stock', 'introduce', 'img', 'goods_no'],
                        'data' => $new_data
                    );
                    $this->addTaobao($importData, $wid, $uniqid, $categoryId, $groupID, 'taobao');
                }
            }

            //删除临时文件 @todo 解压的图片 需要保留 暂时不删除解压后的文件夹
            //FileService::deleteFiles($tmpPath, true);
        } else {
            error('请选择一个包含csv文件和tbi图片的zip文件');
        }
    }

    /**
     * 解压解析导入的阿里巴巴商品zip文件
     * @param $categoryId int 类目id
     */
    public function parseAliZip($categoryId, $groupID, $wid)
    {
        set_time_limit(0);
        $uniqid = uniqid();

        if (substr($_FILES['upload_ali']['name'], -4) == '.zip') {
            //重新index
            $this->renameIndex('upload_ali');

            //解压到临时文件夹
            $tmpPath = base_path('public') . '/hsshop/import/' . $uniqid;
            $this->extractZip($tmpPath, 'upload_ali');

            //获取解压后的所有文件
            $files = FileService::getFileList($tmpPath, true);
            foreach($files as $f){
                if(substr($f['path'], -4) == '.csv'){
                    $new_data = $this->parseAliCsv($f['path']);

                    $importData = array(
                        'fields' => ['title', 'price', 'stock', 'introduce', 'img'],
                        'data' => $new_data
                    );

                    $this->addTaobao($importData, $wid, $uniqid, $categoryId, $groupID, 'ali');
                }
            }

            //删除临时文件 @todo 解压的图片 需要保留 暂时不删除解压后的文件夹
            //FileService::deleteFiles($tmpPath, true);
        } else {
            error('请选择一个包含csv文件和tbi图片的zip文件');
        }
    }

    /**
     * 重命名图片并重新index
     */
    private function renameIndex($inputFile)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($_FILES[$inputFile]['tmp_name']);
        if ($res!==true){
            error('打开zip错误');
        }
        for ($i=0;$i<$zip->numFiles;$i++){
            $filename   = $zip->getNameIndex($i);
            if (substr(strrchr($filename, '.'), 1)=='csv'){
                $csvname = md5($filename).'.csv';
                $zip->renameIndex($i,$csvname);
            }else {
                $zip->renameIndex($i, md5(strtolower($filename)).'.jpg');
            }
        }
        $zip->close();
    }

    /**
     * 将上传的zip文件解压到指定临时目录
     */
    private function extractZip($tmpPath, $inputFile)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($_FILES[$inputFile]['tmp_name']);
        if ($res === TRUE) {
            $zip->extractTo($tmpPath);
            $zip->close();
        } else {
            echo 'failed, code:' . $res;
        }
    }

    /**
     * 解析淘宝csv商品数据
     */
    public function parseTaobaoCsv($csvPath)
    {
        if (($handle = FileService::fopen_utf8($csvPath)) === FALSE) {
            error('文件打开失败');
        }
        $title_arr = array();#存储标题栏
        $data_arr = array(); #存储数据栏
        $is_title_flag = false;
        $return_title = ['宝贝名称', '宝贝价格', '宝贝数量', '宝贝描述', '新图片', '商家编码'];
        while ($data = fgetcsv($handle, null,"\t")) {
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
        return $this->taobao_to_weshop($title_arr, $data_arr, $return_title);
    }

    /**
     * 解析ali csv商品数据
     */
    public function parseAliCsv($csvPath)
    {
        if (($handle = FileService::fopen_utf8($csvPath)) === FALSE) {
            error('文件打开失败');
        }
        $title_arr = array();#存储标题栏
        $data_arr = array(); #存储数据栏
        $is_title_flag = false;
        $return_title = ['标题', '价格区间', '可售数量', '产品详情', '产品图片'];
        while ($data = fgetcsv($handle, null,"\t")) {
            $temp_data = $data;
            if ($is_title_flag) { #标题找到后才往数组里面塞数据
                $data_arr[] = $temp_data;
            }
            if ($temp_data[0] == '产品ID') {
                $title_arr = $temp_data; #塞入整个标题组
                $is_title_flag = true;
            }
            unset($data);
            unset($temp_data);
        }
        fclose($handle);
        return $this->taobao_to_weshop($title_arr, $data_arr, $return_title);
    }

    /**
     * 处理阿凡提xls数据
     * @param string $xlsPath
     * @param numeric $categoryId
     * @return void|boolean
     */
    public function processAfantiXls($xlsPath, $categoryId, $wid)
    {
        $xlsSheetArrays = \PHPExcel_IOFactory::load($xlsPath)->getSheet(0)->toArray();
        $xlsSheetArrays = $this->convert2AccessContent($xlsSheetArrays);
        $this->createRecords($xlsSheetArrays, $categoryId, $wid);
    }

    /**
     * 处理小程序xls数据
     * @param string $xlsPath
     * @param numeric $categoryId
     * @return void|boolean
     */
    public function processXCXXls($xlsPath, $categoryId, $wid)
    {
        $xlsSheetArrays = \PHPExcel_IOFactory::load($xlsPath)->getSheet(0)->toArray();
        $xlsSheetArrays = $this->getContent($xlsSheetArrays);
        $this->createXCXRecords($xlsSheetArrays, $categoryId, $wid);
    }

    /**
     * 处理导入AI智能获系统的商品数据
     * @param $wid  要导入店铺的id
     * @param array $importData  要导入的商品数组
     * @param int $categoryId   商品品类映射id，默认为0
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月29日 15:03:55
     */
    public function processCardXls($wid, $importData = [], $categoryId = 0)
    {
        // 如果导入数据为空，直接返回false
        if (empty($importData)) {
            return false;
        }
        /**
         * 首先对导入的数据根据商品id进行正序排序
         * 主要是导入的数据放到队列中执行，按照先进后出的原则，
         * 最早添加的商品最先放到队列中，最后出来，这样就能处理
         * 把最新添加的商品显示在第一个
         */
        $sorted = collect($importData)->sortBy(function ($value) {
            return $value[0];
        });
        $importData = $sorted->values()->all();
        foreach ($importData as $key => $datum) {
            // 因为智能获客系统导出固定为12个字段信息，如果不足作异常处理
            if (count($datum) != 12) {
                error('数据格式不正确');
                break;
            }
            $saveData['wid'] = $wid;
            $saveData['status'] = 2;
            $saveData['category_id'] = $categoryId;
            $saveData['title'] = $datum[1] ?? '';
            // 由于会搜云新零售系统价格是以分为单位，会搜云是以元为单位，这里需要进行转换
            $price = $datum[2] ?? 0;
            $saveData['price'] = $price / 100;
            $oprice = $datum[3] ?? 0;
            $saveData['oprice'] = $oprice / 100;
            $saveData['stock'] = $datum[4] ?? 0;
            $saveData['stock_show'] = $datum[5] ?? 0;
            $saveData['sold_num'] = $datum[6] ?? 0;
            $saveData['img'] = $datum[7] ?? '';
            // 根据会搜云小程序导入的商品详情处理，跟名片商品详情字段存储有区别，这里主要放入content组键中
            $detail = $datum[8] ?? '';
            $content = [
                'showRight' => true,
                'cardRight' => 17,
                'type'      => 'shop_detail',
                'editing'   => 'editing',
                // 去掉图片高度属性 改成自适应
                'content'   => $detail
            ];
            $saveData['content'] = json_encode([$content], JSON_UNESCAPED_UNICODE);
            $saveData['introduce'] = '';
            $saveData['buy_min'] = $datum[9] ?? 1;
            $sku = $datum[10] ?? '';
            $isBuy = $datum[11] ?? 0;
            // 是否多规格标识, 是否为面议商品
            $skuFlag = $isPriceNegotiable = 0;
            if ($sku) {
                // 多规格标识
                $skuFlag = 1;
            }
            if ($isBuy == 0) {
                // 开启面议标识
                $isPriceNegotiable = 1;
            }
            $saveData['sku_flag'] = $skuFlag;
            $saveData['is_price_negotiable'] = $isPriceNegotiable;
            // 考虑到一次性导出店铺的商品数据量可能会比较大，这里用队列处理
            dispatch(new importCardProduct($saveData, $sku));
        }
    }

    /**
     * @param $saveData     已处理完的对应的要插入入库的商品数据数组
     * @param null $skuData 对应导入商品的规格数据，默认为null
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月29日 15:03:20
     */
    public function insertProductRecord($saveData, $sku = NULL)
    {
        // 根据会搜云的格式对规格进行添加默认值处理
        if ($sku) {
            $skuData = json_decode($sku, true);
            $sku = $this->hanldSpecIndex($skuData);
        }
        // 事务处理全部导入
        \DB::transaction(function () use ($saveData, $sku) {
            $storeDirectory = public_path() . DIRECTORY_SEPARATOR . 'hsshop' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . date('Ymd');
            // 插入导入的商品表记录
            $pid = ProductModel::insertGetId($saveData);
            // 处理商品规格
            if ($sku) {
                $this->dealImportSku($sku, $saveData['wid'], $pid, $storeDirectory);
            }
            // 处理批量插入商品图片(这里直接用会搜云新零售系统的cdn图)
            if ($saveData['img']) {
                $imgs = explode(',', $saveData['img']);
                foreach ($imgs as $key => $img) {
                    $saveImgData[] = ['wid' => $saveData['wid'], 'product_id' => $pid, 'img' => $img];
                    $messageData = ['productId' => $pid, 'imgUrl' => $img, 'targetDir' => $storeDirectory, 'wid' => $saveData['wid']];
                    dispatch((new ProcessImportPictureUrl($messageData))->onQueue('ProcessImportPictureUrl'));
                }
            }
        });
    }

    /**
     * @description   处理规格的索引
     * @param $skuData 对应导入商品的规格数据
     * @return json    处理完后的json数据
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月30日 13:58:47
     */
    public function hanldSpecIndex($skuData)
    {
        // 如果不是数组直接返回false
        if (!is_array($skuData)) {
            return false;
        }
        // 多规格确保title1,spec1不为空，至少存在一个规格
        $spec1 = $spec2 = $spec3 = [];
        foreach ($skuData as $skuDatum) {
            if (empty($skuData['title1']) || empty($skuData['spec1'])) {
                continue;
            }
            $spec1[] = $skuDatum['spec1'] ?? '';
            $spec2[] = $skuDatum['spec2'] ?? '';
            $spec3[] = $skuDatum['spec3'] ?? '';
        }
        // 对规格去重，格式化初始键值，最后进行键值对换, 并进行是否有存在判断处理
        $spec1 = array_values(array_unique($spec1));
        if (!empty($spec1[0])) {
            $spec1 = array_flip(array_values(array_unique($spec1)));
        } else {
            $spec1 = '';
        }
        // 另外对spec2 spec3进行是否有存在判断，额外处理
        $spec2 = array_values(array_unique($spec2));
        if (!empty($spec2[0])) {
            $spec2 = array_flip(array_values(array_unique($spec2)));
        } else {
            $spec2 = '';
        }
        $spec3 = array_values(array_unique($spec3));
        if (!empty($spec3[0])) {
            $spec3 = array_flip(array_values(array_unique($spec3)));
        } else {
            $spec3 = '';
        }
        // 重新组装数据
        foreach ($skuData as &$value) {
            if ($spec1) {
                $spec1_index = $spec1[$value['spec1']];
            } else {
                $spec1_index = 0;
            }
            if ($spec2) {
                $spec2_index = $spec2[$value['spec2']];
            } else {
                $spec2_index = 0;
            }
            if ($spec3) {
                $spec3_index = $spec3[$value['spec3']];
            } else {
                $spec3_index = 0;
            }
            $value['spec1_index'] = $spec1_index;
            $value['spec2_index'] = $spec2_index;
            $value['spec3_index'] = $spec3_index;
            $value['title1'] = $value['title1'] ?? '';
            $value['title2'] = $value['title2'] ?? '';
            $value['title3'] = $value['title3'] ?? '';
            $value['spec1'] = $value['spec1'] ?? '';
            $value['spec2'] = $value['spec2'] ?? '';
            $value['spec3'] = $value['spec3'] ?? '';
            $value['price'] = $value['price'] / 100;
            $value['img'] = '';
            $value['sold_num'] = $value['sales_num'];
            // unset掉会搜云不存在的字段
            unset($value['sales_num']);
        }
        return json_encode($skuData, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 将索引数组转换成关联数组,并去掉头
     * @param array $xlsSheetArrays
     * @return array
     * @author mfd
     */
    protected function getContent(array $xlsSheetArrays)
    {
        if (count($xlsSheetArrays) <= 1) {
            return $xlsSheetArrays;
        }
        $header = array_shift($xlsSheetArrays);

        $titleArr = ['商品名称', '售价', '原价', '货号', '商品详情', '展示图片', '库存'];
        foreach ($titleArr as $title) {
            !in_array($title, $header) && error('导入数据格式不正确');
        }

        return array_map(function ($row) use ($header) {
            return array_combine(array_values($header), array_values($row));
        }, $xlsSheetArrays);
    }

    /**
     * 将索引数组转换成关联数组,并去掉头
     * @param array $xlsSheetArrays
     * @return array
     * @author mfd
     */
    protected function convert2AccessContent(array $xlsSheetArrays)
    {
        if (count($xlsSheetArrays) <= 1) {
            return $xlsSheetArrays;
        }
        $header = array_shift($xlsSheetArrays);

        //阿凡提导出商品格式应该变了 没有 销量/库存 和 商品主图 字段 Herry 20171024
        $titleArr = ['名称', '销售价', '市场价', '货号', '商品详情', '商品图片', '库存'];
        foreach ($titleArr as $title) {
            !in_array($title, $header) && error('导入数据格式不正确');
        }
        $data =array_map(function ($row) use ($header) {
            return array_combine(array_values($header), array_values($row));
        }, $xlsSheetArrays);
        foreach($data as $k=>&$v)
        {
            $v['商品详情'] = $this->picturesAft($v['商品详情']);
        }
        return $data;
    }

    /**
     * @author fuguowei
     * @date 20180105
     * @desc  阿凡提导入商品详情的图片加域名处理
     */
    public  function  picturesAft($str='')
    {

        $str = htmlspecialchars_decode($str);//dd($str);
        preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/",$str,$matches);

        $img_src_arr = $matches[3];//dd($matches);
        $url ="http://35304.vshop.afantisoft.cn:80";
        if($img_src_arr)
        {
            foreach($img_src_arr as $k=>$v)
            {
                $http =strpos($v,'ttp:');
                $https =strpos($v,'ttps:');
                // echo $k.'你号'.$http.' 你好 '.$https."<br>";
                //echo $k.$https."<br>";
                if($http != 1 && $https !=1)
                {
                    $str = str_replace($v,$url."$v",$str);
                }
            }
        }
        $str = htmlspecialchars($str);
        return $str;
    }


    /**
     * 创建商品记录并将图片下载任务放入队列
     * @param array $xlsSheetArrays
     * @param numeric $categoryId
     * @return void
     * @author mfd
     */
    protected function createRecords(array $xlsSheetArrays, $categoryId, $wid)
    {
        $storeDirectory = public_path() . DIRECTORY_SEPARATOR . 'hsshop' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . date('Ymd') . $categoryId;
        if (!file_exists($storeDirectory) || !is_dir($storeDirectory)) {
            if(mkdir($storeDirectory, 0777, true) === false) {
                error('创建目录失败');
            }
        }
        if(chmod($storeDirectory, 0777) === false) {
            error('没有目标目录权限');
        }
        foreach ($xlsSheetArrays as $xlsSheetArray) {
            if (is_null($xlsSheetArray['名称']) || is_null($xlsSheetArray['商品图片'])) {
                continue;
            }

            //处理详情 Herry 20171208
            $insertData = [
                'category_id' => $categoryId,
                'status' => 2,
                'wid' => $wid,
                'title' => $xlsSheetArray['名称'],
                'price' => $xlsSheetArray['销售价'],
                'oprice' => $xlsSheetArray['市场价'],
                //'stock' => is_null($xlsSheetArray['销量/库存']) ? 0 : explode('/', $xlsSheetArray['销量/库存'])[1],
                'stock' => $xlsSheetArray['库存'], //阿凡提导出商品格式应该变了 没有 销量/库存 和 商品主图 字段 Herry 20171024
                'goods_no' => $xlsSheetArray['货号'],
            ];
            $insertData['introduce'] = preg_replace('/(?<=href=)([^>]*)(?=>)/i', 'javascript:;', $xlsSheetArray['商品详情']);
            $insertData['introduce'] = html_entity_decode($insertData['introduce']);
            $content = [];
            $content[] = [
                'showRight' => true,
                'cardRight' => 17,
                'type'      => 'shop_detail',
                'editing'   => 'editing',
                'content'   => preg_replace('/height=\"(.+?)\"/i', 'height="auto"', $insertData['introduce'])  //Herry 去掉图片高度属性 改成自适应
            ];
            //中文编码问题 Herry 使用JSON_UNESCAPED_UNICODE
            $insertData['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
            $insertData['introduce'] = '';

            $id = ProductModel::insertGetId($insertData);
            if ($id === false) {
                continue;
            }
            //阿凡提导出商品格式应该变了 没有 销量/库存 和 商品主图 字段 Herry 20171024
            //图片新格式 用分号分隔的多图 Herry 20171213
            $messageData = ['productId' => $id, 'imgUrl' => $xlsSheetArray['商品图片'], 'targetDir' => $storeDirectory, 'wid' => $wid];
            //不使用默认队列 新开一个 Herry 20171214
            //dispatch(new ProcessImportPictureUrl($messageData));
            dispatch((new ProcessImportPictureUrl($messageData))->onQueue('ProcessImportPictureUrl'));
        }
    }

    /**
     * 创建商品记录并将图片下载任务放入队列
     * @param array $xlsSheetArrays
     * @param numeric $categoryId
     * @return void
     * @author mfd
     */
    protected function createXCXRecords(array $xlsSheetArrays, $categoryId, $wid)
    {
        $storeDirectory = public_path() . DIRECTORY_SEPARATOR . 'hsshop' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . date('Ymd') . $categoryId;
        if (!file_exists($storeDirectory) || !is_dir($storeDirectory)) {
            if(mkdir($storeDirectory, 0777, true) === false) {
                error('创建目录失败');
            }
        }
        if(chmod($storeDirectory, 0777) === false) {
            error('没有目标目录权限');
        }
        foreach ($xlsSheetArrays as $xlsSheetArray) {
            if (is_null($xlsSheetArray['商品名称']) || is_null($xlsSheetArray['展示图片'])) {
                continue;
            }

            //处理详情 Herry 20171208
            $insertData = [
                'category_id' => $categoryId,
                'status' => 2,
                'wid' => $wid,
                'title' => $xlsSheetArray['商品名称'],
                'price' => $xlsSheetArray['售价'],
                'oprice' => $xlsSheetArray['原价'],
                'stock' => $xlsSheetArray['库存'],
                'goods_no' => $xlsSheetArray['货号'],
                //'sku_flag' => !empty($xlsSheetArray['规格']) ? 1 : 0

            ];

            //拼接商品详情接口
            $url = '';
            if (!empty($xlsSheetArray['商品ID']) && !empty($xlsSheetArray['域名'])) {
                $url = 'http://' . $xlsSheetArray['域名'] . '.m.huisou.com/miniprogram/product/getproductinfo&id=' . $xlsSheetArray['商品ID'];
                //excel单元格最大字符限制32767 当字符打到30000 详情会有贝截断可能 解决方案：调用接口获取详情
                if (mb_strlen($xlsSheetArray['商品详情']) > 30000) {
                    $product = @file_get_contents($url);
                    \Log::info('[导入小程序商品调接口获取详情]ID:' . $xlsSheetArray['商品ID'] . ',url:' . $url);
                    $product = json_decode($product, true);
                    if (!empty($product['list'])) {
                        $xlsSheetArray['商品详情'] = $product['list']['summary'];
                    } else {
                        $xlsSheetArray['商品详情'] = '';
                    }
                }
            }

            $insertData['introduce'] = preg_replace('/(?<=href=)([^>]*)(?=>)/i', 'javascript:;', $xlsSheetArray['商品详情']);

            //todo 待优化 小程序导出详情里的图片是相对路径 转化成绝对路径
            if (!empty($xlsSheetArray['域名'])) {
                $insertData['introduce'] = str_replace('src="/Uploads', 'src="http://' . $xlsSheetArray['域名'] . '.m.huisou.com/Uploads', $insertData['introduce']);
            }

            $insertData['introduce'] = html_entity_decode($insertData['introduce']);
            $content = [];
            $content[] = [
                'showRight' => true,
                'cardRight' => 17,
                'type'      => 'shop_detail',
                'editing'   => 'editing',
                'content'   => preg_replace('/height=\"(.+?)\"/i', 'height="auto"', $insertData['introduce'])  //Herry 去掉图片高度属性 改成自适应
            ];
            //中文编码问题 Herry 使用JSON_UNESCAPED_UNICODE
            $insertData['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
            $insertData['introduce'] = '';

            $id = ProductModel::insertGetId($insertData);
            if ($id === false) {
                continue;
            }

            //规格处理
            $this->dealImportSku($xlsSheetArray['规格'], $wid, $id, $storeDirectory, $url);

            //阿凡提导出商品格式应该变了 没有 销量/库存 和 商品主图 字段 Herry 20171024
            //图片新格式 用分号分隔的多图 Herry 20171213
            $messageData = ['productId' => $id, 'imgUrl' => $xlsSheetArray['展示图片'], 'targetDir' => $storeDirectory, 'wid' => $wid];
            //不使用默认队列 新开一个 Herry 20171214
            //dispatch(new ProcessImportPictureUrl($messageData));
            dispatch((new ProcessImportPictureUrl($messageData))->onQueue('ProcessImportPictureUrl'));
        }
    }

    /*
     * 导入数据处理
     * @params：array header_title  导入的数据组成的标题数组
     * @params：array data  导入的数据组成的数组
     * @params：array return_title  要返回的title
     * @return：返回待插入的二维数组集合
     */
    public function taobao_to_weshop($header_title = array(),$data = array(),$return_title = array()){
        if(empty($header_title) || empty($data) || empty($return_title)){
            error('导入数据为空或错误');
        }
        $new_turn_title = array_flip($return_title); #键值对翻转后 将键存入数组
        $return_data = array();
        foreach($data as $goods){
            $temp_goods = array();
            foreach($header_title as $k => $htitle){
                if(in_array($htitle,$return_title)){ #如果某个字段是需要返回的
                    $temp_goods[$new_turn_title[$htitle]] = isset($goods[$k])?$goods[$k]:''; #按键存取数据
                }
            }
            $return_data[] = $temp_goods;
            unset($temp_goods);
        }
        return $return_data;
    }

    /**
     * 替换详情图片url为本地url
     */
    private function imgToLocal($string, $imgArr)
    {
        foreach ($imgArr as $k => $v) {
            $string = str_replace($k, $v, $string);
        }
        return $string;
    }

    /**
     * 获取商品详情 默认取redis redis不存在则取数据库
     */
    public function getDetail($productId)
    {
        $productRedis = new ProductRedis();
        $product = $productRedis->getRow($productId);
        if (empty($product)) {
            //redis不存在 取数据库
            $product = ProductModel::where('id', $productId)->first();
            if(empty($product)) return [];
            $product = $product->toArray();
            //保存redis
            $productRedis->add($product);
        }
        return $product;
    }

    /**
     * 根据商品ID删除规格
     */
    private function deletePropsByProductId($productId)
    {
        //删除属性
        $propsModel = new ProductPropsToValues();
        $propsRedis = new PropsRedis();
        $propIds = $propsModel->select('id')->where('pid', $productId)->pluck('id')->toArray();
        if (!empty($propIds)) {
            $propsRedis->deleteArr($propIds);
            $propsModel->where('pid', $productId)->delete();
        }

        //删除库存
        $skuModel = new ProductSku();
        $skuRedis = new SkuRedis();
        $skuIds = $skuModel->select('id')->where('pid', $productId)->pluck('id')->toArray();
        if (!empty($skuIds)) {
            $skuRedis->deleteArr($skuIds);
            $skuModel->where('pid', $productId)->delete();
        }
    }

    /**
     * 根据商品ID删除图片
     */
    private function deleteImgsByProductId($productId)
    {
        $imgModel = new ProductImg();
        $imgRedis = new ImgRedis();
        //获取图片id列表
        $imgIds = $imgModel->select('id')->where('product_id', $productId)->pluck('id')->toArray();
        //删除图片列表
        $imgRedis->deleteArr($imgIds);
        $imgModel->where('product_id', $productId)->delete();
    }

    /**
     * 根据商品ID删除留言
     */
    private function deleteMsgsByProductId($productId)
    {
        $msgModel = new ProductMsg();
        $msgRedis = new MsgRedis();
        //获取图片id列表
        $msgIds = $msgModel->select('id')->where('product_id', $productId)->pluck('id')->toArray();
        //删除图片列表
        $msgRedis->deleteArr($msgIds);
        $msgModel->where('product_id', $productId)->delete();
    }

    /**
     * 根据商品ID删除留言
     */
    private function deleteWholesaleByProductId($productId)
    {
        $wholesaleModel = new ProductWholesale();
        //获取图片id列表
        $wholesaleIds = $wholesaleModel->select('id')->where('product_id', $productId)->pluck('id')->toArray();
        //删除图片列表
        (new WholesaleRedis())->deleteArr($wholesaleIds);
        $wholesaleModel->where('product_id', $productId)->delete();
    }

    /**
     * 更新数据库和redis
     */
    public function update($productID, $data)
    {
        ProductModel::where('id', $productID)->update($data);
        $data['id'] = $productID;
        $this->updateRedis($data);
        return true;
    }

    /**
     * 更新redis
     */
    public function updateRedis($data)
    {
        (new ProductRedis())->updateRow($data);
    }

    /**
     * 批量更新redis
     */
    public function batchUpdateRedis($data)
    {
        (new ProductRedis())->batchUpdateRedis($data);
    }

    /**
     * 根据条件获取商品总数
     */
    public function getCountByWhere($where = [])
    {
        return $this->count($where);
    }

    /*********************************************************/
    /**
     * author: meijie
     * @param array $productInfo 无规格商品信息
     * @param 用户ID
     * @return int|mixed 重构之后的商品价格
     * @update 许立 2019年11月22日 16:48:42 非会员如果设置显示会员价 返回最大力度折扣价
     */
    public function reSetNoSkuPrice($productInfo = [], $mid, $wid)
    {
        //根据会员卡折扣以及会员价格重构价格
        //优先级：1会员卡价格，2会员卡折扣
        //注意：商品分为有规格，以及无规格,如果后台没有勾选“参加会员折扣”，会员卡折扣无用
        $price                   = $productInfo['price'];
        $tempPrice               = $productInfo['price'];
        $MemberCardRecordService = new MemberCardRecordService();
        $default_card            = $MemberCardRecordService->useCard($mid, $wid);
        $vip                     = 0;
        $bestCardPrice           = 0;
        $temp = json_decode($productInfo['vip_card_price_json'], 1);
        if (isset($default_card['data']['info']['card_id'])) {
            $cardInfo = $default_card['data'];
            $card_id  = $cardInfo['info']['card_id'];
            if ($cardInfo['info']['isDiscount'] == 1 && $productInfo['is_discount']) {
                $vip = 1;
                //如果为会员商品打折
                $price = $cardInfo['info']['discount'] * $tempPrice * 0.1;
            }
            if ($productInfo['vip_card_price_json']) {
                if ($productInfo['vip_discount_way'] == 1 && isset($temp[$card_id]) && $temp[$card_id] != 0) {
                    $vip   = 1;
                    $price = $tempPrice - $temp[$card_id];
                }
                if ($productInfo['vip_discount_way'] == 2 && isset($temp[$card_id]) && $temp[$card_id] != 0) {
                    $vip   = 1;
                    $price = $temp[$card_id];
                }
            }
            $price = sprintf('%.2f', $price);
        }

        if (!$vip && $productInfo['is_discount'] && $productInfo['is_show_vip_price']) {
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
            if ($productInfo['vip_discount_way'] == 1) {
                $bestCardPrice = $tempPrice - $maxCardPrice;
            } elseif ($productInfo['vip_discount_way'] == 2 && $minCardPrice) {
                $bestCardPrice = $minCardPrice;
            }
        }

        return [
            'price'  => $price,
            'is_vip' => $vip,
            'bestCardPrice' => $bestCardPrice
        ];

    }

    /**
     * author: meijie
     * @param array $propData 有规格商品信息
     * @param $mid 用户信息
     * @return mixed|string 重构的价格
     * @update 许立 2018年09月14日 默认不是vip
     */
    public function reSetSkuPrice( $propData= [],$mid,$wid)
    {
        $pData = $this->getDetail($propData['pid']);
        $vip = 0;
        $price = $propData['price'];
        $tempPrice = $propData['price'];
        $MemberCardRecordService = new MemberCardRecordService();
        $default_card = $MemberCardRecordService->useCard($mid,$wid);
        $cardInfo = $default_card['data'];
        $card_id = $cardInfo['info']['card_id'];
        if($cardInfo['info']['isDiscount'] == 1 && $pData['is_discount'])
        {
            $vip = 1;
            //如果为会员商品打折
            $price =  $cardInfo['info']['discount'] * $tempPrice * 0.1;
        }
        $temp = json_decode($propData['vip_card_price_json'],1);
        if($propData['vip_discount_way'] == 1 && isset($temp[$card_id]) && $temp[$card_id] != 0)
        {
            $vip = 1;
            $price =   $tempPrice - $temp[$card_id] ;
        }
        if($propData['vip_discount_way'] == 2 && isset($temp[$card_id]) && $temp[$card_id] != 0)
        {
            $vip = 1;
            $price =    $temp[$card_id] ;
        }
        $price  = sprintf('%.2f',$price );
        return [
            'price'=>$price,
            'is_vip'=>$vip
        ];
    }

    public function getRowById($id)
    {
        $result = [];
        $redis = new ProductRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->addArr($result);
        }
        return $result;
    }

    public function getModel()
    {
        return $this->model;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 减数据
     * @desc
     * @param $id
     * @param $num
     * @return bool
     */
    public function decrement($id,$field,$num)
    {
        $where = [
            'id'    => $id,
            $field =>['>=',$num],
        ];
        $res = $this->model->wheres($where)->decrement($field,$num);
        if ($res){
            $redis = new ProductRedis();
            $num = -$num;
            $redis->incr($id,$field,$num);
            return true;
        }else{
            return false;
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 加数据
     * @desc
     * @param $id
     * @param $num
     * @return bool
     */
    public function increment($id,$field,$num)
    {
        $where = [
            'id'    => $id,
        ];
        $res = $this->model->wheres($where)->increment($field,$num);
        if ($res){
            $redis = new ProductRedis();
            $redis->incr($id,$field,$num);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据排序规则排序商品ID数组
     * @param $idArr array 商品ID数组
     * @param $firstSort int 第一优先排序值 0序号越大越靠前 3最热的排在前面
     * @param $secondSort int 第二优先排序值 1创建时间越晚越靠前 2创建时间越早越靠前 3最热的排在前面
     */
    public function sortProductIDArr($idArr, $firstSort, $secondSort)
    {
        if (!empty($idArr) && is_array($idArr)) {
            $select = $this->model
                ->select('id')
                ->wheres(['id' => ['in', $idArr]]);

            $firstOrder = '';
            if ($firstSort == 0) {
                $firstOrder = 'sort desc';
            } elseif ($firstSort == 3) {
                $firstOrder = 'sold_num desc';
            }

            $secondOrder = '';
            if ($secondSort == 1) {
                $secondOrder = 'created_at desc';
            } elseif ($secondSort == 2) {
                $secondOrder = 'created_at asc';
            } elseif ($secondSort == 3) {
                $secondOrder = 'sold_num desc';
            }

            $select = $select->order($firstOrder)->order($secondOrder);
            $idArr = $select->pluck('id')->toArray();
        }

        return $idArr;
    }

    /**
     * 导出excel表格
     * @param $data array 导出的列表
     * @param $type string 导出类型
     * @author 付国维
     * @since 2018/01/04 14:30
     */
    public function exportExcelXls($data = [], $type = 'order')
    {
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("导出商品")
            ->setSubject("导出商品")
            ->setDescription("导出商品")
            ->setKeywords("导出商品")
            ->setCategory("result file");
        //设置单元格宽度
        if ($type == 'order') {
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(50);
            $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(50);
            $excelObj->getActiveSheet()->getColumnDimension('M')->setWidth(50);

            //标题行
            $excelObj->setActiveSheetIndex()

                ->setCellValue('A1', '名称')
                ->setCellValue('B1', '位置')
                ->setCellValue('C1', '商品图片')
                ->setCellValue('D1', '商品分组')
                ->setCellValue('E1', '现价')
                ->setCellValue('F1', '原价')
                ->setCellValue('G1', '库存')
                ->setCellValue('H1', '虚拟销量')
                ->setCellValue('I1', '商品详情')
                ->setCellValue('J1', '限购数量')
                ->setCellValue('k1', '规格和规格库存和规格销量')
                ->setCellValue('L1', '轮播图')
            ;
            foreach ($data as $k => $v) {
               //dd($data);
                $num = $k + 2;
                //Excel数据填充
                if($v['img'])
                {
                    $http =strpos($v['img'],'ttp:');
                    $https =strpos($v['img'],'ttps:');
                    if($http != 1 && $https !=1)
                    {
                        $img=config('app.source_img_url').$v['img'];
                    }else{
                        $img = $v['img'];
                    }
                }
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $v['title'])
                    ->setCellValue('B' . $num, $v['sort'])
                    ->setCellValue('C' . $num, $img ??'');
                $title='';
                foreach($v['productGroup'] as $val)
                {
                    $titl = $val['title'] ?? "";
                    $title = $titl.'  '.$title;
                }
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('D' . $num, $title);
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('E' . $num, $v['price'] ?? '')
                    ->setCellValue('F' . $num, $v['oprice'] ?? '')
                    ->setCellValue('G' . $num, $v['stock'] ?? '')
                    ->setCellValue('H' . $num, $v['sold_num'] ?? '')
                    ->setCellValue('I' . $num, $v['contend'] ?? '')
                    ->setCellValue('J' . $num, $v['quota'] ?? '')
                ;
                $stock_num = [];//dd($v['productSku']);
                if($v['productSku']['stocks'])
                {
                    $flag = (count($v['productSku']['props']));
                    foreach($v['productSku']['stocks'] as $key=>$val)
                    {
                        for ($i=1;$i<=$flag;$i++){
                            $stock_num[$key]["title$i"] = $val['k'.$i] ?? '';
                            $stock_num[$key]["spec$i"] = $val['v'.$i] ?? '';
                            $stock_num[$key]["price"] = $val['price'] ?? '';
                            $stock_num[$key]["stock_num"] = $val['stock_num'] ?? '';
                            $stock_num[$key]["sold_num"] = $val['sold_num'] ?? '';
                        }
                    }
                }
                if($stock_num)
                {
                    $excelObj->setActiveSheetIndex()
                        ->setCellValue('k' . $num, json_encode($stock_num));
                }

                if($v['productImg'])
                {
                    $productImg = [];
                    foreach($v['productImg'] as $val)
                    {
                        $http =strpos($val['img'],'ttp:');
                        $https =strpos($val['img'],'ttps:');
                        if($http != 1 && $https !=1)
                        {
                            $productImg[]=config('app.source_img_url').$val['img'];
                        }else{
                            $productImg[] = $val['img'];
                        }
                    }
                    $productImg = implode(',',$productImg);
                }

                $excelObj->setActiveSheetIndex()
                    ->setCellValue('L' . $num,$productImg ?? '');
            }//dd(77);
            //准备导出
            $excelObj->setActiveSheetIndex();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="商品表_' . time() . '.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
            $objWriter->save('php://output');
        }
    }

    /**
     * @author fuguowei
     * @date 20180105
     * @desc  导出的多个选中的商品
     */
    public function getProduc($productsID=[])
    {
        $where['id'] = ['in',$productsID];
        $where['wid'] = session('wid');//dd($productsID);
        $products = $this->model->wheres($where)->get();
        if($products)
        {
            $products = $products->toArray();
            foreach($products as &$v)
            {
                $v['contend'] = '';
                if($v['content'])
                {
                    $data = json_decode($v['content'],true);
                    $v['contend'] = $data[0]['content'] ?? '';
                    $v['contend'] =$this->pictures($v['contend']);
                }
                $v['productSku'] ='';
                $sku = (new ProductPropsToValuesService())->getSkuList($v['id']);//dd($sku);
                if($sku)
                {
                    $v['productSku'] = $sku;
                }
                //获取商品图片
                $v['productImg'] = (new ProductImgService())->getListByProduct($v['id']);//dd($v['productImg']);
                $groupIds = explode(',',$v['group_id']);
                $group=(new ProductGroupService())->productGroup($groupIds);//dd($group);
                $v['productGroup'] = $group;

            }
        }//dd($products);
        return $products;

    }

    /**
     * @author fuguowei
     * @date 20180105
     * @desc  根据商品的上架下架卖完状态导出所有的数据
     */
    public function getPro($where)
    {
        //dd($where);
        $products = $this->model->wheres($where)->get();

        if($products)
        {
            $products = $products->toArray();
            foreach($products as &$v)
            {
                $v['contend'] = '';
                if($v['content'])
                {
                    $data = json_decode($v['content'],true);
                    $v['contend'] = $data[0]['content'] ?? '';
                    $v['contend'] =$this->pictures($v['contend']);
                }
                $v['productSku'] ='';//dd($v['id']);
                $sku = (new ProductPropsToValuesService())->getSkuList($v['id']);//dd($sku);
                if($sku)
                {
                    $v['productSku'] = $sku;
                }
                //获取商品图片
                $v['productImg'] = (new ProductImgService())->getListByProduct($v['id']);

                $groupIds = explode(',',$v['group_id']);
                $group=(new ProductGroupService())->productGroup($groupIds);//dd($group);
                $v['productGroup'] = $group;
            }

        }
        return $products;

    }

    /**
     * @author fuguowei
     * @date 20180105
     * @desc  导出商品详情的图片加域名处理
     */
     public  function  pictures($str='')
     {
        preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/",$str,$matches);
        $img_src_arr = $matches[3];//dd($matches);
        $url =config('app.source_img_url');
        if($img_src_arr)
        {
            foreach($img_src_arr as $k=>$v)
            {
                $http =strpos($v,'ttp:');
                $https =strpos($v,'ttps:');
               // echo $k.'你号'.$http.' 你好 '.$https."<br>";
                //echo $k.$https."<br>";
                if($http != 1 && $https !=1)
                {
                    $str = str_replace($v,$url."$v",$str);
                }
            }
        }
        return $str;
     }

    /**
     * 处理小程序商品导入的库存
     * @param $sku string 规格json字符串
     * @param $wid int 店铺ID
     * @param $pid int 导入后的新商品ID
     * @param $imgPath string 规格图片保存路径
     * @param $url string 获取小程序商品信息接口路径 如：http://ynsl.m.huisou.com/miniprogram/product/getproductinfo&id=739
     * @return bool true 成功导入规格 | false 无规格处理
     */
    private function dealImportSku($sku, $wid, $pid, $imgPath, $url = '')
    {
        //为空 则无规格
        if (empty($sku)) {
            return false;
        }

        //规格数组
        $sku = json_decode($sku, true);
        if (empty($sku)) {
            if (empty($url)) {
                return false;
            }

            //20180309 导出的规格字段不为空 excel表格字符数限制 规格字符串被截断 单独调用商品接口 获取规格
            $product = @file_get_contents($url);
            \Log::info('[导入小程序 直接调接口获取规格] ' . $url);
            $product = json_decode($product, true);
            if (!empty($product['list'])) {
                $sku = json_decode($product['list']['spec'], true);
            } else {
                return false;
            }
        }

        if (empty($sku[0]['title1']) || empty($sku[0]['spec1'])) {
            return false;
        }

        //导入的属性与属性ID关联数组
        $propAssocArr = $valueAssocArr = [];
        //属性的属性值数组
        $values1 = $values2 = $values3 = [];
        //属性的属性值个数
        $valuesCount1 = $valuesCount2 = $valuesCount3 = 0;
        $propService = new ProductPropsService();
        for ($i = 1; $i < 4; $i++) {
            //属性下标变量
            $propIDKey = 'propID' . $i;
            $propTitleKey = 'propTitle' . $i;
            //属性变量
            $$propIDKey = 0;
            $$propTitleKey = $sku[0]['title' . $i];
            if (!empty($$propTitleKey)) {
                $$propIDKey = $propService->add($wid, $$propTitleKey);
                $propAssocArr[$$propTitleKey] = $$propIDKey;
            }

            //属性值数组下标变量
            $valuesKey = 'values' . $i;
            //某属性的属性值个数下标变量
            $valuesCountKey = 'valuesCount' . $i;
            //属性值数组变量
            $$valuesKey = [];
            //某属性的属性值个数变量
            $$valuesCountKey = 0;
            if ($$propIDKey) {
                $$valuesKey = array_values(array_unique(array_column($sku, 'spec' . $i)));
                $$valuesCountKey = count($$valuesKey);
            }
        }

        //组装属性和属性值关系props
        $props = [];
        $valueService = new ProductPropValuesService();
        for ($i = 1; $i < 4; $i++) {
            $propIDKey = 'propID' . $i;
            $propTitleKey = 'propTitle' . $i;
            $valueIDKey = 'valueID' . $i;
            $valuesKey = 'values' . $i;
            if ($$propIDKey) {
                //组装属性值数组
                $valueArr = [];
                foreach ($$valuesKey as $k => $v) {
                    $$valueIDKey = $valueService->addValue($v);
                    $valueAssocArr[$v] = $$valueIDKey;
                    $valueArr[] = [
                        'id' => $$valueIDKey,
                        'img' => '' //todo 图片在队列里单独处理 后续更新
                    ];
                }

                $props[] = [
                    'prop' => [
                        'id' => $$propIDKey,
                        'title' => $$propTitleKey,
                        'show_img' => 0 //todo 图片在队列里单独处理 后续更新
                    ],
                    'values' => $valueArr
                ];
            }
        }

        //组装sku
        $stocks = [];
        //前端合并单元格使用字段判断逻辑 @俞江南 @魏冬冬
        $rowspan0 = $rowspan1 = 1;
        if ($valuesCount1 > 0 && $valuesCount2 > 0 && $valuesCount3 > 0) {
            $rowspan0 = $valuesCount2 * $valuesCount3;
            $rowspan1 = $valuesCount3;
        } elseif ($valuesCount1 > 0 && $valuesCount2 > 0 && $valuesCount3 == 0) {
            $rowspan0 = $valuesCount2;
        } elseif ($valuesCount1 > 0 && $valuesCount2 == 0 && $valuesCount3 > 0) {
            $rowspan0 = $valuesCount3;
        } elseif ($valuesCount1 == 0 && $valuesCount2 > 0 && $valuesCount3 > 0) {
            $rowspan1 = $valuesCount3;
        }

        //sku图片数组
        $propImgArr = [];
        //第一个属性ID
        $firstPropID = 0;
        foreach ($sku as $k => $v) {
            //todo 小程序商品坑。。。不是所有域名下都有spec1_index spec2_index spec3_index字段
            if (!isset($v['spec1_index']) || !isset($v['spec2_index']) || !isset($v['spec3_index'])) {
                return false;
            }

            //前端合并单元格使用字段判断逻辑 @俞江南 @魏冬冬
            $show = $show1 = false;
            if ($valuesCount1 > 0 && $valuesCount2 > 0 && $valuesCount3 > 0) {
                if ($v['spec3_index'] == 0 && $v['spec2_index'] == 0) {
                    $show = true;
                }
                if ($v['spec3_index'] == 0) {
                    $show1 = true;
                }
            } elseif ($valuesCount1 > 0 && $valuesCount2 > 0 && $valuesCount3 == 0) {
                if ($v['spec2_index'] == 0) {
                    $show = true;
                }
                $show1 = true;
            } elseif ($valuesCount1 > 0 && $valuesCount2 == 0 && $valuesCount3 > 0) {
                if ($v['spec3_index'] == 0 && $v['spec2_index'] == 0) {
                    $show = true;
                }
            } elseif ($valuesCount1 > 0 && $valuesCount2 == 0 && $valuesCount3 == 0) {
                $show = true;
            } elseif ($valuesCount1 == 0 && $valuesCount2 > 0 && $valuesCount3 > 0) {
                if ($v['spec3_index'] == 0) {
                    $show1 = true;
                }
            } elseif ($valuesCount1 == 0 && $valuesCount2 > 0 && $valuesCount3 == 0) {
                $show1 = true;
            }

            $stock = [
                'k1' => $v['title1'],
                'k1_id' => $propAssocArr[$v['title1']] ?? 0,
                'k2' => $v['title2'],
                'k2_id' => $propAssocArr[$v['title2']] ?? 0,
                'k3' => $v['title3'],
                'k3_id' => $propAssocArr[$v['title3']] ?? 0,
                'v1' => $v['spec1'],
                'v1_id' => $valueAssocArr[$v['spec1']] ?? 0,
                'v2' => $v['spec2'],
                'v2_id' => $valueAssocArr[$v['spec2']] ?? 0,
                'v3' => $v['spec3'],
                'v3_id' => $valueAssocArr[$v['spec3']] ?? 0,
                'price' => $v['price'],
                'stock_num' => $v['stock'],
                'rowspan0' => $rowspan0,
                'rowspan1' => $rowspan1,
                'is_show' => $show,
                'is_show1' => $show1
            ];

            $stocks[] = $stock;

            $firstPropID = $propAssocArr[$v['title1']] ?? 0;
            //todo 属性图片暂时拼接死的域名
            empty($propImgArr[$stock['v1_id']]) && $propImgArr[$stock['v1_id']] = 'http://ynsl.m.huisou.com/' . $v['img'];
        }

        if ($props) {
            //保存sku
            $this->addProps($pid, ['props' => $props, 'stocks' => $stocks], $wid);

            //保存sku图片
            $propImgData = ['type' => 'PROP', 'productId' => $pid, 'propID' => $firstPropID, 'imgUrl' => $propImgArr, 'targetDir' => $imgPath, 'wid' => $wid];
            dispatch((new ProcessImportPictureUrl($propImgData))->onQueue('ProcessImportPictureUrl'));

            //更新商品表是否有规格字段
            $this->model->where(['id' => $pid])->update(['sku_flag' => 1]);
        }

        return true;
    }

    /**
     * 获取某店铺下的所有商品数据
     * @param $wid int
     * @author Herry
     * @since 2018/01/26
     */
    public function getAllByWID($wid)
    {
        return $this->getList(['wid' => $wid, 'status' => ['<>', -1], 'is_default' => 0]);
    }

    /**
     * 模糊搜索ES获取分页pageHtml
     * @author hsz 20180529
     * @param $request
     * @param $data
     * @return mixed
     */
    public function getPageHtml($request, $data){
        $paginator = new LengthAwarePaginator([], $data['totalRows'], $data['pageRecorders'], null, ['path' => $request->url()]);
        $list = $paginator->appends($request->input());
        $pageHtml = $list->links();
        return $pageHtml;
    }

    /**
     * 设置商品默认分组
     * @author 吴晓平  <2018年08月23日>
     * @return [type] [description]
     */
    public function authSetDefaultGroup($wid)
    {
        $groupDefaultData = (new ProductGroupService())->getGroupIdByDefault($wid);
        if (!empty($groupDefaultData)) {
            $where['wid'] = $wid;
            $where['group_id'] = "";
            $where['status'] = 1;
            $this->model->where($where)->chunk(200,function($data) use ($groupDefaultData){
                foreach ($data as $key => $value) {
                    if ($value['cam_id'] <> 0 && empty($value['group_id'])) {
                        $this->update($value['id'],['group_id' => $groupDefaultData[1]['id']]);
                    }else {
                        if (empty($value['group_id'])) {
                            $this->update($value['id'],['group_id' => $groupDefaultData[0]['id']]);
                        }
                    }
                }
            });
        }
    }


    /**
     * 根据商品获取商品分组
     * @param $gid
     * @author 张永辉 2018年8月6日
     */
    public function getProductByGroupId($gids,$wid)
    {
        $query = $this->model->where('wid',$wid);
        $query->where(function ($query) use ($gids){
            foreach ($gids as $val){
                $query->orWhereRaw("find_in_set($val,group_id)");
            }
        });
        $res = $query->get(['id','title','price','img'])->toArray();
        return $res;
    }

    /**
     * 批量设置商品通用接口
     * @param array $input 修改的字段
     * @return bool
     * @author 许立 2018年11月16日
     */
    public function batchEdit($input)
    {
        $update = [];
        // 是否开启积分
        isset($input['is_point']) && $update['is_point'] = (int) $input['is_point'];
        if ($update) {
            // 更新数据库
            $this->model->whereIn('id', $input['ids'])->update($update);

            // 更新redis
            $productRedis = new ProductRedis();
            foreach ($input['ids'] as $id) {
                $update['id'] = $id;
                $productRedis->updateRow($update);
            }
        }

        return true;
    }

    /**
     * 根据商品编码获取对应的商品
     * @param $wid 店铺id
     * @param $title 搜索内容
     * @author 何书哲 2019年03月11日
     * @update 何书哲 2019年03月12日 商品编码全匹配
     */
    public function getProductIdsByTitle($wid, $title)
    {
        //有规格的商品对应ds_product_sku表code
        //无规格商品对应ds_product表goods_no
        $skuService = new ProductSkuService();

        //获取有规格的商品集合
        $allProductIds = $this->model->where('wid', $wid)->pluck('id')->toArray();
        $skuIds = $skuService->model->where('code', $title)->whereIn('pid', $allProductIds)->pluck('pid')->toArray();

        //获取无规格的商品集合
        $unSkuIds = $this->model->where('wid', $wid)->where('goods_no', $title)->pluck('id')->toArray();

        return array_collapse([$skuIds, $unSkuIds]);
    }

    /**
     * 获取商品编码
     * @param $product_id 商品id
     * @param $product_prop_id 商品规格id
     * @return string
     * @author 何书哲 2019年04月18日
     */
    public function getProductCode($product_id, $product_prop_id)
    {
        $skuService = new ProductSkuService();

        //有规格商品
        if ($product_prop_id) {
            $sku = $skuService->model->find($product_prop_id);
            return $sku ? $sku->code : '';
        }

        //无规格商品
        $product = $this->model->find($product_id);
        return $product ? $product['goods_no'] : '';
    }

}
