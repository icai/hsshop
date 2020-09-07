<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/10/11
 * Time: 15:17
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\Module\DistributeModule;
use App\Module\EvaluateModule;
use App\Module\GroupsRuleModule;
use App\Module\OrderModule;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Member\MemberService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Wechat\WeixinLableService;
use App\Services\Order\OrderService;
use App\Services\ProductEvaluateService;
use App\Services\Shop\MemberAddressService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use Validator;
use App\S\Weixin\ShopService;

class GroupsController extends Controller
{
    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购详情
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2019年06月27日 团购关联商品已删除情况
     */
    public function detail(Request $request, ShopService $shopService, $ruleId)
    {
        $wid = $request->input('wid');
        $mid = $request->input('mid');
        if (!is_numeric($ruleId)) {
            xcxerror('团id必须为数值');
        }
        $ruleModule = new GroupsRuleModule();
        $ruleData = $ruleModule->getById($ruleId, $mid);
        if ($ruleData === false) {
            xcxerror('关联商品已删除');
        }
        if (!$ruleData) {
            xcxerror('团购不存在');
        }
        //计算佣金
        /*$shopData = (new WeixinService())->init()->model->where('id',$wid)->get()->toArray();
        if ($shopData){
            $shopData = current($shopData);
        }*/
        $shopData = $shopService->getRowById($wid);
        $max = $ruleData['max'];
        if (!$ruleData['max']) {
            $max = $ruleData['min'];
        }
        $tid = $ruleData['distribute_template_id'];
        $distribute = (new DistributeModule())->getProductDistributePrice($shopData, $max, $mid, $tid);
        $ruleData['distribute'] = $distribute[0];
        //end

        $groupsData = $ruleModule->getDetailByRuleId($ruleId);
        $ruleData['pnum'] = $groupsData['pnum'];
        if (strtotime($ruleData['end_time']) <= time()) {
            $ruleData['is_over'] = 1;
        } else {
            $ruleData['is_over'] = 0;
        }
        //获取店铺标签
        $wid = $request->input('wid');
        $ruleData['weixinLable'] = $ruleModule->getShopLable($wid);
        $ruleData['surplus'] = $ruleModule->limtNum($mid, $ruleId);
        $ruleData['shop'] = $shopData;
        $ruleData['now_time'] = date('Y-m-d H:i:s');
        xcxsuccess('操作成功', $ruleData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购列表
     */
    public function getGroups($ruleId, Request $request)
    {
        $ruleModule = new GroupsRuleModule();
        if (!is_numeric($ruleId)) {
            xcxerror('团购id不存在');
        }
        $result['num'] = $ruleModule->getGroupsNum($ruleId, $request->input('mid'));
        $result['data'] = $ruleModule->getGroups($ruleId, $request->input('mid'));
        xcxsuccess('操作成功', $result);
    }

    public function getDetailEvaluate($pid)
    {
        $productEvaluate = new ProductEvaluateService();
        $evaluateModule = new EvaluateModule();
        $pid = intval($pid);
        if (empty($pid)) {
            xcxerror('请传递商品id');
        }
        $result['num'] = $productEvaluate->getEvaluateNumByPid($pid);
        $data = $evaluateModule->getProductEvaluate($pid);
        $result['data'] = [];
        if ($data) {
            $result['data'] = $data[0];
        }
        xcxsuccess('操作成功', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取商品评价信息
     * @param $pid
     */
    public function getEvaluateClassify(Request $request, $pid)
    {
        $productEvaluate = new ProductEvaluateService();
        $pid = intval($pid);
        if (empty($pid)) {
            xcxerror('请传递商品id');
        }
        $res = $productEvaluate->getCountByClassify($pid);
        xcxsuccess('操作成功', $res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 获取商品评价信息
     * @param Request $request
     * @param $pid
     */
    public function getProductEvaluate(Request $request, $pid)
    {
        $classifyName = $request->input('classifyName') ?? '';
        $evaluateModule = new EvaluateModule();
        $result = $evaluateModule->getProductEvaluate($pid, $classifyName);
        xcxsuccess('操作成功', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc
     */
    public function recommendGroups(Request $request)
    {
        $wid = $request->input('wid');
        $ruleid = $request->input('ruleId', '');
        $res = (new GroupsRuleModule())->getRecommend($wid, $ruleid);
        xcxsuccess('操作成功', $res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc  获取团购商品sku
     * @param Request $request
     * @param GroupsSkuService $groupsSkuService
     * @param GroupsRuleService $groupsRuleService
     */
    public function getSkus(Request $request, GroupsSkuService $groupsSkuService, GroupsRuleService $groupsRuleService, $rule_id)
    {
        $ruleData = $groupsRuleService->getRowById($rule_id);
        if (!$ruleData) {
            xcxerror('团不存在');
        }
        $skus = (new ProductPropsToValuesService())->getSkuList($ruleData['pid']);
        $res = $groupsSkuService->getlistByRuleId($rule_id);
        $groupsSkus = [];
        foreach ($res as $val) {
            $groupsSkus[$val['sku_id']] = $val;
        }
        foreach ($skus['stocks'] as &$v) {
            $v['price'] = $groupsSkus[$v['id']]['price'] ?? $val['price'];
        }
        xcxsuccess('操作成功', ['data' => $skus]);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 获取结算信息
     * @param Request $request
     * @update 陈文豪 返回用户信息用于余额支付
     */
    public function getSettlementInfo(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'pid' => 'required',
            'num' => 'required|integer|min:1',

        );
        $message = Array(
            'pid.required' => '商品id不能为空',
            'num.required' => '数量不能为空',
            'num.integer' => '数量必须是正整数',
            'num.min' => '最小数量为1',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }

        $ruleModule = new GroupsRuleModule();
        $res = $ruleModule->getSettlementInfo($input['pid'], $request->input('sku_id'));
        if ($res['errCode'] != 0) {
            xcxerror($res['errMsg']);
        }
        $pData = $res['data'];
        $result = [
            'rule_id' => $request->input('rule_id') ?? 0,
            'groups_id' => intval($request->input('groups_id')) ?? 0,
            'pid' => $pData['id'],
            'title' => $pData['title'],
            'sku_flag' => $pData['sku_flag'],
            'skuData' => $pData['skuData'],
            'num' => $input['num'],
            'no_logistics' => $pData['no_logistics'],
        ];

        if ($request->input('rule_id')) {
            $ruleData = (new GroupsRuleService())->getRowById($request->input('rule_id'));
            if (!$ruleData) {
                xcxerror('团规则不存在');
            }
            if ($ruleData['pid'] != $input['pid']) {
                xcxerror('商品有误');
            }
            $skuId = $request->input('sku_id') ?? 0;
            list($ruleSkuData) = (new GroupsSkuService())->getlistByWhere(['rule_id' => $input['rule_id'], 'sku_id' => $skuId]);
            if (!intval($request->input('groups_id'))) {
                $result['allPrice'] = $input['num'] * $ruleSkuData['head_price'];
                $result['price'] = $ruleSkuData['head_price'];
            } else {
                $result['allPrice'] = $input['num'] * $ruleSkuData['price'];
                $result['price'] = $ruleSkuData['price'];
            }
            $result['ruleNum'] = $ruleData['num'];
        } else {
            if ($result['sku_flag']) {
                $result['allPrice'] = $input['num'] * $pData['skuData']['price'];
                $result['price'] = $pData['skuData']['price'];
            } else {
                $result['allPrice'] = $input['num'] * $pData['price'];
                $result['price'] = $pData['price'];
            }
            $result['ruleNum'] = $pData['quota'];
        }

        //拼团代付款页面返回运费字段 Herry 20171208
        $groupBuyInfo = [
            [
                'product_id' => $input['pid'],
                'prop_id' => $input['sku_id'],
                'num' => $input['num']
            ]
        ];
        //运费
        //获取umid
        $member = (new MemberService())->getRowById($input['mid']);
        $addressID = $request->input('address_id', 0);
        $result['freight'] = (new OrderModule())->getFreightByCartIDArr([], $input['wid'], $input['mid'], $member['umid'], $addressID, $groupBuyInfo);
        $result['freight'] = sprintf('%.2f', $result['freight']);
        //加上运费的总金额
        $result['allPrice'] = $result['allPrice'] + $result['freight'];

        $result['allPrice'] = sprintf('%.2f', $result['allPrice']);
        $result['surplus'] = $ruleModule->limtNum($request->input('mid'), $result['rule_id']);
        $result['member'] = $member;
        xcxsuccess('操作成功', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 创建订单
     */
    public function createOrder(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'pid' => 'required',
            'num' => 'required',
            'address_id' => 'required'
        );
        $message = Array(
            'pid.required' => '商品id不能为空',
            'num.required' => '数量不能为空',
            'address_id.required' => '地址id不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }
        $input['address_id'] = intval($input['address_id']);
        $addressData = (new MemberAddressService())->getAddressById($input['address_id']);
        if (!$addressData) {
            $addressData['id'] = 0;
            $addressData['title'] = $addressData['phone'] = $addressData['detail'] = $addressData['province']['title'] = $addressData['city']['title'] = $addressData['area']['title'] = '';
        }

        $groups_id = $request->input('groups_id') ?? 0;
        if ($request->input('rule_id') && !$request->input('groups_id')) {
            $res = (new GroupsRuleModule())->createGroups($request->input('rule_id'));
            if ($res['success'] == 0) {
                xcxerror($res['message']);
            } else {
                $groups_id = $res['data'];
            }
        }

        $mData = (new MemberService())->getRowById($request->input('mid'));
        $source = 1;
        if ($request->input('come_from') && $request->input('come_from') == 'byteDance') {
            $source = 4;
        }
        $res = (new OrderModule())->createOrder($mData, $addressData, $groups_id, '', $source);
        if ($res['errCode'] != 0) {
            xcxerror($res['errMsg']);
        } else {
            xcxsuccess('操作成功', $res['data']);
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 参团信息
     */
    public function groupsDetail(Request $request, ShopService $shopService, $groups_id)
    {
        $groupsService = new GroupsService();
        $ruleModule = new GroupsRuleModule();
        $groupsData = $groupsService->getRowById($groups_id);
        if (!$groupsData) {
            xcxerror('该团不存在');
        }
        $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
        $res = $ruleModule->getGroupsById($groups_id, $request->input('mid'));
        if ($res['success'] == 0) {
            xcxerror($res['message']);
        }
        $result['rule'] = $res['rule'];
        $result['rule']['save'] = bcsub($result['rule']['product']['price'], $result['rule']['min'], 2);
        $groups = [];
        $result['groups'] = $res['groups'];
        $result['groups']['is_join'] = 0;
        $mid = $request->input('mid');
        foreach ($res['groupsDetail'] as $val) {
            $groups[] = [
                'id' => $val['id'],
                'groups_id' => $val['groups_id'],
                'oid' => $val['oid'],
                'is_head' => $val['is_head'],
                'headimgurl' => $val['member']['headimgurl'],
                'nickname' => $val['member']['nickname'],
                'created_at' => $val['created_at'],
            ];
            if ($mid == $val['member_id']) {
                $result['groups']['is_join'] = 1;
                $result['groups']['join_time'] = $val['created_at'];
            }
        }
        //如果手动成团添加虚拟名额
        if ($result['groups']['status'] == 2 && $result['rule']['groups_num'] > count($groups)) {
            $num = $result['rule']['groups_num'] - count($groups);
            for ($i = 0; $i < $num; $i++) {
                $groups[] = [
                    'id' => '',
                    'groups_id' => '',
                    'oid' => '',
                    'is_head' => '',
                    'headimgurl' => $ruleModule->fictitiousMember($groups_id),
                    'nickname' => '',
                    'created_at' => '',
                ];
            }
        }

        $result['groupsDetail'] = $groups;

        /*  拼团结束时间*/
        $start_time = $result['groups']['open_time'];

        //未成团订单存在时间 取活动配置的小时数 Herry 20171108
        if ($result['rule']['expire_hours']) {
            $end_time = date("Y/m/d H:i:s", (strtotime($start_time) + $result['rule']['expire_hours'] * 3600));
            if (strtotime($end_time) > strtotime($result['rule']['end_time'])) {
                $end_time = $result['rule']['end_time'];
            }
        } else {
            $end_time = $result['rule']['end_time'];
        }
        if (strtotime($result['rule']['end_time']) <= time()) {
            $result['rule']['is_over'] = 1;
        } else {
            $result['rule']['is_over'] = 0;
        }

        $result['groups']['end_time'] = date('Y/m/d H:i:s', strtotime($end_time));
        $result['groups']['now_time'] = date('Y/m/d H:i:s', time());
        $result['groups']['oid'] = $res['order_id'];
        $result['order'] = $res['order'];
        //获取店铺标签
        $result['weixinLable'] = $ruleModule->getShopLable($request->input('wid'));
        $result['surplus'] = $ruleModule->limtNum($mid, $groupsData['rule_id']);
        $result['shopData'] = $shopService->model->where('id', $request->input('wid'))->value('shop_name');
        xcxsuccess('操作成功', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 团列表
     */
    public function groupsList(Request $request)
    {
        $ids = $request->input('ids') ?? [];
        if ($ids) {
            $ids = json_decode($ids, true);
        }
        $ruleModule = new GroupsRuleModule();
        $data = $ruleModule->groupsList($ids, $request->input('wid'));
        xcxsuccess('操作成功', $data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171016
     * @desc 我的团购
     */
    public function myGroups(Request $request)
    {
        $ruleModule = new GroupsRuleModule();
        $data = $ruleModule->myGroups($request->input('mid'), $request->input('status'));
        xcxsuccess('操作成功', [$data, $request->input('wid')]);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $id
     */
    public function groupsById($id)
    {
        $res = (new GroupsService())->getRowById($id);
        xcxsuccess('操作成功', $res);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171025
     * @desc 获取团购消息
     */
    public function getGroupsMessage(Request $request)
    {
        $result = (new GroupsRuleModule())->getGroupsMessage($request->input('wid'));
        xcxsuccess('操作成功', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 获取分享信息
     * @param $groups_id
     */
    public function getShareData($groups_id)
    {
        $result = (new GroupsRuleModule())->getShareData($groups_id);
        xcxsuccess('操作成功', $result);
    }

}