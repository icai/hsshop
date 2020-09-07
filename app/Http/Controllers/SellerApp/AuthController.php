<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/3/5
 * Time: 17:15
 */

namespace App\Http\Controllers\SellerApp;


use App\Http\Controllers\Controller;
use App\Jobs\LoginStatistics;
use App\Module\BaseModule;
use App\Module\RSAModule;
use App\S\Foundation\VerifyCodeService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180305
     * @desc 用户登陆
     * @param Request $request
     * @param RSAModule $RSAModule
     * @update 何书哲 2018年9月19日 登录日志发送数据中心
     */
    public function login(Request $request,RSAModule $RSAModule)
    {
        $input = $RSAModule->decrypt($request->input('parameter')??'');
        if (!$input){
            apperror('参数不能为空');
        }
        $rules = array(
            'mobile'   => 'required|regex:mobile',
            'passwd'  => 'required|between:6,18',
            'token'  => 'required',
        );
        $messages = array(
            'mobile.required'   => '请输入手机号码',
            'mobile.regex'      => '手机号码格式不正确',
            'passwd.required' => '请输入密码',
            'passwd.between'  => '请输入6-18位字符长度的密码',
            'token.required'  => '令牌不能为空',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            apperror( $validator->errors()->first());
        }
        $res = (new BaseModule())->getTokenData($input['token']);
        if ($res && $res['is_login'] == 1){
            apperror('您已登录请不要重复登陆');
        }
        $result = (new UserService())->checkUserLogin($input['mobile'],$input['passwd'],$input['token']);
        if ($result['errCode'] == 0){
            //何书哲 2018年9月19日 登录日志发送数据中心
            dispatch((new LoginStatistics($result['data']['id'], getIP(), 1))->onQueue('LoginStatistics'));
            appsuccess('登录成功', ['id' => $result['data']['id']]);
        }else{
            apperror($result['errMsg']);
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180306
     * @desc 修改密码
     */
    public function modifyPasswd(Request $request,UserService $userService)
    {
        $input = $request->input('parameter');
        $tokenData = $request->input('_tokenData');
        $rules = array(
            'passwd'            => 'required|between:6,18',
            'newpasswd'         => 'required|between:6,18',
            'confirmpasswd'     => 'required|between:6,18',
        );
        $messages = array(
            'passwd.required' => '请输入原密码',
            'passwd.between'  => '请输入6-18位字符长度的原密码',
            'newpasswd.required' => '请输入新密码',
            'newpasswd.between'  => '请输入6-18位字符长度的新密码',
            'confirmpasswd.required' => '请输入确认密码',
            'confirmpasswd.between'  => '请输入6-18位字符长度的确认密码',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            apperror( $validator->errors()->first());
        }
        if ($input['newpasswd'] != $input['confirmpasswd']){
            apperror('两次输入密码不一致');
        }
        $res = $userService->modifyPasswd($tokenData['userInfo']['mphone'],$input['passwd'],$input['newpasswd']);
        if ($res['errCode'] == 0){
            appsuccess('修改密码成功');
        }else{
            apperror($res['errMsg']);
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180315
     * @desc 忘记密码
     * @param Request $request
     */
    public function forgetPasswd(Request $request,RSAModule $RSAModule,BaseModule $baseModule)
    {
        $input = $RSAModule->decrypt($request->input('parameter')??'');
        if (!$input){
            apperror('参数不能为空');
        }
        $rules = array(
            'mobile'   => 'required|regex:mobile',
            'passwd'  => 'required|between:6,18',
            'code'  => 'required',
            'token'  => 'required',
        );
        $messages = array(
            'mobile.required'   => '请输入手机号码',
            'mobile.regex'      => '手机号码格式不正确',
            'passwd.required' => '请输入密码',
            'passwd.between'  => '请输入6-18位字符长度的密码',
            'token.required'  => '令牌不能为空',
            'code.required'  => '验证码不能',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            apperror( $validator->errors()->first());
        }
        $tokenData = $baseModule->getTokenData($input['token']);

        if (isset($tokenData['code'][$input['mobile']]) && in_array($input['code'],$tokenData['code'][$input['mobile']])){
            $baseModule->delTokenData($input['token'],['code']);
            $res = (new UserService())->forgetPasswd($input['mobile'],$input['passwd']);
            if ($res['errCode'] == 0){
                appsuccess('找回密码成功');
            }else{
                apperror($res['errMsg']);
            }
        }else{
            apperror('验证码错误');
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180215
     * @desc 发送验证码
     */
    public function sendCode(Request $request,RSAModule $RSAModule,VerifyCodeService $verifyCodeService,BaseModule $baseModule)
    {
        $input = $RSAModule->decrypt($request->input('parameter')??'');
        if (!$input){
            apperror('参数不能为空');
        }
        $rules = array(
            'mobile'   => 'required|regex:mobile',
            'token'  => 'required',
        );
        $messages = array(
            'mobile.required'   => '请输入手机号码',
            'mobile.regex'      => '手机号码格式不正确',
            'token.required'  => '令牌不能为空',
        );
        $validator = Validator::make($input, $rules, $messages);
        if ( $validator->fails() ) {
            apperror( $validator->errors()->first());
        }
        $code = rand(1000,9999);
        $contactPhone = '商家客服';
        $datas = [$code,1,$contactPhone];
        $result = $verifyCodeService->sendCode($input['mobile'],$datas,2);
        if($result->statusCode!=0) {
            apperror((string)$result->statusMsg);
        }else{
            $tokenData = $baseModule->getTokenData($input['token']);
            $code = array_merge([$code],$tokenData['code'][$input['mobile']]??[]);
            $result = $baseModule->setDataInToken($input['token'],['code'=>[$input['mobile']=>$code]]);
            if ($result['errCode'] == 0){
                appsuccess('验证码发送成功');
            }else{
                apperror('验证码发送失败');
            }

        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180321
     * @desc
     */
    public function logout(Request $request,BaseModule $baseModule)
    {
        $tokenData = $request->input('_tokenData');
        $baseModule->setDataInToken($tokenData['token'],['is_login'=>0]);
        appsuccess('退出成功');
    }

    /**
     * @description：注销账号
     * @param Request $request 请求类
     * @param BaseModule $baseModule 基础类
     *
     * @return AuthController|mixed|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月27日 13:39:15
     */
    public function setAccountLogOff(Request $request, BaseModule $baseModule)
    {
        $token = $request->input('token') ?? '';
        if (empty($token)) {
            error('令牌不能为空');
        }
        // 检测参数token是否正确
        $tokenData = $baseModule->getTokenData($token);
        if (!$tokenData || !$tokenData['is_login']) {
            error('未登录或登录超时');
        }
        // 执行注销
        $result = $baseModule->accountCancel($tokenData['userInfo']['id']);
        if ($result) {
            // 退出登录
            $baseModule->setDataInToken($token, ['is_login' => 0]);
            success('注销成功');
        }
        error('注销失败');
    }

}
