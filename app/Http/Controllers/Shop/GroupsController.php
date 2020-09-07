<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/10/11
 * Time: 15:17
 */

namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Model\Favorite;
use App\Module\DistributeModule;
use App\Module\EvaluateModule;
use App\Module\FavoriteModule;
use App\Module\GroupsRuleModule;
use App\Module\OrderModule;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\S\Member\MemberService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Wechat\WeixinLableService;
use OrderService;
use App\Services\ProductEvaluateService;
use App\Services\Shop\MemberAddressService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use Validator;
use App\S\Lift\ReceptionService;
use ProductService;
use App\S\Weixin\ShopService;

class GroupsController extends Controller
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购详情
     * @update 许立 2018年09月04日 增加是否收藏字段
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2019年06月27日 团购关联商品已删除情况
     */
    public function detail(Request $request, FavoriteModule $favoriteModule, ShopService $shopService, $ruleId)
    {
        $wid = session('wid');
        $mid = session('mid');
        $ruleModule = new GroupsRuleModule();
        $ruleData = $ruleModule->getById($ruleId, $mid);
        if ($ruleData === false) {
            error('关联商品已删除');
        }
        if (!$ruleData) {
            error('团不存在');
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
        $wid = session('wid');
        $ruleData['weixinLable'] = $ruleModule->getShopLable($wid);
        $ruleData['surplus'] = $ruleModule->limtNum($mid, $ruleId);
        $ruleData['member'] = (new MemberService())->getRowById(session('mid'));
        $shopData['sign'] = md5(session('wid') . session('mid') . 'huisou');
        $ruleData['shop'] = $shopData;
        $ruleData['isFavorite'] = $favoriteModule->isFavorite($mid, $ruleId, Favorite::FAVORITE_TYPE_GROUP);
        $ruleData['now_time'] = date('Y-m-d H:i:s');
        success('操作成功', '', $ruleData);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171011
     * @desc 获取团购列表
     */
    public function getGroups($ruleId)
    {
        $ruleModule = new GroupsRuleModule();
        $result['num'] = $ruleModule->getGroupsNum($ruleId, session('mid'));
        $result['data'] = $ruleModule->getGroups($ruleId, session('mid'));
        success('操作成功', '', $result);
    }

    public function getDetailEvaluate($pid)
    {
        $productEvaluate = new ProductEvaluateService();
        $evaluateModule = new EvaluateModule();
        $result['num'] = $productEvaluate->getEvaluateNumByPid($pid);
        $data = $evaluateModule->getProductEvaluate($pid);
        $result['data'] = [];
        if ($data) {
            $result['data'] = $data[0];
        }
        success('操作成功', '', $result);
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
        $res = $productEvaluate->getCountByClassify($pid);
        success('操作成功', '', $res);
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
        success('操作成功', '', $result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc
     */
    public function recommendGroups(Request $request)
    {
        $wid = session('wid');
        $res = (new GroupsRuleModule())->getRecommend($wid);
        success('操作成功', '', $res);
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
            error('团不存在');
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

        success('操作成功', '', $skus);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 获取结算信息
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getSettlementInfo(Request $request, ShopService $shopService)
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
            error($validator->errors()->first());
        }
        $ruleModule = new GroupsRuleModule();
        $res = $ruleModule->getSettlementInfo($input['pid'], $request->input('sku_id'));
        if ($res['errCode'] != 0) {
            error($res['errMsg']);
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
                error('团规则不存在');
            }
            if ($ruleData['pid'] != $input['pid']) {
                error('商品有误');
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
        $addressID = $request->input('address_id', 0);
        $result['freight'] = (new OrderModule())->getFreightByCartIDArr([], session('wid'), session('mid'), session('umid'), $addressID, $groupBuyInfo);
        $result['freight'] = sprintf('%.2f', $result['freight']);
        //加上运费的总金额
        $result['allPrice'] = $result['allPrice'] + $result['freight'];

        $result['allPrice'] = sprintf('%.2f', $result['allPrice']);

        //获取用户余额 Herry 20171221
        $member = (new MemberService())->getRowById(session('mid'));
        $result['surplus'] = $ruleModule->limtNum(session('mid'), $result['rule_id']);

        /**add by wuxiaoping 2018.06.11 添加自提模块功能**/
        //$store = (new WeixinService())->getStageShop(session('wid'));
        $store = $shopService->getRowById(session('wid'));
        $isDeliveryShow = false;  // 是否显示配送方式选择
        if ($store['is_ziti_on'] == 1) {
            $detail = ProductService::getDetail($result['pid']);
            if ($detail['is_hexiao'] == 1) {
                $isDeliveryShow = true;
            }
        }//end
        return view('shop.grouppurchase.getSettlementInfo', array(
            'title' => '结算页面',
            'data' => $result,
            'balance' => !empty($member) ? $member['money'] / 100 : 0.00,
            'isDeliveryShow' => $isDeliveryShow
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 创建订单
     * @update 许立 2018年10月16日 百度小程序来源处理
     */
    public function createOrder(Request $request)
    {
        $input = $request->input();
        $isHexiao = $input['is_hexiao'] ?? 0;
        $heXiaoData = [];
        if ($isHexiao == 1) {
            $heXiaoData = ['zitiContact' => $input['zitiContact'], 'zitiPhone' => $input['zitiPhone'], 'zitiId' => $input['zitiId'], 'zitiDatetime' => $input['zitiDatetime']];
            $rule = Array(
                'zitiContact' => 'required',
                'zitiPhone' => 'required',
                'zitiId' => 'required',
                'zitiDatetime' => 'required'
            );
            $message = Array(
                'zitiContact.required' => '提货人不能为空',
                'zitiPhone.required' => '提货人手机号不能为空',
                'zitiId.required' => '提货地址不能为空',
                'zitiDatetime.required' => '提货时间不能为空'
            );
        } else {
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
        }

        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $addressData = [];
        if ($isHexiao == 0) {
            $addressData = (new MemberAddressService())->getAddressById($input['address_id']);
            if (!$addressData) {
                $addressData['id'] = 0;
                $addressData['title'] = $addressData['phone'] = $addressData['detail'] = $addressData['province']['title'] = $addressData['city']['title'] = $addressData['area']['title'] = '';
            }
        }
        //修复团购下单参数传递问题 永辉 20171207
        $groups_id = $request->input('groups_id') ?? 0;
        $groups_id = intval($groups_id);
        if ($request->input('rule_id') && !$groups_id) {
            $res = (new GroupsRuleModule())->createGroups($request->input('rule_id'));
            if ($res['success'] == 0) {
                error($res['message']);
            } else {
                $groups_id = $res['data'];
            }
        }

        $mData = (new MemberService())->getRowById(session('mid'));

        if (session('reqFrom') == 'aliapp') {
            $source = 2;
        } elseif (session('reqFrom') == 'baiduapp') {
            $source = 3;
        } else {
            $source = 0;
        }

        $res = (new OrderModule())->createOrder($mData, $addressData, $groups_id, session('wid'), $source, $isHexiao, $heXiaoData);
        if ($res['errCode'] != 0) {
            error($res['errMsg']);
        } else {
            success('操作成功', '', $res['data']);
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
            error('该团不存在');
        }
        $ruleData = (new GroupsRuleService())->getRowById($groupsData['rule_id']);
        $res = $ruleModule->getGroupsById($groups_id, session('mid'));
        if ($res['success'] == 0) {
            error($res['message']);
        }
        $result['rule'] = $res['rule'];
        $result['rule']['save'] = bcsub($result['rule']['product']['price'], $result['rule']['min'], 2);
        $groups = [];
        $result['groups'] = $res['groups'];
        $result['groups']['is_join'] = 0;
        $mid = session('mid');
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
            for ($i = 0; $i < ($result['rule']['groups_num'] - count($groups)); $i++) {
                $groups[] = [
                    'id' => '',
                    'groups_id' => '',
                    'oid' => '',
                    'is_head' => '0',
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

        $result['weixinLable'] = $ruleModule->getShopLable(session('wid'));
        $result['surplus'] = $ruleModule->limtNum($mid, $groupsData['rule_id']);
        $result['shopData'] = $shopService->model->where('id', $request->input('wid'))->value('shop_name');

        success('操作成功', '', $result);
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
        $data = $ruleModule->groupsList($ids, session('wid'));
        success('操作成功', '', $data);
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
        $data = $ruleModule->myGroups(session('mid'), $request->input('status'));
        success('操作成功', '', [$data, session('wid')]);
    }

    public function addGroupList()
    {
        return view('shop.grouppurchase.addGroupList', array(
            'title' => '一键参团',
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171025
     * @desc 获取团购消息
     */
    public function getGroupsMessage()
    {
        $result = (new GroupsRuleModule())->getGroupsMessage(session('wid'));
        success('操作成功', '', $result);
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
        success('操作成功', '', $result);
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
        success('操作成功', '', $res);
    }

    public function showMyGroups()
    {
        return view('shop.grouppurchase.showMyGroups', array(
            'title' => '我的团购',
        ));
    }

    /**
     * 拼团获取运费接口
     */
    public function getFreight(Request $request)
    {
        //检查参数
        $input = $request->input();
        $rule = [
            'pid' => 'required',
            'num' => 'required|integer|min:1',
        ];
        $message = [
            'pid.required' => '商品id不能为空',
            'num.required' => '数量不能为空',
            'num.integer' => '数量必须是正整数',
            'num.min' => '最小数量为1',
        ];
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $addressID = $request->input('address_id', 0);

        $groupBuyInfo = [
            [
                'product_id' => $input['pid'],
                'prop_id' => $input['sku_id'],
                'num' => $input['num']
            ]
        ];

        $freight = (new OrderModule())->getFreightByCartIDArr([], session('wid'), session('mid'), session('umid'), $addressID, $groupBuyInfo);

        success('', '', $freight);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201804228
     * @desc 拼团详情预览
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2019年06月27日 团购关联商品已删除情况
     */
    public function preview(ShopService $shopService, $ruleId)
    {
        $ruleModule = new GroupsRuleModule();
        $ruleData = $ruleModule->getById($ruleId);
        if ($ruleData === false) {
            error('关联商品已删除');
        }
        if (!$ruleData) {
            error('团不存在');
        }

        //计算佣金
        $wid = session('wid');
        $mid = session('mid');
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
        //end

        $groupsData = $ruleModule->getDetailByRuleId($ruleId);
        $ruleData['pnum'] = $groupsData['pnum'];
        if (strtotime($ruleData['end_time']) <= time()) {
            $ruleData['is_over'] = 1;
        } else {
            $ruleData['is_over'] = 0;
        }
        //获取店铺标签
        $wid = session('wid');
        $ruleData['weixinLable'] = $ruleModule->getShopLable($wid);
        $ruleData['member'] = (new MemberService())->getRowById(session('mid'));
        $shopData['sign'] = md5(session('wid') . session('mid') . 'huisou');
        $ruleData['shop'] = $shopData;
        return view('shop.grouppurchase.preview', array(
            'title' => '拼团列表',
            'rule_id' => $ruleId,
            'ruleData' => $ruleData,
        ));
    }

    public function getPreViewGroups($ruleId)
    {
        $ruleModule = new GroupsRuleModule();
        $result['num'] = $ruleModule->getGroupsNum($ruleId, 0);
        $result['data'] = $ruleModule->getGroups($ruleId, 0);
        success('操作成功', '', $result);
    }

    public function preViewRecommendGroups(Request $request)
    {
        $wid = session('wid');
        $res = (new GroupsRuleModule())->getRecommend($wid);
        success('操作成功', '', $res);
    }

    public function getPreViewDetailEvaluate($pid)
    {
        $productEvaluate = new ProductEvaluateService();
        $evaluateModule = new EvaluateModule();
        $result['num'] = $productEvaluate->getEvaluateNumByPid($pid);
        $data = $evaluateModule->getProductEvaluate($pid);
        $result['data'] = [];
        if ($data) {
            $result['data'] = $data[0];
        }
        success('操作成功', '', $result);
    }


}