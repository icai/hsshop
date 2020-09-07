<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/3/15
 * Time: 18:56
 */

namespace App\Http\Controllers\SellerApp;


use App\Http\Controllers\Controller;
use App\Model\WeixinRole;
use App\Module\BaseModule;
use App\Module\OrderModule;
use App\Module\StoreModule;
use App\S\File\FileInfoService;
use App\S\File\UserFileService;
use App\Services\Order\OrderService;
use App\Services\Permission\WeixinRoleService;
use App\Services\UserService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use Validator;
use PermissionService;
use QrCode;
use Storage;
use App\S\Weixin\ShopService;

class StoreController extends Controller
{
    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20170315
     * @desc 创建店铺
     * @param Request $request
     */
    function create(Request $request, WeixinService $weixinService, BaseModule $baseModule)
    {
        $tokenData = $request->input('_tokenData');
        $userId = $tokenData['userInfo']['id'];
        $count = $weixinService->init()->wheres(['uid' => $userId])->count();
        if ($count > 30) {
            apperror("超过可建立店铺数量");
        }

        $input = $request->input('parameter');
        $rules = array(
            'shop_name' => 'required|between:1,60',
            'company_name' => 'between:1,60',
            'province_id' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'address' => 'required|between:1,200',
        );
        $messages = array(
            'shop_name.required' => '请填写店铺名称',
            'shop_name.between' => '店铺名称最多填写60个字符',
            'company_name.between' => '公司名称最多填写60个字符',
            'province_id.required' => '请选择省份',
            'city_id.required' => '请选择城市',
            'area_id.required' => '请选择地区',
            'address.required' => '请填写联系地址',
            'address.between' => '联系地址最多填写200个字符',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $keys = array_keys($rules);
        foreach ($input as $key => $item) {
            if (!in_array($key, $keys)) {
                unset($input[$key]);
            }
        }
        $input['uid'] = $userId;
        $wid = $weixinService->init('uid', $userId)->add($input, false);
        $baseModule->setDataInToken($tokenData['token'], ['wid' => $wid]);
        (new StoreModule())->afterCreateShop($wid, $input, $userId);
        appsuccess('创建店铺成功');
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180316
     * @desc 首页信息
     * @update 何书哲 2019年06月05日 新增返回登录用户id
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function index(Request $request, OrderService $orderService)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $today = $orderService->getTodayOrderInfo($tokenData['wid']);
        $all = $orderService->getOrderInfoByWid($tokenData['wid']);
        $deliveryCount = $orderService->getOrderStatusData($tokenData['wid']);
        $result = array_merge($today, $all, ['deliveryCount' => $deliveryCount]);
        $weixinData = (new WeixinService())->getStore($tokenData['wid']);
        if ($weixinData['errCode'] != 0) {
            apperror();
        }
        $weixinData = $weixinData['data'];
        $result['shop_name'] = $weixinData['shop_name'];
        $result['logo'] = $weixinData['logo'] ? $weixinData['logo'] : 'hsshop/image/static/huisouyun_120.png';
        $result['is_overdue'] = 0;
        $result['tip'] = '';
        $result['surplus'] = 0;
        $result['phone'] = '0571-87796692';
        //返回登录用户id
        $result['id'] = $tokenData['userInfo']['id'];
        $weixinRoleData = (new WeixinRoleService())->init()->model->where('wid', $tokenData['wid'])->get()->toArray();
        if ($weixinRoleData) {
            $weixinRoleData = current($weixinRoleData);
        }
        if ($weixinRoleData) {
            if (strtotime($weixinRoleData['end_time']) < time()) {
                $result['is_overdue'] = 1;
                $result['tip'] = '您的店铺(' . $weixinData['shop_name'] . ')已打烊，为不影响正常运营，请订购会搜云微商城。';
            } else {
                $days = (strtotime($weixinRoleData['end_time']) - time()) / 86400;
                $days = ceil($days);
                if ($days <= 30) {
                    $result['is_overdue'] = 2;
                    $result['tip'] = '您的店铺(' . $weixinData['shop_name'] . ')距打烊仅剩' . $days . '天，为不影响正常运营，请订购会搜云微商城。';
                    $days <= 6 ? $result['surplus'] = $days : $result['surplus'] = 0;
                }

            }
        }
        appsuccess('操作成功', $result);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180319
     * @desc
     * @param Request $request
     * @update 何书哲 2018年8月13日 选择店铺返回客服聊天信息
     */
    public function setStore(Request $request, BaseModule $baseModule)
    {
        $input = $request->input('parameter');
        $rules = array(
            'wid' => 'required|integer',
        );
        $messages = array(
            'wid.required' => '店铺id不能为空',
            'wid.integer' => '店铺id必须是整数',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $tokenData = $request->input('_tokenData');
        $res = $baseModule->setDataInToken($input['token'], ['wid' => $input['wid']]);
        if ($res['errCode'] == 0) {
            PermissionService::addPermissionToRedis($tokenData['userInfo']['id'], $input['wid']);
            //何书哲 2018年8月13日 选择店铺返回客服聊天信息
            $data = ['id' => 0, 'wid' => 0, 'sign' => ''];
            $tokenData = $baseModule->getTokenData($input['token']);
            isset($tokenData['is_login']) && $tokenData['is_login'] == 1 &&
            isset($tokenData['userInfo']) && $tokenData['userInfo'] &&
            $data = [
                'id' => $tokenData['userInfo']['id'],
                'wid' => $input['wid'],
                'sign' => md5($input['wid'] . $tokenData['userInfo']['id'] . 'huisou'),
                'host' => 'https://hsim.huisou.cn/app/#/transfer',
            ];
            appsuccess('选择店铺成功', $data);
        } else {
            apperror($res['errMsg']);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180319
     * @desc
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function shopShareInfo(Request $request, StoreModule $storeModule)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $shopQrCode = $storeModule->createShopQRCode($tokenData['wid']);
        $result = (new WeixinService())->getStore($tokenData['wid']);
        if ($result['errCode'] == 0) {
            $weixinData = $result['data'];
        } else {
            apperror();
        }
        $result = [
            'url' => config('app.url') . 'shop/index/' . $tokenData['wid'],
            'shopQrCode' => $shopQrCode,
            'share_title' => $weixinData['share_title'] ? $weixinData['share_title'] : $weixinData['shop_name'],
            'share_desc' => $weixinData['share_desc'] ? $weixinData['share_desc'] : $weixinData['shop_name'],
            'share_logo' => $weixinData['share_logo'] ? imgUrl($weixinData['share_logo']) : imgUrl($weixinData['logo']),
        ];
        appsuccess('操作成功', $result);

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180320
     * @desc 修改用户信息
     */
    public function setUserInfo(Request $request, UserService $userService)
    {
        $parameter = $request->input('parameter');
        $tokenData = $request->input('_tokenData');
        $data = [];
        foreach ($parameter as $key => $item) {
            switch ($key) {
                case 'name' :
                    $data['name'] = $item;
                    break;
                case 'head_pic':
                    $data['head_pic'] = $item;
                    break;
                case 'sex':
                    $data['sex'] = $item;
                    break;
                case 'qq':
                    $data['qq'] = $item;
                    break;
                case 'sign' :
                    $data['sign'] = $item;
            }
        }

        if (!$data) {
            unset($tokenData['userInfo']['password']);
            appsuccess('操作成功', $tokenData['userInfo']);
        }
        $res = $userService->init()->where(['id' => $tokenData['userInfo']['id']])->update($data, false);
        if ($res) {
            $userData = $userService->init()->model->find($tokenData['userInfo']['id']);
            if ($userData) {
                $userData = $userData->toArray();
            }
            (new BaseModule())->setDataInToken($tokenData['token'], ['userInfo' => $userData]);
            appsuccess('修改用户信息成功');
        } else {
            apperror('修改用户信息失败');
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180321
     * @desc 上传图片
     */
    public function upFile(Request $request, FileInfoService $fileInfoService, UserFileService $userFileService)
    {
        if (!$request->hasFile('file')) {
            apperror('请上传图片');
        }
        $result = $fileInfoService->upFile($request->file('file'));
        if ($result['success'] == 1 && $request->input('halt', 0) == 1) {
            apperror();
        }
        $tokenData = $request->input('_tokenData');
        $userFileData = Array(
            'user_id' => $tokenData['userInfo']['id'],
            'file_info_id' => $result['data']['id'],
            'file_classify_id' => $request->input('classifyId', 0),
            'weixin_id' => $tokenData['wid'],
            'file_mine' => $result['data']['file_mine'],
            'file_cover' => $request->input('file_cover', ''),
        );
        $userFileId = $userFileService->add($userFileData);
        if (!$userFileId) {
            apperror();
        }
        $where = [];
        $where['file_classify_id'] = $userFileData['file_classify_id'];
        $where['weixin_id'] = $tokenData['wid'];
        $where['id'] = $userFileId;
        list($data) = $userFileService->getlistPage($where);
        $data = current($data['data']);
        $result = [
            'id' => $data['id'],
            'file_info_id' => $data['file_info_id'],
            'file_classify_id' => $data['file_classify_id'],
            'name' => $data['FileInfo']['name'],
            'path' => $data['FileInfo']['path'],
            'type' => $data['FileInfo']['type'],
            's_path' => $data['FileInfo']['s_path'],
            'm_path' => $data['FileInfo']['m_path'],
            'l_path' => $data['FileInfo']['l_path'],
            'img_size' => $data['FileInfo']['img_size'],
            'size' => $data['FileInfo']['size'],
        ];
        appsuccess('上传成功', $result);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180402
     * @desc 店铺信息
     * @update 何书哲 2019年07月22日 增加判断token必要参数
     */
    public function storeInfo(Request $request)
    {
        $tokenData = $request->input('_tokenData');
        // update 何书哲 2019年07月22日 增加判断token必要参数
        if (!isset($tokenData['wid'])) {
            apperror('无权访问');
        }
        $shopData = (new WeixinService())->getStore($tokenData['wid']);
        $shopData = $shopData['data'];
        $roleData = (new WeixinRoleService())->init()->model->where('wid', $tokenData['wid'])->get()->toArray();
        if (!$roleData) {
            apperror('权限不存在');
        }
        $roleData = current($roleData);
        if (strtotime($roleData['end_time']) > time()) {
            $effective = '有效期至:' . date('Y.m.d', strtotime($roleData['end_time']));
        } else {
            $effective = '已过期';
        }
        $result = [
            'name' => $tokenData['userInfo']['name'],
            'mphone' => $tokenData['userInfo']['mphone'],
            'head_pic' => $tokenData['userInfo']['head_pic'],
            'shop_name' => $shopData['shop_name'],
            'effective' => $effective,
            'logo' => $shopData['logo'] ? $shopData['logo'] : 'hsshop/image/static/huisouyun_120.png'
        ];
        appsuccess('操作成功', $result);
    }

}