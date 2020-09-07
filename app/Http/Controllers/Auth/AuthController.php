<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Jobs\LoginStatistics;
use App\S\File\FileInfoService;
use App\Lib\Redis\SMSKeys;
use App\Model\User;
use App\S\Foundation\VerifyCodeService;
use App\Services\Lib\JSSDK;
use Captcha;
use Cookie;
use Hash;
use Illuminate\Http\Request;
use Response;
use Session;
use Validator;
use App\Services\UserService;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     * @param Hasher $hasher
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * 登录页面
     * @return [type] [description]
     * @update 陈文豪 20180717 处理空格问题
     * @update 吴晓平 20180905 限制商家登录错误次数
     * @update 何书哲 2018年9月19日 登录日志发送数据中心
     * @update 何书哲 2019年09月30日 去除手机号码、密码空格
     */
    public function login(Request $request)
    {
        $user = new User;
        if ($request->isMethod('post')) {
            // 数据接收
            $input = $request->input();

            // 数据验证
            $rules = array(
                'mphone' => 'required|string|regex:mobile',
                'password' => 'required|string|between:6,18',
                'captcha' => 'required|string',
            );
            $messages = array(
                'mphone.required' => '请输入手机号码',
                'mphone.string' => '手机号码格式不正确',
                'mphone.regex' => '手机号码格式不正确',
                'password.required' => '请输入密码',
                'password.string' => '密码格式不正确',
                'password.between' => '请输入6-18位字符长度的密码',
                'captcha.required' => '请输入验证码',
                'captcha.string' => '验证码格式不正确',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return myerror($validator->errors()->first());
            }

            // update 何书哲 2019年09月30日 去除手机号码、密码空格
            $input['mphone'] = trim($input['mphone']);
            $input['password'] = trim($input['password']);
            $key = 'limit-' . $input['mphone'];

            // 验证验证码
            if (Captcha::check($input['captcha']) === false) {
                return myerror('验证码错误');
            }

            // 查询用户信息
            $map['mphone'] = $input['mphone'];
            $userInfo = $user->where($map)->first(['id', 'name', 'password', 'mphone', 'head_pic']);
            !$userInfo['id'] && error('用户不存在');
            $userService = new UserService();
            // add by 吴晓平2018年09月05日 商家后台限制登录次数
            if ($userService->getErrLogins($key) == 5) {
                return myerror('每天限制登录5次，请您在一个小后再登录');
            }
            // 验证密码
            if (Hash::check($input['password'], $userInfo->password) === false) {
                $userService->setErrLogins($key);
                return myerror('密码错误');
            }
            $userInfo->isWeakPasswd = UserService::checkIsWeakPasswd($input['password']);
            $this->_setssession($request, $userInfo);
            $userService->cleanErrLogins($key);

            // 登陆次数+1
            $user->where($map)->increment('logins');
            // 保存登陆
            if (isset($input['remember']) && $input['remember'] === 'on') {
                $identifier = md5(md5($input['mphone'] . time()));
                $rememberToken = md5(uniqid(rand(), true));
                $timeout = 60 * 24 * 3;
                // 存入cookie
                Cookie::queue('auth', "$identifier:$rememberToken", $timeout);

                $remember_time = time() + 24 * 3600 * 3;
                $updateArr = array(
                    'identifier' => $identifier,
                    'remember_token' => $rememberToken,
                    'remember_time' => $remember_time
                );
                $user->where($map)->update($updateArr);
            }
            // 何书哲 2018年9月19日 登录日志发送数据中心
            dispatch((new LoginStatistics($userInfo['id'], getIP(), 1))->onQueue('LoginStatistics'));
            return mysuccess('登陆成功', '/merchants/team');
        }

        $userInfo = $request->session()->get('userInfo');
        if ($userInfo) {
            return redirect('/merchants/team');
        }
        // 检查是否保持登陆

        $auth = $request->cookie('auth');
        if ($auth) {
            list($identifier, $rememberToken) = explode(':', $auth);
            $cmap['identifier'] = $identifier;
            $userInfo = $user->where($cmap)->first(['id', 'name', 'password', 'mphone', 'head_pic', 'remember_token', 'identifier', 'remember_time']);
            if (isset($userInfo->remember_token) &&
                !empty($userInfo->remember_token) &&
                isset($userInfo->remember_time) &&
                !empty($userInfo->remember_time) &&
                $userInfo->remember_token == $rememberToken &&
                $userInfo->remember_time > time()) {
                $this->_setssession($request, $userInfo);
                return redirect('/merchants/team');
            }
        }

        return view('auth.login', [
            'title' => '登录'
        ]);
    }

    public function limitLogin($username = '', $ip = '127.0.0.1')
    {
        $userRedis = new Redis();
        if ($userRedis->exists($username . $ip)) {
            $userRedis->incr($username . $ip);
        } else {
            $userRedis->set($username . $ip, 1);
        }
    }

    /**
     * [sendCode 注册验证码]
     * @return [type] [description]
     */
    public function sendCode(Request $request, VerifyCodeService $verifyCodeService)
    {
        $phone = $request->input('mphone');

        if (!$phone) {
            error('请输入您的手机号码');
        }

        $type = $request->input('type');
        if (!isset($type) && empty($type)) {
            /* 验证验证码 */
            $type = 1;
            $typeString = 'register_';
        } elseif ($type == 4) {
            $typeString = 'forget_';
        } else {
            error('类型错误');
        }

        //生成验证码 随机生成4位
        $code = rand(1000, 9999);
        $datas = [$code, 1, '0571-87796692'];
        $result = $verifyCodeService->sendCode($phone, $datas, $type);
        if ($result->statusCode != 0) {
            error((string)$result->statusMsg);
        } else {
            $smsKeys = new SMSKeys($typeString . $phone);
            $smsKeys->set($code);
            success('验证码发送成功');
        }
    }

    /**
     * 注册页面
     * @return [type] [description]
     */
    public function registerUser(Request $request)
    {
        $close = 1; // 1是打开注册
        if ($request->isMethod('post')) {
            if ($close == 0) {
                error('注册关闭');
            }
            $input = $request->input();
            $rules = array(
                'mphone' => 'required|regex:mobile',
                'name' => 'between:1,80',
                'password' => 'required|confirmed|between:6,18',
            );
            $messages = array(
                'mphone.required' => '请输入手机号码',
                'mphone.regex' => '手机号码格式不正确',
                'name.between' => '个人昵称长度为1-80个字符',
                'password.required' => '请输入密码',
                'password.between' => '请输入6-18位字符长度的密码',
                'password.confirmed' => '请输入正确的确认密码',
            );
            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $smsKeys = new SMSKeys('register_' . $input['mphone']);
            $captcha = $smsKeys->get();
            if ($captcha != $input['code']) {
                error('验证码错误');
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
                success('注册成功', '/auth/login');
            } else {
                error('注册失败');
            }
        }
        return view('auth.register', array(
            'title' => '注册',
            'close' => $close //控制注册开关
        ));
    }


    /**
     * 退出登录
     * @return [type] [description]
     * @upadte 张永辉 2018年7月6日
     */
    public function loginout(Request $request)
    {
        $request->session()->flush();
        $cookie = Cookie::forget('auth');
        if (\Route::current()->getUri() == 'auth/loginout/shop') {
            return myerror('缓存清除成功');
        }
        return redirect('/auth/login')->withCookie($cookie);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    private function _setssession(Request $request, $userInfo)
    {
        /* 将用户信息保存到session中 */
        $request->session()->put('userInfo', [
            'id' => $userInfo->id,
            'name' => $userInfo->name,
            'mphone' => $userInfo->mphone,
            'head_pic' => $userInfo->head_pic,
            'isWeakPasswd' => $userInfo->isWeakPasswd ?? false,
        ]);

    }

    public function forgetPsd()
    {
        return view('auth.forgetpsd');
    }

    public function updateForgetPsd(Request $request)  //ChangePasswordRequest $request
    {
        $input = $request->input();
        $rules = array(
            'mphone' => 'required|regex:mobile',
            'password' => [
                'required',
                'between:8,18',
                'regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/'
            ],
            'code' => 'required',
        );
        $messages = array(
            'mphone.required' => '请输入手机号码',
            'code.required' => '请输入手机验证码',
            'mphone.regex' => '手机号码格式不正确',
            'password.required' => '请输入密码',
            'password.between' => '请输入8-18位字符长度的密码',
            'password.regex' => '只支持8-18位字母跟数字组合密码'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $smsKeys = new SMSKeys('forget_' . $input['mphone']);
        $captcha = $smsKeys->get();
        if ($captcha != $input['code']) {
            error('验证码错误');
        }

        $user = new User;
        $map['mphone'] = $input['mphone'];
        $userInfo = $user->where($map)->first(['id']);
        if (!$userInfo) {
            error('该账号不存在');
        }
        $password = bcrypt($input['password']);
        $insertInfo = $user->where($map)->update(['password' => $password]);
        if ($insertInfo) {
            success('修改成功', '/auth/login');
        }
        error('修改失败');
    }

    public function updateChangePsd(ChangePasswordRequest $request)  //Request $request
    {
        $userInfo = $request->session()->get('userInfo');
        if (!isset($userInfo['mphone']) || empty($userInfo['mphone'])) {
            return redirect('/auth/login');
        }
        $input = $request->input();
        $rules = array(
            'password' => [
                'required',
                'between:8,18',
                'regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/',
                'confirmed'
            ],
        );
        $messages = array(
            'password.required' => '请输入新密码',
            'password.between' => '请输入8-18位字符长度的密码',
            'password.regex' => '只支持8-18位字母跟数字组合密码',
            'password.confirmed' => '两次输入密码不一致'
        );
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $user = new User;
        $map['mphone'] = $userInfo['mphone'];
        $userInfo = $user->where($map)->first(['id', 'password']);
        if (!$userInfo) {
            error('该账号不存在');
        }
        /* 验证密码 */
        if (Hash::check($input['old_password'], $userInfo->password) === false) {
            return myerror('旧密码错误');
        }

        $password = bcrypt($input['password']);
        $insertInfo = $user->where($map)->update(['password' => $password]);
        if ($insertInfo) {
            $userInfo = $request->session()->get('userInfo');
            $userInfo['isWeakPasswd'] = false;
            $request->session()->put('userInfo', $userInfo);
            $request->session()->save();
            success('修改成功', '/merchants/team');
        }
        error('修改失败');
    }

    public function changePsd(Request $request)
    {
        $userInfo = $request->session()->get('userInfo');
        if (!isset($userInfo['mphone']) || empty($userInfo['mphone'])) {
            return redirect('/auth/login');
        }
        return view('auth.changepsd');
    }

    public function set(Request $request)
    {
        $userInfo = $request->session()->get('userInfo');
        if (!isset($userInfo['mphone']) || empty($userInfo['mphone'])) {
            return redirect('/auth/login');
        }
        return view('auth.set');
    }

    public function updateSet(Request $request)
    {
        $userInfo = $request->session()->get('userInfo');
        if (!isset($userInfo['mphone']) || empty($userInfo['mphone'])) {
            return redirect('/auth/login');
        }
        $input = $request->input();
        $rules = array(
            'name' => 'required',
        );
        $messages = array(
            'name.required' => '昵称不能为空',
        );
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        $user = new User;
        $map['mphone'] = $userInfo['mphone'];
        $userInfo = $user->where($map)->first(['id', 'password']);
        if (!$userInfo) {
            error('该账号不存在');
        }

        $insertInfo = $user->where($map)->update(['name' => $input['name'], 'head_pic' => $input['logo']]);
        if ($insertInfo) {
            $request->session()->put('userInfo.name', $input['name']);
            $request->session()->put('userInfo.head_pic', $input['logo']);
            $request->session()->save();
            success('修改成功', '/auth/set');
        }
        error('修改失败');
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @desc 上传文件
     * @param Request $request
     * @param FileInfoService $fileInfoService
     */
    public function upfile(Request $request, FileInfoService $fileInfoService)
    {
        if ($request->hasFile('file')) {
            $result = $fileInfoService->upFile($request->file('file'));
            if ($result['success'] == 1) {
                $content = array('status' => 1, 'info' => '上传成功', 'url' => '', 'data' => $result['data']);
                echo json_encode($content);
                exit();
            } else {
                error('文件上传失败');
            }
        } else {
            error('请上传文件');
        }
    }


    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->input();
            $rule = [
                'mphone' => 'required|regex:/^1[34578][0-9]{9}$/',
                'sms_code' => 'required',
                'nickname' => 'required',
                'password' => 'required|between:6,12',
            ];

            $message = [
                'mphone.required' => '请输入手机号码',
                'mphone.reqex' => '手机号码输入错误',
                'sms_code.required' => '请输入短信验证码',
                'nickname.required' => '请输入个人昵称',
                'password.required' => '请设置密码',
                'password.between' => '请设置6-12位字母和数字组合的密码'
            ];

            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $smsKeys = new SMSKeys('register_' . $input['mphone']);
            $captcha = $smsKeys->get();
            if ($captcha != $input['sms_code']) {
                error('验证码错误');
            }

            $user = new User;
            $user->mphone = $input['mphone'];
            $user->name = $input['nickname'];
            $user->password = bcrypt($input['password']);
            $user->source = 1;
            $insertInfo = $user->save();
            if ($insertInfo) {
                success('注册成功');
            } else {
                error('注册失败');
            }
        }
        return view('shop.share.register', [
            'title' => '内部推荐享豪礼',
            'appurl' => 'https://abc-test.huisou.cn/'
        ]);
    }

    /**
     * 验证是否已经注册
     * @return boolean [description]
     */
    public function isRegister(Request $request)
    {
        $user = new User;
        $map['mphone'] = $request->input('tel');
        $userInfo = $user->where($map)->first(['id']);
        if ($userInfo) {
            error('该账号已存在');
        }
        success('该账号可以注册');
    }


    /**
     * 分享注册成功页面
     * @return [type] [description]
     */
    public function regSuccess()
    {
        return view('shop.share.regSuccess', [
            'title' => '分享注册--成功',
            'appurl' => 'https://abc-test.huisou.cn/'
        ]);
    }

    public function getShareData(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $appId = config('app.public_auth_appid');
        $secret = config('app.public_auth_secret');
        $url = $request->input('url');
        try {
            $jssdk = new JSSDK($appId, $secret);
            $signPackage = $jssdk->GetSignPackage($url);
            if (!empty($signPackage)) {
                $returnData['data'] = $signPackage;
            } else {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '没有获取到微信api数据';
            }
        } catch (\Exception $ex) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $ex->getMessage();
        }
        return $returnData;
    }


}
