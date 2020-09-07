<?php
namespace App\S\Product;

use App\Lib\Redis\ProductSku;
use App\Model\ProductPropsToValues;
use App\Model\ProductSku as SkuModel;
use App\Lib\Redis\ProductPropsToValues as ValueSku;
use App\S\S;
use DB;

/**
 * 快递
 */
class ProductSkuService extends S
{
    public function __construct()
    {
        parent::__construct('ProductSku');
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
     * 获取一条sku详情
     * @param $skuID
     */
    public function getSkuDetail($skuID)
    {
        //获取详情
        $row = $this->getRowById($skuID);
        if (empty($row)) {
            return [];
        }
        //处理属性
        $list = (new ProductPropsToValuesService())->getListByProductID($row['pid']);
        $propIds = array_column($list, 'prop_id');
        $valueIds = array_column($list, 'value_id');
        $return = $this->addTitleToSku($propIds, $valueIds, [$row]);
        $return = $return[0] ?? [];
        //获取规格图片
        $return['img'] = $this->getSkuImg($return['pid'], $return['k1_id'], $return['v1_id']);
        return $return;
    }

    /**
     * 根据主键获取详情
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $redis = new ProductSku();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result){
                return $result;
            }
            $result = $result->toArray();
            $redis->add($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ProductSku();
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
     * 根据商品ID获取属性列表
     */
    public function getListByProductID($pid)
    {
        //获取规格id列表
        $skuModel = new SkuModel();
        //属性和属性值按照sort排序
        $skuIds = $skuModel->select('id')->where('pid', $pid)->orderBy('id')->pluck('id')->toArray();
        $skuRedis = new ProductSku();
        $redisArr = $skuRedis->getArr($skuIds);
        $dataNotInRedis = [];

        //处理redis中不存在的数据
        $queryFromDB = [];
        foreach ($skuIds as $k => $id) {
            if (empty($redisArr[$k])) {
                $queryFromDB[] = $id;
                unset($redisArr[$k]);
            }
        }
        if (!empty($queryFromDB)) {
            $dataNotInRedis = $skuModel->whereIn('id', $queryFromDB)->get()->toArray();
            $skuRedis->setArr($dataNotInRedis);
        }

        $list = array_merge($redisArr, $dataNotInRedis);

        return $list;
    }

    /**
     * 添加属性和属性值名
     * @param $propIds
     * @param $valueIds
     * @param $skuList
     */
    public function addTitleToSku($propIds, $valueIds, $skuList)
    {
        list($propList) = (new ProductPropsService())->listWithoutPage(['id' => ['in', $propIds]]);
        list($valueList) = (new ProductPropValuesService())->listWithoutPage(['id' => ['in', $valueIds]]);

        $return = [];
        foreach ($skuList as $sku) {
            $stock = [];
            $stock['id'] = $sku['id'];
            $stock['pid'] = $sku['pid'];
            $stock['price'] = $sku['price'];
            $stock['stock_num'] = $sku['stock'];
            $stock['sold_num'] = $sku['sold_num'];
            $stock['code'] = $sku['code'];
            $stock['weight'] = $sku['weight'];
            $stock['vip_discount_way'] = $sku['vip_discount_way'];
            $stock['vip_card_price_json'] = $sku['vip_card_price_json'];
            $stock['is_show'] = $sku['is_show'];
            $stock['is_show1'] = $sku['is_show1'];
            $stock['rowspan0'] = $sku['rowspan0'];
            $stock['rowspan1'] = $sku['rowspan1'];
            //key_key = '1:2;2:4';
            $keyArr = explode(';', $sku['sku_key']);
            foreach ($keyArr as $k => $v) {
                //$v = '1:2' index = $k+1
                $key = explode(':', $v);
                //Kx_id
                $stock['k' . ($k + 1) . '_id'] = $key[0];
                //Kx
                foreach ($propList['data'] as $prop) {
                    if ($key[0] == $prop['id']) {
                        $stock['k' . ($k + 1)] = $prop['title'];
                    }
                }
                //Vx_id
                $stock['v' . ($k + 1) . '_id'] = $key[1] ?? '';
                //Vx
                foreach ($valueList['data'] as $value) {
                    if (($key[1] ?? '') == $value['id']) {
                        $stock['v' . ($k + 1)] = $value['title'];
                    }
                }
            }

            //剩余属性层级初始化
            for ($i = count($keyArr) + 1; $i < 4; $i++) {
                $stock['k' . $i . '_id'] = 0;
                $stock['k' . $i] = '';
                $stock['v' . $i . '_id'] = 0;
                $stock['v' . $i] = '';
            }

            $return[] = $stock;
        }

        return $return;
    }

    /**
     * 获取规格图片
     */
    private function getSkuImg($productID, $propID, $valueID)
    {
        $propValueRow = ProductPropsToValues::select('value_img')
            ->where('pid', $productID)
            ->where('prop_id', $propID)
            ->where('value_id', $valueID)
            ->first()
            ->toArray();

        return $propValueRow['value_img'] ?? '';
    }

    /**
     * 更新sku数据库和redis
     */
    public function update($skuID, $data)
    {
        SkuModel::where('id', $skuID)->update($data);
        $data['id'] = $skuID;
        (new ProductSku())->updateRow($data);
    }

    /**
     * 更新sku(编辑商品时)
     * @param $resetWeight bool 是否重置重量为0
     * @update 许立 2018年10月15日 属性图片检查
     * @update 许立 2019年01月17日 价格上限100万
     * @update 许立 2019年01月25日 价格改为1000万
     */
    public function updateSku($pid, $skuArr, $resetWeight = false)
    {
        //更新属性 图片
        $propValueService = new ProductPropsToValuesService();
        foreach ($skuArr['props'] as $v) {
            foreach ($v['values'] as $vv) {
                $propValueRow = $propValueService->model->select('id')
                    //->where('wid', $wid)
                    ->where('pid', $pid)
                    ->where('prop_id', $v['prop']['id'])
                    ->where('value_id', $vv['id'])
                    ->first();
                if (!empty($propValueRow->id)) {
                    //更新数据库
                    $isImg = 0;
                    $img = '';
                    if (!empty($v['prop']['show_img']) && !empty($vv['img'])) {
                        $isImg = 1;
                        $img = $vv['img'];
                    }
                    $updateData = ['prop_is_img' => $isImg, 'value_img' => $img];
                    $propValueService->model->where('id', $propValueRow->id)->update($updateData);
                    //更新redis
                    $updateData['id'] = $propValueRow->id;
                    (new ValueSku())->updateRow($updateData);
                }
            }
        }

        //更新库存
        foreach ($skuArr['stocks'] as $sku) {
            $newSku = [
                'price' => $sku['price'] >= 10000000 ? 9999999.99 : $sku['price'],
                'stock' => $sku['stock_num'],
                'code' => $sku['code'],
                'weight' => $resetWeight ? 0 : $sku['weight'],  //如果设置为统一运费 重量重置为0
                'sold_num' => $sku['sold_num'], //销量可以修改 20171226
                'rowspan0' => (empty($sku['rowspan0']) || $sku['rowspan0'] < 1) ? 1 : $sku['rowspan0'], //至少为1 为0火狐不兼容 20171227
                'rowspan1' => (empty($sku['rowspan1']) || $sku['rowspan1'] < 1) ? 1 : $sku['rowspan1'],
            ];
            $this->update($sku['id'], $newSku);
        }
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
            $redis = new ProductSku();
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
            $redis = new ProductSku();
            $redis->incr($id,$field,$num);
            return true;
        }else{
            return false;
        }
    }


    /**
     * 根据商品id获取商品的售价最高的sku
     * @param $pids  商品id
     * @return array
     * @author 张永辉  2018年10月8日
     */
    public function getMaxPriceSku($pids)
    {
        if (!$pids) {
            return [];
        }
        $sql = 'select MAX(`price`) as max_price,pid from `ds_product_sku` where `pid` in (' . implode(',', $pids) . ') and `ds_product_sku`.`deleted_at` is null group by `pid`';
        $res = DB::select($sql);
        $result = [];
        foreach ($res as $val){
            $result[$val->pid] = $val->max_price;
        }
        return $result;
    }


    /**
     * 批量获取商品sku
     * @param $pids
     * @author 张永辉 2018年10月30日
     */
    public function getSkuListByPids($pids)
    {
        $where = [
            'pid'   => ['in',$pids],
        ];
        $data = $this->getList($where,'', '', $orderBy = "price", $order = "desc");
        $result = [];
        foreach ($data as $val){
            $result[$val['pid']][] = $val;
        }
        return $result;
    }


}
