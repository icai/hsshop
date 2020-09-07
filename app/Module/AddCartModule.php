<?php
/**
  加入购物车
  TODO
  1、购物车 service重写
  (new CartService())->init('mid',session('mid'))
  2、应该调用service  此处调用的是model
  SeckillSku
 */

namespace App\Module;

use App\Jobs\SendAdvanceSubMsg;
use App\Module\GroupsRuleModule;
use App\Module\SeckillModule;
use App\S\Groups\GroupsService;
use App\S\Product\ProductSkuService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use ProductService;
use App\S\Market\SeckillService;
use App\Model\SeckillSku;
use App\S\Groups\GroupsDetailService;

use App\Services\Shop\CartService;
class AddCartModule
{
    protected $productData = null;
    protected $propData = null;

    public $request;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->groupsRuleModule = new GroupsRuleModule();
        $this->request = app('request');
    }

    private function _checkGroupsId($mid , $wid)
    {
        $request   = app('request');
        $rule_id   = $request->input('rule_id');
        $groupsId = $request->input('groups_id');

        if ($rule_id) //开团加入购物车
        {
            $result = $this->groupsRuleModule->createGroups($rule_id);
            if ($result['success'] != 1)
                return $this->returnErr($result['message']);

            return $result['data'];
        }

        if ($groupsId) //参加团购加入购物车
        {
            $groupsService = new GroupsService();
            $groupsData    = $groupsService->getRowById($groupsId);
            if (!$groupsData || $groupsData['status'] == 0)
                return $this->returnErr('团不存在');

            if ($groupsData['status'] != 1)
                return $this->returnErr('亲!该团已结束,赶紧再去开一个团吧！');

            if ((new GroupsDetailService())->isExist($mid, $groupsId))
                return $this->returnErr('你已参加过该团');

            return $groupsId;
        }

        return 0;
    }

    private function _checkSeckillId($seckillId)
    {
        if ($seckillId > 0) {
            //秒杀活动是否未开始或结束或失效
            if (!(new SeckillService())->checkValidity($seckillId))
                return $this->returnErr('活动不在进行中或已失效');
        }
        return true;
    }

    public function returnErr($msg, $success = 0)
    {
        return ['success' => $success, 'message' => $msg];
    }

    private function _checkStock($mid, $type , $num)
    {
        $stock = $this->productData['stock']; //库存
        $sku_stock = 0;
        if(isset($this->propData['stock_num']))
            $sku_stock = $this->propData['stock_num'];

        switch ($type['type']) {
            case 'normal':
            case 'groups':
                if ($num > $stock)
                    return $this->returnErr('购买数量不能大于库存');
                if ($stock <= 0)
                    return $this->returnErr('已售罄');

                if (!empty($this->propData))
                {
                    if($sku_stock <= 0)
                        return $this->returnErr('已售罄');

                    if ($num > $sku_stock)
                        return $this->returnErr('购买数量不能大于库存');
                }

                break;
            case 'seckill':
                $seckillModule = new SeckillModule();
                $limitNum = $seckillModule->isLimited($type['id'], $mid, $num);
                if ($limitNum)
                    return $this->returnErr('该商品每人限购' . $limitNum . '件');

                if (isset($this->propData['id'])) {
                    if ($seckillModule->canSeckill($type['id'], $this->propData['id'], $num) === false) {
                        return $this->returnErr('秒杀库存不足');
                    }
                }
                //Herry 上一步已经判断秒杀库存了 而且秒杀库存是独立的
                /*if (!empty($this->propData) && $sku_stock <= 0)
                    return $this->returnErr('已售罄');*/
                break;
            default:
                # code...
                break;
        }
        return true;
    }

    private function _recountPrice($mid, $type,$wid)
    {
        $price     = isset($this->propData['price']) ? $this->propData['price'] : $this->productData['price'];

        //会员卡   如果会员卡过期 处理
        $default_card = \MemberCardRecordService::useCard($mid,$wid);
        $card_id = isset($default_card['data']['info']['card_id']) ? $default_card['data']['info']['card_id'] : '';

        if ($card_id && $type['type'] == 'normal') {
            if ($this->propData ) {  //非团购
                $reData = ProductService::reSetSkuPrice($this->propData,$mid,$wid);
            }
            if (empty($this->propData)) { //普通 非规格商品
                $reData = ProductService::reSetNoSkuPrice($this->productData,$mid,$wid);
            }
            $price  = $reData['price'];
        }

        return $price;
    }

    private function _checkInsert($mid, $wid, $type, $propid)
    {
        $where = [
            'wid'           => $wid,
            'mid'           => $mid,
            'product_id'   => $this->productData['id'],
            'status'       => 1,
        ];
        $groupsId = $seckillId = 0;

        if ($type['type'] == 'seckill'){
            $where['seckill_id'] = $type['id'];
        }else {
            $where['seckill_id'] = 0;
        }

        if ($type['type'] == 'groups') {
            $where['groups_id'] = $type['id'];
        }else{
            $where['groups_id'] = 0;
        }

        if (!empty($this->propData)) {
            $where['prop_id'] = $propid;
            $where['is_prop'] = 1;
        } else {
            $where['is_prop'] = 0;
        }
        list($resData) = $this->cartService->init('mid',$mid)->where($where)->getList(false);
        return $resData;
    }

    private function _getType($groupsId, $seckillId)
    {
        $type['type'] = 'normal';
        if ($groupsId > 0) {
            $type['type'] = 'groups';
            $type['id']   = $groupsId;
        }
        if ($seckillId > 0) {
            $type['type'] = 'seckill';
            $type['id']   = $seckillId;
        }
        return $type;
    }

    private function _initInsertData($mid, $wid, $type, $price, $num, $content, $propid)
    {
        $insertData = [
            'wid'           => $wid,
            'mid'           => $mid,
            'product_id'    => $this->productData['id'],
            'type'          => $this->productData['type'],
            'title'         => $this->productData['title'],
            'img'           => $this->productData['img'],
            'price'         => $price,
            'oprice'        => $this->productData['oprice'],
            'num'           => $num,
            'content'       => $content,
        ];

        $insertData['is_prop'] = 0;

        $groupsId = $seckillId = 0;

        if ($type['type'] == 'seckill')
            $insertData['seckill_id'] = $type['id'];

        if ($type['type'] == 'groups')
            $insertData['groups_id'] = $type['id'];

        if (!empty($this->productData['cam_id'])) {
            $insertData['is_show'] = 0;
        }

        if (!empty($this->propData)) {
            $insertData['is_prop'] = 1;
            $insertData['prop1'] = $this->propData['k1'];
            $insertData['prop2'] = $this->propData['k2'];
            $insertData['prop3'] = $this->propData['k3'];
            $insertData['prop_value1'] = $this->propData['v1'];
            $insertData['prop_value2'] = $this->propData['v2'];
            $insertData['prop_value3'] = $this->propData['v3'];
            $insertData['price'] = $price;
            $insertData['market_price'] = $this->propData['price'];
            $insertData['activity_price'] = $this->propData['price'];
            $insertData['prop_img'] = $this->propData['img'];
            $insertData['prop_id'] = $propid;
        }
        return $insertData;
    }

    // @update 吴晓平 2018年08月02日 添加是否为自提商品字段
    private function _insertData($mid, $wid, $type, $price, $num, $content, $propid, $tag,$is_ziti=0)
    {
        //检查是新建购 还是修改
        $resData   = $this->_checkInsert($mid, $wid, $type, $propid);
        $insertData = $this->_initInsertData($mid, $wid, $type, $price, $num, $content, $propid);
        $insertData['is_ziti'] = $is_ziti; //添加是否为自提商品字段 add by 吴晓平 2018.08.02
        if ($resData['data']){
            if ($tag == 1 || $type['type'] == 'seckill' || $type['type'] == 'groups')
                $insertData['num'] = $insertData['num'];
            else
                $insertData['num'] += $resData['data'][0]['num'];//如果多商品处理 todo

            //再次检查数量
            if ($insertData['num'] > $num) {
                $checkStock = $this->_checkStock($mid, $type, $insertData['num']);
                if ($checkStock !== true)
                    return $this->returnErr($checkStock['message']);
            }

            $res = $this->cartService->init('mid',$mid)->where(['id'=>$resData['data'][0]['id']])->update($insertData,false);
            if ($res){
                $result['success'] = 1;
                $insertData['id'] = $resData['data'][0]['id'];
                $result['data']    = $insertData;
                return $result;
            }else{
                return $this->returnErr('加入购物车失败');
            }
        }else{
            $id = $this->cartService->init('mid',$mid)->add($insertData,false);
            if ($id){
                $result['success'] = 1;
                $insertData['id'] = $id;
                $result['data'] = $insertData;
            }
            return $result;
        }
    }

    public function addCart($mid, $wid)
    {
        $pid       = $this->request->input('id');
        $num       = $this->request->input('num');
        $content   = $this->request->input('content');
        $propid    = $this->request->input('propid');
        $tag       = $this->request->input('tag');  //加入购物车 or 直接购买
        $is_ziti   = $this->request->input('is_ziti') ?? 0; //是否为自提商品 add by 吴晓平 2018年08月02日

        //检测groupsId
        $checkGroupsId = $this->_checkGroupsId($mid, $wid);
        if (is_array($checkGroupsId))
            return $checkGroupsId;
        else
            $groupsId = $checkGroupsId;

        //检测seckillId
        $seckillId = $this->request->input('seckillID');
        $checkSeckillId = $this->_checkSeckillId($seckillId);
        if ($checkSeckillId !== true)
            return $checkSeckillId;

        //获取基本数据==================================
        $this->productData = ProductService::getDetail($pid);
        if (!$this->productData)
            return $this->returnErr('商品不存在');

        // 如果是预售商品，提前5分钟发送订阅模板消息 吴晓平 2019年12月26日 16:08:25
        if ($this->productData['sale_time_flag'] == 2) {
            // 提交5分钟发送订阅消息模板（剩于时间）
            $doSeconds = Carbon::now()->diffInSeconds(Carbon::parse($this->productData['sale_time'])->subMinutes(5), false);
            if ($doSeconds > 0) {
                // 添加购物车成功后，把对应的key加入到redis(用于监听后续发送预售商品的订阅模板消息)
                $data = [
                    'wid' => $wid,
                    'mid' => $mid,
                    'pid' => $pid,
                    'time' => $this->productData['sale_time']
                ];

                // 发送预售商品订阅模板消息(延时发送)
                dispatch((new SendAdvanceSubMsg($data))->delay($doSeconds));
            }
        }

        $sku_flag  = $this->productData['sku_flag'];
        if ($sku_flag){
            if (empty($propid))
                return $this->returnErr('请传递规格参数');
            $propService = new ProductSkuService();
            $this->propData = $propService->getSkuDetail($propid);
            if (!$this->propData)
                return $this->returnErr('规格不存在');
        }

        $type = $this->_getType($groupsId, $seckillId);

        //检查库存======================================
        $checkStock = $this->_checkStock($mid, $type, $num);
        if ($checkStock !== true)
            return $checkStock;

        //重构价格=====================================
        $price = $this->_recountPrice($mid, $type,$wid);

        //入库========================================
        $result = $this->_insertData($mid, $wid, $type, $price, $num, $content, $propid, $tag, $is_ziti);
        if ($result['success'] == 1) {
            $result['data']['cartNum'] = $this->cartService->cartNum($mid,$wid);
        }
        return $result;
    }

    //购物车不存在秒杀
    public function cartShowPrice($mid, $wid, $pid,  $propid = 0, $groupsId = 0, $seckillId = 0)
    {
        //获取基本数据==================================
        $this->productData = ProductService::getDetail($pid);
        if (!$this->productData)
            return $this->returnErr('商品不存在');

        $sku_flag  = $this->productData['sku_flag'];
        if ($sku_flag){
            if (empty($propid))
                return $this->returnErr('请传递规格参数');
            $propService = new ProductSkuService();
            $this->propData = $propService->getSkuDetail($propid);
            if (!$this->propData)
                return $this->returnErr('规格不存在');
        }

        $type = $this->_getType($groupsId, $seckillId);

        //重构价格=====================================
        $price = $this->_recountPrice($mid, $type,$wid);

        return $price;
    }
}
