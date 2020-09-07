<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/27
 * Time: 11:22
 */

namespace App\Module;


use App\Events\NewUserEvent;
use App\Jobs\SendGroupsLog;
use App\Jobs\SendTakeAway;
use App\Jobs\SendTplMsg;
use App\Jobs\SubMsgPushJob;
use App\Lib\Redis\NewUserFlagRedis;
use App\Lib\Redis\RedisClient;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductImg;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Groups\GroupsDetailService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductService;
use App\S\Product\ProductSkuService;
use App\S\WXXCX\SubscribeMessagePushService;
use App\S\WXXCX\WXXCXSendTplService;
use App\Services\Order\OrderDetailService;
use App\Services\Order\OrderRefundService;
use App\Services\Permission\WeixinRoleService;
use Illuminate\Support\Facades\Event;
use OrderService;
use DB;
use MallModule as ProductStoreService;
use Redisx;
use MemberAddressService;
use App\S\Market\CommendDetailService;
use App\S\Market\CommendInfoService;
use App\Lib\Redis\GroupMemberRedis;

class GroupsRuleModule
{

    public $request;

    public function __construct()
    {
        $this->request = app('request');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 获取团购
     * @desc 获取团购规则
     * @param $where 搜索条件
     * @param  $orderBy 排序字段
     * @param $order 排序方式
     * @return  array 团购列表的二位数组
     * @update 何书哲 2018年7月10日 添加列表订单实付金额、成团订单数、成团人数
     */
    public function getRule($where = [], $orderBy = '', $order = '')
    {
        $time = date('Y-m-d H:i:s', time());
        $where['status'] = ['<>', -2];
        if ($status = $_REQUEST['status'] ?? 0) {
            switch ($status) {
                case 1:
                    $where['start_time'] = ['>=', $time];
                    break;
                case 2:
                    $where['start_time'] = ['<=', $time];
                    $where['end_time'] = ['>=', $time];
                    $where['status'] = 0;
                    break;
                case 3:
                    $where['end_time'] = ['<=', $time];
                    break;
                default:

            }
        }
        $groupsRuleService = new GroupsRuleService();
        $data = $groupsRuleService->getlistPage($where, $orderBy, $order);
        $ruleids = [];
        $now = time();
        //处理团购规则状态，
        foreach ($data[0]['data'] as &$val) {
            $pids[] = $val['pid'];
            $ruleids[] = $val['id'];
            if ($val['status'] == -1) {
                $val['state'] = -1;
            } elseif (strtotime($val['start_time']) >= $now) {
                $val['state'] = 2; //未开始
            } elseif (strtotime($val['start_time']) <= $now && strtotime($val['end_time']) >= $now) {
                $val['state'] = 1; //正在进行中
            } elseif (strtotime($val['end_time']) <= $now) {
                $val['state'] = 3; //已过期
            }
            //update 何书哲 2018年7月10日 添加列表订单实付金额、成团订单数、成团人数
            list($val['total_pay_price'], $val['total_group_order_num'], $val['total_group_member_num']) = $groupsRuleService->getTotalGroup($val['id']);
        }
        //处理团购团购关联的商品及商品的规格，最低价最高价等
        if ($data[0]['data']) {
            $res = Product::whereIn('id', $pids)->get(['id', 'img', 'price'])->toArray();
            $skus = $this->getSkus($ruleids);
            $pdata = [];
            foreach ($res as $value) {
                $pdata[$value['id']] = $value;
            }
            foreach ($data[0]['data'] as &$v) {
                $v['product'] = $pdata[$v['pid']];
                //判断是否有活动图片
                if ($v['img']) {
                    $v['product']['img'] = $v['img'];
                }
                $v['skus'] = $skus[$v['id']] ?? [];
                $temp = [];
                if ($v['skus']) {
                    foreach ($v['skus'] as $item) {
                        $temp[] = $item['price'];
                    }
                }

                $v['min'] = $temp[0] ?? 0;
                $v['max'] = array_pop($temp) ?? '';
            }
        }

        return $data;

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170705
     * @desc 获取拼团的sku
     * @param $ruleId
     */
    public function getSkus($ruleIds)
    {
        $groupsSkuService = new GroupsSkuService();
        $skus = $groupsSkuService->getlistByRuleIds($ruleIds);
        $result = [];
        foreach ($skus as $val) {
            $result[$val['rule_id']][] = $val;
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170705
     * @desc  获取单个团购
     * @param $mid int 用户ID 目前只有获取商品价格区间时用到 Herry
     * @return  array  单个团购的二位数组
     * @update 梅杰 2018年8月14号 增加新用户标识
     * @update 何书哲 2019年06月27日 关联商品不存在返回false
     */
    public function getById($id, $mid = 0)
    {
        $groupsRuleService = new GroupsRuleService();
        $data = $groupsRuleService->getRowById($id);
        if (!$data) {
            return [];
        }
        $img = ProductImg::where('product_id', $data['pid'])->where('status', 1)->get(['id', 'img'])->toArray();
        $data['img'] = $img;
        $skus = $this->getSkus([$data['id']]);
        $temp = [];
        //团长价格
        $headPriceArr = [];
        if ($skus) {
            foreach ($skus[$data['id']] as $item) {
                $temp[] = $item['price'];
                $headPriceArr[] = $item['head_price'];
            }
        }
        sort($temp);
        sort($headPriceArr);
        $data['headMin'] = $headPriceArr[0] ?? 0;
        $data['min'] = $temp[0] ?? 0;
        $data['max'] = array_pop($temp) ?? '';
        if ($data['max'] == $data['min']) {
            $data['max'] = '';
        }
        $product = Product::find($data['pid']);
        if ($product) {
            $product = $product->toArray();
        } else {
            // update 何书哲 2019年06月27日 关联商品不存在返回false
            return false;
        }
        if (!empty($product['content'])) {
            $product['content'] = ProductStoreService::processTemplateData($data['wid'], $product['content']);
            //详情替换图片路径 20180502修复 使用绝对路径 不然小程序图片无法显示
            $product['content'] = ProductModule::addProductContentHost($product['content']);
        }
        $product['noteList'] = (new ProductMsgService())->getListByProduct($product['id']);
        //价格区间
        $product['showPrice'] = $product['price'];
        if ($product['sku_flag'] == 1) {
            $tmp = [];
            $sku = (new ProductModule())->handleSkuDiscountPrice($product['id'], $mid)['data'];
            if (!empty($sku['stocks'])) {
                foreach ($sku['stocks'] as $k => $val) {
                    $tmp[] = $val['price'];
                }
                sort($tmp);
                $max = $tmp[0];
                $min = end($tmp);
                //价格没有区间 则只显示一个价格
                $product['showPrice'] = $max == $min ? $max : $max . '～' . $min;
            }
        }
        #todo
        //增加新用户统计
        if ((new NewUserFlagRedis())->get($mid)) {
            $newdata = [
                'page' => '/web/groups/detail',
                'type' => 2,
                'param_id' => $id,
                'register_time' => time(),
            ];
            Event::fire(new NewUserEvent($newdata));
        }


        $data['product'] = $product;
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170717
     * @desc 创建团购
     * @param $rule_id
     */
    public function createGroups($rule_id)
    {
        $result = [
            'success' => 0,
            'message' => '',
        ];
        $groupsRuleService = new GroupsRuleService();
        $groupsService = new GroupsService();
        $rule = $groupsRuleService->getRowById($rule_id);
        if (!$rule) {
            $result['message'] = '团被删除或不存在';
            return $result;
        }
        //判断状态
        if ($rule['status'] == -1) {
            $rule['state'] = -1; //失效
        } elseif (strtotime($rule['start_time']) >= time()) {
            $rule['state'] = 2; //未开始
        } elseif (strtotime($rule['start_time']) <= time() && strtotime($rule['end_time']) >= time()) {
            $rule['state'] = 1; //正在进行中
        } elseif (strtotime($rule['end_time']) <= time()) {
            $rule['state'] = 3; //已过期
        }
        if ($rule['state'] == 2) {
            $result['message'] = '拼团活动未开始';
            return $result;
        }
        if ($rule['state'] != 1) {
            $result['message'] = '该拼团活动已结束';
            return $result;
        }
        //判断团购限购商品数量
        if ($rule['num'] > 0 && $_REQUEST['num'] > $rule['num']) {
            $result['message'] = '该团购商品最多购买' . $rule['num'] . '件';
            return $result;
        }
        $groupsData = [
            'identifier' => $groupsService->getIdentifier(),
            'wid' => $rule['wid'],
            'rule_id' => $rule_id,
            'num' => 0,
        ];

        $groups_id = $groupsService->add($groupsData);

        $result['success'] = 1;
        $result['data'] = $groups_id;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170711
     * @desc 订单支付以后处理团购数据
     * @param $order
     * @update 张永辉 2018年7月16日 开团成团发送数据到数据中心
     * @update 梅杰 20180717 拼团统计--拼团类型判断优化
     */
    public function afterOrder($order)
    {
        $result = [
            'success' => 0,
            'message' => '',
        ];
        $groupsService = new GroupsService();
        $groupsDetailService = new GroupsDetailService();
        $groupsRuleService = new GroupsRuleService();

        $groupsData = $groupsService->getRowById($order['groups_id']);
        $groupsRuleData = $groupsRuleService->getRowById($groupsData['rule_id']);
        if (!$groupsData) {
            $result['message'] = '该团不存在';
            return $result;
        }
        //添加团购详情
        $count = $groupsDetailService->model->where('groups_id', $order['groups_id'])->count();
        if ($count <= 0) {
            $is_head = 1;
            //add MayJay 团长消息队列
            $time = strtotime($groupsRuleData['end_time']) - time();
            if ($groupsRuleData['expire_hours'] > 0 && $time > $groupsRuleData['expire_hours'] * 3600) {
                $delayTime = $groupsRuleData['expire_hours'] * 3600;
            } else {
                $delayTime = $time;
            }
            (new MessagePushModule($groupsData['wid'], MessagesPushService::ActivityGroup))->setDelay($delayTime * 4 / 5)->sendMsg(['oid' => $order['id'], 'num' => $groupsRuleData['groups_num'], 'group_type' => 'group_dead_time']);
        } else {
            $is_head = 0;
        }
        //留言编号
        $orderDetail = (new OrderDetailService())->init()->model->where('oid', $order['id'])->get()->toArray();
        if ($orderDetail) {
            $orderDetail = current($orderDetail);
        }
        $groupsDetailData = [
            'groups_id' => $order['groups_id'],
            'member_id' => $order['mid'],
            'is_head' => $is_head,
            'oid' => $order['id'],
            'remark_no' => $orderDetail['remark_no'] ?? '',
        ];
        $groupsdetailId = $groupsDetailService->add($groupsDetailData);

        #将最新参团的mid数据插入redis
        (new GroupMemberRedis($groupsData['rule_id']))->add($order['mid']);
        dispatch((new SendGroupsLog($groupsdetailId, $is_head == 1 ? 1 : 2))->onQueue('SendGroupsLog'));//发送参团数据到数据中心队列
        //更新团购
        $count = $count + 1;
        if (!$groupsData['open_time']) {
            $groupsData['open_time'] = date("Y-m-d H:i:s", time());
            $groupsData['status'] = 1;
        }
        if ($groupsRuleData['groups_num'] <= $count) {
            $groupsData['status'] = 2;
            $groupsData['complete_time'] = date('Y-m-d H:i:s', time());

            /*判断该团购是否开启抽奖 add by wuxiaoping 2017.11.07 */
            if ($groupsRuleData['is_open_draw'] == 1) { //如果开启了抽奖,则设置订单状态为待抽奖

                $this->saveDrawGroupOrderStatus($order);
            } else {  //如果未开启抽奖则按以前的正常流程
                //更新订单状态
                $this->upCompleteGroupsOrder($order);
            }
            //Add MayJay 成团提醒
            //获取所有参团人信息

            $groupsDetail = $groupsDetailService->getListByWhere(['groups_id' => $order['groups_id']]);

            $mids = array_column($groupsDetail, 'member_id');
            $oids = array_column($groupsDetail, 'oid');

            foreach ($mids as $key => $mid) {
                // @update 吴晓平 2019年12月23日 17:21:47 把小程序拼团发送模板消息改为发送订阅模板消息
                // 模板发送的初步数据
                $data = [
                    'wid' => $order['wid'],
                    'openid' => '',
                    'param' => [
                        'oid' => $oids[$key],
                        'groups_id' => $order['groups_id'],
                    ]
                ];
                // 发送模板的相关内容
                $param = [
                    'mid' => $mid,                    // 拼团用户id
                    'title' => $groupsRuleData['title'],  // 拼团活动名称
                    'oid' => $oids[$key], // 对应拼团订单id
                    'groups_num' => $groupsRuleData['groups_num'], // 参团人数
                    'notice' => '拼团成功，商家正在努力发货，请耐心等待'
                ];
                // 组装后的数据
                $sendData = app(SubscribeMessagePushService::class)->packageSendData(2, $data);
                dispatch(new SubMsgPushJob(2, $order['wid'], $sendData, $param));
            }
            dispatch((new SendGroupsLog($groupsdetailId, '3'))->onQueue('SendGroupsLog'));  //发送成团数据到数据中心队列
        } else {
            $this->_closeNoPayOrder($order);
        }

        $groupsData['num'] = $count;
        $groupsData['pnum'] = $groupsData['pnum'] + $this->getProductNum($order['id']);
        unset($groupsData['deleted_at']);
        unset($groupsData['updated_at']);
        unset($groupsData['created_at']);
        $groupsService->update($groupsData['id'], $groupsData);
        $result['success'] = 1;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180424
     * @desc 关闭未支付的该订单
     */
    private function _closeNoPayOrder($order)
    {
        $where = [
            'groups_id' => $order['groups_id'],
            'status' => 0,
            'id' => ['<>', $order['id']],
            'mid' => $order['mid'],
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id'])->toArray();
        foreach ($orderData as $val) {
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
        }
    }


    public function upCompleteGroupsOrder($order)
    {
        $groups_id = $order['groups_id'];
        $where = [
            'groups_id' => $groups_id,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id'])->toArray();
        foreach ($orderData as $val) {
            $val['groups_status'] = 2;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
//            $this->sendGroupMsg($order);

        }
        //更新未支付订单
        $where = [
            'groups_id' => $groups_id,
            'status' => 0,
            'id' => ['<>', $order['id']],
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id'])->toArray();
        foreach ($orderData as $val) {
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
        }
    }

    /**
     * 更改抽奖团的订单状态
     * 如果是抽奖团，支付成功已成团的情况下，订单状态改为待抽奖
     * @author wuxiaoping 2017.11.13
     * @return [type] [description]
     */
    public function saveDrawGroupOrderStatus($order)
    {
        $groups_id = $order['groups_id'];
        $where = [
            'groups_id' => $groups_id,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id', 'status'])->toArray();
        foreach ($orderData as $val) {
            if ($val['status'] == 1) {
                $val['groups_status'] = 2;
                $val['status'] = 7;
            }
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
//            $this->sendGroupMsg($order);

        }
        //更新未支付订单
        $where = [
            'groups_id' => $groups_id,
            'status' => 0,
            'id' => ['<>', $order['id']],
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id'])->toArray();
        foreach ($orderData as $val) {
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
        }
    }

    /**
     * 分别更新拼团用户是否中奖的订单状态
     * @author [name] <wuxiaoping 2017.11.08>
     * @param  [array] $drawGroups [拼团活动规则数据]
     * @return [array]                 [description]
     */
    public function getDrawData($drawGroups)
    {
        //定义中奖更新状态数据，未中奖更新数据，未中奖条件，中奖条件
        $saveWinData = $saveNoWinData = $saveData = [];
        $closeOrderIds = $drawOrderIds = [];
        foreach ($drawGroups as $key => $value) {
            (new GroupsService())->update($key, ['is_draw' => 1]); //更新该拼团已进行抽奖(标识)
            $orderWhere['groups_id'] = $key;
            $orderWhere['status'] = 7;
            $orderData = OrderService::init()->model->wheres($orderWhere)->get(['id', 'groups_id', 'mid', 'address_phone'])->toArray();
            //设置中奖人数为0，或订单状态为未支付（转为退款处理）
            if ($value['draw_pnum'] == 0) {
                if ($orderData) {
                    foreach ($orderData as $orderKey => $orderValue) {
                        $closeOrderIds[] = $orderValue['id'];
                    }
                }
                array_push($saveWinData, []);
                array_push($saveNoWinData, $closeOrderIds);
            } else {
                if ($orderData) {
                    foreach ($orderData as $k => $orderVal) {
                        $mids[$orderVal['groups_id']][] = $orderVal['mid'];
                    }
                    $mids = array_unique(array_pop($mids)); //过滤掉数组中重复的值
                    //设置中奖类型为随机
                    if ($value['draw_type'] == 0) {
                        $returnData = $this->handleData($mids, $orderData, $value['draw_pnum']);
                        array_push($saveWinData, $returnData['drawOrderIds']);
                        array_push($saveNoWinData, $returnData['closeOrderIds']);
                    } //设置中奖类型为指定用户(以手机号作为标识)
                    else {
                        $phones = explode(',', $value['draw_phones']);
                        $matchMids = [];
                        foreach ($orderData as $k => $orderVal) {
                            if (in_array($orderVal['address_phone'], $phones)) {
                                $matchMids[] = $orderVal['mid'];
                            }
                        }
                        $prizeUserNum = count($matchMids);
                        //如果匹配的中奖手机号与后台设置的中奖人数不一致，则其他的随机获取
                        if ($prizeUserNum <> $value['draw_pnum']) {
                            if ($prizeUserNum >= $value['draw_pnum']) {
                                $returnData = $this->handleData($mids, $orderData, 0, $matchMids);
                            } else {
                                $leftNum = ((int)$value['draw_pnum'] - (int)$prizeUserNum);
                                $returnData = $this->handleData($mids, $orderData, $leftNum, $matchMids);
                            }
                            array_push($saveWinData, $returnData['drawOrderIds']);
                            array_push($saveNoWinData, $returnData['closeOrderIds']);
                        } else {
                            //中奖用户
                            $returnData = $this->handleData($mids, $orderData, 0, $matchMids);
                            array_push($saveWinData, $returnData['drawOrderIds']);
                            array_push($saveNoWinData, $returnData['closeOrderIds']);
                        }
                    }
                }
            }
        }
        //处理中奖与未中奖的订单id数据
        $drawOrderIds = [];
        if (!empty($saveWinData)) {
            foreach ($saveWinData as $value) {
                foreach ($value as $val) {
                    $saveWinOrderIds[] = $val;
                }
            }
            $winWhere['id'] = ['in', $saveWinOrderIds];
            $winOrderData = OrderService::init()->model->wheres($winWhere)->with('orderDetail')->get(['id', 'wid', 'groups_id', 'mid', 'status', 'pay_price'])->toArray();
            if ($winOrderData) {
                foreach ($winOrderData as $wkey => $wValue) {
                    if ($wValue['status'] == 7) {  //选取待抽奖用户进行抽奖
                        $drawOrderIds[] = $wValue['id'];
                    }
                }
            }
        }
        //获取需要关闭的订单信息
        $notWinOrderData = [];
        if (!empty($saveNoWinData)) {
            foreach ($saveNoWinData as $value) {
                foreach ($value as $val) {
                    $saveNoWinOrderIds[] = $val;
                }
            }
            $notWinWhere['id'] = ['in', $saveNoWinOrderIds];
            $notWinOrderData = OrderService::init()->model->wheres($notWinWhere)->with('orderDetail')->get(['id', 'wid', 'groups_id', 'mid', 'status', 'pay_price', 'pay_way'])->load('orderDetail')->toArray();
        }
        if ($drawOrderIds) {
            $this->upDrawOrder($drawOrderIds);
        }
        if ($notWinOrderData) {
            $this->closeDrawOrder($notWinOrderData);
        }

    }

    /**
     * [upDrawOrder 更新中奖订单]
     * @param  [array] $oids [订单id数组]
     * @return [type]       [description]
     */
    public function upDrawOrder($oids)
    {
        foreach ($oids as $key => $value) {
            $where['id'] = $value;
            $saveData['status'] = 1;
            $saveData['groups_status'] = 2;
            OrderService::init()->where($where)->update($saveData, false);
        }

    }

    /**
     * [closeDrawOrder 更新未中奖订单]
     * @param  [array] $oids [订单id数组]
     * @return [type]       [description]
     * @update 许立 2018年08月01日 增加支付宝退款
     */
    public function closeDrawOrder($orders)
    {
        $orderModule = new OrderModule();
        $refundModule = new RefundModule();
        foreach ($orders as $key => $value) {
            $where['id'] = $value['id'];
            $saveData['refund_status'] = 3; //商家同意退款
            $saveData['groups_status'] = 3; //未成团
            OrderService::init()->where($where)->update($saveData, false);

            //已付款 关闭订单 需要退款
            $res = $orderModule->groupOrderRefund($value['id'], $value['orderDetail'][0]['product_id']);

            $prop_id = $value['orderDetail'][0]['product_prop_id'] ?? 0;

            if ($res['code'] == 'SUCCESS') {
                //微信退款审核成功 更改订单状态为微信审核成功
                if ($value['pay_way'] != 3 && $value['pay_way'] != 2) {
                    //非余额支付订单的退款 才改变状态 Herry 20171226
                    OrderService::init('wid', $value['wid'])
                        ->where(['id' => $value['id']])
                        ->update(['status' => 4, 'refund_status' => 4], false);
                }
            } else {
                //如果直接退款失败 可能是没上传商户证书或者商家余额不足等原因
                //模拟用户申请退款 走通用退款流程 商家可以在后台同意退款 但是商家不可拒绝退款
                //如果商家拒绝退款 提示未成团订单退款必须同意
                $refundModule->closeGroupOrderApplyRefund($value, $value['orderDetail'][0]['product_id'], $value['pay_price'], $prop_id);
            }
        }
    }

    /**
     * 团购中奖与未中奖数据处理
     * @return [type] [description]
     */
    public function handleData($mids, $orderData, $draw_pnum = 0, $matchMids = [])
    {
        $drawMids = $notWinMids = [];
        $returnData = $drawOrderIds = $closeOrderIds = [];
        //$number必须大于0
        if ($draw_pnum) {
            if ($matchMids) {
                $leftMids = array_diff($mids, $matchMids);
                $number = $draw_pnum >= count($leftMids) ? count($leftMids) : $draw_pnum;
                //随机获取对应的中奖数
                $randKeys = array_rand($leftMids, $number);
            } else {
                $number = $draw_pnum >= count($mids) ? count($mids) : $draw_pnum;
                //随机获取对应的中奖数
                $randKeys = array_rand($mids, $number);
            }

            //是否获取多个匹配用户
            if (is_array($randKeys)) {
                foreach ($randKeys as $val) {
                    $drawMids[] = $mids[$val];
                }
            } else {
                $drawMids[] = $mids[$randKeys];
            }
        }
        //合并匹配中奖的用户
        if ($matchMids) {
            $drawMids = array_merge($matchMids, $drawMids);
        }
        //未中奖用户
        $notWinMids = array_diff($mids, $drawMids);
        foreach ($orderData as $k => $orderVal) {
            if (in_array($orderVal['mid'], $drawMids)) {
                $drawOrderIds[] = $orderVal['id'];
                // 防止一个用户同一个团参加多次的情况
                if (count($drawOrderIds) > $number) {
                    $subDrawOrderIds = array_slice($drawOrderIds, 0, $number);
                    if ($matchMids) {
                        array_push($subDrawOrderIds, $orderVal['id']);
                    } else {
                        $subDrawOrderIds = array_slice($drawOrderIds, 0, $number);
                    }
                    if (!empty(array_diff($drawOrderIds, $subDrawOrderIds))) {
                        array_push($closeOrderIds, array_values(array_diff($drawOrderIds, $subDrawOrderIds))[0]);
                    }
                    $drawOrderIds = $subDrawOrderIds;
                }
            } else {
                array_push($closeOrderIds, $orderVal['id']);
            }
        }
        $returnData['drawOrderIds'] = $drawOrderIds;
        $returnData['closeOrderIds'] = $closeOrderIds;
        return $returnData;
    }

    //商品名称
    //{{keyword1.DATA}}
    //订单金额
    //{{keyword2.DATA}}
    //发货时间
    //{{keyword3.DATA}}
    //温馨提示
    //{{keyword4.DATA}}
    public function sendGroupMsg($order)
    {
        $memberService = new MemberService();
        $memberInfo = $memberService->model->select(['openid', 'xcx_openid'])->find($order['mid']);
        $memberInfo = $memberInfo->toArray();
        if (!$memberInfo['xcx_openid']) {
            return;
        }
        $orderInfo = OrderService::getOrderInfo($order['id']);
        $title = '';
        foreach ($orderInfo['data']['orderDetail'] as $value) {
            $title .= $value['title'] . " ";
        }
        $data['touser'] = $memberInfo['xcx_openid'];
        $data['form_id'] = $order['prepay_id'];
        $data['page'] = 'pages/order/orderDetail/orderDetail?oid=' . $order['id'];
        $data['data']['keyword1'] = [
            'value' => $title,
        ];
        $data['data']['keyword2'] = [
            'value' => $order['pay_price'],
        ];
        $data['data']['keyword3'] = [
            'value' => '商家将于5天内发货',
        ];
        $data['data']['keyword4'] = [
            'value' => "如果未按承诺时间发货，系统将按照规则进行赔偿。",
        ];
        (new WXXCXSendTplService($order['wid']))->sendTplNotify($data, WXXCXSendTplService::GROUP_NOTIFY);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171023
     * @desc 获取团购订单的商品数量
     */
    public function getProductNum($oid)
    {
        $orderDetailService = new OrderDetailService();;
        $orderData = $orderDetailService->init()->model->where('oid', $oid)->sum('num');
        return $orderData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170711
     * @desc 根据团id，获取拼团信息
     * @param $groups_id
     * @update 梅杰 2018年8月14号 增加新用户标识
     */
    public function getGroupsById($groups_id, $mid)
    {
        $result = ['success' => 0, 'message' => ''];
        $groupsData = (new GroupsService())->getRowById($groups_id);
        if (!$groupsData) {
            $result['message'] = '团不存在';
            return $result;
        }

        $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
        $product = Product::select(['id', 'img', 'price', 'title', 'stock'])->find($ruleData['pid']);
        if ($product) {
            $product = $product->toArray();
        }
        $product['noteList'] = (new ProductMsgService())->getListByProduct($product['id']);
        $ruleData['product'] = $product;
        //获取团购最低价格
        $skus = $this->getSkus([$groupsData['rule_id']]);
        $temp = [];
        if ($skus) {
            foreach ($skus[$groupsData['rule_id']] as $item) {
                $temp[] = $item['price'];
            }
        }
        sort($temp);
        $ruleData['min'] = $temp[0] ?? 0;
        $ruleData['max'] = array_pop($temp) ?? '';
        if ($ruleData['max'] == $ruleData['min']) {
            $ruleData['max'] = '';
        }
        //团购名单
        $groupsDetailData = (new GroupsDetailService())->getListByWhere(['groups_id' => $groups_id], '', '', 'id', 'asc');
        $memberIds = [];
        foreach ($groupsDetailData as $val) {
            $memberIds[] = $val['member_id'];
        }
        $memberData = (new MemberService())->getListById(array_unique($memberIds));
        $members = [];
        foreach ($memberData as $val) {
            $members[$val['id']] = $val;
        }
        foreach ($groupsDetailData as &$v) {
            $v['member'] = $members[$v['member_id']];
        }
        //获取当前用户的该团订单id
        $order = Order::select(['id', 'address_name', 'address_phone', 'address_detail', 'address_id'])->where('mid', $mid)->where('groups_id', $groups_id)->first();

        $result['success'] = 1;
        $result['groups'] = $groupsData;
        $result['rule'] = $ruleData;
        $result['groupsDetail'] = $groupsDetailData;
        $result['order_id'] = $order->id ?? 0;
        if ($order) {
            $result['order'] = $order->toArray();
        } else {
            $result['order'] = [];
        }
        #todo
        //增加新用户统计
        if ((new NewUserFlagRedis())->get($mid)) {
            $data = [
                'page' => '/web/groups/groupsDetail',
                'type' => 3,
                'param_id' => $groups_id,
                'register_time' => time(),
            ];
            Event::fire(new NewUserEvent($data));
        }

        return $result;

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170712
     * @desc 获取团
     * @param $rule_id
     */
    public function getGroups($rule_id, $mid = '')
    {
        $ids = $this->getMyGroupsIds($mid);
        list($res) = (new GroupsService())->getlistPage(['rule_id' => $rule_id, 'status' => 1, 'id' => ['not in', $ids]], 'num', 'desc');
        //获取团信息
        $groups = [];
        if (!$this->request->input('page')) {
            $groups = array_slice($res['data'], 0, 10);
        }
//        show_debug($groups);
        if ($groups) {
            $groupsDetailService = new GroupsDetailService();
            $ids = [];
            foreach ($groups as $val) {
                $ids[] = $val['id'];
            }
            $groupsDataiData = $groupsDetailService->getListByWhere(['groups_id' => ['in', $ids], 'is_head' => 1]);
            $memberids = [];
            $groupsDetail = [];
            foreach ($groupsDataiData as $v) {
                $memberids[] = $v['member_id'];
                $groupsDetail[$v['groups_id']] = $v;
            }
            //获取开团用户图片
            $res = (new MemberService())->getListById($memberids);
            $member = [];
            foreach ($res as $value) {
                $member[$value['id']] = $value;
            }
            foreach ($groupsDetail as &$item) {
                $item['members'] = $member[$item['member_id']];
            }
            foreach ($groups as &$val) {
                $val['groupDetail'] = $groupsDetail[$val['id']] ?? '';
            }
        }
        return $this->dealGroups($groups, $mid);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @desc 处理团购的关联信息
     * @other 修改请通知zhangyh
     * @param $groups 团信息
     * @param $mid 用户标识
     * @return array
     */
    public function dealGroups($groups, $mid)
    {
        $result = [];
        if ($groups) {
            $ruleData = (new GroupsRuleService())->getRowById($groups[0]['rule_id']);
            foreach ($groups as $val) {
                if ($mid == $val['groupDetail']['member_id']) {
                    continue;
                }
                $temp = [];
                $temp['id'] = $val['id'];
                $temp['identifier'] = $val['identifier'];
                $temp['headimgurl'] = $val['groupDetail']['members']['headimgurl'] ?? '';
                $temp['nickname'] = $val['groupDetail']['members']['nickname'] ?? '';
                $temp['num'] = $ruleData['groups_num'] - $val['num'];

                //未成团订单存在时间 取活动配置的小时数 Herry 20171108
                //$temp['end_time'] = strtotime($val['open_time'])+86400;
                if ($ruleData['expire_hours']) {
                    $temp['end_time'] = strtotime($val['open_time']) + $ruleData['expire_hours'] * 3600;
                    if ($temp['end_time'] > strtotime($ruleData['end_time'])) {
                        $temp['end_time'] = strtotime($ruleData['end_time']);
                    }
                } else {
                    $temp['end_time'] = strtotime($ruleData['end_time']);
                }

                $temp['now_time'] = date("Y/m/d H:i:s", time());
                $temp['stop_time'] = date('Y/m/d H:i:s', $temp['end_time']);

                $temp['end_time'] = implode(':', $this->dealTime($temp['end_time']));
                $result[] = $temp;
            }
        }

        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170719
     * @desc 判断商品是否能够删除
     */
    public function isDelProduct($ids, $oper = 'edit')
    {
        $groupsRuleService = new GroupsRuleService();
        $now = date("Y-m-d H:i:s", time());
        $res = $groupsRuleService->model->where('end_time', '>', $now)->where('status', 0)->whereIn('pid', $ids)->first();
        if ($res) {
            return false;
        } else {
            if ($oper == 'del') {
                //删除过期的团购活动
                $data = $groupsRuleService->model->whereIn('pid', $ids)->get(['id', 'pid'])->toArray();
                if ($data) {
                    foreach ($data as $val) {
//                        $groupsRuleService->del($val['id']);
                        $groupsRuleService->update($val['id'], ['status' => -2]);
                    }
                }
            }
            return true;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 定时任务处理团购
     * @update 张永辉 发送成团数据到数据中心
     */
    public function autoGroups()
    {
        /*添加where status=2条件，查询开启抽奖团并成团的订单*/

        $field = ['g.id', 'g.rule_id', 'g.num', 'g.open_time', 'g.status', 'gr.end_time', 'gr.pid', 'gr.auto_success', 'gr.expire_hours', 'gr.is_open_draw', 'gr.draw_pnum', 'gr.draw_type', 'gr.draw_phones'];
        $res = DB::table('groups as g')->leftJoin('groups_rule as gr', 'g.rule_id', '=', 'gr.id')->where('g.status', 1)
            ->orWhere(function ($query) {
                $query->where('g.status', 2)->where('g.is_draw', 0)->where('gr.is_open_draw', 1);
            })->whereNull('g.deleted_at')->get($field)->toArray();
        $now = time();

        //成团id
        $ids = [];
        //关闭id
        $closeIDs = $closeDrawIds = [];
        //商品id
        $pids = [];

        /*抽奖团数据 add by wuxiaoping 2017.11.13*/
        $drawGroups = [];

        if ($res) {
            foreach ($res as $val) {
                //开团24小时，或团活动结束前未成团则改变状态
                //未成团订单存在时间 取活动配置的小时数 Herry 20171108
                //$endTime = strtotime($val->open_time)+86400;
                if ($val->status == 0) {
                    //未付款订单关闭时间直接以拼团结束为准
                    $endTime = strtotime($val->end_time);
                } else {
                    if ($val->expire_hours) {
                        $endTime = strtotime($val->open_time) + $val->expire_hours * 3600;
                        if ($endTime > strtotime($val->end_time)) {
                            $endTime = strtotime($val->end_time);
                        }
                    } else {
                        $endTime = strtotime($val->end_time);
                    }
                }

                if ($now >= $endTime) {
                    if ($val->auto_success) {
                        //自动成团
                        if ($val->status == 1) { //过虑掉已成团的group_id
                            $ids[] = $val->id;
                        }
                        $this->sendCompleteQueue($ids);   //发送成团数据到数据中心
                    } else {
                        if ($val->is_open_draw == 1) {
                            if ($val->status == 2) {   //开启抽奖，已成团
                                $drawGroups[$val->id]['draw_pnum'] = $val->draw_pnum;
                                $drawGroups[$val->id]['draw_type'] = $val->draw_type;
                                $drawGroups[$val->id]['draw_phones'] = $val->draw_phones;
                            } else {   //开启抽奖，未成团
                                $closeDrawIds[] = $val->id;
                                $pids[$val->id] = $val->pid;
                            }
                        } else {
                            //自动关闭
                            $closeIDs[] = $val->id;
                            $pids[$val->id] = $val->pid;
                        }

                    }
                }
            }
        }
        //合并关闭的抽奖团id
        $closeIDs = ($closeIDs + $closeDrawIds);
        //更新团状态为未成团,已支付订单设置为待退款，未支付订单设置为关闭订单
        DB::beginTransaction();

        //批量更新团,更新团为已成团
        $now = date('Y-m-d H:i:s', time());
        $groupService = new GroupsService();
        $groupService->batchUpdate($ids, ['status' => 2, 'complete_time' => $now]);

        //批量更新团为关闭
        $groupService->batchUpdate($closeIDs, ['status' => 3]);

        //更新自动成团订单
        $this->upOrder($ids);

        //更新自动关闭拼团
        $this->closeGroups($closeIDs, $pids);

        /*add by wuxiaoping 2017.11.14 抽奖团信息处理*/
        if ($drawGroups) {
            $this->getDrawData($drawGroups);
        }

        DB::commit();

    }


    /**
     * 团购定时任务自动成团发送数据到数据中心的队列
     * @param $data
     * @author 张永辉 2018年7月17日 发送数据导数据中心
     */
    public function sendCompleteQueue($data)
    {
        $groupsDetailService = new GroupsDetailService();
        $where = [
            'groups_id' => ['in', $data],
            'is_head' => '1',
        ];
        $detailData = $groupsDetailService->getListByWhere($where);
        if (!$detailData) {
            return true;
        }

        foreach ($detailData as $val) {
            dispatch((new SendGroupsLog($val['id'], '3'))->onQueue('SendGroupsLog'));
        }
        return true;

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 处理订单
     */
    public function upOrder($ids)
    {
        //更新订单已支付订单
        $where = [
            'groups_id' => ['in', $ids],
            'status' => 1,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id', 'status', 'pay_price', 'wid'])->toArray();
        foreach ($orderData as $val) {
            unset($val['status']);
            unset($val['pay_price']);
//            $val['refund_status'] = 1;
            $val['groups_status'] = 2;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
            //Add MayJay 成团提醒
            //获取所有参团人信息

            $groupsDetail = (new GroupsDetailService())->getListByWhere(['groups_id' => $val['groups_id']]);

            $mids = array_column($groupsDetail, 'member_id');

            foreach ($mids as $mid) {
                (new MessagePushModule($val['wid'], MessagesPushService::ActivityGroup))->sendMsg(['oid' => $val['id'], 'mid' => $mid, 'group_type' => 'group_success']);
            }

            //何书哲 2018年11月15日 外卖订单导入第三方
            dispatch((new SendTakeAway($val['id'])));

//            $this->addRefund($val);
        }

        //更新未支付订单
        $where = [
            'groups_id' => ['in', $ids],
            'status' => 0,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id', 'status'])->toArray();
        foreach ($orderData as $val) {
            unset($val['status']);
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
        }
    }

    /**
     * 关闭拼团 已支付的进行退款
     * @param $ids
     * @update 许立 2018年08月01日 增加支付宝退款
     * @update 吴晓平 2019年12月23日 17:21:47 把小程序拼团发送模板消息改为发送订阅模板消息
     */
    public function closeGroups($ids, $pids)
    {
        //更新订单已支付订单
        $where = [
            'groups_id' => ['in', $ids],
            'status' => 1,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id', 'status', 'pay_price', 'mid', 'wid', 'address_phone', 'pay_way'])->load('orderDetail')->toArray();
        $orderModule = new OrderModule();
        $refundModule = new RefundModule();
        foreach ($orderData as $val) {
            $status = $val['status'];
            $payPrice = $val['pay_price'];
            unset($val['status']);
            unset($val['pay_price']);
            $val['groups_status'] = 3;

            $prop_id = $val['orderDetail'][0]['product_prop_id'] ?? 0;
            unset($val['orderDetail']);

            //0元订单 直接关闭订单
            if ($payPrice <= 0) {
                $val['status'] = 4;
                $val['refund_status'] = 8;
            }


            OrderService::init()->where(['id' => $val['id']])->update($val, false);
            // @update 吴晓平 2019年12月23日 17:21:47 把小程序拼团发送模板消息改为发送订阅模板消息
            // 模板发送的初步数据
            $data = [
                'wid' => $val['wid'],
                'openid' => '',
                'param' => [
                    'oid' => $val['id'],
                    'groups_id' => $val['groups_id'],
                ]
            ];

            // 发送模板的相关内容
            $param = [
                'mid' => $val['mid'],     // 拼团用户id
                'groups_id' => $val['groups_id'],  // 拼团活动id
                'oid' => $val['id'],    // 对应拼团订单id
                'notice' => '团购失败，参团人数不足，钱将原路退回'
            ];
            // 组装后的数据
            $sendData = app(SubscribeMessagePushService::class)->packageSendData(2, $data);
            dispatch(new SubMsgPushJob(2, $val['wid'], $sendData, $param));
            if ($payPrice > 0) {
                //已付款 关闭订单 需要退款
                $res = $orderModule->groupOrderRefund($val['id'], $pids[$val['groups_id']]);
                //\Log::info('【拼团】拼团未成团自动退款微信审核返回结果：' . json_encode($res));
                if (!empty($res['code']) && $res['code'] == 'SUCCESS') {
                    //微信退款审核成功 更改订单状态为微信审核成功
                    //微信审核成功 就关闭订单 后续是微信打款过程
                    if ($val['pay_way'] != 3 && $val['pay_way'] != 2) {
                        //非余额支付订单的退款 才改变状态 Herry 20171226
                        OrderService::init()->where(['id' => $val['id']])->update(['refund_status' => 4, 'status' => 4], false);
                    }
                } else {
                    //如果直接退款失败 可能是没上传商户证书或者商家余额不足等原因
                    //模拟用户申请退款 走通用退款流程 商家可以在后台同意退款 但是商家不可拒绝退款
                    //如果商家拒绝退款 提示未成团订单退款必须同意
                    $data = DB::table('groups as g')
                        ->leftJoin('groups_rule as gr', 'g.rule_id', '=', 'gr.id')
                        ->where('g.id', $val['groups_id'])->get(['gr.pid']);
                    if ($data) {
                        $refundModule->closeGroupOrderApplyRefund($val, $data[0]->pid, $payPrice, $prop_id);
                    }
                }
            }
        }

        //更新未支付订单
        $where = [
            'groups_id' => ['in', $ids],
            'status' => 0,
        ];
        $orderData = OrderService::init()->model->wheres($where)->get(['id', 'groups_id', 'status'])->toArray();
        foreach ($orderData as $val) {
            unset($val['status']);
            $val['status'] = 4;
            $val['groups_status'] = 3;
            OrderService::init()->where(['id' => $val['id']])->update($val, false);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170808
     * @desc 添加退款详情
     */
    public function addRefund($order)
    {
        //orderDetail
        $orderDetailService = new OrderDetailService();
        $orderDetailData = $orderDetailService->init()->model->where('oid', $order['id'])->get(['id', 'product_id'])->toArray();
        $data = [
            'oid' => $order['id'],
            'pid' => $orderDetailData[0]['product_id'],
            'amount' => $order['pay_price'],
            'type' => 0,
            'order_status' => 0,
            'reason' => 0,
            'phone' => 0,
            'remark' => '团购未成团进行退款',
            'imgs' => '',
        ];

        //插入订单退款表
        $orderRefundService = new OrderRefundService();
        $resRefund = $orderRefundService->init('oid', $order['id'])->add($data, false);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc  处理结束时间距离现在的时间
     * @param $time 结束时间戳
     */
    public function dealTime($t)
    {
        $result = [];
        $time = $t - time();
        if ($time > 0) {
            $result['hour'] = (int)($time / 3600);
            $result['minute'] = (int)($time % 86400 % 3600 / 60);
            $result['sec'] = $time % 86400 % 3600 % 60;
        }
        foreach ($result as &$item) {
            if (strlen($item) < 2) {
                $item = '0' . $item;
            }
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 团购列表
     * @desc status=0 全部列表，1、未开始，2、正在进行中，3、已结束，4，未开始和正在进行中
     */
    public function getGroupsRuleList($wid, $status = 0, $orderBy = '', $order = '')
    {
        $groupsRuleService = new GroupsRuleService();
        $data = $groupsRuleService->getGroupRuleList($wid, $status, $orderBy = '', $order = '');
        $data[0]['data'] = $this->dealGroupsRule($data[0]['data']);
        return $data;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 处理团购数据
     * @param  $data 单个团购信息
     * @return mixed
     */
    public function dealGroupsRule($data)
    {
        if (!$data) {
            return $data;
        }
        $pids = $ruleids = [];
        foreach ($data as $val) {
            $pids[] = $val['pid'];
            $ruleids[] = $val['id'];
        }

        //获取团购关联的商品信息
        $res = (new ProductService())->getListById($pids);
        $pData = [];
        foreach ($res as $val) {
            $pData[] = [
                'id' => $val['id'],
                'title' => $val['title'],
                'img' => $val['img'],
                'price' => $val['price'],
            ];
        }
        $pData = $this->dealKey($pData);
        $skus = $this->getSkus($ruleids);
        foreach ($data as &$v) {
            $v['skus'] = $skus[$v['id']] ?? [];
            $temp = [];
            if ($v['skus']) {
                foreach ($v['skus'] as $item) {
                    $temp[] = $item['price'];
                }
            }
            sort($temp);
            $v['min'] = $temp[0] ?? 0;
            $v['max'] = array_pop($temp) ?? 0;
            $v['groups'] = $this->getDetailByRuleId($v['id']);
            $v['pdata'] = $pData[$v['pid']];
        }
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购信息
     * @param  $ruleId 团规则id
     */
    public function getDetailByRuleId($ruleId)
    {
        $groupsService = new GroupsService();
        $groupsDetailService = new GroupsDetailService();
        $memberService = new MemberService();
        $groupsData = $groupsService->getListByRuleId($ruleId);
        $num = $pnum = $mnum = 0;
        $memberData = [];
        $groupsIds = [];
        if ($groupsData) {
            foreach ($groupsData as $val) {
                $num = $num + $val['num'];
                $pnum = $pnum + $val['pnum'];
                $groupsIds[] = $val['id'];
            }
            $detailData = $groupsDetailService->getListByWhere(['groups_id' => ['in', $groupsIds]]);  //获取团购规则相关的团信息
            //获取参团用户信息，包括昵称，头像，id，是否是团长等
            $mids = [];
            if ($detailData) {
                foreach ($detailData as $val) {
                    $mnum = $mnum + 1;
                    $mids[] = $val['member_id'];
                }
                $res = $memberService->getListById($mids);
                foreach ($res as $val) {
                    $memberData[] = [
                        'id' => $val['id'],
                        'headimgurl' => $val['headimgurl'],
                        'nickname' => $val['nickname'],
                    ];
                }
            }
        }
        $pnum = (new GroupsRuleService())->getProductSoldNum($ruleId);
        $data = [
            'num' => $num,
            'pnum' => $pnum,
            'mnum' => $mnum,
            'member' => $memberData,
        ];
        return $data;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取团购数量
     * @param $ruleId
     */
    public function getGroupsNum($ruleId, $mid)
    {
        return (new GroupsService())->getGroupsCount($ruleId, $mid);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取推荐团购
     */
    public function getRecommend($wid, $nowId = '')
    {
        $time = date('Y-m-d H:i:s', time());
        $where['start_time'] = ['<=', $time];
        $where['end_time'] = ['>=', $time];
        $where['status'] = 0;
        $where['wid'] = $wid;

        //add by jonzhang 2018-04-08 团购推荐
        $isAuto = false;
        $ids = [];
        $commendData = (new  CommendInfoService())->getListByCondition(['type' => 2, 'wid' => $wid]);
        if ($commendData['errCode'] == 0 && !empty($commendData['data'])) {
            //定向推荐
            $isAuto = $commendData['data'][0]['is_auto'];
            if ($isAuto) {
                $cid = $commendData['data'][0]['id'];
                //按id排序后，查询出来的id,按照id由大到小
                $commendDetailData = (new CommendDetailService())->getListBycondition(['cid' => $cid, 'current_status' => 0], 'id', 'desc', 20);
                if ($commendDetailData['errCode'] == 0 && !empty($commendDetailData['data'])) {
                    $groupIDs = [];
                    foreach ($commendDetailData['data'] as $item) {
                        //拼团推荐 去重
                        if ($nowId && $item['recommendation_id'] == $nowId) {
                            continue;
                        }
                        $where['id'] = $item['recommendation_id'];
                        //此处查询出来的ID 按照rids中数组的顺序
                        $groupId = (new GroupsRuleService())->model->wheres($where)->get(['id'])->toArray();
                        //$cnt=count($groupIDs);
                        if (!empty($groupId) && count($groupId) > 0) {
                            array_push($groupIDs, $groupId[0]);
                        }
                    }
                    if (count($groupIDs) > 0) {
                        $cnt = count($groupIDs);
                        //拼团商品推荐 最多展示四个
                        if ($cnt >= 4) {
                            for ($i = 0; $i < 4; $i++) {
                                array_push($ids, $groupIDs[$i]);
                            }
                        } else {
                            //1,3条数据时，只显示两个或者一个
                            if ($cnt % 2 != 0) {
                                unset($groupIDs[$cnt - 1]);
                                if (count($groupIDs) > 0) {
                                    $ids = $groupIDs;
                                }
                            } else {
                                //2条数据
                                if ($cnt > 0) {
                                    $ids = $groupIDs;
                                }
                            }
                        }
                    }
                }
            }
        }
        //end
        if (!$isAuto) {
            $res = (new GroupsRuleService())->model->wheres($where)->get(['id'])->toArray();
            //$ids = [];
            if (count($res) > 4) {
                foreach (array_rand($res, 4) as $val) {
                    $ids[] = $res[$val]['id'];
                }
            } else {
                foreach ($res as $val) {
                    $ids[] = $val['id'];
                }
            }
        }
        if ($nowId && in_array($nowId, $ids)) {
            $key = array_search($nowId, $ids);
            unset($ids[$key]);
        }

        //自动推荐，只推荐偶数
        if (count($ids) % 2) {
            array_pop($ids);
        }
        $where['id'] = ['in', $ids];
        $data = (new GroupsRuleService())->getList($where);
        $data = $this->dealGroupsRule($data);
        return $data;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 获取结算信息
     * @param $pid
     * @param string $skuId
     * @return mixed
     */
    public function getSettlementInfo($pid, $skuId = '')
    {
        $productService = new ProductService();
        $productData = $productService->getDetail($pid);
        $productData['skuData'] = [
            'img' => $productData['img'],
        ];
        if ($productData['sku_flag']) {
            if (empty($skuId)) {
                $result['errCode'] = -1;
                $result['errMsg'] = '商品规格不能为空';
                return $result;
            }
            $propService = new ProductSkuService();
            $skuData = $propService->getSkuDetail($skuId);
            if (!$skuData) {
                $result['errCode'] = -2;
                $result['errMsg'] = '商品规格不存在';
                return $result;
            }
            $productData['skuData'] = $skuData;
        }

        if (!$productData['skuData']['img']) {
            $productData['skuData']['img'] = $productData['img'];
        }

        $result['errCode'] = 0;
        $result['data'] = $productData;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 随机获取团列表
     * @desc 20171016
     * @param array $ids
     */
    public function groupsList($ids = [], $wid)
    {
        $groupsService = new GroupsService();
        $query = $groupsService->model;
        if ($ids) {
            $query = $query->whereNotIn('id', $ids);
        }
        $query = $query->where('status', 1)->where('wid', $wid);
        $res = $query->groupBy('rule_id')->inRandomOrder()->get()->take(5)->toArray();
        if (count($res) < 5 && !$ids) {
            $res2 = $groupsService->model->whereIn('id', $ids)->take(5 - count($res))->get()->toArray();
            $res = array_merge($res, $res2);
        }
        $ruleIds = $gids = [];
        foreach ($res as $val) {
            $ruleIds[] = $val['rule_id'];
            $gids[] = $val['id'];
        }
        $ruleService = new GroupsRuleService();
        $where['id'] = ['in', $ruleIds];
        $ruleData = $ruleService->getList($where);
        $ruleData = $this->dealGroupsRule($ruleData);
        $res = $this->dealKey($res, 'rule_id');
        $now = date('Y/m/d H:i:s', time());
        //获取参团信息
        $result = (new GroupsDetailService())->getGroups($gids);
        $gData = [];
        foreach ($result as $val) {
            $gData[$val['groups_id']][] = $val;
        }
        foreach ($ruleData as &$item) {
            $item['groupData'] = $res[$item['id']];
            $item['groupData']['member'] = $gData[$item['groupData']['id']] ?? '';
            /*  拼团结束时间*/
            $start_time = $item['groupData']['open_time'];

            //未成团订单存在时间 取活动配置的小时数 Herry 20171108
            //$end_time = date("Y/m/d H:i:s",(strtotime($start_time)+86400));
            if ($item['expire_hours']) {
                $end_time = date("Y/m/d H:i:s", (strtotime($start_time) + $item['expire_hours'] * 3600));
                if (strtotime($end_time) > strtotime($item['end_time'])) {
                    $end_time = $item['end_time'];
                }
            } else {
                $end_time = $item['end_time'];
            }

            $item['groupData']['end_time'] = date('Y/m/d H:i:s', strtotime($end_time));
            $item['now_time'] = $now;
        }
        return $ruleData;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $data
     * @param string $key
     * @return array
     */
    public function dealKey($data, $key = 'id')
    {
        $result = [];
        foreach ($data as $val) {
            $result[$val[$key]] = $val;
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171017
     * @desc 我的团购列表
     * @param $status 1:待成团，2：已发货，3：未成团
     */
    public function myGroups($mid, $status = '')
    {
        $groupsDetailService = new GroupsDetailService();
        $groups = $groupsDetailService->myGroups($mid, $status);
        $oids = $rids = [];
        foreach ($groups as $val) {
            $oids[] = $val['oid'];
            $rids[] = $val['rule_id'];
        }
        $where = [
            'id' => ['in', $rids]
        ];
        $res = (new GroupsRuleService())->getList($where);
        $ruleData = $this->dealGroupsRule($res);
        $temp = [];
        foreach ($ruleData as $val) {
            $temp[] = [
                'id' => $val['id'],
                'ptitle' => $val['pdata']['title'],
                'pimg' => $val['pdata']['img'],
                'min' => $val['min'],
                'max' => $val['max'],
                'groups_num' => $val['groups_num'],
            ];
        }
        $ruleData = $this->dealKey($temp);
        foreach ($groups as &$item) {
            $item['rule'] = $ruleData[$item['rule_id']] ?? [];
            $item['shareData'] = $this->getShareData($item['id']);
        }
        return $groups;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171019
     * @desc 根据id获取
     * @return  errCode = -1,团不存在,，-2：团活动未开始，-3，t团活动已结束，-4团不正常
     */
    public function getRuleById($id)
    {
        $ruleService = new GroupsRuleService();
        $ruleData = $ruleService->getRowById($id);
        if (!$ruleData) {
            $result['errCode'] = -1;
            $result['errMsg'] = '团不存在';
            return $result;
        }
        $now = time();
        if (strtotime($ruleData['start_time']) > $now) {
            $result['errCode'] = -2;
            $result['errMsg'] = '团活动未开始';
            return $result;
        }
        if (strtotime($ruleData['end_time']) < $now) {
            $result['errCode'] = -3;
            $result['errMsg'] = '团活动已结束';
            return $result;
        }
        if ($ruleData['status'] != 0) {
            $result['errCode'] = -4;
            $result['errMsg'] = '团活动不正常';
            return $result;
        }

        list($data) = $this->dealGroupsRule([$ruleData]);
        $result['errCode'] = 0;
        $result['data'] = $data;
        return $result;
    }


    /**
     * 获取首页获取信息
     * @param $id
     * @return mixed
     * @author 张永辉 2018年7月25
     * @update 何书哲 2018年11月09日 返回添加开始结束时间字段
     */
    public function getFirstGroups($id)
    {
        $ruleService = new GroupsRuleService();
        $ruleData = $ruleService->getRowById($id);
        if (!$ruleData) {
            $result['errCode'] = -1;
            $result['errMsg'] = '团不存在';
            return $result;
        }
        $now = time();
        // if (strtotime($ruleData['start_time'])>$now){
        //     $result['errCode'] = -2;
        //     $result['errMsg'] = '团活动未开始';
        //     return $result;
        // }
        if (strtotime($ruleData['end_time']) < $now) {
            $result['errCode'] = -3;
            $result['errMsg'] = '团活动已结束';
            return $result;
        }
        if ($ruleData['status'] != 0) {
            $result['errCode'] = -4;
            $result['errMsg'] = '团活动不正常';
            return $result;
        }
        $redisClient = (new RedisClient())->getRedisClient();
        $key = 'micropage:rule:id:' . $id;
        $resultData = $redisClient->get($key);
        if ($resultData) {
            return json_decode($resultData, true);
        }
        $groupsService = new GroupsService();
        $memberService = new MemberService();
        $min = (new GroupsSkuService())->getMinPriceByRule($ruleData['id']);
        $product = (new ProductService())->getRowById($ruleData['pid']);
        $mnum = $groupsService->getSumNum($ruleData['id']);
        #todo 优化从redis中获取刚参加过该拼团活动的用户id
        $member = [];
        if ($mids = $groupsService->getLastMemberIds($ruleData['id'])) {
            $res = $memberService->getListById($mids);
            foreach ($res as $val) {
                $member[] = [
                    'id' => $val['id'],
                    'headimgurl' => $val['headimgurl'],
                    'nickname' => $val['nickname'],
                ];
            }
        }

        $result['errCode'] = '0';
        $result['errMsg'] = '';
        $data = [
            'id' => $ruleData['id'],
            'label' => $ruleData['label'],
            'title' => $ruleData['title'],
            'subtitle' => $ruleData['subtitle'],
            'min' => $min,
            'img2' => $ruleData['img2'],
            'img' => $ruleData['img'],
            'groups_num' => $ruleData['groups_num'],
            'mnum' => $mnum,
            'pnum' => $product['sold_num'] ?? 0,
            'member' => $member,
            'start_time' => $ruleData['start_time'],
            'end_time' => $ruleData['end_time'],
        ];
        $result['data'] = $data;
        $redisClient->set($key, json_encode($result));
        $redisClient->EXPIRE($key, 60);
        return $result;

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171023
     * @desc 获取假的用户头像
     * @param $groups_id 团id
     * @return  string 头像连接地址
     */
    public function fictitiousMember($groups_id)
    {
        //随机生成一张图片，然后存储到redis里面，下次不在重新生成，以免出现同团在不同时间参团头像不相同的问题
        $key = 'fictitious_member_hreadimg:' . $groups_id;
        $headImg = Redisx::GET($key);
        if (!$headImg) {
            $sourceData = ['001', '002', '003', '004', '005', '006', '007', '008', '009', '010'];
            $headImgkey = array_rand($sourceData, 1);
            $headImg = config('app.url') . 'hsshop/other/headimg/' . $sourceData[$headImgkey] . '.jpg';
            Redisx::SET($key, $headImg);
        }
        return $headImg;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171025
     * @desc 获取拼团信息
     * @param $wid
     */
    public function getGroupsMessage($wid)
    {
        $groupDetailService = new GroupsDetailService();
        $res = $groupDetailService->getGroupsMessage($wid);
        $mids = [];
        foreach ($res as $val) {
            $mids[] = $val['member_id'];
        }
        $memberData = (new MemberService())->getListById($mids);
        $memberData = $this->dealKey($memberData);
        $result = [];
        //获取拼团的相关头像信息
        foreach ($res as $val) {
            $result[] = [
                'id' => $val['id'],
                'groups_id' => $val['groups_id'],
                'headimgurl' => $memberData[$val['member_id']]['headimgurl'] ?? '',
                'nickname' => $memberData[$val['member_id']]['nickname'] ?? '',
                'sec' => rand(1, 11),
            ];
        }

        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 根据团ID获取团分享信息
     * @param $groups_id
     * @return array 返回分享信息数组
     */
    public function getShareData($groups_id)
    {
        $groupsData = (new GroupsService())->getRowById($groups_id);
        $result = [];
        if ($groupsData) {
            $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
            if (!$ruleData) {
                return $result;
            }
            $result = [
                'share_title' => $ruleData['share_title'] ? $ruleData['share_title'] : $ruleData['title'],
                'share_desc' => $ruleData['share_desc'] ? $ruleData['share_desc'] : $ruleData['subtitle'],
                'share_img' => $ruleData['share_img'] ? $ruleData['share_img'] : $ruleData['img2'],
            ];
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 根据店铺id获取店铺标签
     * @param $wid
     */
    public function getShopLable($wid)
    {
        if (in_array($wid, config('app.li_wid'))) {
            return [
                'img' => 'hsshop/other/headimg/276874300591292416.png',
                'wid' => $wid,
                'title' => '服务保障',
                'id' => 1,
                'content' => [
                    [
                        'title' => '小程序定制先行者·助力商户畅享小程序流量红利',
                        'content' => ''
                    ],
                ]
            ];
        } else {
            return [
                'img' => 'hsshop/other/headimg/276874300591292416.png',
                'wid' => $wid,
                'title' => '服务保障',
                'id' => 1,
                'content' => [
                    [
                        'title' => '全场包邮',
                        'content' => 'you家支持全国绝大部分地区包邮（偏远地区除外，如新疆、西藏、内蒙古、宁夏、青海、甘肃等）'
                    ],
                    [
                        'title' => '品质保证',
                        'content' => 'you家精选商城所售商品，直接与大型品牌生产厂家合作，保证商品品质。'
                    ],
                    [
                        'title' => '七天无忧退换',
                        'content' => '买家收到商品后7天内，符合消费者保障法规，可以申请无理由退换货（特殊商品除外，如直接接触皮肤商品、食品类商品、定做类商品、明示不支持无理由退换货商品等）'
                    ]
                ]
            ];
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171027
     * @desc 获取我的参团id
     */
    public function getMyGroupsIds($mid)
    {
        $res = (new GroupsDetailService())->getListByWhere(['member_id' => $mid]);
        $ids = [];
        if ($res) {
            foreach ($res as $val) {
                $ids[] = $val['groups_id'];
            }
        }
        return $ids;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171225
     * @desc当前可购买数量
     * @param $mid
     * @param $rule_id
     */
    public function limtNum($mid, $rule_id)
    {
        $groupsRuleService = new GroupsRuleService();
        $ruleData = $groupsRuleService->getRowById($rule_id);
        if ($ruleData['limit_type'] == -1) {
            return -1;
        } elseif ($ruleData['limit_type'] == 0) {
            return $ruleData['num'];
        } elseif ($ruleData['limit_type'] == 1) {
            $groupsService = new GroupsService();
            $groupsData = $groupsService->getListByRuleId($rule_id);
            if ($groupsData) {
                $gids = array_column($groupsData, 'id');
                $num = OrderService::getGroupsNum($gids, $mid);
                return $ruleData['num'] - $num;
            } else {
                return $ruleData['num'];
            }
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180509
     * @desc 判断店铺是否有权限
     */
    public function checkShopIsPermission($wid, $type)
    {
        $weixinRoleService = new WeixinRoleService();
        $res = $weixinRoleService->getShopPermission($wid);
        if (!in_array($type, $res)) {
            return false;
        } else {
            return true;
        }
    }


}
