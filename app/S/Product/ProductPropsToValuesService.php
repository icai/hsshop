<?php
namespace App\S\Product;

use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductPropsToValues;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Model\Product;
use App\Model\ProductPropsToValues as PropsToValuesModel;
use App\Model\ProductSku;
use App\Model\ProductSku as SkuModel;
use App\S\Member\MemberCardService;
use App\S\S;

/**
 * 快递
 */
class ProductPropsToValuesService extends S
{
    public function __construct()
    {
        parent::__construct('ProductPropsToValues');
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

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ProductPropsToValues();
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
     * 根据商品ID格式化属性列表
     * @param $pid
     */
    public function getSkuList($pid)
    {
        //处理属性
        $list = $this->getListByProductID($pid);

        if (empty($list)) {
            return [
                'props' => [],
                'stocks' => []
            ];
        }

        //获取属性标题
        $propIds = array_column($list, 'prop_id');
        $list = (new ProductPropsService())->addPropTitleToProps($propIds, $list);

        //获取属性值标题
        $valueIds = array_column($list, 'value_id');
        $list = (new ProductPropValuesService())->addValueTitleToProps($valueIds, $list);

        //封装格式
        $list = $this->formatProps($list);

        //处理库存
        $skuList = (new ProductSkuService())->getListByProductID($pid);

        //获取属性标题
        $skuList = (new ProductSkuService())->addTitleToSku($propIds, $valueIds, $skuList);

        //stocks遍历加上img
        $skuList = $this->addSkuImg($skuList, $list[0]);

        return [
            'props' => $list,
            'stocks' => $skuList
        ];
    }

    /**
     * 根据商品ID获取属性列表
     */
    public function getListByProductID($pid)
    {
        //获取规格id列表
        $propModel = new PropsToValuesModel();
        //属性和属性值按照sort排序
        if (is_array($pid)){
            $propIds = $propModel->select('id')->whereIn('pid', $pid)->orderBy('prop_sort')->orderBy('value_sort')->pluck('id')->toArray();
        }else{
            $propIds = $propModel->select('id')->where('pid', $pid)->orderBy('prop_sort')->orderBy('value_sort')->pluck('id')->toArray();
        }
        $propRedis = new ProductPropsToValues();
        $redisArr = $propRedis->getArr($propIds);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($propIds as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $propModel->whereIn('id', $queryFromDB)->get()->toArray();
            $propRedis->setArr($dataNotInRedis);
        }

        return array_merge($redisArr, $dataNotInRedis);
    }

    /**
     * 组装属性列表格式 返回前端
     * @param $list
     * @return array
     */
    private function formatProps($list)
    {
        $return = [];
        $propArr = [];
        $valueArr = [];
        $currentPropID = 0;
        foreach ($list as $prop) {
            //下一个属性 重置参数
            if (!empty($propArr) && $currentPropID != $prop['prop_id']) {
                $return[] = [
                    'props' => $propArr,
                    'values' => $valueArr
                ];
                $propArr = [];
                $valueArr = [];
            }

            //属性
            $currentPropID = $prop['prop_id'];
            $propArr['id'] = $prop['prop_id'];
            $propArr['title'] = $prop['prop_title'];
            $propArr['show_img'] = $prop['prop_is_img'];
            $propArr['sort'] = $prop['prop_sort'];
            //属性值数组
            $value = [];
            $value['id'] = $prop['value_id'];
            $value['title'] = $prop['title'];
            $value['img'] = $prop['value_img'];
            $value['sort'] = $prop['value_sort'];
            $valueArr[] = $value;
        }

        $return[] = [
            'props' => $propArr,
            'values' => $valueArr
        ];

        return $return;
    }

    /**
     * 获取商品会员价格记录
     * @author hsz 2018/6/19
     * @param array $params 参数数组
     * @param int $wid 店铺id
     * @return array
     * @update 许立 2019年11月22日 13:41:44 增加非会员是否显示会员价参数
     */
    public function getPropMemLevel($params = array(), $wid = 0)
    {
        $result = [
            'status'    => 1,
            'message'   => '获取成功',
            'data'      => [],
        ];
        $return_data = [];
        $product_id = $params['product_id'];
        $product = (new ProductService())->getDetail($product_id);
        if(!$product){
            $result['status'] = -1;
            $result['message'] = '您要查询的商品不存在';
        }
        $vip_discount_way = $prop1 = $prop2 = $prop3 = '';
        //获取该店铺的所有的会员卡等级（包含id和title字段）
        $memberCard = (new MemberCardService())->model->where(['wid'=>$wid, 'state'=>1])->orderBy('card_rank', 'DESC')->pluck('title', 'id');
        if (!$memberCard) {
            $result['status'] = -2;
            $result['message'] = '暂未设置会员卡';
        }
        //查询该店铺下对应的所有属性
        $product_props = $this->getSkuList($product_id);
        if ($product_props['stocks']) { //有规格商品，从ds_product_props_to_values数据表获取
            foreach ($product_props['stocks'] as $prop) {
                $prop1 = $prop['k1'];
                $prop2 = $prop['k2'];
                $prop3 = $prop['k3'];
                $vip_discount_way = $prop['vip_discount_way'];
                //属性id 属性值1 属性值2 属性值3 商品售价
                $temp_values_arr = [$prop['id'], $prop['v1'], $prop['v2'], $prop['v3'], $prop['price']];
                $is_config_json = !empty($prop['vip_card_price_json']) ? json_decode($prop['vip_card_price_json'],true): [];
                foreach ($memberCard as $key => $val) {//添加会员卡对应的等级id和title
                    $temp_values_arr[] = $key; //等级id
                    $temp_values_arr[] = isset($is_config_json[$key]) ? $is_config_json[$key] : 0.00; //等级对应的价格
                }
                $return_data['prop_level_values'][] = $temp_values_arr;
                unset($temp_values_arr);
            }
        } else { //无规格商品，从ds_product数据表获取
            $temp_values_arr = [0, '', '', '', $product['price']];
            $is_config_json = !empty($product['vip_card_price_json']) ? json_decode($product['vip_card_price_json'],true) : [];
            foreach($memberCard as $key => $val){
                $temp_values_arr[] = $key;  // 等级id
                $temp_values_arr[] = isset($is_config_json[$key]) ? $is_config_json[$key] : 0.00; // 等级对应的价格
            }
            $vip_discount_way = $product['vip_discount_way'];
            $return_data['prop_level_values'][] = $temp_values_arr;
            unset($temp_values_arr);
        }
        //拼接title数组
        $title_arr = ['prop_id', $prop1, $prop2, $prop3, '正常售价'];
        foreach($memberCard as $level){
            $title_arr[] = 'vip_id';
            $title_arr[] = $level;
        }
        //返回标题数组 属性数组 优惠方式 商品标题
        $return_data['prop_level_title'] = $title_arr;
        $return_data['vip_discount_way'] = $vip_discount_way;
        $return_data['title'] = $params['title'] ?? '';
        $return_data['is_show_vip_price'] = $product['is_show_vip_price'];
        $result['data'] = $return_data;
        return $result;
    }

    /**
     * 设置商品会员价格
     * @param array $params
     * @param int $wid
     * @return array
     * @update 许立 2019年11月22日 13:41:44 增加非会员是否显示会员价参数
     */
    public function setPropMemLevel($params = array(), $wid = 0){
        $result = [
            'status'    => 1,
            'message'   => '设置成功',
        ];

        //获取商品信息
        $productId = $params['productId'];
        $product = (new ProductService())->getDetail($productId);
        if (empty($product)) {
            $result['status'] = 0;
            $result['message'] = '商品不存在';
            return $result;
        }

        $updateData = [];
        $prop_values = $params['prop_values'];
        if ($prop_values[0]['prop_id']) { //有规格
            foreach($prop_values as $prop_val){
                $update_data['vip_discount_way'] = $params['vip_discount_way'];
                $vipPrice = $this->keyTval($prop_val['prop_values'],$prop_val['prop_id']);
                $update_data['vip_card_price_json'] = $vipPrice;
                //先更新规格表数据库
                ProductSku::where('id', $prop_val['prop_id'])->update($update_data);
                //再更新商品主表redis
                $propRedis = new SkuRedis();
                $update_data['id'] = $prop_val['prop_id'];
                $propRedis->updateRow($update_data);
                unset($update_data);
            }
        } else {
            // 无规格
            $updateData = [
                'vip_discount_way' => $params['vip_discount_way'],
                'vip_card_price_json' => $this->keyTval($prop_values[0]['prop_values'], 0, $product['price'])
            ];
        }

        // 设置非会员是否显示会员价
        $updateData['is_show_vip_price'] = $params['is_show_vip_price'];
        Product::where('id', $productId)->update($updateData);
        $updateData['id'] = $productId;
        $productRedis = new ProductRedis();
        $productRedis->updateRow($updateData);

        return $result;
    }

    /*
     * 将逗号分隔的数据 转成键值对
     */
    public function keyTval($prop_string = '',$prop_id = 0, $productPrice = 0){
        /*if(!$prop_id){
            error('产品规格不存在！');
        }*/
        if(!$prop_string){
            return '';
        }
        if ($prop_id) {
            $prop = (new ProductSkuService())->getSkuDetail($prop_id);
            if(!$prop){
                error('产品规格不存在！');
            }
        }

        $values = explode(',',$prop_string);
        $keys = array();
        $vals = array();
        $count = 1;
        foreach($values as $pop){
            if($count % 2 == 0){
                //当金额大于等于1 去掉左边的0
                if ($pop > 0 && $pop < 0.01) {
                    error('价格最多设置两位小数');
                }
                $vals[] = $pop >= 1 ? ltrim($pop, '0') : $pop;
            }else{
                $keys[] = $pop;
            }
            $count++;
        }
        $prop_data = array();
        foreach($keys as $k => $v){
            #验证价格
            # 1. 减价 减价幅度 或 指定价格 不能大于原价  且不能小于 0
            $price = $productPrice ? $productPrice : $prop['price'];
            if($vals[$k] > $price){
                #echo '现价：'.$vals[$k]. '. 原价' .$prop['price'];
                error('减价幅度或指定价格不能大于原价');
            }else if($vals[$k] < 0){
                error('减价幅度或指定价格不能小于0');
            }
            $prop_data[$keys[$k]] = $vals[$k];
        }
        return json_encode($prop_data);
    }

    /**
     * 给库存列表加入sku图片
     */
    private function addSkuImg($skuList, $propList)
    {
        //prop里组装img
        $imgArr = [];
        foreach ($propList['values'] as $v) {
            $imgArr[$v['id']] = $v['img'];
        }

        //遍历赋值img
        foreach ($skuList as $k => $sku) {
            $sku['img'] = $imgArr[$sku['v1_id']]??'';
            $skuList[$k] = $sku;
        }

        return $skuList;
    }

    /**
     * 复制某商品的规格
     * @param $id int 商品ID
     */
    public function copySkuByProductID($oldID, $newID)
    {
        //先复制属性关联关系
        list($props) = $this->listWithoutPage(['pid' => $oldID], 'id', 'asc');
        if ($props['data']) {
            foreach ($props['data'] as $prop) {
                unset($prop['id'], $prop['created_at'], $prop['updated_at']);
                $prop['pid'] = $newID;
                $prop['deleted_at'] = null;
                PropsToValuesModel::insertGetId($prop);
            }

            //再复制库存信息
            list($sku) = (new ProductSkuService())->listWithoutPage(['pid' => $oldID], 'id', 'asc');
            if ($sku['data']) {
                foreach ($sku['data'] as $v) {
                    unset($v['id'], $v['created_at'], $v['updated_at']);
                    $v['pid'] = $newID;
                    $v['deleted_at'] = null;
                    SkuModel::insertGetId($v);
                }
            }
        }
    }
}