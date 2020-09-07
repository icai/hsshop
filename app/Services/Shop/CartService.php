<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/23  10:36
 * DESC
 */

namespace App\Services\Shop;


use App\Model\Cart;
use App\Model\SeckillSku;
use App\Model\Trolley;
use App\Module\AddCartModule;
use App\Module\DiscountModule;
use App\Module\SeckillModule;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Market\SeckillService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductSkuService;
use App\Services\Service;
use Illuminate\Http\Request;
use ProductService;
use WeixinService;
use App\S\Weixin\ShopService;


class CartService extends Service
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */

        $this->field = ['id','wid','product_id','type','title','img','price','oprice','is_prop','prop_id','prop1','prop2','prop3','prop_value1','prop_value2','prop_value3','market_price','activity_price','prop_img','num','content','groups_id','status','created_at','updated_at', 'seckill_id'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Cart(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703241535
     * @desc 加入购物车
     * @param $pid
     * @param null $propid
     */
    public function  addCart($pid,$num,$propid=null,$content='',$groups_id=0, $seckillID = 0)
    {
        $result = [
            'success' => 0,
            'message' => '',
        ];

        $propService = new ProductSkuService();
        $productData = ProductService::getDetail($pid);

        if (!$productData){
            $result['message'] = '商品不存在';
            return $result;
        }
        //判断加入购物车数量是否大于库存
        if ($seckillID == 0 && $num>$productData['stock']){
            $result['message'] = '购买数量不能大于库存';
            return $result;
        }

        if ($seckillID) {
            //秒杀活动是否未开始或结束或失效
            if (!(new SeckillService())->checkValidity($seckillID)) {
                $result['message'] = '活动不在进行中或已失效';
                return $result;
            }

            //用户秒杀限额检查
            $seckillModule = new SeckillModule();
            $limitNum = $seckillModule->isLimited($seckillID, session('mid'), $num);
            if ($limitNum) {
                $result['message'] = '该商品每人限购' . $limitNum . '件';
                return $result;
            }

            //秒杀库存检查
            if ($seckillModule->canSeckill($seckillID, $propid, $num) === false) {
                $result['message'] = '秒杀库存不足';
                return $result;
            }
        }

        $trolleyData = [
            'wid'           => session('wid'),
            'mid'           => session('mid'),
            'product_id'   => $pid,
            'type'          => $productData['type'],
            'title'         => $productData['title'],
            'img'           => $productData['img'],
            'price'         => $productData['price'],
            'oprice'        => $productData['oprice'],
            'num'           => $num,
            'content'       => $content,
            'groups_id'     => $groups_id,
            'seckill_id'    => $seckillID, //秒杀ID
        ];

        /************************************/
        /**
         * 重构价格
         * add by meiJie
         */
        $mid = session('mid');
        $default_card = \MemberCardRecordService::useCard($mid);
        if($productData['sku_flag'] == 0 && isset($default_card['data']['info']['card_id']) && $groups_id == 0)
        {
            $reData = ProductService::reSetNoSkuPrice($productData,$mid);
            $trolleyData['price'] = $reData['price'];
        }
        /************************************/

        //判断商品在购物车中是否存在条件组装
        $where = [
            'wid'           => session('wid'),
            'mid'           => session('mid'),
            'product_id'   => $pid,
            'status'       => 1,
            'groups_id'    => $groups_id,
            'seckill_id'   => $seckillID,
        ];
        //查看是否开启规格
        if ($productData['sku_flag']){
            if (is_null($propid)){
                $result['message'] = '请传递规格参数';
                return $result;
            }
            $propData = $propService->getSkuDetail($propid);
            //查看规格是否可用
            if (!$propData){
                $result['message'] = '规格不存在';
                return $result;
            }
            if ($seckillID == 0 && $propData['stock_num']<=0){
                $result['message'] = '已售罄';
                return $result;
            }
            /**********************************************************/
            /**
             * add by meiJie
             */
            if(isset($default_card['data']['info']['card_id']) &&  $groups_id == 0 && $seckillID == 0)
            {
                $reData = ProductService::reSetSkuPrice($propData,$mid);
                $propData['price']  = $reData['price'];
            }

            /**********************************************************/
            $trolleyData['is_prop'] = 1;
            $trolleyData['prop1'] = $propData['k1'];
            $trolleyData['prop2'] = $propData['k2'];
            $trolleyData['prop3'] = $propData['k3'];
            $trolleyData['prop_value1'] = $propData['v1'];
            $trolleyData['prop_value2'] = $propData['v2'];
            $trolleyData['prop_value3'] = $propData['v3'];
            $trolleyData['price'] = $propData['price'];
            $trolleyData['market_price'] = $propData['price'];
            $trolleyData['activity_price'] = $propData['price'];
            $trolleyData['prop_img'] = $propData['img'];
            $trolleyData['prop_id'] = $propid;
            $where['prop_id'] = $propid;
            $where['is_prop'] = 1;
        }else{
            //判断商品是否售罄
            if ($seckillID == 0 && $productData['stock']<=0){
                $result['message'] = '已售罄';
                return $result;
            }

            //判断库存是否还足够这次购买
            if ($seckillID == 0 && $productData['stock'] < $num){
                $result['message'] = '库存不足';
                return $result;
            }

            $trolleyData['is_prop'] = 0;
            $where['is_prop'] = 0;
        }

        //判断商品购物车中是否存在
        list($resData) = $this->init('mid',session('mid'))->init()->where($where)->getList(false);

        //秒杀商品 使用秒杀价格 Herry
        if ($seckillID) {
            $seckillSku = SeckillSku::select('seckill_price')
                ->where('seckill_id', $seckillID)
                ->where('sku_id', $propid)
                ->first();
            if (!empty($seckillSku)) {
                $seckillSku = $seckillSku->toArray();
                $trolleyData['price'] = $seckillSku['seckill_price'];
            }
        }

        if ($resData['data']){
            if ((isset($_REQUEST['tag']) && $_REQUEST['tag'] == 1) || $groups_id != 0 || $seckillID != 0){
                $trolleyData['num'] = $trolleyData['num'];
            }else{
                $trolleyData['num'] = $resData['data'][0]['num']+$trolleyData['num'];
            }

            //判断购物车库存是否大于商品总库存
            if ($trolleyData['num']>$productData['stock']){
                $result['message'] = '购物车数量已超过库存';
                return $result;
            }
            $res = $this->init('mid',session('mid'))->init()->where(['id'=>$resData['data'][0]['id']])->update($trolleyData,false);
            if ($res){
                $result['success'] = 1;
                $trolleyData['id'] = $resData['data'][0]['id'];
                $result['data'] = $trolleyData;
                return $result;
            }else{
                $result['message'] = '加入购物车失败';
                return $result;
            }
        }else{
            $id = $this->init('mid',session('mid'))->add($trolleyData,false);
            if ($id){
                $result['success'] = 1;
                $trolleyData['id'] = $id;
                $result['data'] = $trolleyData;
            }
            return $result;
        }

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703231059
     * @desc 根据用户ID获取当前店铺的购物车列表
     * @param null $memberId
     * @update 吴晓平 2018年08月02日 添加is_ziti字段过滤掉自提商品在购物车中显示
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getCart($memberId,$wid)
    {
        $trolleyData = $this->init('mid',$memberId)->where(['wid'=>$wid,'groups_id'=>0,'seckill_id'=>0,'is_ziti'=>0,'is_show'=>1])->getList(false);
        if ($trolleyData[0]['data']){
            $this->dealCart($trolleyData[0]['data'],$memberId,$wid);
        }

        //按照店铺分组
//        $trolleyData[0]['data'] = $this->groupCart($trolleyData[0]['data']);
        if ($trolleyData[0]['data']){
            $shopService = new ShopService();
            $trolleyData[0]['shop'] = $shopService->getRowById($wid); //WeixinService::init()->getinfo($wid);
        }
        return $trolleyData;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703231148
     * @desc 处理购物车数据
     * @param $data
     * @flag ,0:商品下架，1：正常，-1;商品已删除,,3:规格发生变化,4;//已售罄, 5:价格面议
     * @update 陈文豪 2018年8月16日 修复会员卡打折显示
     * @update 许立 2018年09月11日 代码优化
     */
    public function dealCart(&$data,$mid,$wid)
    {
        $default_card = \MemberCardRecordService::useCard($mid,$wid);
        if (!$data){
            return ;
        }
        $pids = array_unique(array_column($data,'product_id'));
        $productData = array_column(ProductService::getListById($pids), null, 'id');

        $skuids = array_unique(array_column($data,'prop_id'));
        $skuDatas = $this->getSkuByIds($skuids);
        foreach ($data as $key=>&$val)
        {
            $val['flag'] = 1;
            /*如果如果规格有图片显示规格图片否者显示商品图片*/
            if ($val['prop_img']){
                $val['img'] = $val['prop_img'];
            }else{
                $val['img'] = $val['img'];
            }
            //获取商品的信息
            $product = $productData[$val['product_id']];
            //判断商品是否删除
            if (!$product || $product['status'] == -1){
                $val['flag'] = -1;
                continue;
            }
            //判断商品是否下架
            if ($product['status'] == 0){
                $val['flag'] = 0;
                continue;
            }
            //是否开启规格发生变化
            if ($val['is_prop'] != $product['sku_flag']){
                $val['flag'] = 3;
                continue;
            }

            //商品改成价格面议后 在购物车里算失效 Herry 20180423
            if ($product['is_price_negotiable']){
                $val['flag'] = 5;
                continue;
            }

            if ($val['is_prop'] == 1){
                $prop = $skuDatas[$val['prop_id']]??'';
                //当sku发生变化时
                if (!$prop || $prop['k1'] != $val['prop1'] || $prop['k2'] != $val['prop2'] || $prop['k3'] != $val['prop3'] || $prop['v1'] != $val['prop_value1'] || $prop['v2'] != $val['prop_value2'] || $prop['v3'] != $val['prop_value3']){
                    $val['flag'] = 3;
                    continue;
                }
                if ($prop['stock_num'] <= 0){
                    $val['flag'] = 4;
                }
                $prop['is_discount'] = $product['is_discount'];
                //当sku最小其买量大于购物车数量时，购物车数量改为最小起卖量
                /**************************************************/
                /**
                 * add by meiJie
                 */
                //重构价格
                $price = $prop['price'];
                if(isset($default_card['data']['info']['card_id'])) {
                    $prop['price'] = $this->reLoadPrice($price,$default_card,$prop);
                }

                /**************************************************/
                $val['price']   = $prop['price'];
                $val['oprice']  = $prop['price'];
            }else{
                if ($product['stock'] <= 0){
                    $val['flag'] = 4;
                    continue;
                }
                //当商品的最大购买量小于购物车数量时，购物车数量修改为最大购买量
                if ($product['quota'] !=0 && $product['quota'] < $val['num']){
                    $val['num'] = $product['quota'];
                }
                if(isset($default_card['data']['info']['card_id'])) {
                    $val['price'] = $this->reLoadPrice($val['price'],$default_card,$product);
                }
                $val['price'] = $product['price'];
                $val['oprice'] = $product['oprice'];
                if(isset($default_card['data']['info']['card_id'])) {
                    $val['price'] = $this->reLoadPrice($val['price'],$default_card,$product);
                }
                $val['buy_min'] = $product['buy_min'];
                $val['quota']   = $product['quota'];
            }
        }
        $this->is_valid($data);
    }


    /**
     * 重构价格
     * @param $price
     * @param $default_card
     * @author 张永辉 2018年8月03日
     * @update 陈文豪 2018年8月16日 修复会员卡打折显示
     */
    public function reLoadPrice($price,$default_card,$data){
        $lastPrice = $price;
        $cardInfo = $default_card['data'];
        $card_id = $cardInfo['info']['card_id'];
        if($cardInfo['info']['isDiscount'] == 1 && $data['is_discount'] == 1) {
            //如果为会员商品打折
            $lastPrice =  $cardInfo['info']['discount']* $price * 0.1;
        }
        $temp = json_decode($data['vip_card_price_json'],1);
        if($data['vip_discount_way'] == 1 && isset($temp[$card_id]) && $temp[$card_id] != 0) {
            $lastPrice =   $price - $temp[$card_id] ;
        }
        if($data['vip_discount_way'] == 2 && isset($temp[$card_id]) && $temp[$card_id] != 0) {
            $lastPrice =    $temp[$card_id] ;
        }
        $lastPrice  = sprintf('%.2f',$lastPrice );
        return $lastPrice;
    }

    /**
     * 批量获取sku值
     * @param $ids
     * @author 张永辉
     */
    public function getSkuByIds($ids)
    {
        $productSkuService = new ProductSkuService();
        //获取详情
        $rows = $productSkuService->getListById($ids);
        if (empty($rows)) {
            return [];
        }
        $pids = array_column($rows,'pid');
        //处理属性
        $list = (new ProductPropsToValuesService())->getListByProductID($pids);
        $propIds = array_column($list, 'prop_id');
        $valueIds = array_column($list, 'value_id');
        $res = $productSkuService->addTitleToSku($propIds, $valueIds, $rows);
        $result = [];
        foreach ($res as $val){
            $result[$val['id']] = $val;
        }
        return $result;
    }


    public function is_valid(&$data)
    {
        $valid = [];
        $invalid = [];
        foreach ($data as $val)
        {
            if ($val['flag'] == 1){
                $valid[] = $val;
            }else{
                $invalid[] = $val;
            }
        }

        $input = $this->request->input();
        $page = isset($input['page'])?$input['page']:1;
        $data = array_merge($valid,$invalid);
        $offset = ($page-1)*300;
        $data = array_slice($data,$offset,300);

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703241516
     * @desc 分组购物
     * @param $data
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function groupCart($data)
    {
        $result = [];
        $wids = [];
        foreach ($data as $val)
        {
            $result[$val['wid']]['data'][] = $val;
        }
        //$weixinService = new WeixinService();
        $shopService = new ShopService();
        foreach ($result as $key=>&$val)
        {
            $val['shop'] = $shopService->getRowById($key); //$weixinService->init()->getInfo($key);
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170504
     * @desc 获取当前店铺购物车的数量
     * @return mixed
     * @update 吴晓平 2018年08月02日 添加is_ziti字段过滤掉自提商品数量
     * @update 陈文豪 2018年08月09日 添加is_show字段过滤掉自提商品数量
     */
    public function cartNum($mid,$wid)
    {
        $where = [
            'mid'        => $mid,
            'wid'        => $wid,
            'groups_id'  => 0,
            'seckill_id' => 0,
            'is_ziti'    => 0,
            'is_show'    => 1,
        ];
        $res = $this->init('mid',$mid)->model->where($where)->count();
        return $res;
    }

}






















