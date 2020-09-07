<?php
/**
 * 通用设置模块
 *
 * @package default
 * @author  大王叫我来巡山
 */
namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Lib\WXXCX\ThirdPlatform;
use App\Model\FileInfo;
use App\Model\WeixinAddress;
use App\S\Foundation\ExpressService;
use App\S\Foundation\RegionService;
use App\S\Product\ProductService;
use App\S\Store\StoreService;
use App\S\Wechat\ALiPayService;
use App\S\Wechat\WeChatShopConfService;
use App\S\Wechat\WeixinLableService;
use App\S\Wechat\WeixinRefundService;
use App\S\Wechat\WeixinSmsConfService;
use App\Services\FreightService;
use App\Services\Permission\RoleService;
use App\Services\Permission\WeixinUserService;
use App\Services\ServicePartnerService;
use App\Services\UserService;
use App\Services\Wechat\ApiService;
use App\Services\WeixinBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PaymentService;
use Validator;
use WeixinService;
use App\S\Customer\KefuService;
use Illuminate\Validation\Rule;
use QrCode;
use App\S\Weixin\ShopService;
use App\Module\CommonModule;
use App\Lib\Redis\ShopRedis;

class CurrencyController extends Controller
{
    public $regionService;
    /**
     * 构造函数
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 09:00:35
     */
    public function __construct(RegionService $regionService) {
        /* 设置左侧导航标识 */
        $this->leftNav = 'currency';
        $this->regionService = $regionService;

    }

    /**
     * 店铺信息
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 09:01:18
     *
     * @return view
     */
    public function index( Request $request,ShopService $shopService ) {
        $uid = session('userInfo')['id'];
        //获取店铺信息
        //$weixin = WeixinService::init('uid', $uid)->getInfo(session('wid'));
        $weixin = $shopService->getRowById(session('wid'));
        //获取主营类目
        if ($weixin) {
            $business = (new WeixinBusinessService())->init()->getInfo($weixin['business_id']);
        }
        if($request->isMethod('post')) {
            if ($weixin) {
                $input = $request->input();
                $updateData = [];
                if (!empty($input['shop_name'])) {
                    $updateData['shop_name'] = $input['shop_name'];
                }
                if (!empty($input['logo'])) {
                    $updateData['logo'] = $input['logo'];
                }
                if ($updateData) {
                    //$flag = WeixinService::init('uid', $uid)->where(['id' => $input['id']])->update($updateData, false);
                    $flag = $shopService->update($input['id'],$updateData);
                    if ($flag) {
                        /* 存店铺logo */
                        $request->session()->put('logo', $input['logo']);
                        /* 手动保存session */
                        $request->session()->save();
                        return mysuccess('设置店铺成功');
                    } else {
                        return myerror('设置店铺失败');
                    }
                }
            }
        }
        return view('merchants.currency.index',array(
            'title'     => '店铺信息',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'weixin'    => $weixin,
            'business'  => $business['title'] ?? ''
        ));
    }

    /**
     * 联系我们
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 09:01:18
     *
     * @return view
     */
    public function contact( Request $request ) {
        return view('merchants.currency.contact',array(
            'title'     => '联系我们',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index'
        ));
    }

    /**
     * 门店管理
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 09:01:18
     *
     * @return view
     */
    public function outlets( Request $request ,StoreService $storeService,RegionService $regionService) {
        $store = $this->storeList($storeService,$regionService);
        return view('merchants.currency.outlets',array(
            'title'     => '门店管理',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'store'     => $store,
        ));
    }

