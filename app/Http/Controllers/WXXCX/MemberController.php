<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/8/17
 * Time: 15:09
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\Lib\WXXCX\WXXCXHelper;
use App\Model\Favorite;
use App\Module\CommonModule;
use App\Module\DistributeModule;
use App\Module\FavoriteModule;
use App\Module\MemberCardModule;
use App\S\Market\CouponLogService;
use App\S\Market\CouponService;
use App\S\FavoriteService;
use App\S\Member\MemberService;
use App\S\WXXCX\WXXCXConfigService;
use App\Services\Permission\WeixinRoleService;
use Illuminate\Http\Request;
use MemberAddressService;
use App\S\Foundation\RegionService;
use Validator;
use WeixinService;
use ProductService;
use App\Services\CashLogService;
use App\Model\Income;
use App\Model\Member;
use App\Model\CashLog;
use App\Model\DistributeBank;
use DB;
use App\Model\Bank;

use App\S\BalanceRuleService;
use App\S\BalanceLogService;
use MemberHomeService;
use App\S\Member\MemberHomeModuleService;
use QrCodeService;
use MemberCardService;
use MemberCardRecordService;
use App\S\Weixin\ShopService;

class MemberController extends Controller
{

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc
     * @param Request $request
     */
    public function addressAdd(Request $request, RegionService $regionService)
    {
        $mid = $request->input('mid');
        $input = $request->input();
        //验证传递参数
        $this->checkAddress($input);
        $input['mid'] = $mid;
        //是否存在收货地址
        $memberData = (new MemberService())->getRowById($mid);
        if ($memberData['umid'] != 0) {
            list($res) = MemberAddressService::init()->where(['umid' => $memberData['umid']])->getList();
        } else {
            list($res) = MemberAddressService::init()->where(['mid' => $mid])->getList();
        }
        if (empty($res['data'])) {
            $input['type'] = 1;
        }
        if ($input['type'] == 1) {
            if ($memberData['umid'] != 0) {
                list($data) = MemberAddressService::init()->where(['type' => 1, 'umid' => $memberData['umid']])->getList(false);
            } else {
                list($data) = MemberAddressService::init()->where(['type' => 1, 'mid' => $mid])->getList(false);
            }
            foreach ($data['data'] as $val) {
                MemberAddressService::init()->where(['id' => $val['id']])->update(['type' => 0], false);
            }
        }
        $addressData = [
            'umid' => $memberData['umid'],
            'mid' => $input['mid'],
            'title' => $input['title'],
            'province_id' => $input['province_id'],
            'city_id' => $input['city_id'],
            'area_id' => $input['area_id'],
            'address' => $input['address'],
            'phone' => $input['phone'],
            'type' => $input['type'],
            'zip_code' => $input['zip_code'] ?? '',
        ];

        //验证地址ID 20180509 zyh
        if ($addressData['province_id'] == 0 || $addressData['city_id'] == 0 || $addressData['area_id'] == 0) {
            xcxerror('请选择省市区');
        }
        if (isset($input['id']) && !empty($input['id'])) {
            $res = MemberAddressService::init()->model->where('id', $input['id'])->update($addressData);
            if ($res) {
                $data = MemberAddressService::init()->model->find($input['id'])->load('province')->load('city')->load('area')->toArray();
                MemberAddressService::init()->where(['id' => $input['id']])->updateR([$data], [], false);
                xcxsuccess('操作成功', $input);
            }
        } else {
            $id = MemberAddressService::init()->add($addressData, false);
            $input['id'] = $id;
            xcxsuccess('操作成功', $input);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20170817
     * @desc 验证添加编辑地址数据
     * @param $input
     */
    private function checkAddress($input)
    {
        $rule = Array(
            'title' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'address' => 'required',
            'phone' => 'required|regex:mobile',
            'type' => 'required|in:0,1',
        );
        $message = Array(
            'title.required' => '请填写收货人地址',
            'province_id.required' => '请选择省',
            'city_id.required' => '请选择市',
            'area_id.required' => '请选择区',
            'address.required' => '请填写详细地址',
            'phone.required' => '请填写手机号码',
            'phone.regex' => '手机号码不正确',
            'type.required' => '是否默认收货地址不能为空',
            'type.in' => '默认收货地址只能是0和1',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291058
     * @desc 收货地址列表
     * @param Request $request
     */
    public function addressList(Request $request)
    {
        $mid = $request->input('mid');
        $memberData = (new MemberService())->getRowById($mid);
        if ($memberData['umid'] != 0) {
            list($memberAddressData) = MemberAddressService::init()->where(['umid' => $memberData['umid']])->getList(false);
        } else {
            list($memberAddressData) = MemberAddressService::init()->where(['mid' => $mid])->getList(false);
        }
        xcxsuccess('操作成功', $memberAddressData['data']);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291058
     * @desc 收货地址列表
     * @param Request $request
     */
    public function getDefaultAddress(Request $request)
    {
        $mid = $request->input('mid');
        $memberData = (new MemberService())->getRowById($mid);
        if ($memberData['umid'] != 0) {
            list($memberAddressData) = MemberAddressService::init()->where(['umid' => $memberData['umid']])->order('type desc')->getList(false);
        } else {
            list($memberAddressData) = MemberAddressService::init()->where(['mid' => $mid])->order('type desc')->getList(false);
        }
        if ($memberAddressData['data']) {
            xcxsuccess('操作成功', $memberAddressData['data'][0]);
        } else {
            xcxsuccess('操作成功', []);
        }

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291347
     * @desc 删除收货地址
     * @param Request $request
     */
    public function addressDel(Request $request, $id)
    {
        $mid = $request->input('mid');
        $res = MemberAddressService::init()->where(['id' => $id])->delete($id, false);
        if ($res) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703291400
     * @desc 设置默认地址
     * @param Request $request
     */
    public function addressDefault(Request $request, $id)
    {
        $mid = $request->input('mid');
        $memberData = (new MemberService())->getRowById($mid);
        if ($memberData['umid'] != 0) {
            list($data) = MemberAddressService::init()->where(['type' => 1, 'umid' => $memberData['umid']])->getList(false);
        } else {
            list($data) = MemberAddressService::init()->where(['type' => 1, 'mid' => $mid])->getList(false);
        }
        foreach ($data['data'] as $val) {
            MemberAddressService::init()->where(['id' => $val['id']])->update(['type' => 0], false);
        }
        MemberAddressService::init()->where(['id' => $id])->update(['type' => 1], false) ? xcxsuccess() : xcxerror();

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 获取地址
     * @desc
     * @param RegionService $regionService
     */
    public function region(RegionService $regionService)
    {
        $regions = $regionService->getAll();
        foreach ($regions as $key => $item) {
            if ($item['status'] == -1 || $item['status'] == -2) {
                unset($regions[$key]);
            }
        }
        $regionList = [];
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }
        xcxsuccess('操作成功', $regionList);
    }

    /**
     * 我的优惠券领取列表
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function couponList(Request $request, ShopService $shopService, $status)
    {
        //参数
        $input = $request->input();
        $wid = $input['wid'];
        $mid = $input['mid'];
        empty($status) && xcxerror('参数不完整');
        $data = [
            'wid' => $wid,
            'status' => $status,
            'shop' => $shopService->getRowById($wid),
            'list' => (new CouponLogService())->getCoupons($status, $wid, $mid)
        ];
        xcxsuccess('', $data);
    }

    /**
     * 优惠券详情
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function couponDetail(Request $request, ShopService $shopService, $id)
    {
        //参数
        $input = $request->input();
        $wid = $input['wid'];

        //获取优惠券使用记录
        $couponLog = (new CouponLogService())->getDetail($id);
        $couponLog || xcxerror('优惠券使用记录不存在');

        //获取优惠券详情
        $coupon = (new CouponService())->getDetail($couponLog['coupon_id']);
        $coupon || xcxerror('优惠券不存在');

        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);
        $coupon['shop_name'] = $shop['shop_name'] ?? '';
        $coupon['shop_logo'] = $shop['logo'] ?? '';
        $coupon['real_amount'] = $couponLog['amount'];

        $data = [
            'id' => $id,
            'wid' => $wid,
            'isValid' => ($couponLog['status'] || $couponLog['end_at'] <= date('Y-m-d H:i:s')) ? 0 : 1,
            'data' => $coupon,
            'start_at' => $couponLog['start_at'],
            'end_at' => $couponLog['end_at'],
            'my_coupon' => $couponLog, //我的优惠券详情要使用我领取的优惠券
        ];
        xcxsuccess('', $data);
    }

    /**
     * 优惠券指定商品列表
     */
    public function couponProducts(Request $request, ShopService $shopService, $id)
    {
        //参数
        $wid = $request->input('wid');

        //获取优惠券使用记录
        $couponLog = (new CouponLogService())->getDetail($id);
        $couponLog || xcxerror('优惠券使用记录不存在');
        $couponLog['range_type'] || xcxerror('该优惠券适用于全店铺商品');

        $data = [
            'id' => $id,
            'wid' => $wid,
            'shop' => $shopService->getRowById($wid),
            'list' => ProductService::getListById(explode(',', $couponLog['range_value']))
        ];

        xcxsuccess('', $data);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170929
     * @desc 获取当前用户的信息
     */
    public function getMember(Request $request)
    {
        $mid = $request->input('mid');
        $memberData = (new MemberService())->getRowById($mid);
        if ($memberData && $memberData['province_id'] && $memberData['city_id'] && $memberData['area_id']) {
            //获取省市区信息
            $regionService = new RegionService();
            $memberData['province_name'] = $regionService->getRowById($memberData['province_id'])['title'];
            $memberData['city_name'] = $regionService->getRowById($memberData['city_id'])['title'];
            $memberData['area_name'] = $regionService->getRowById($memberData['area_id'])['title'];
        }
        xcxsuccess('操作成功', $memberData);
    }


    /**
     * 我的财富
     * @author wuxiaoping <2017.10.10>
     * 需要传递的参数
     * @param  mid 用户id   wid店铺id   page当前页
     * @return 用户统计数据、提现日志
     */
    public function wealth(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $page = $request->input('page') ?? 1;
        $pagesize = config('database.perPage');
        $offset = ($page - 1) * $pagesize;
        //保存最后返回的数据
        $returnData = [];
        //分页获取提现日志
        $cash = CashLog::where('wid', $wid)->where('mid', $mid)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->toArray();
        //用户财富统计
        $member = Member::find($mid)->toArray();
        $member['amount'] = Income::where('mid', $mid)->where('wid', $wid)->where('status', 0)->sum('money');
        $member['complete'] = CashLog::where('wid', $wid)->where('mid', $mid)->where('status', 2)->sum('money');

        $returnData['cash'] = $cash;
        $returnData['member'] = $member;
        xcxsuccess('', $returnData);
    }

    /**
     * 提现金额页面数据
     * @author wuxiaoping <2017.10.10>
     * @param  Request $request [description]
     * @param  CashLogService $cashLogService [description]
     * @return [提现帐号与用户可提现金额]
     */
    public function withdrawal(Request $request, CashLogService $cashLogService)
    {
        $input = $request->input();
        //获取账户可提现金额
        $member = Member::find($input['mid'])->toArray();
        //显示页面
        $bank = [];
        if (isset($input['id'])) {
            $res = DistributeBank::find($input['id']);
        } else {
            $res = DistributeBank::where('mid', $input['mid'])->first();
        }
        if ($res) {
            $bank = $res->toArray();
            $bank['account'] = substr($bank['account'], -4);
        }
        $returnData = [];  //保存返回的数据
        //确认提现
        if ($request->isMethod('post')) {
            $rule = Array(
                'bank_id' => 'required',
                'money' => 'required|integer|min:1',

            );
            $message = Array(
                'bank_id.required' => '账户ID不能为空',
                'money.required' => '提现金额不能为空',
                'money.min' => '最低提取一元',
                'money.integer' => '金额必须为整数',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                xcxerror($validator->errors()->first());
            }
            if ($input['money'] > $member['cash']) {
                xcxerror('最多可提现' . $member['cash']);
            }
            //判断账户是否存在
            $bank = DistributeBank::find($input['bank_id']);
            if (!$bank) {
                xcxerror('账号不存在');
            }
            $bank = $bank->toArray();
            $cashLog = [
                'mid' => $input['mid'],
                'money' => $input['money'],
                'type' => $bank['type'],
                'name' => $bank['name'],
                'type_name' => $bank['bank_name'],
                'account' => $bank['account'],
                'wid' => $input['wid']
            ];
            DB::beginTransaction();
            $res1 = Member::where('id', $input['mid'])->decrement('cash', $input['money']);
            $res2 = $cashLogService->add($cashLog);
            if ($res1 && $res2) {
                DB::commit();
                xcxsuccess('提现成功');
            } else {
                DB::rollBack();
                xcxerror('提现失败');
            }
        }

        $returnData['bank'] = $bank;
        $returnData['member'] = $member;
        xcxsuccess('', $returnData);
    }

    /**
     * 添加银行帐号\支付宝帐号
     * @author wuxiaoping <2017.10.10>
     * @param Request $request [description]
     */
    public function addAccount(Request $request)
    {
        //银行帐户列表
        $bank = $returnData = [];
        $obj = Bank::get();
        if ($obj) {
            $bank = $obj->toArray();
        }
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rule = Array(
                'account' => 'required',
                'bank_name' => 'required',
                'type' => 'required|in:1,2',
                'name' => 'required',
                'logo' => 'required'
            );
            $message = Array(
                'account.required' => '账号不能为空',
                'bank_name.required' => '银行名称不能为空',
                'type.required' => '类型不能为空',
                'logo.required' => 'logo不能为空'
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $bank = [
                'mid' => $input['mid'],
                'bank_name' => $input['bank_name'],
                'account' => $input['account'],
                'name' => $input['name'],
                'type' => $input['type'],
                'logo' => $input['logo'],
            ];
            $id = DistributeBank::insertGetId($bank);
            $bank['id'] = $id;
            xcxsuccess('操作成功', '', $bank);
        }

        $returnData['bank'] = $bank;
        xcxsuccess('', $returnData);
    }

    //获取银行帐户列表
    //@author wuxiaoping <2017.10.10>
    public function selectAccount(Request $request)
    {
        $mid = $request->input('mid');
        $bank = [];
        $obj = DistributeBank::where('mid', $mid)->get();
        if ($obj) {
            $bank = $obj->toArray();
            foreach ($bank as &$val) {
                $val['account'] = substr($val['account'], -4);
            }
        }
        xcxsuccess('', $bank);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171220
     * @desc 开启财富眼
     * @return $this|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function isOpenWeath(Request $request)
    {
        $mid = $request->input('mid');
        $memberService = new MemberService();
        $member = $memberService->getRowById($mid);
        if ($member['is_open_weath'] == 0) {
            $status = 1;
        } else {
            $status = 0;
        }
        $res = $memberService->batchUpdate([$mid], ['is_open_weath' => $status]);
        if ($res) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171220
     * @desc 是否显示财富眼
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function isShowWealthEye(Request $request, ShopService $shopService)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $shopPermission = (new WeixinRoleService())->getShopPermission($wid);
        //$weixin = WeixinService::getStageShop($wid);
        $weixin = $shopService->getRowById($wid);
        $member = (new MemberService())->getRowById($mid);
        if (in_array('merchants/distribute', $shopPermission) && $weixin['is_distribute'] == 1) {
            if ($weixin['distribute_grade'] == 0 || ($weixin['distribute_grade'] == 1 && $member['is_distribute'] == 1)) {
                $member['is_open_weath'] ? $eyeCode = 1 : $eyeCode = 2;
            } else {
                $eyeCode = 0;
            }
        } else {
            $eyeCode = 0;
        }
        xcxsuccess('操作成功', $eyeCode);
    }

    /**
     * @author fuguowei
     * @date 20180118
     * @desc xcx同步选择微信地址
     * @update 处理上海北京问题 2018年08月22日 陈文豪
     * @update 处理某些显市区不存在导致同步失败问题 2018年10月20日 梅杰
     * @update 2019年08月19日 何书哲 区或镇存在情况下返回，区或镇不存在且市存在的情况下创建区或镇
     */
    public function addressAddFormWechat(Request $request, RegionService $regionService)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $province = $request->input('province');
        $city = $request->input('city');
        $area = $request->input('area');
        $detail = $request->input('detail');
        $name = $request->input('userName');
        $tel = $request->input('telNumber');
        $postCode = $request->input('postalCode');
        $province = preg_replace('/(省|市|特别行政区|自治区)/', '', $province);
        $provinceId = $cityId = $areaId = 0;

        if ($flag = in_array($province, ['北京', '上海'])) {
            $city = $area;
            $area = '其他';
        }

        // 省份信息
        $provinceData = $regionService->getRowByTitle($province, 0);
        if ($provinceData) {
            $provinceId = $provinceData['id'];
        }

        // 市级信息
        $cityData = $regionService->getRowByTitle($city, 1);
        if ($cityData) {
            $cityId = $cityData['id'];
        }

        // 区或镇信息
        $areaData = $regionService->getRowByTitle($area, 2);
        // update 何书哲 区或镇存在情况下返回，区或镇不存在且市存在的情况下创建区或镇
        if ($areaData) {
            $areaId = $areaData['id'];
        } elseif (!$flag && !$areaData && $cityData) {
            // 不存在则完善信息
            $areaId = $regionService->add(['title' => $area, 'pid' => $cityData['id'], 'level' => 2]);
        }

        if ($provinceId && $cityId && $areaId) {
            $addressData['mid'] = $request->input('mid');
            $memberData = (new MemberService())->getRowById($addressData['mid']);
            $addressData['umid'] = $memberData['umid'];
            $addressData['title'] = $name;
            $addressData['province_id'] = $provinceId;
            $addressData['city_id'] = $cityId;
            $addressData['area_id'] = $areaId;
            $addressData['address'] = $detail;
            $addressData['phone'] = $tel;
            $addressData['zip_code'] = $postCode;
            list($memberAddressData) = MemberAddressService::init()->where($addressData)->getList(false);
            if (empty($memberAddressData['data'])) {
                $addressId = MemberAddressService::init()->add($addressData, false);
                if ($addressId) {
                    $returnData['data']['address_id'] = $addressId;
                    xcxsuccess('', $returnData);
                } else {
                    xcxerror('同步微信地址失败', -2);
                }
            }
        } else {
            xcxerror($city . '地区信息不完整,请手动添加');
        }
    }

    /**
     * 获取会员卡信息
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function memberCardDetail(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $card_id = $request->input(['card_id']);
        if (!$card_id) {
            xcxerror('card_id missing');
        }
        if ($card = $cardModule->memberCardDetail($card_id, $mid)) {
            xcxsuccess('', $card);
        }
        xcxerror('该会员卡不存在');
    }

    /**
     * 领取会员卡
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function getMemberCard(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $wid = $request->input(['wid']);
        $card_id = $request->input(['card_id']);
        if (!$card_id) {
            xcxerror('card_id missing');
        }
        $card = $cardModule->getMemberCard($card_id, $mid, $wid);
        if ($card['err_code'] == 0) {
            xcxsuccess('', $card);
        }
        xcxerror($card['msg']);
    }

    /**
     * 激活会员卡
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function memberCardActive(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $wid = $request->input(['wid']);
        $card_id = $request->input(['card_id']);
        $data = $request->input();
        if (!$card_id && isset($data['tag']) && $data['tag'] == 1) {
            xcxerror('card_id missing');
        }
        $rule = [
            'name' => 'required',
            'gender' => 'required',
            'weixin' => 'required',
            'member_province' => 'required',
            'member_city' => 'required',
            'member_county' => 'required'
        ];
        $message = [
            'name.required' => '请输入姓名',
            'gender.required' => '请输入性别',
            'weixin.required' => '请输入微信号',
            'member_province.required' => '请选择省',
            'member_city.required' => '请选择市',
            'member_county.required' => '请选择区'
        ];
        $validator = Validator::make($data, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }
        $re = $cardModule->ActiveMemberCard($data, $mid, $wid, $card_id);
        if ($re['err_code'] != 0) {
            xcxerror();
        }
        xcxsuccess();
    }


    /**
     * 获取用户的会员卡列表
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function getMemberCardList(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $wid = $request->input(['wid']);
        $cardList = $cardModule->getMemberCardList($mid, $wid);
        xcxsuccess('', $cardList);
    }

    /**
     * 设置默认会员卡
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function setDefaultMemberCard(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $wid = $request->input(['wid']);
        $card_id = $request->input(['card_record_id']);
        if (!$card_id) {
            xcxerror('card_record_id missing');
        }
        if ($cardModule->setDefaultMemberCard($mid, $wid, $card_id)) {
            xcxsuccess();
        }
        xcxerror();
    }


    /**
     * 删除会员卡
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function deleteMemberCard(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $card_id = $request->input(['card_id']);
        if (!$card_id) {
            xcxerror('card_id missing');
        }
        if ($cardModule->deleteMemberCard($mid, $card_id)) {
            xcxsuccess();
        }
        xcxerror();
    }

    /**
     * 是否有新的会员卡
     * @param Request $request
     * @param MemberCardModule $module
     * @author: 梅杰 2018年9月12日
     */
    public function newMemberCard(Request $request, MemberCardModule $module)
    {
        $re = $module->newMemberCard($request->input('mid', 0));
        xcxsuccess('操作成功', $re);
    }

    /**
     * 是否有新的会员卡回调
     * @param Request $request
     * @param MemberCardModule $module
     * @author: 梅杰 2018年9月12日
     */
    public function newMemberCardCallBack(Request $request, MemberCardModule $module)
    {
        $mid = $request->input('mid', 0);
        $card_record_id = $request->input('recordId', 0);
        $re = $module->newMemberCardCallBack($mid, $card_record_id);
        xcxsuccess('操作成功', $re);
    }


    /**
     * 会员卡设置页面三级联动信息
     * Author: MeiJay
     * @param RegionService $regionService
     */
    public function getMemberCardSetting(RegionService $regionService)
    {
        $regions = $regionService->getAll();
        foreach ($regions as $key => $item) {
            if ($item['status'] == -1) {
                unset($regions[$key]);
            }
        }
        $regionList = [];
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];
        $returnData = [
            'regions_data' => json_encode($regionList),
            'regionList' => $regionList,
            'provinceList' => $provinceList,
        ];
        xcxsuccess('', $returnData);
    }


    /**
     * 会员卡弹出二维码API
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function getMemberCardCode(Request $request, MemberCardModule $cardModule)
    {
        $mid = $request->input(['mid']);
        $card_id = $request->input(['card_id']);
        $re = $cardModule->getMemberCardCode($mid, $card_id);
        if ($re['err_code'] == 0) {
            xcxsuccess('', ['code_path' => $re['data']]);
        }
        xcxerror($re['msg']);
    }


    /**
     * 充值页面展示
     */
    public function cardRecharge(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');

        $member = (new MemberService())->getRowById($mid);
        $balanceRule = new BalanceRuleService();
        $ruleList = $balanceRule->getWidRule($wid);

        $data = [
            'member' => $member,
            'ruleList' => $ruleList
        ];
        xcxsuccess('', $data);
    }

    /**
     * 余额明细
     */
    public function balanceDetailAjax(Request $request, MemberService $memberService)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');

        $balanceLogService = new BalanceLogService();
        $type = intval($request->input('type'));
        if ($type != 1 && $type != 2) {
            $type = 0;
        }

        list($list) = $balanceLogService->getUserLog($wid, $mid, $type);
        $data = [];

        if (!empty($list['data'])) {
            foreach ($list['data'] as $key => $value) {
                $data[$key]['type_name'] = '-';
                $data[$key]['pay_name'] = '支付';
                $data[$key]['pay_way_name'] = '余额支付';

                if ($value['type'] == 1) {
                    $data[$key]['type_name'] = '+';
                    $data[$key]['pay_name'] = '充值成功';
                    $data[$key]['pay_way_name'] = '微信安全支付';
                }

                if ($value['pay_way'] == 4) {
                    $data[$key]['pay_way_name'] = '系统操作';
                }
                if ($value['pay_way'] == 5) {
                    $data[$key]['pay_way_name'] = '系统退款';
                }
                $data[$key]['pay_desc'] = $value['pay_desc'];
                $data[$key]['money'] = $value['money'] / 100;
                $data[$key]['created_at'] = date('Y-m-d H:i:s', $value['created_at']);
            }
        }
        xcxsuccess('', $data);
    }

    public function addBalance(Request $request)
    {
        $mid = $request->input('mid');
        $wid = $request->input('wid');
        $money = $request->input('money');
        $token = $request->input('token');
        $xcxid = (new CommonModule())->getXcxConfigIdByToken($token);
        $balanceLogService = new BalanceLogService();
        $balanceId = $balanceLogService->insertLog($wid, $mid, $money, $pay_way = 1, $type = 1, $status = 0, $msg = '', $xcxid);

        $data = ['balanceId' => $balanceId];
        xcxsuccess('', $data);
    }

    /**
     * 小程序获取个人中心功能模块
     * @author 吴晓平 <2018年08月29日>
     * @return [type] [description]
     */
    public function getMemberHomeModule(Request $request, DistributeModule $distributeModule)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        //接收参数
        $token = $request->input('token');
        if (empty($token)) {
            $returnData['code'] = -100;
            $returnData['hint'] = '没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo = (new CommonModule())->getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        //取wid
        $wid = $userInfo[1];
        $homeResult = MemberHomeService::getRow($wid);
        if ($homeResult['errCode'] == 0 && !empty($homeResult['data'])) {
            $homeData = $homeResult['data'];
            $customInfo = json_decode($homeData['custom_info'], true);
            $title = '个人中心';
            $bgImg = '';
            if ($customInfo) {
                foreach ($customInfo as $key => $value) {
                    if ($value['type'] == 'member') {
                        $title = $value['title'];
                        $bgImg = $value['thumbnail'];
                    }
                }
            }
            $module_ids = $homeData['module_ids'] ?? [];
            if (empty($module_ids)) {
                //列出默认显示的功能模块
                $module_ids = [2, 4, 5, 6, 8, 9];
            } else {
                $module_ids = explode(',', $module_ids);
            }
            if (!$distributeModule->distributePermission($wid, $request->input('mid'))) {
                $key = array_search('4', $module_ids);
                if ($key) {
                    unset($module_ids[$key]);
                }
            }
            $list = (new MemberHomeModuleService())->getListByIds($module_ids, $wid);
            $data['title'] = $title;
            $data['bgImg'] = $bgImg;
            $data['data'] = $list;
            $returnData['list'] = $data;
        }
        return $returnData;
    }

    /**
     * 小程序会员中心分销二维码生成页面
     * @author 吴晓平 <2018年08月29日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function distributionExplan(Request $request)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        //接收参数
        $token = $request->input('token');
        if (empty($token)) {
            $returnData['code'] = -100;
            $returnData['hint'] = '没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo = (new CommonModule())->getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }
        //取wid和mid
        $wid = $userInfo[1];
        $mid = $userInfo[2];
        $url = 'pages/main/pages/member/distributionRedirect/distributionRedirect';
        $shopUrl = $url . '?_pid_=' . $mid . '&wid=' . $wid;   //设置分销pid
        $data = (new CommonModule())->qrCode($wid, $shopUrl, 1);
        $returnData['list'] = $data;
        return $returnData;
    }

    /**
     * 请求接口，设置分销上下级
     * @author 吴晓平 <2018年08月29日>
     * @param Request $request [description]
     */
    public function setDistributionLevel(Request $request)
    {
        //定义返回数据数组
        $returnData = ['code' => 40000, 'hint' => '', 'list' => []];
        //接收参数
        $token = $request->input('token');
        if (empty($token)) {
            $returnData['code'] = -100;
            $returnData['hint'] = '没有传递token';
            return $returnData;
        }
        //获取缓存中保存的用户信息
        $userInfo = (new CommonModule())->getAllByToken($token);
        if (empty($userInfo[1]) || empty($userInfo[2])) {
            $returnData['code'] = -102;
            $returnData['hint'] = 'token存放数据有问题';
            return $returnData;
        }

        return $returnData;
    }

    /**
     * 会员中心-我的收藏接口
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月10日
     * @update 许立 2018年09月14日 加上店铺筛选条件
     */
    public function favoriteListApi(Request $request, FavoriteModule $favoriteModule)
    {
        // 查询条件
        $where = [
            'mid' => $request->input('mid'),
            'wid' => $request->input('wid')
        ];

        // 收藏类型, PRODUCT:商品, ACTIVITY:活动
        $type = $request->input('type', 'PRODUCT');
        $favoriteService = new FavoriteService();
        if ($type == 'PRODUCT') {
            $where['type'] = 0;
            $data = $favoriteService->listWithPage($where);
            // 处理商品是否失效
            $data[0]['data'] = $favoriteModule->handleProductValidity($data[0]['data']);
        } else {
            $where['type'] = ['>', 0];
            $data = $favoriteService->listWithPage($where);
            // 处理活动是否失效
            $data[0]['data'] = $favoriteModule->handleActivityValidity($data[0]['data']);
        }

        xcxsuccess('', $data[0]);
    }

    /**
     * 是否收藏
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月10日
     */
    public function isFavorite(Request $request, FavoriteModule $favoriteModule)
    {
        // 验证参数
        $input = $request->input();
        empty($input['relativeId']) && xcxerror('关联id不能为空');
        isset($input['type']) || xcxerror('类型不能为空');

        xcxsuccess('', ['isFavorite' => $favoriteModule->isFavorite($input['mid'], $input['relativeId'], $input['type'])]);
    }

    /**
     * 收藏
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月10日
     */
    public function favorite(Request $request, FavoriteModule $favoriteModule)
    {
        // 验证参数
        $input = $request->input();
        empty($input['relativeId']) && xcxerror('关联id不能为空');
        isset($input['type']) || xcxerror('类型不能为空');
        empty($input['title']) && xcxerror('标题不能为空');
        empty($input['price']) && xcxerror('价格不能为空');
        empty($input['image']) && xcxerror('图片不能为空');

        if ($input['type'] == Favorite::FAVORITE_TYPE_SHARE && empty($input['share_product_id'])) {
            xcxerror('享立减商品id不能为空');
        }

        // 收藏
        if ($favoriteModule->favorite($input['wid'], $input['mid'], $input)) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }

    /**
     * 取消收藏
     * @param Request $request 参数类
     * @param FavoriteModule $favoriteModule 收藏module
     * @return json
     * @author 许立 2018年09月10日
     */
    public function cancelFavorite(Request $request, FavoriteModule $favoriteModule)
    {
        // 验证参数
        $input = $request->input();
        empty($input['relativeId']) && xcxerror('关联id不能为空');
        isset($input['type']) || xcxerror('类型不能为空');

        // 取消收藏
        if ($favoriteModule->cancelFavorite($input['mid'], $input['relativeId'], $input['type'])) {
            xcxsuccess();
        } else {
            xcxerror();
        }
    }


    /**
     * 小程序授权获取的用户信息更新
     * @param Request $request
     * @param MemberService $service
     * @author: 梅杰 2018年9月18日
     */
    public function authorizeUserInfo(Request $request, MemberService $service)
    {
        if ($request->isMethod('post')) {
            $id = $request->input('mid');
            $userInfo = $request->input('userInfo', '');
            !$userInfo && xcxerror('userInfo 为空');
            $updateData = [
                'nickname' => $userInfo['nickName'],
                'truename' => $userInfo['nickName'],
                'headimgurl' => $userInfo['avatarUrl'],
                'sex' => $userInfo['gender'],
                'country' => $userInfo['country'],
                'province' => $userInfo['province'],
                'city' => $userInfo['city'],
                'unionid' => $userInfo['unionid'] ?? ''
            ];
            $re = $service->updateData($id, $updateData);
            $re['errCode'] != 0 && xcxerror();
            xcxsuccess();
        }
        $userInfo = $service->getRowById($request->input('mid', 0));
        xcxsuccess('操作成功', $userInfo);
    }

    /**
     * 小程序授权获取的用户信息更新
     * @param Request $request
     * @param CommonModule $commonModule
     * @author: 梅杰 2018年9月18日
     */
    public function authorizePhoneNumber(Request $request, CommonModule $commonModule)
    {
        $token = $request->input('token');
        $iv = $request->input('iv', '');
        $encryptedData = $request->input('encryptedData', '');
        (!$iv || !$encryptedData) && xcxerror('参数缺失');
        $configId = $commonModule->getXcxConfigIdByToken($token);
        $appId = (new WXXCXConfigService())->getAppId($configId);
        $sessionKey = $commonModule->getSessionKeyByToken($token);
        $decrypt = (new WXXCXHelper(['appid' => $appId]))->getUserInfo($encryptedData, $iv, $sessionKey);
        $decrypt['code'] && xcxerror($decrypt['message'], $decrypt['code']);
        xcxsuccess('操作成功', $decrypt['data']);

    }
}