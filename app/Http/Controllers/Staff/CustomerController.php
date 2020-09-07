<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/4/20
 * Time: 13:32
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Module\ExportModule;
use App\Module\PermissionModule;
use App\S\Foundation\VerifyCodeService;
use App\Services\Permission\AdminRoleService;
use App\Services\ReserveService;
use App\S\User\UserService;
use App\S\Staff\LiteappHistoryService;
use App\S\Staff\LiteappService;
use Illuminate\Http\Request;
use Validator;

class CustomerController extends Controller
{

    public function registerUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rules = array(
                'mphone' => 'required|regex:mobile',
                'name' => 'between:1,80',
                'password' => 'required|confirmed|between:8,18',
            );
            $messages = array(
                'mphone.required' => '请输入手机号码',
                'mphone.regex' => '手机号码格式不正确',
                'name.between' => '个人昵称长度为1-80个字符',
                'password.required' => '请输入密码',
                'password.between' => '请输入8-18位字符长度的密码',
                'password.confirmed' => '请输入正确的确认密码',
            );
            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $user = new User;
            $map['mphone'] = $input['mphone'];
            $userInfo = $user->where($map)->first(['id']);
            if ($userInfo) {
                error('该账号已存在');
            }
            $user->mphone = $input['mphone'];
            $user->name = $input['name'];
            $user->password = bcrypt($input['password']);
            $insertInfo = $user->save();
            if ($insertInfo) {
                return redirect('/staff/userlist');
            } else {
                error('注册失败');
            }
        }
        return view('staff.customer.registerUser', array(
            'title' => '店铺管理',
            'sliderba' => 'registerUser',
        ));
    }

    public function userlist(Request $request, UserService $userService)
    {
        /**把service\UserService 迁移到S\User\UserService  总后台数据重构，增加统计总的店铺数，商品数，会员数，销售额 吴晓平 2018年09月18日**/
        $input = $request->input() ?? [];
        $where = [];
        if (isset($input['name']) && $input['name']) {
            $where['name'] = $input['name'];
        }
        if (isset($input['mphone']) && $input['mphone']) {
            $where['mphone'] = $input['mphone'];
        }
        $orderBy = $input['orderby'] ?? '';
        $order = $input['order'] ?? '';
        list($shopData,$pageHtml) = $userService->getAllList($where,$orderBy,$order);
        if ($shopData['data']) {
            foreach ($shopData['data'] as $key => &$value) {
                if (isset($value['weixin']) && $value['weixin']) {
                    $value['weixin'] = json_decode($value['weixin'],true);
                    $value['shopCountTotal'] = count($value['weixin']);
                    $value['productCountTotal'] = array_sum(array_column($value['weixin'], 'productCount'));
                    $value['memberCountTotal'] = array_sum(array_column($value['weixin'], 'member_sum'));
                    $value['SaleCountTotal'] = array_sum(array_column($value['weixin'], 'sale_sum'));
                }else {
                    $value['weixin'] = [];
                    $value['shopCountTotal'] = 0;
                    $value['productCountTotal'] = 0;
                    $value['memberCountTotal'] = 0;
                    $value['SaleCountTotal'] = 0;
                }
            }
        }
        return view('staff.customer.userlist', array(
            'title'    => '店铺管理',
            'sliderba' => 'registerUser',
            'shopData' => $shopData['data'],
            'pageHtml' => $pageHtml,
        ));
    }

    /**
     * 总后台修改用户登录后台的帐号，密码
     * add by wuxiaoping 2018.05.29
     * @return [type] [description]
     * @update 只修改帐号 2018.09.07
     */
    public function userModify(Request $request)
    {
        $input = $request->input();
        $rules = array(
            'phone' => 'required|regex:mobile',
        );
        $messages = array(
            'phone.required' => '手机帐号不能为空',
            'phone.regex'    => '手机号码格式不正确',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $user = User::find($input['uid']);
        if (isset($input['phone']) && $input['phone']) {
            $user->mphone = $input['phone'];
            $obj = User::where('mphone', $input['phone'])->first(['id']);
            if ($obj) {
                $userId = $obj->toArray();
                if ($userId && $userId['id']) {
                    error('此账号已注册过其它店铺');
                }
            }
            $insertInfo = $user->save();
            if ($insertInfo) {
                success('修改成功');
            } else {
                error('修改失败');
            }
        }else {
            error('未提交任何修改');
        }

    }

    /**
     * 总后台修改用户登录后台的密码
     * add by wuxiaoping 2018.09.07
     * @return [type] [description]
     */
    public function passwordModify(Request $request)
    {
        $input = $request->input();
        $rules = array(
            'password' => [
                'required',
                'between:8,18',
                'regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/'
            ],
        );
        $messages = array(
            'password.required' => '请输入要修改的密码',
            'password.between'  => '请输入8-18位长度的密码',
            'password.regex'    => '只支持8-18位英文跟数字组合的密码，不含字符'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $user = User::find($input['uid']);
        if (isset($input['password']) && $input['password']) {
            $user->password = bcrypt($input['password']);
            $insertInfo = $user->save();
            if ($insertInfo) {
                success('修改成功');
            } else {
                error('修改失败');
            }
        }else {
            error('未提交任何修改');
        }
    }
    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20140420
     * @desc 潜在客户管理
     * @param Request $request
     * @param ReserveService $reserveService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reserveManage(Request $request, ReserveService $reserveService)
    {
        $input = $request->input();
        $where = [
            'action' => 0,
        ];
        $tag = 'reserveManage';
        if (isset($input['status'])) {
            $where['status'] = $input['status'];
            $tag = 'reserveManage1';
        }
        $data = $reserveService->init()->where($where)->getList();
        return view('staff.customer.reserveManage', array(
            'title' => '潜在客户管理',
            'sliderbar' => $tag,
            'reserve' => $data,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170420
     * @desc 潜在客户加星
     * @param ReserveService $reserveService
     * @param $id
     * @param $status
     */
    public function addStar(ReserveService $reserveService, $id, $status)
    {
        $reserveService->init()->where(['id' => $id])->update(['id' => $id, 'status' => $status]);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date
     * @desc 删除潜在用户
     * @param ReserveService $reserveService
     * @param $id
     */
    public function delete(ReserveService $reserveService, $id)
    {
        $reserveService->init()->where(['id' => $id])->delete($id);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170810
     * @desc
     */
    public function export(ReserveService $reserveService)
    {
        $data['data'] = $reserveService->init()->model->orderBy('id', 'desc')->get()->toArray();

        $data['title'] = [
            'id' => '序号',
            'name' => '姓名',
            'phone' => '电话',
            'industry' => '行业',
            'source' => '来源',
            'link_source' => '推广来源',
            'type' => '预约类型1:分销，2：app定制,3:微信小程序，4：微信营销总裁班',
            'created_at' => '预约时间',
            'status' => '0:正常，1：已加星',
        ];
        (new ExportModule())->derive($data, '潜在客户管理', $style = 'xlsx');
    }

    /**
     * 小程序查重
     */
    public function searchXCX(Request $request, ReserveService $reserveService)
    {
        $input = $request->input();
        $where = [
            'action' => 1,
        ];

        if (isset($input['status']) && $input['status'] != 'all') {
            $where['status'] = $input['status'];
        }

        if (isset($input['is_register']) && $input['is_register'] != 'all') {
            $where['is_register'] = $input['is_register'];
        }

        $data = $reserveService->init()->where($where)->getList();

        return view('staff.customer.searchXCX', array(
            'title' => '小程序',
            'sliderbar' => 'searchXCX',
            'reserve' => $data,
        ));
    }

    /**
     * 小程序查重-删除 加星 去星0操作(包括批量)
     */
    public function operate(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $reserveService = new ReserveService();

            //类型 0删除 1加星 2取消加星
            $type = $input['type'];
            //ID数组
            $ids = $input['ids'];

            if (!isset($type) || empty($ids)) {
                error('参数不完整');
            }

            if ($type == 0) {
                //删除
                foreach ($ids as $id) {
                    $reserveService->init()->where(['id' => $id])->delete($id, false);
                }
            } elseif ($type == 1) {
                //加星
                foreach ($ids as $id) {
                    $reserveService->init()->where(['id' => $id])->update(['status' => 1], false);
                }
            } else {
                //取消加星
                foreach ($ids as $id) {
                    $reserveService->init()->where(['id' => $id])->update(['status' => 0], false);
                }
            }

            success('操作成功');
        } else {
            error('操作非法');
        }
    }

    /**
     * 导出小程序查询客户列表
     */
    public function exportSearchXCX(ReserveService $reserveService)
    {
        $data['data'] = $reserveService->init()->model->where('action', 1)->orderBy('id', 'desc')->get()->toArray();

        $data['title'] = [
            'id' => '序号',
            'name' => '姓名',
            'phone' => '电话',
            'industry' => '行业',
            'source' => '来源',
            'link_source' => '推广来源',
            'liteapp_title' => '查询的小程序',
            'created_at' => '查询时间',
            'status' => '0:正常，1：已加星',
            'is_register' => '0未注册,1注册',
        ];
        (new ExportModule())->derive($data, '查询小程序客户', $style = 'xlsx');
    }

    /**
     * 小程序列表
     */
    public function liteapp(Request $request)
    {
        $input = $request->input();
        $where = [
            1 => 1,
        ];

        if (isset($input['title'])) {
            $where['title'] = ['like', "%" . $input['title'] . "%"];
        }

        $data = (new LiteappService())->listWithPage($where);

        return view('staff.customer.liteapp', array(
            'title' => '小程序',
            'sliderbar' => 'liteapp',
            'data' => $data,
        ));
    }

    /**
     * 新建小程序
     */
    public function liteappAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            //获取参数
            $input = $request->input();

            if (empty($input['title'])) {
                error('小程序名称为空');
            }

            //批量插入
            (new LiteappService())->add(explode(',', $input['title']));

            success('操作成功');
        }
    }

    /**
     * 小程序删除操作(包括批量)
     */
    public function liteappDelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();

            //ID数组
            $ids = $input['ids'];

            if (empty($ids)) {
                error('参数不完整');
            }

            //删除
            (new LiteappService())->delete($ids);

            success('操作成功');
        } else {
            error('操作非法');
        }
    }

    /**
     * 导出小程序查询客户列表
     */
    public function exportLiteapp()
    {
        $data['data'] = (new LiteappService())->getAll();

        $data['title'] = [
            'id' => '序号',
            'title' => '小程序名称',
        ];
        (new ExportModule())->derive($data, '小程序列表', $style = 'xlsx');
    }

    /**
     * 小程序查询历史
     */
    public function liteappHistory()
    {
        $data = (new LiteappHistoryService())->listWithPage();

        success('', '', $data[0]['data']);
    }

    /**
     * 小程序查询历史-新增
     */
    public function liteappHistoryAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();

            //参数
            $phoneArr = $input['phone'] ?? [];
            $titleArr = $input['title'] ?? [];

            if (empty($phoneArr) || empty($titleArr)) {
                error('参数不完整');
            }
            if (count($phoneArr) != count($titleArr)) {
                error('数据不对应');
            }

            //新增
            (new LiteappHistoryService())->add($phoneArr, $titleArr);

            success('操作成功');
        } else {
            error('操作非法');
        }
    }

    /**
     * 一键开通权限
     * @param Request $request
     * @param AdminRoleService $adminRoleService
     * @param VerifyCodeService $codeService
     * @param PermissionModule $module
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年7月31日
     */
    public function openPermission(Request $request,AdminRoleService $adminRoleService,VerifyCodeService $codeService,PermissionModule $module)
    {

        //验证参数
        if ($request->isMethod('post')) {
            $input = $request->input();
            //是否创建店铺
            $rules = [
                'isRegisterUser' => 'required', //是否创建账号
                'phone' => 'required', //电话号码
                'isCreateShop' => 'required', //是否创建店铺
                'isSendMsg' => 'required ', //是否发送短信
            ];
            $messages = array(
                'isRegisterUser.required' => '请选择是否创建账号',
                'isCreateShop.required' => '请选择是否创建店铺',
                'isSendMsg.required' => '请选择是否发送短信',
                'phone.required'    => '请输入电话号码'
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            if($input['isCreateShop'] && !isset($input['permission'])) {
                error('请设置店铺权限');
            }
            if($input['isSendMsg'] && !in_array($input['msgTemplateId'],[11,14])) {
                error('请选择将要发送的短信模板');
            }
            if (!$module->openPermission($input)) {
                error();
            }
            success('系统正在处理中','/staff/openPermission');
        }else {
            //获取权限，短信模板
            $adminRoleData = $adminRoleService->init()->where(['1'=>1])->getList(false);
            $return['permission'] = $adminRoleData[0]['data'];
            return view('staff.customer.openPermission', array(
                'title' => '店铺管理',
                'sliderba' => 'openPermission',
                'data'     => $return
            ));
        }
    }

    /**
     * 获取一键开通权限日志
     * @param PermissionModule $module
     * @author: 梅杰 time
     */
    public function openPermissionLog(PermissionModule $module)
    {
        $log = $module->openPermissionLog();
        return view('staff.customer.openPermissionLog', array(
            'title' => '店铺管理',
            'sliderba' => 'openPermission',
            'data'     => $log
        ));
    }

}