    /**
     * 新建/编辑门店
     * @param Request $request 请求参数
     * @param StoreService $storeService 门店类
     * @param RegionService $regionService 地址类
     * @return view
     * @author 黄东 2017年2月10日
     * @update 许立 2018年6月26日 修改运营时间功能
     */
    public function outletsAdd( Request $request ,StoreService $storeService,RegionService $regionService) {

        $storeData = [];
        if ($request->input('id')){
            $storeData = $storeService->getRowById($request->input('id'));
            $storeData['phone'] = explode('-',$storeData['phone']);
            if ($storeData['imgs']){
                $ids = explode(',',$storeData['imgs']);
                $storeData['file'] = FileInfo::whereIn('id',$ids)->get()->toArray();
            }

            // 许立 2018年6月26日 修改运营时间功能
            $storeData = $storeService->dealWithOpenTime($storeData);
        }
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];
//        show_debug($storeData);
        return view('merchants.currency.outletsAdd',array(
            'title'     => '新建/编辑门店',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'storeData' => $storeData,
            'provinceList'   => $provinceList,
            'regions'       => json_encode($regionList),
            'regionList'    => $regionList,
        ));
    }

    /**
     * 获取门店二维码
     * @author: 梅杰 2018年08月2日
     */
    public function getStoreCode(StoreService $service)
    {

        $wid = session('wid') ?? 321;
        $result['url']  = config('app.url').'shop/store/getStore/'.$wid;
        $result['code'] = QrCode::size(150)->generate(URL($result['url']));
        $result['xcxCode'] = $service->getStoreXcxCode($wid);
        success('操作成功','',$result);
    }


    /**
     * 下载门店小程序码
     * @param StoreService $service
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author: 梅杰 2018年8月2日
     */
    public function downloadStoreXcxCode(StoreService $service)
    {
        if ($file = $service->downloadStoreXcxCode(session('wid'))) {
            return response()->download($file, '门店'.'.png');  //下载
        }
        error('下载失败');
    }


    /**
     * 退货/维权
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 09:01:18
     *
     * @return view
     */
    public function afterSale( Request $request ) {

        //获取地区
        $regions = $this->regionService->getAll();

        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];

        $service = new WeixinRefundService();
        $id = 0;

        //设置详情
        $wid = session('wid');
        $detail = $service->model->where('wid', $wid)->first();
        if ($detail) {
            $id = $detail->id;
        }

        if($request->isMethod('post')) {
            $input = $request->input();
            $data = $input['data'];
            $data['show_in_order'] = !empty($data['show_in_order']) ? 1 : 0;

            if (!empty($id)) {
                //编辑
                $service->model->where('id', $id)->update($data);
            } else {
                //新增
                $data['wid'] = $wid;
                $service->model->insertGetId($data);
            }

            success('保存成功', URL('/merchants/currency/afterSale'));
        }

        return view('merchants.currency.afterSale',array(
            'title'     => '退货/维权',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'regions_data' => json_encode($regionList),
            'regionList'   => $regionList,
            'provinceList' => $provinceList,
            'detail'       => $detail
        ));
    }

    /**
     * 服务协议
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 09:01:18
     *
     * @return view
     */
    public function service( Request $request ) {
        return view('merchants.currency.service',array(
            'title'     => '服务协议',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'service'
        ));
    }

    /**
     * 服务商协议
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 17:26:16
     *
     * @return view
     */
    public function serviceShop( Request $request ) {
        return view('merchants.currency.serviceShop',array(
            'title'     => '服务商协议',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'service'
        ));
    }

    /**
     * 店铺管理员
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 17:26:16
     *
     * @return view
     */
    public function admin( Request $request , WeixinUserService $weixinUserService,RoleService $roleService) {
        $manager = $weixinUserService->getManager($request->getSession()->get('wid'), 20);
        $roleData = $roleService->init()->getList(false);
        return view('merchants.currency.admin',array(
            'title'     => '店铺管理员',
            'leftNav'   => $this->leftNav,
            'manager'   => $manager,
            'roleData'  => $roleData,
            'slidebar'  => 'admin'
        ));
    }

    /**
     * 添加管理员
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 17:26:16
     * @modify zhangyh 201703081744
     * @return view
     */
    public function adminAdd( Request $request,WeixinUserService $weixinUserService,UserService $userService,RoleService $roleService) {
        //post添加管理员
        if ($request->isMethod('post')){
            $rule = Array(
                'phone'  => 'required',
                'roleId' => 'required|integer',
            );
            $message = Array(
                'phone.required'  => '会搜账号不能为空',
                'roleId.required' => '请选择权限',
            );
            $validator = Validator::make($request->all(),$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $phone = $request->input('phone');
            $roleId = $request->input('roleId');
            //获取用户信息
            $userData = $userService->init()->model->where('mphone',$phone)->first();
            if (!$userData){
                error('该用户还未注册会搜账号！赶紧去邀请注册吧！');
            }else{
                $userData->toArray();
            }
            $roleData = $roleService->init()->model->where('id',$roleId)->first()->toArray();
            if (!$roleData){
                error('该权限不存在！');
            }
            $weixinUserService->addAdmin($userData['id'],$roleId);
        }
        //页面展示
        $roleData = $roleService->init()->model->where(['status'=>1])->get()->toArray();
        $tagData = Array();
        if ($id = $request->input('id')){
            $weixinUserData = $weixinUserService->init()->model->where('id',$id)->get()->load('user')->toArray();
            if (!$weixinUserData){
                error();
            }else{
                $tagData['id'] = $weixinUserData[0]['id'];
                $tagData['phone'] = $weixinUserData[0]['user']['mphone'];
                $tagData['roleId'] = $weixinUserData[0]['role_id'];
            }
        }
        return view('merchants.currency.adminAdd',array(
            'title'     => '添加管理员',
            'leftNav'   => $this->leftNav,
            'roleData'  => $roleData,
            'tagData'   => $tagData,
            'slidebar'  => 'admin'
        ));
    }
    /**
     * 店铺绑定微信号接受相关消息
     */
    public function bindWeChat(Request $request)
    {
        $weixinUserService = new WeixinUserService();
        if($request->isMethod('post')){
            $uid = $request->input(['uid']);
            if(!$uid){
                error('请选择管理员');
            }
            $wid = session('wid');
            $data  = [
                'expire_seconds'    => 300,
                'action_name'       => 'QR_STR_SCENE',
            ];
            $data['action_info']['scene']['scene_str'] = 'bindAdmin_'.$uid;
            $apiService = new ApiService();
            $re = $apiService->tempQrcodeCreated($wid,$data);
            if(isset($re['errcode'])){
                error('获取二维码失败，请确保已绑定店铺绑定微信号（服务号）');
            }
            $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$re['ticket'];
            success('','',$url);
        }
        $userInfo = session('userInfo');
        $where = [
            'uid' =>$userInfo['id'],
            'wid' => session('wid'),
        ];
        $manager = $weixinUserService->init()->where($where)->getInfo();
        return view('merchants.currency.bindWechat',[
            'title'     => '添加管理员',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'bindAdmin',
            'uid'       => $userInfo['id'],
            'manager'   => $manager
        ]);
    }

    public function unbindWeChat(Request $request,WeixinUserService $weixinUserService)
    {
        $uid = $request->input(['uid']);
        if(!$uid){
            error('请选择管理员');
        }
        $wid = session('wid');
        //判断是否是该店铺的管理员
        $manager = $weixinUserService->init()->model->where(['uid'=>$uid,'wid'=>$wid])->first();
        if(!$manager){
            error('该店铺不存在该管理员');
        }
        $manager = $manager->toArray();
        $manager['open_id'] = null;
        $manager['nick_name'] = null;
        $re = $weixinUserService->init()->model->where(['uid'=>$uid,'wid'=>$wid])->update($manager);
        if($re){
            success();
        }
        error();
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703152122
     * @desc 删除管理员
     * @param Request $request
     * @param WeixinUserService $weixinUserService
     */
    public function  delManger(Request $request,WeixinUserService $weixinUserService)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'     => '删除ID不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $weixinUserData = $weixinUserService->init()->getInfo($input['id']);
        if ($weixinUserData){
            if ($weixinUserData['uid'] == $weixinUserData['oper_id']){
                error('创建者不能删除');
            }else{
                $weixinUserService->init()->where(['id'=>$input['id']])->delete($input['id']);
            }
        }else{
            error('该管理员不存在');
        }


    }

    /**
     * @auth zhangyh20170309
     * @desc 修改管理员
     * @param Request $request
     * @param WeixinUserService $weixinUserService
     */
    public  function  modifyAdmin(Request $request,WeixinUserService $weixinUserService)
    {
        $rule = Array(
            'id'     => 'required',
            'roleId'   => 'required|integer',
        );
        $message = Array(
            'Id.required'    => '修改ID不能为空',
            'roleId.required'   => '请选择权限',
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        //修改权限
        $weixinUserService->modifyRole($request->input('roleId'),$request->input('id'));

    }


    /**
     * 我的拍档
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月10日 17:26:16
     *
     * @return view
     */
    public function partner( Request $request ,ServicePartnerService $partner) {
        $where= ['is_delete'=>1];
        list($list,$pageHtml) = $partner->init('wid',session('wid'))->where($where)->getList();
        return view('merchants.currency.partner',array(
            'title'     => '我的拍档',
            'leftNav'   => $this->leftNav,
            'partner'   =>$list['data'],
            'pageHtml' =>$pageHtml,
            'slidebar'  => 'admin'
        ));
    }


    /*
     * 删除拍档
     */
    public function partnerDel(Request $request,ServicePartnerService $partner ){
        $id = $request->input('id');
        if($id) {
            $where = ['id' => $id];
            $data = ['is_delete' => 0];
            $partner->init('wid', session('wid'))->where($where)->update($data,false);
            success('删除成功','/merchants/currency/partner');
            return false;
        }
        error();
    }

    /**
     * 支付方式
     * @param  Request $request
     * @return view
     */
    public function payment(Request $request)
    {
        $wid = session('wid');
        $aLiPayService = new ALiPayService();
        $weChatShopConfService = new WeChatShopConfService();
        if ( $request->isMethod('post') ) {
            $input = $request->input();
            $input['status'] = $input['status'] ?? 0;
            unset($input['_token']);

            if (!isset($input['payment']) || !in_array($input['payment'], [1,2]) ) {
                error('参数错误');
            }

            switch ($input['payment']) {
                case '1':
                    $rule = [
                        'config'            => 'required',
                        'config.payee'      => 'required',
                        'config.app_id'     => 'required',
                        'config.app_secret' => 'required',
                        /*'config.mch_id'     => 'required',
                        'config.mch_key'    => 'required',*/
                        'status'            => 'required|in:0,1',
                    ];
                    $message = [
                        'config.required'            => '参数错误',
                        'config.payee.required'      => '请填写收款方名称',
                        'config.app_id.required'     => '请先绑定微信公众号',
                        'config.app_secret.required' => '请填写应用密钥',
                        /*'config.mch_id.required'     => '请填写商户号',
                        'config.mch_key.required'    => '请填写API密钥',*/
                        'status.required'            => '状态参数缺失',
                        'status.in'                  => '状态错误',
                    ];
                    $validator = Validator::make($input, $rule, $message);
                    if ($validator->fails()){
                        error($validator->errors()->first());
                    }
                    //添加微信支付配置信息
                    if (!$weChatShopConfService->updateData($input)) {
                        error('微信支付信息配置失败');
                    }
                    success('微信支付信息配置成功');
                    break;
                case '2':
                    $rule = [
                        'config'           => 'required',
                        'config.payee'     => 'required',
                        'config.partner'   => 'required',
                        'config.seller_id' => 'required',
                        'config.key'       => 'required',
                        'status'           => 'required|in:0,1',
                    ];
                    $message = [
                        'config.required'           => '参数错误',
                        'config.payee.required'     => '请填写收款方名称',
                        'config.partner.required'   => '请填写合作身份者ID',
                        'config.seller_id.required' => '请填写收款支付宝账号',
                        'config.key.required'       => '请填写安全检验码',
                        'status.required'           => '状态参数缺失',
                        'status.in'                 => '状态错误',
                    ];
                    $validator = Validator::make($input, $rule, $message);
                    if ($validator->fails()){
                        error($validator->errors()->first());
                    }
                    //添加支付宝配置信息
                    if($aLiPayService->saveData($wid,$input) == false){
                        error('支付宝支付信息配置失败');
                    }
                    success('支付宝支付信息配置成功');
                    break;
                default:
                    error('参数错误');
                    break;
            }

        }

        //获取支付宝支付配置信息
        $aLiPayInfo = $aLiPayService->getRowByWid($wid);
        //获取微信支付配置
        $weChatPayInfo = $weChatShopConfService->getRowByWid($wid);
        $weixinInfo = D('Weixin', 'uid', session('userInfo')['id'])->getInfo($wid);
        return view('merchants.currency.payment',array(
            'title'      => '支付/交易',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'payment',
            'weixinInfo' => $weixinInfo,
            'aLiPayInfo' => $aLiPayInfo,
            'weChatPayInfo' => $weChatPayInfo
        ));
    }

    /**
     * 消费保障
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月15日 10:10:45
     *
     * @return view
     */
    public function guarantee( Request $request ) {
        return view('merchants.currency.guarantee',array(
            'title'     => '消费保障',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'guarantee'
        ));
    }

    /**
     * 保证金
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月15日 10:10:48
     *
     * @return view
     */
    public function margin( Request $request ) {
        return view('merchants.currency.margin',array(
            'title'     => '保证金',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'guarantee'
        ));
    }

    /**
     * 订单设置 - 上门自提
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月15日 10:10:48
     *
     * @return view
     */
    public function orderSet( Request $request ) {
        return view('merchants.currency.orderSet',array(
            'title'     => '订单设置 - 上门自提',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'orderSet'
        ));
    }

    /**
     * 订单设置 - 同城配送
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月15日 10:10:48
     *
     * @return view
     */
    public function localCity( Request $request ) {
        return view('merchants.currency.localCity',array(
            'title'     => '订单设置 - 同城配送',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'orderSet'
        ));
    }

    /**
     * 订单设置 - 快递发货
     * @param Request $request
     * @return view
     * @author Herry
     * @since 2018/6/20
     * @update 张永辉 2019年8月19日 当地区不存在是直接跳过
     */
    public function express( Request $request ) {
        $where = ['wid' => session('wid')];
        list($list,$pageHtml) = (new FreightService())->init('wid',session('wid'))->where($where)->order('sort DESC,id DESC')->perPage(5)->getList();

        //省市区
        $regionService = new Regionservice();
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }

        $regionService = new RegionService();
        foreach ($list['data'] as $index => $freight) {
            $rules = json_decode($freight['delivery_rule'], true);
            if ($rules) {
                if(!is_array($rules)) {
                    $rules = json_decode($rules, true);
                }
                if (is_array($rules)) {
                    foreach ($rules as $k => $rule) {
                        //老数据 首件可能为0 默认设置为1 其余字段如果为空 都设置为0
                        empty($rule['first_amount']) && $rule['first_amount'] = 1;
                        empty($rule['first_fee']) && $rule['first_fee'] = 0;
                        empty($rule['additional_amount']) && $rule['additional_amount'] = 0;
                        empty($rule['additional_fee']) && $rule['additional_fee'] = 0;

                        if ($rule['regions'][0] != 0) {
                            //处理后的 取出的市下的所有地区数组
                            $areaIDNewArr = [];
                            //非默认全国区域 则处理
                            $addresses = $regionService->getListById($rule['regions']);
                            foreach ($addresses as $address) {
                                //20171201 规则地区保存的是最低级 如果某市下所有区选中 则只存该市ID，省同理
                                //20171201 如果保存的是市ID 则查询所有该ID下所有区ID 并移除该市ID，省同理
                                if ($address['level'] == 1) {
                                    //A. 市
                                    $areaList = $regionList[$address['id']];
                                    $areaIDNewArr = array_merge($areaIDNewArr, array_column($areaList, 'id'));
                                    //移除市ID
                                    $rule['regions'] = array_remove_value($rule['regions'], $address['id']);
                                } elseif ($address['level'] == 0) {
                                    //B. 省
                                    $cityList = $regionList[$address['id']];
                                    foreach ($cityList as $city) {
                                        //遍历 执行A步骤
                                        if (empty($regionList[$city['id']])) {
                                            continue;
                                        }
                                        $areaList = $regionList[$city['id']];
                                        $areaIDNewArr = array_merge($areaIDNewArr, array_column($areaList, 'id'));
                                        //移除市ID
                                        $rule['regions'] = array_remove_value($rule['regions'], $city['id']);
                                    }
                                    //移除省ID
                                    $rule['regions'] = array_remove_value($rule['regions'], $address['id']);
                                }
                            }
                            //把所有处理的区ID push到regions中 返回给前端
                            if ($areaIDNewArr) {
                                $rule['regions'] = array_merge($rule['regions'], $areaIDNewArr);
                            }
                        }
                        $rules[$k] = $rule;
                    }
                } else {
                    unset($list['data'][$index]);
                }
                $freight['delivery_rule'] = json_encode($rules);
                $list['data'][$index] = $freight;
            } else {
                //保存数据格式错误 不返回到列表 Herry 20171113
                unset($list['data'][$index]);
            }
        }

        return view('merchants.currency.express',array(
            'title'     => '订单设置 - 快递发货',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'orderSet',
            'list'      => $list['data'],
            'regions_data' => json_encode($regionList),
            'pageHtml'  => $pageHtml
        ));
    }

    /**
     * 订单设置 - 新增编辑快递模版
     * @param Request $request
     * @param int $id 运费模板ID
     * @return view | json
     * @author Herry
     * @since 2018/6/20
     */
    public function expressSet( Request $request, $id = 0 ) {
        $wid = session('wid');
        //省市区
        $regionService = new Regionservice();
        $regions = $regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }

        //对省份进行排序
        //$provinceList = array_reverse($regionList[-1]);
        $provinceList = $regionList[-1];
        //获取模板详情
        $freight = new FreightService();
        $data = $id ? $freight->getOne($id) : [];

        if ($request->isMethod('post')) {
            $data = $request->input('data');
            //运费模板规则json拼接 前端给出 20171115 验证格式合法性
            $verifyResult = $freight->verifyFormat($data);
            if ($verifyResult['errCode']) {
                error($verifyResult['errMsg']);
            }
            if (empty($id)) {
                //新增
                $data['wid'] = $wid;
                if ($freight->addOne($data)) {
                    success();
                } else {
                    error('失败');
                }
            } else {
                //编辑
                $data['id'] = $id;
                if ($freight->updateOne($data)) {
                    success();
                } else {
                    error('失败');
                }
            }
        } else {
            //处理规则 返回地区信息
            if ($data) {
                $rules = json_decode($data['delivery_rule'], true);
                if ($rules) {
                    if(!is_array($rules)) {
                        $rules = json_decode($rules, true);
                    }
                    if (is_array($rules)) {
                        foreach ($rules as $k => $rule) {
                            //老数据 首件可能为0 默认设置为1 其余字段如果为空 都设置为0
                            empty($rule['first_amount']) && $rule['first_amount'] = 1;
                            empty($rule['first_fee']) && $rule['first_fee'] = 0;
                            empty($rule['additional_amount']) && $rule['additional_amount'] = 0;
                            empty($rule['additional_fee']) && $rule['additional_fee'] = 0;

                            if ($rule['regions'][0] != 0) {
                                //处理后的 取出的市下的所有地区数组
                                $areaIDNewArr = [];
                                //非默认全国区域 则处理
                                $addresses = $regionService->getListById($rule['regions']);
                                foreach ($addresses as $address) {
                                    //20171201 规则地区保存的是最低级 如果某市下所有区选中 则只存该市ID，省同理
                                    //20171201 如果保存的是市ID 则查询所有该ID下所有区ID 并移除该市ID，省同理
                                    if ($address['level'] == 1) {
                                        //A. 市
                                        $areaList = $regionList[$address['id']];
                                        $areaIDNewArr = array_merge($areaIDNewArr, array_column($areaList, 'id'));
                                        //移除市ID
                                        $rule['regions'] = array_remove_value($rule['regions'], $address['id']);
                                    } elseif ($address['level'] == 0) {
                                        //B. 省
                                        $cityList = $regionList[$address['id']];
                                        foreach ($cityList as $city) {
                                            // 遍历 执行A步骤 @update 张永辉 2019年8月19日 当城市不存在时直接跳过
                                            if (empty($regionList[$city['id']])) {
                                                continue;
                                            }
                                            $areaList = $regionList[$city['id']];
                                            $areaIDNewArr = array_merge($areaIDNewArr, array_column($areaList, 'id'));
                                            //移除市ID
                                            $rule['regions'] = array_remove_value($rule['regions'], $city['id']);
                                        }
                                        //移除省ID
                                        $rule['regions'] = array_remove_value($rule['regions'], $address['id']);
                                    }
                                }
                                //把所有处理的区ID push到regions中 返回给前端
                                if ($areaIDNewArr) {
                                    $rule['regions'] = array_merge($rule['regions'], $areaIDNewArr);
                                }
                            }
                            $rules[$k] = $rule;
                        }
                    } else {
                        $rules = [];
                    }

                    $data['delivery_rule'] = $rules;
                }
            }
        }

        return view('merchants.currency.expressSet',array(
            'title'     => '订单设置 - 新增编辑快递模版',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'orderSet',
            'express'   => (new ExpressService())->getListWithoutPage(),
            'id'        => $id,
            'regions_data' => json_encode($regionList),
            'regionList'   => $regionList,
            'provinceList' => $provinceList,
            'data'      => $data,
            'jsonData'  => json_encode($data)
        ));
    }

    /**
     * 订单设置 - 展开或者收缩运费模板
     * @param int $id 运费模板ID
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function expressToggle($id)
    {
        //查询模板
        $freightService = new FreightService();
        $freight = $freightService->getOne($id);
        if (empty($freight)) {
            error('运费模板不存在');
        }

        //切换操作
        $isReduced = $freight['is_reduced'] == 1 ? 0 : 1;
        $freightService->updateOne(['id' => $id, 'is_reduced' => $isReduced]);
        success('切换模板成功');
    }

    /**
     * 订单设置 - 删除运费模板
     * @param int $id 运费模板ID
     * @return json
     * @author Herry
     * @since 2018/6/20
     */
    public function expressDel($id)
    {
        if (empty($id)) {
            error('参数不完整');
        }

        //查询模板
        $freightService = new FreightService();
        $freight = $freightService->getOne($id);
        if (empty($freight)) {
            error('运费模板不存在');
        }

        //查询正在使用此模板的商品个数
        $num = (new ProductService())->model->wheres(['freight_type' => 2, 'freight_id' => $id])->count();
        if ($num) {
            error($num .'个商品正在使用该模板,不能删除');
        }

        if ($freightService->delOne($id)) {
            success('成功');
        } else {
            error('失败');
        }
    }

    /**
     * 订单设置 - 交易设置
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月15日 10:10:48
     *
     * @return view
     */
    public function tradingSet( Request $request ) {
        return view('merchants.currency.tradingSet',array(
            'title'     => '订单设置 - 交易设置',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'orderSet'
        ));
    }

    /**
     * 通用设置
     *
     * @param   Request $request [http请求类实例]
     * @return  view
     *
     * @author 许立 843168640@qq.com
     * @version 2017年2月24日 17:40:00
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月28日 17:35:17 重写
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年4月17日 21:34:33 bug修正
     */
    public function generalSet(Request $request,ShopService $shopService)
    {
        $wid = session('wid');
        $weixinInfo = $shopService->getRowById($wid);
        /*$WeixinService = D('Weixin', 'uid', session('userInfo')['id']);*/
        // 新增或修改
        if ( $request->isMethod('post') ) {
            $weixinConfigMasterService = D('WeixinConfigMaster');
            // 接收参数并验证
            $input = $request->only($weixinConfigMasterService->getAllColumn());
            $rules = [
                'wid'            => 'required',
                'is_title'       => 'filledIn:1',
                'title'          => 'required_with:is_title|max:10',
                'is_shopname'    => 'filledIn:1',
                'is_cart'        => 'filledIn:1',
                'cart_icon'      => 'required_with:is_cart|in:1,2,3,4',
                'is_record'      => 'required|in:0,1',
                'is_comment'     => 'required|in:0,1,2',
                'is_more'        => 'required|in:0,1',
                'is_sellout'     => 'required|in:0,1',
                'is_service'     => 'required|in:0,1',
                'is_included'    => 'required|in:0,1',
                'is_nav'         => 'required|in:0,1',
                'is_business'    => 'required|in:0,1',
                'is_all_day'     => 'required_with:is_business|in:0,1',
                'business_start' => 'required_if_and:is_business,1,is_all_day,0|date_format:Y-m-d H:i:s',
                'business_end'   => 'required_if_and:is_business,1,is_all_day,0|date_format:Y-m-d H:i:s|after:business_start',
                'is_auto'        => 'required_if:is_business,0|in:0,1',
                'auto_time'      => 'required_if_and:is_business,0,is_auto,1|date_format:Y-m-d H:i:s',
                'footer_logo'    => 'required_if:is_footer_logo,1',
            ];
            $messages = [
                'id.required'                    => '店铺异常',
                'is_title.filled_in'             => '参数错误:is_title',
                'title.required_with'            => '请填写统一后缀文字',
                'title.max'                      => '统一后缀文字最多填写10个字',
                'is_shopname.filled_in'          => '参数错误:is_shopname',
                'is_cart.filled_in'              => '参数错误:is_cart',
                'cart_icon.required_with'        => '请选择图标样式',
                'cart_icon.in'                   => '参数错误:cart_icon',
                'is_record.required'             => '请设置销量及成交记录',
                'is_record.in'                   => '参数错误:is_record',
                'is_comment.required'            => '请设置商品评价',
                'is_comment.in'                  => '参数错误:is_comment',
                'is_more.required'               => '请设置更多商品推荐',
                'is_more.in'                     => '参数错误:is_more',
                'is_sellout.required'            => '请设置列表显示售罄商品',
                'is_sellout.in'                  => '参数错误:is_sellout',
                'is_service.required'            => '请设置联系商家/在线客服',
                'is_service.in'                  => '参数错误:is_service',
                'is_included.required'           => '请设置会搜买家版收录',
                'is_included.in'                 => '参数错误:is_included',
                'is_nav.required'                => '请设置店铺顶部导航',
                'is_nav.in'                      => '参数错误:is_nav',
                'is_business.required'           => '请设置营业状态',
                'is_business.in'                 => '参数错误:is_business',
                'is_all_day.required_with'       => '请设置营业时间',
                'is_all_day.in'                  => '参数错误:is_all_day',
                'business_start.required_if_and' => '请设置营业开始时间',
                'business_start.date_format'     => '营业开始时间格式不正确',
                'business_end.required_if_and'   => '请设置营业结束时间',
                'business_end.date_format'       => '营业结束时间格式不正确',
                'business_end.after'             => '营业结束时间不能小于营业开始时间',
                'is_auto.required_if'            => '请设置自动开业',
                'is_auto.in'                     => '参数错误:is_auto',
                'auto_time.required_if_and'      => '请设置自动开业时间',
                'auto_time.date_format'          => '自动开业时间格式不正确',
                'footer_logo.required_if'        => '请上传店铺底部logo',
            ];
            // 调用验证器执行验证方法
            $validator = Validator::make($input, $rules, $messages);
            // 验证不通过则提示错误信息
            if ( $validator->fails() ) {
                error( $validator->errors()->first() );
            }

            // mysql操作
            if ( empty($input['id']) ) {
                // 新增
                $result = $weixinConfigMasterService->addD($input, false);
                $input['id'] = $result;
            } else {
                // 编辑
                $result = $weixinConfigMasterService->where(['id'=>$input['id']])->updateD($input, false);
            }

            // 同步redis数据
            if ( $result ) {
                // 先获取店铺信息 确保redis数据存在
                //$WeixinService->setRedisKey()->getInfo($wid);
                // 更新redis数据
                $redisDatas['weixinConfigMaster'] = json_encode($input);
                //$WeixinService->updateR($wid, $redisDatas);
                (new ShopRedis())->updateHashRow($wid,$redisDatas);
            }

            error();
        }

        // 获取配置信息
        //$weixinInfo = $WeixinService->getInfo($wid);
        $detail = $weixinInfo['weixinConfigMaster'];

        return view('merchants.currency.generalSet',array(
            'title'     => '通用设置',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'generalSet',
            'detail'    => $detail
        ));
    }

    /**
     * 掌柜任务
     * @return [type] [description]
     */
    public function task(){
        return view('merchants.currency.task',array(
            'title'=>'掌柜任务',
            'leftNav'=>$this->leftNav,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170609
     * @desc 商家地址
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function location(){

        $address = WeixinAddress::where('wid',session('wid'))->get()->toArray();
        $temp = [];
        foreach ($address as $val){
            $temp[] = $val['province_id'];
            $temp[] = $val['city_id'];
            $temp[] = $val['area_id'];
        }
        $region = $this->regionService->getListById($temp);

        $tmpAddr = [];
        foreach ($region as $value){
            $tmpAddr[$value['id']] = $value['title'];
        }
        foreach ($address as &$val){
            $val['province_id'] = $tmpAddr[$val['province_id']];
            $val['city_id'] = $tmpAddr[$val['city_id']];
            $val['area_id'] = $tmpAddr[$val['area_id']];
        }
        return view('merchants.currency.location',array(
            'title'=>'商家地址库',
            'leftNav'=>$this->leftNav,
            'slidebar'  => 'index',
            'address'   => $address,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170609
     * @desc 编辑添加地址
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editAddress(Request $request)
    {
        $input = $request->input();
        if ($request->isMethod('post')){
            $rule = Array(
                'name'  => 'required',
                'mobile' => 'required',
                'province_id'  => 'required',
                'city_id'      => 'required',
                'area_id'      => 'required',
                'address'      => 'required',
                'is_default'   => 'required|in:0,1',
                'type'          => 'required|in:0,1,2,3',
            );
            $message = Array(
                'name.required'  => '联系人不能为空',
                'mobile.required' => '电话不能为空',
                'province_id.reqiored' => '请选择省',
                'city_id.reqiored' => '请选择市',
                'area_id.reqiored' => '请选择区',
                'area_id.reqiored' => '请选择区',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $wid = session('wid');
            $weixinAddress = [
                'wid'             => $wid,
                'name'            => $input['name'],
                'mobile'          => $input['mobile'],
                'province_id'     => $input['province_id'],
                'city_id'         => $input['city_id'],
                'area_id'         => $input['area_id'],
                'address'         => $input['address'],
                'is_default'      => $input['is_default'],
                'is_send_default' => $input['is_send_default'] ?? 0,
                'zip_code'        => $input['zip_code'] ?? 0,
                'type'            => $input['type'],
            ];
            if ($input['type'] == 0 && $input['is_default'] == 1) { //只选默认的退货地址
                $re = WeixinAddress::where(['wid'=>$wid])->update(['is_default'=>0]);
            }else if($input['type'] == 2) { //只选择默认的发货地址
                //将之前的默认地址改掉
                $re = WeixinAddress::where(['wid'=>$wid])->update(['is_send_default'=>0]);
            }else if($input['type'] == 3) { //同时选择默认的退货地址与发货地址
                //将之前的默认地址改掉
                $re = WeixinAddress::where(['wid'=>$wid])->update(['is_send_default'=>0,'is_default'=>0]);
            }
            if (isset($input['id']) && !empty($input['id'])){

                $weixinAddress['id'] = $input['id'];
                $res = WeixinAddress::where('id',$input['id'])->update($weixinAddress);
                if ($res){

                    return mysuccess('操作成功','location',$weixinAddress);
                }else{
                    return myerror();
                }
            }

            $res = WeixinAddress::insertGetId($weixinAddress);
            if ($res){
                $weixinAddress['id'] = $res;

                return mysuccess('操作成功','location',$weixinAddress);
            }else{
                return myerror();
            }

        }
        $address = [];
        if (isset($input['id'])){
            $address = WeixinAddress::find($input['id']);
            if ($address){
                $address = $address->toArray();
            }
        }
        //获取地区
        $regions = $this->regionService->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];

        return view('merchants.currency.editAddress',array(
            'title'=>'编辑地址',
            'leftNav'=>$this->leftNav,
            'slidebar'  => 'index',
            'address' => $address,
            'regions_data' => json_encode($regionList),
            'regionList'   => $regionList,
            'provinceList' => $provinceList,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170615
     * @desc 删除商家地址
     */
    public function delAddress($id)
    {
        $res = WeixinAddress::where('id',$id)->delete();

        if ($res){
            return mysuccess('操作成功','location');
        }else{
            return myerror('操作失败','location');
        }
    }

    /**
     * 获取门店列表
     * @param StoreService $storeService 门店类
     * @param RegionService $regionService 地址类
     * @return view
     * @author 张永辉 2017年6月26日
     * @update 许立   2018年6月26日 修改运营时间功能
     */
    public function storeList(StoreService $storeService,RegionService $regionService)
    {
        $store = $storeService->getlistPage();
        $addressIds = [];
        foreach ($store[0]['data'] as $value){
            $addressIds[] = $value['province_id'];
            $addressIds[] = $value['city_id'];
            $addressIds[] = $value['area_id'];
        }
        $temp = $regionService->getListById(array_unique($addressIds));
        $addressData = [];
        foreach ($temp as $val){
            $addressData[$val['id']] = $val['title'];
        }
        foreach ($store[0]['data'] as &$value){
            $value['province_title'] = $addressData[$value['province_id']];
            $value['city_title'] = $addressData[$value['city_id']];
            $value['area_title'] = $addressData[$value['area_id']];

            // 许立 2018年6月26日 截取关门时间
            $end_time_array = explode(',', $value['end_time']);
            $value['close_time'] = end($end_time_array);
        }

        return $store;
    }


    /**
     * 添加/修改门店
     * @param Request $request 参数类
     * @param StoreService $storeService 门店类
     * @return json
     * @author 张永辉 2017年6月26日
     * @update 许立   2018年6月26日 修改运营时间功能
     */
    public function editStore(Request $request,StoreService $storeService)
    {
        $input = $request->input();
        $rule = Array(
            'title'         => 'required',
            'phone'         => 'required',
            'province_id'  => 'required',
            'city_id'      => 'required',
            'area_id'      => 'required',
            'address'      => 'required',
            'longitude'   => 'required',
            'latitude'    => 'required',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'comment'     => 'required',
        );
        $message = Array(
            'title.required'  => '门店标题不能为空',
            'phone.required'  => '电话不能为空',
            'province_id.required' => '请选择省',
            'city_id.required' => '请选择市',
            'area_id.required' => '请选择区',
            'area_id.required' => '请选择区',
            'address.required' => '详细地址不能为空',
            'longitude.required' => '经度不能为空',
            'latitude.required'  => '纬度不能为空',
            'start_time.required'  => '开始营业时间不能为空',
            'end_time.required'  => '结束营业时间不能为空',
            'comment.required'  => '店铺推荐不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        foreach ($input['phone'] as  $value) {
            # code...
            if ($value){
                if (!is_numeric($value)) {
                    # code...
                    error('电话必须为数字');
                }
            }
        }

        //许立 2018年6月26日 修改运营时间功能
        if (empty($input['monday']) && empty($input['tuesday']) && empty($input['wednesday']) && empty($input['thursday'])
            && empty($input['friday']) && empty($input['saturday']) && empty($input['sunday'])) {
            error('请至少选择一天运营时间');
        }
        // 拼接营业时间数据
        $start = '';
        $end = '';
        if (!empty($input['monday'])) {
            $start .= '星期一,';
            $end .= '星期一,';
        }
        if (!empty($input['tuesday'])) {
            $start .= '星期二,';
            $end .= '星期二,';
        }
        if (!empty($input['wednesday'])) {
            $start .= '星期三,';
            $end .= '星期三,';
        }
        if (!empty($input['thursday'])) {
            $start .= '星期四,';
            $end .= '星期四,';
        }
        if (!empty($input['friday'])) {
            $start .= '星期五,';
            $end .= '星期五,';
        }
        if (!empty($input['saturday'])) {
            $start .= '星期六,';
            $end .= '星期六,';
        }
        if (!empty($input['sunday'])) {
            $start .= '星期日,';
            $end .= '星期日,';
        }
        $start .= $input['start_time'][0];
        $end .= $input['end_time'][0];

        $storeData = [
            'wid'           => session('wid'),
            'title'         => $input['title'],
            'phone'         => implode('-',$input['phone']),
            'province_id'  => $input['province_id'],
            'city_id'       => $input['city_id'],
            'area_id'       => $input['area_id'],
            'address'       => $input['address'],
            'longitude'     => $input['longitude'],
            'latitude'      => $input['latitude'],
            'imgs'           => trim($input['imgs'],',')??'',
            'start_time'    => $start,
            'end_time'      => $end,
            'comment'       => $input['comment'],
        ];

        if (isset($input['id']) && !empty($input['id'])){
            $res = $storeService->update($input['id'],$storeData);
        }else{
            $res = $storeService->add($storeData);
        }

        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 删除门店
     * @param StoreService $storeService
     * @param $id
     */
    public function delStore(StoreService $storeService,$id)
    {
        $res = $storeService->del($id);
        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }
    }

    public function getRegion(Request $request)
    {
        $pid = $request->input('pid')??-1;
    }


    /**
     * [shareSet description]
     * @return [type] [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function shareSet(ShopService $shopService)
    {
        $wid = session('wid');
        $uid = session('userInfo')['id'];
        //店铺信息
        //$info = WeixinService::init('uid', $uid)->getInfo($wid);
        $info = $shopService->getRowById($wid);
        return view('merchants.currency.shareSet',[
                'title'    => '店铺分享设置',
                'leftNav'  => $this->leftNav,
                'slidebar' => 'shareSet',
                'info'     => $info
            ]);
    }


    /**
     * [设置店铺分享信息]
     * @param Request $request [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function addShareInfo(Request $request,ShopService $shopService)
    {
        $uid = session('userInfo')['id'];
        $wid = session('wid');
        $data = [];
        $input = $request->all();
        $rule = Array(
            'title' => 'required|max:18',
            'desc'  => 'required|max:50',
            'logo'  => 'required'
        );
        $message = Array(
            'title.required' => '分享标题不能为空',
            'title.max'      => '分享标题不能超过18个字',
            'desc.required'  => '分享介绍不能为空',
            'desc.max'       => '分享介绍不能超过50个字',
            'logo.required'  => '分享logo图片不能为空'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $data['share_title'] = $input['title'];
        $data['share_desc']  = $input['desc'];
        $data['share_logo']  = $input['logo'];
        //$res = WeixinService::init('uid',$uid)->where(['id' => $wid])->update($data,false);
        $res = $shopService->update($wid,$data);
        if($res){
            success();
        }else{
            error();
        }

    }


    /**
     * [分享Logo上传]
     * @param  [string] 文件名   [description]
     * @param  [string] 文件路径 [description]
     * @return [mix]   失败返回一个数组，包含错误码与提示信息
     */
    public function uploadImg(Request $request)
    {
        $filename = $request->file('file');
        $returnData = ['errcode' => 0,'errmsg' => ''];
        //检查文件是否存在
        if ($request::hasFile($filename)){
            //获取文件
            $file = $request::file($filename);
            //其次检查图片是否合法
            if ($file->isValid()){
            //先得到文件后缀,然后将后缀转换成小写,然后看是否在否和图片的数组内
                if(in_array( strtolower($file->extension()),['jpeg','jpg','gif','gpeg','png'])){
                    //将文件取一个新的名字
                    $newName = 'img'.time().rand(100000, 999999).$file->getClientOriginalName();
                    //移动文件,并修改名字
                    if($file->move($filepath,$newName)){
                        $returnData = ['errcode' => 0,'errmsg' => $filepath.'/'.$newName];
                    }else{
                        $returnData = ['errcode' => 4, 'errmsg' => '上传文件失败'];
                    }
                }else{
                    $returnData = ['errcode' => 3, 'errmsg' => '上传文件格式不正确'];
                }

            }else{
                $returnData = ['errcode' => 2, 'errmsg' => '上传文件不合法'];
            }
        }else{
            $returnData = ['errcode' => 1, 'errmsg' => '文件不存在'];
        }

        return $returnData;

    }

	/**
     * 客服列表
     * @author wuxiaoping  2017.09.28
     * @param  Request $request [description]
     * @return [type]           [description]
     */

	public function kefu(Request $request)
    {
        $wid = session('wid');
        $kefuService = new KefuService();
        //添加客服
        if($request->isMethod('post')){
            $input = $request->input();
            $rule = [
                'qq' => [
                    'required',
                    'numeric',
                    Rule::unique('kefu')->where(function ($query) use($wid){
                        $query->whereNull('deleted_at')->where('wid', $wid);

                    })
                ],
                'name' => 'sometimes|between:2,10',
            ];

            $message = Array(
                'qq.required'  => '请输入客服QQ号',
                'qq.numeric'   => '请输入正确的QQ号',
                'qq.unique'    => '客服QQ号重复',
                'name.between' => '客服姓名长度为2-10个字符',
            );

            $validator = Validator::make($input,$rule,$message);
            //验证表单是否报错
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $input['wid'] = $wid;
            if($kefuService->add($input)){
                success('添加成功');
            }

            error('添加失败');
        }
        list($list,$pageHtml) = $kefuService->getAllList();
        return view('merchants.currency.kefu',[
            'title'    => '客服设置',
            'slidebar' => 'kefu',
            'leftNav'  => $this->leftNav,
            'list'     => $list,
            'pageHtml' => $pageHtml
		]);
	}


    //删除客服人员
    public function kefuDel(Request $request,$id)
    {
        if(empty($id)){
            error('请选择要删除的客服');
        }

        $kefuService = new KefuService();

        $data = $kefuService->getRowById($id);
        if(empty($data)){
            error('该客服人员不存在或已被删除');
        }

        $rs = $kefuService->del($id);
        if($rs){
            success('删除成功');
        }

        error('删除失败');
	}

    //ajax返回列表数据
    public function getListForAjax(Request $request,KefuService $kefuService)
    {
        $input = $request->input() ?? [];
        list($list) = $kefuService->getAllList($input,5);

        success('','',$list);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170927
     * @desc 设置短信配置
     * @param Request $request
     */
    public function setSmsConf(Request $request,WeixinSmsConfService $weixinSmsConfService)
    {
        $input = $request->all();
        $rule = Array(
            'account_sid' => 'required',
            'account_token'  => 'required',
            'app_id'  => 'required',
            'code'  => 'required',
            'phone'  => 'required',
        );
        $message = Array(
            'account_sid.required'  => 'sid不能为空',
            'account_token.required'     => 'token不能为空',
            'app_id.required'       => 'app_id 不能为空',
            'code.required'               => '短信模板id不能为空',
            'phone.required'        => '短信联系电话'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $smsData = [
            'wid'           => session('wid'),
            'account_sid'  => trim($request->input('account_sid')),
            'account_token'=> trim($request->input('account_token')),
            'app_id'        => trim($request->input('app_id')),
            'code'          => trim($request->input('code')),
            'phone'         => trim($request->input('phone')),
        ];
        if (isset($input['id']) && !empty($input['id'])){
            $res = $weixinSmsConfService->update($input['id'],$smsData);
        }else{
            $res = $weixinSmsConfService->add($smsData);
        }
        if ($res){
            success();
        }else{
            error();
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170927
     * @desc  商家后台短信配置
     * @param Request $request
     */
    public function smsConf(Request $request,WeixinSmsConfService $weixinSmsConfService)
    {
        $data = $weixinSmsConfService->getList(['wid'=>session('wid')]);
        if ($data){
            $data = array_pop($data);
        }
        return view('merchants.currency.smsConf',[
            'title'    => '短信配置',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'smsconf',
            'info'     => $data
        ]);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170927
     * @desc 关闭短信配置
     * @param WeixinSmsConfService $weixinSmsConfService
     */
    public function delSmsConf(WeixinSmsConfService $weixinSmsConfService)
    {
        $res = $weixinSmsConfService->getList(['wid'=>session('wid')]);
        if ($res){
            $id = $res[0]['id'];
            $weixinSmsConfService->del($id)?success():error();
        }
        success();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc  店铺标签设置
     */
    public function shopLable(Request $request)
    {
        $data = [];
        $weixinLable = new WeixinLableService();
        list($data) = $weixinLable->getList(['wid'=>session('wid')]);
        if ($data){
            $data['content'] = json_decode($data['content'],true);
        }
        return view('merchants.currency.shopLable',array(
            'title'     => '退货/维权',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'index',
            'data'      => json_encode($data),
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 添加店铺标签
     * @param Request $request
     */
    public function addShopLable(Request $request)
    {
        $rule = Array(
            'title'    => 'required',
            'img'      => 'required',
            'content' => 'required',
        );
        $message = Array(
            'title.required'  => '主标题不能为空',
            'img.required' => '图片不能为空',
            'content.required' => '标签不能为空',
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $data = [
            'wid'       => session('wid'),
            'title'     => $request->input('title'),
            'content'   => json_encode($request->input('content'))
        ];
        $weixinLableService = new WeixinLableService();
        if ($request->input('id')){
            $weixinLableService->update($request->input('id'),$data)?success():error();
        }else{
            $weixinLableService->add($data)?success():error();
        }

    }

    /**
     * 商户证书上传API
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 20180720
     * @update 梅杰 增加指定小程序商户证书上传
     * @update 梅杰 20180725 修改小程序商户证书上传保存路径
     * @update 梅杰 20180825 修改小程序商户证书上传保存路径
     */
    public function cert(Request $request)
    {
        $wid = session('wid');
        if($request->isMethod('post')){
            $file_cert = $request->file('file_cert');
            $file_key = $request->file('file_key');
            if(!$file_cert || !$file_key){
                error('未选择上传文件');
            }
            $xcxConfigId = $request->input(['id'],0);
            //判断文件是否上传成功
            if($file_cert->isValid() && $file_key->isValid()){
                $certOriginalName = $file_cert->getClientOriginalName();
                $certRealPath = $file_cert->getRealPath();

                $keyOriginalName = $file_key->getClientOriginalName();
                $keyRealPath = $file_key->getRealPath();
                if($request->input(['type']) == 1) {
                    $certPath = "./hsshop/cert/{$wid}_cert/api_cert/apiclient_cert.pem";
                    $keyPath = "./hsshop/cert/{$wid}_cert/api_cert/apiclient_key.pem";
                }else{
//                    $certPath = $xcxConfigId == 0 ? "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_cert.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_$xcxConfigId/apiclient_cert.pem";
//                    $keyPath = $xcxConfigId == 0 ? "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_key.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_$xcxConfigId/apiclient_key.pem"  ;
                    $certPath = $xcxConfigId == 0 ? "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_cert.pem" :"./hsshop/cert/{$wid}_cert/mini_cert_".$xcxConfigId."/apiclient_cert.pem";
                    $keyPath = $xcxConfigId == 0 ? "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_key.pem" : "./hsshop/cert/{$wid}_cert/mini_cert_".$xcxConfigId."/apiclient_key.pem";
                }
                Storage::put($certPath, file_get_contents($certRealPath));
                Storage::put($keyPath, file_get_contents($keyRealPath));

                if(Storage::exists($certPath) && Storage::exists($keyPath)) {
                    success('文件上传成功');
                }
            }
            error('文件上传失败');
        }
        $api_flag = $mini_flag = 0;
        $apiCertPath = "./hsshop/cert/{$wid}_cert/api_cert/apiclient_cert.pem";
        $apiKeyPath = "./hsshop/cert/{$wid}_cert/api_cert/apiclient_key.pem";
        $miniCertPath = "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_cert.pem";
        $miniKeyPath = "./hsshop/cert/{$wid}_cert/mini_cert/apiclient_key.pem";
        if(Storage::exists($apiCertPath) && Storage::exists($apiKeyPath) ){
            $api_flag = 1;
        }
        if(Storage::exists($miniCertPath) && Storage::exists($miniKeyPath) ){
            $mini_flag = 1;
        }
        return view('merchants.currency.cert',array(
            'title'     => '商户证书上传',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'cert',
            'api_flag'  => $api_flag,
            'mini_flag' => $mini_flag
        ));
    }


    /**
     * 商户证书下载
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author: 梅杰 2018年8月23日
     */
    public function downLoadCert(Request $request)
    {
        if ($id = $request->input('id',0)) {
            $wid = $request->session()->get('wid');
            $type = $request->input('type',1);
            $path = "./hsshop/cert/{$wid}_cert/mini_cert_$id/apiclient_cert.pem";
            $type == 2 &&  $path = "./hsshop/cert/{$wid}_cert/mini_cert_$id/apiclient_key.pem" ;
            $fileName = 'apiclient_cert.pem';
            $type == 2 && $fileName = 'apiclient_key.pem';
            return response()->download($path,$fileName);
        }
        error();
    }

    public function phoneKf(Request $request)
    {
        $wid = session('wid');
        $kefuService = new KefuService();
        //添加客服
        if($request->isMethod('post')){
            $input = $request->input();
            $rule = [
                'telphone' => [
                    'required',
                    'numeric',
                    Rule::unique('kefu')->where(function ($query) use($wid){
                        $query->whereNull('deleted_at')->where('wid', $wid);
                    })
                ],
            ];
            $message = Array(
                'telphone.required'  => '请输入客服电话号码',
                'telphone.numeric'   => '请输入正确的电话号码',
                'telphone.unique'    => '电话号码重复',
            );
            $validator = Validator::make($input,$rule,$message);
            //验证表单是否报错
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $input['wid'] = $wid;
            if($kefuService->add($input)){
                success('添加成功');
            }
            error('添加失败');
        }
        return view('merchants.currency.phoneKf',[
            'title'     => '电话客服',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'kefu',
        ]);
    }

    public function weChatKf()
    {

        return view('merchants.currency.weChatKf',[
            'title'     => '微信客服',
            'leftNav'   => $this->leftNav,
            'slidebar'  => 'kefu',
        ]);
    }

    public function getKfList(Request $request)
    {
    	$type = $request->input(['type']);
        $kefuService = new KefuService();
        $data = $kefuService->getAllList([],15,$type);

        success('','',$data);
    }

    /**
     * 通用设置二维码
     * @param  Request $request [description]
     * @return [type]           [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function commonSetting(Request $request,ShopService $shopService)
    {
        $uid = session('userInfo')['id'];
        $wid = session('wid');
        //$weixinInfo = WeixinService::init('uid', session('userInfo')['id'])->getInfo($wid);
        $weixinInfo = $shopService->getRowById($wid);
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rule = [
                'codeUrl'    =>  'required',
                'name'       =>  'required|max:6',
                //'adver_logo' =>  'required',
            ];
            $messages = [
                'codeUrl.required'  => '请上传二维码',
                'name.required'     => '请输入二维码名称',
                'name.max'          => '需要二维码名称在6个字内',
                //'adver_logo'        => '请上传店铺推广图片'
            ];
            $validator = Validator::make($input,$rule,$messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $data['wechat_qrcode'] = $input['codeUrl'];
            //$data['adver_logo']    = $input['adver_logo'];
            $data['qrcode_name']   = $input['name'];
            //$result = WeixinService::init('uid',$uid)->where(['id' => $wid])->update($data,false);
            $result = $shopService->update($wid,$data);
            if ($result) {
                success();
            }
            error();
        }
    	return view('merchants.currency.commonSetting',[
            'title'      => '通用设置',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'commonSetting',
            'weixinInfo' => $weixinInfo

    	]);
    }

    /**
     * 生成绑定核销员二维码
     * @author 吴晓平 <2018年10月09日>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function hexiaoQrcode(Request $request)
    {
        $uid = $request->input('uid') ?? 0;
        $wid = session('wid');
        $shopUrl = config('app.url') . 'shop/member/bindHexiaoUser?uid='.$uid.'&wid='.$wid;
        // 生成核销二维码
        $commonModule = new CommonModule();
        $url = $commonModule->qrCode($wid, $shopUrl);
        $data['qrcode'] = $url;
        return $data;
    }

    /**
     * 解除绑定核销员
     * @author 吴晓平 <2018年10月09日>
     * @param  Request           $request           [description]
     * @param  WeixinUserService $weixinUserService [description]
     * @return [type]                               [description]
     */
    public function unsetHexiaoUser(Request $request,WeixinUserService $weixinUserService)
    {
        $uid = $request->input('uid') ?? 0;
        if (empty($uid)) {
            error('参数异常');
        }
        $wid = session('wid');
        $saveData['hexiao_mid'] = 0;
        $re = $weixinUserService->init()->model->where(['uid'=>$uid,'wid'=>$wid])->update($saveData);
        if($re){
            success('成功解除绑定店铺核销员');
        }
        error();
    }

}


