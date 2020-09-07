<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/7/3
 * Time: 10:04
 */

namespace App\Http\Controllers\shop;


use App\Http\Controllers\Controller;
use App\S\Groups\GroupsRuleService;
use App\S\Groups\GroupsService;
use App\S\Groups\GroupsSkuService;
use App\Module\GroupsRuleModule;
use App\S\Product\ProductPropsToValuesService;
use App\Services\ProductPropService;
use App\Services\ReserveService;
use App\Services\Shop\CartService;
use Illuminate\Http\Request;
use WeixinService;
use ProductService;
use Bi;
use Validator;
use App\S\PublicShareService;
use App\S\Weixin\ShopService;

class GroupPurchaseController extends Controller
{

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170703
     * @desc 团购列表
     */
    public function index(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        $_REQUEST['status'] = 2;
        $ruleData = $groupsRuleModule->getRule(['wid' => session('wid')]);
        if ($request->input('page') && $request->input('page') != 1) {
            return mysuccess('操作成功', '', $ruleData['0']);
        }
        $shareData = [];
        $shareData['share_title'] = '一起 拼 团 购！！！';
        $shareData['share_desc'] = '最新拼团优惠，赶紧点击'; //去掉换行符
        $shareData['share_img'] = imgUrl() . 'static/images/shareimg.png';
        return view('shop.grouppurchase.index', array(

            'title' => '一起  拼 团 购 ! ! !',
            'rule' => $ruleData,
            'shareData' => $shareData,

        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170704
     * @desc 团购详情
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2019年06月27日 团购关联商品已删除情况
     */
    public function detail(GroupsRuleModule $groupsRuleModule, ShopService $shopService, $id)
    {
        $ruleData = $groupsRuleModule->getById($id);

        // update 何书哲 2019年06月27日 团购关联商品已删除情况
        if ($ruleData === false) {
            error('关联商品已删除');
        }
        if (!$ruleData) {
            error('团不存在');
        }

        $_REQUEST['status'] = 2;
        list($res) = $groupsRuleModule->getRule(['wid' => session('wid')], 'end_time', 'ASC');
        foreach ($res['data'] as $key => $val) {
            if ($val['id'] == $ruleData['id']) {
                unset($res[$key]);
                break;
            }
        }
        $moreData = array_slice($res['data'], 0, 6);
        $ruleData['overtime'] = strtotime($ruleData['end_time']) - time();
        $groups = [];
        if ($ruleData['is_open'] == 1) {
            $groups = $groupsRuleModule->getGroups($id);
        }
        //获取购物车数量
        $num = (new CartService())->cartNum(session('mid'), session('wid'));


        //拼团活动已结束
        if (strtotime($ruleData['end_time']) <= time()) {
            return redirect('shop/product/detail/' . session('wid') . '/' . $ruleData['product']['id']);
        }

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop(session('wid'));
        $shop = $shopService->getRowById(session('wid'));
        /*add by wuxiaoping 分享数据*/
        $shareData = [];
        $shareData['share_title'] = $ruleData['share_title'] ? $ruleData['share_title'] : $ruleData['title'];
        $shareData['share_desc'] = $ruleData['share_desc'] ? str_replace(PHP_EOL, '', $ruleData['share_desc']) : $shop['shop_name'] . '-多人拼团活动'; //去掉换行符
        $shareData['share_img'] = $ruleData['share_img'] ? imgUrl() . $ruleData['share_img'] : imgUrl() . $ruleData['product']['img'];

        //update by wuxiaoping 2017.08.30 通用的分享设置信息
        if (empty($ruleData['share_title']) && empty($ruleData['share_desc']) && empty($ruleData['share_img'])) {
            $shareData = (new PublicShareService())->publicShareSet(session('wid'));
        }


        return view('shop.grouppurchase.detail', array(
            'title' => '团购详情',
            'rule' => $ruleData,
            'more' => $moreData,
            'groups' => $groups,
            'num' => $num,
            'shareData' => $shareData,
            'nowTime' => date("Y/m/d H:i:s", time()),
        ));
    }


    public function detail2($rule_id)
    {
        $groupsRuleModule = new GroupsRuleModule();

        $res = $groupsRuleModule->checkShopIsPermission(session('wid'), 'hsshop_weixin_spell_goods');
        if (!$res) {
            return redirect('shop/member/noPermission');
        }
        return view('shop.grouppurchase.detail2', array(
            'title' => '团购详情',
            'rule_id' => $rule_id,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170713
     * @desc
     */
    public function getSkus(Request $request, GroupsSkuService $groupsSkuService, GroupsRuleService $groupsRuleService)
    {
        $rule_id = $request->input('rule_id');
        if (empty($rule_id)) {
            error('参数不能为空');
        }
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
            $v['price'] = $groupsSkus[$v['id']]['price'];
        }

        success('操作成功', '', $skus);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 玩法详情
     */
    public function guide(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        return view('shop.grouppurchase.guide', array(
            'title' => '玩法详情'
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 拼团-待付款的订单
     */
    public function newOrder(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        return view('shop.grouppurchase.newOrder', array(
            'title' => '待付款的订单',
            'shareData' => (new PublicShareService())->publicShareSet(session('wid'))
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 拼团详情
     */
    public function groupon($group_id, GroupsRuleModule $groupsRuleModule)
    {
        $result = $groupsRuleModule->getGroupsById($group_id, session('mid'));
        if ($result['success'] == 0) {
            error();
        }
        //拼团活动已结束
        if ($result['groups']['status'] == 0 || strtotime($result['rule']['end_time']) <= time()) {
            return redirect('shop/product/detail/' . session('wid') . '/' . $result['rule']['product']['id']);
        }
        //团已结束
        if ((time() - strtotime($result['groups']['open_time'])) > 86400) {
            return redirect('/shop/grouppurchase/detail/' . $result['rule']['id']);
        }
        /*  拼团结束时间*/
        $start_time = $result['groups']['open_time'];
        $end_time = date("Y-m-d H:i:s", (strtotime($start_time) + 86400));
        if (strtotime($end_time) > strtotime($result['rule']['end_time'])) {
            $end_time = $result['rule']['end_time'];
        }

        $product = ProductService::getDetail($result['rule']['product']['id']);
        //分享数据设置
        $shareData = [];
        $shareData['share_title'] = $product['share_title'] ? $product['share_title'] : $product['title'];
        $shareData['share_desc'] = $product['share_desc'] ? str_replace(PHP_EOL, '', $product['share_desc']) : $product['introduce'];
        $shareData['share_img'] = $product['share_img'] ? imgUrl() . $product['share_img'] : imgUrl() . $product['img'];
        //add by wuxiaoping 2017.08.30
        if (empty($product['share_title']) && empty($product['share_desc']) && empty($product['share_img'])) {

            $shareData = (new PublicShareService())->publicShareSet(session('wid'));
        }

        return view('shop.grouppurchase.groupon', array(
            'title' => '拼团详情',
            'rule' => $result['rule'],
            'groups' => $result['groups'],
            'groupsDetail' => $result['groupsDetail'],
            'order_id' => $result['order_id'],
            'end_time' => $end_time,
            'nowTime' => date("Y/m/d H:i:s", time()),
            'shareData' => $shareData,
        ));
    }

    public function groupon2($group_id, GroupsRuleModule $groupsRuleModule)
    {
        return view('shop.grouppurchase.groupon2', array(
            'title' => '拼团详情',
            'group_id' => $group_id,
            'shareData' => '',
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 拼团订单详情
     */
    public function orderDetail(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        return view('shop.grouppurchase.orderDetail', array(
            'title' => '拼团订单详情'
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 退款-不允许退款
     */
    public function notSupport(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        $groups_id = $request->input('groups_id');
        return view('shop.grouppurchase.notSupport', array(
            'title' => '等待商家发货的订单',
            'groups_id' => $groups_id,
            'shareData' => (new PublicShareService())->publicShareSet(session('wid'))
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170707
     * @desc 订单助手
     */
    public function helplist(Request $request, GroupsRuleModule $groupsRuleModule)
    {
        return view('shop.grouppurchase.helplist', array(
            'title' => '订单助手'
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170720
     * @desc 获取凑团信息
     * @param GroupsRuleModule $groupsRuleModule
     * @param GroupsRuleService $groupsRuleService
     * @param $id
     */
    public function getGroups(GroupsRuleModule $groupsRuleModule, GroupsRuleService $groupsRuleService, $id)
    {
        $result = $groupsRuleModule->getGroups($id);
        success('操作成功', '', $result);

    }


    public function ceoInfo(Request $request, ReserveService $reserveService)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rule = Array(
                'name' => 'required',
                'phone' => 'required',
                'position' => 'required',
                'enterprise_name' => 'required',
            );
            $message = Array(
                'name.required' => '名称不能为空',
                'phone.required' => '联系方式不能为空',
                'position.required' => '职位不能为空',
                'enterprise_name.required' => '企业名称',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $data = [
                'name' => $input['name'],
                'phone' => $input['phone'],
                'source' => '9块9拼团营销活动',
                'type' => 5,
                'position' => $input['position'],
                'enterprise_name' => $input['enterprise_name'],
            ];

            $reserveService->init()->add($data, false) ? success() : error();
        }

        $_url = '/shop/grouppurchase/groupon/' . $request->input('groups_id') . '/' . session('wid') . '?group_type=1';
        return view('shop.grouppurchase.ceoInfo', array(
            'title' => '信息统计',
            '_url' => $_url,
        ));

    }


}