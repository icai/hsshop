<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\S\Staff\AccountService;
use Captcha;
use Illuminate\Http\Request;
use RedisString;
use StaffOperLogService;
use Validator;

/**
 * 总后台登陆
 * 
 * @author
 * @version
 */
class LoginController extends Controller {
    /**
     * 登陆
     * @author zhangyh
     * @date 20170313
     * @desc 总后台用户登陆验证
     * @return [type] [description]
     */
    public function index(Request $request,AccountService $accountService) {
        if (session('userData')){
            return redirect('staff/index');
        }
        if ($request->isMethod('post')){
            //验证登陆
            $rule = Array(
                'loginName'          => 'required',
                'loginPasswd'        => 'required',
            );
            $message = Array(
                'loginName.required'         => '登陆名不能为空',
                'loginPasswd.required'       => '密码不能为空',
                'code.required'               => '验证码不能为空'
            );
            $validator = Validator::make($request->all(),$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $res = $accountService->checkUser($request->get('loginName'),$request->get('loginPasswd'));
            if ($res['success'] == 1){
                success('',url('staff/index'));
            }else{
                error($res['message']);
            }



        }else{
             //登陆页面
            return view('staff.login.index',array());

        }
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703131535
     * @desc 添加总后台登陆账户
     * @param Request $request
     * @param AccountService $accountService
     */
    public function addUser(Request $request,AccountService $accountService)
    {
    	if ($request->isMethod('post')){
			//验证登陆
			$rule = Array(
				'loginName'          => 'required',
				'loginPasswd'        => 'required|confirmed|between:8,18',
				'name'                => 'required',
				'status'              => 'required|in:0,1',
			);
			$message = Array(
				'loginName.required'         => '登陆名不能为空',
				'loginPasswd.required'       => '密码不能为空',
				'name.required'               => '名称不能为空',
				'loginPasswd.confirmed'      => '两次输入密码不相同',
				'loginPasswd.between'        => '请输入6-18位字符长度的密码',
				'status.required'             => '状态不能为空',
				'status.in'                    => '状态必须是0，1'
			);
			$validator = Validator::make($request->all(),$rule,$message);
			if ($validator->fails()){
				error($validator->errors()->first());
			}
			$res = $accountService->validPasswd($request->input('loginPasswd'));
			if ($res['errCode'] != 0){
			    error($res['errMsg']);
            }
			$res = $accountService->addUser();
			if ($res['success'] == 1){
				success();
			}else{
				error($res['message']);
			}
		}

		$accountData = [];
    	if ($request->input('id')){
			$accountData = $accountService->getRowById($request->input('id'));
		}
		return view('staff.permission.addUser',array(
			'title'     => '权限管理',
			'sliderba' => 'account',
			'account'	=> $accountData,
		));


    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703131629
     * @desc 修改用户名和密码
     * @param Request $request
     * @param AccountService $accountService
     */
    public function  modifyUser(Request $request,AccountService $accountService)
    {
        $rule = Array(
            'id'                  => 'required',
            'loginPasswd'        => 'required|confirmed|between:8,18',
            'name'                => 'required',
        );
        $message = Array(
            'id.required'                 => '修改用户的ID不能为空',
            'loginPasswd.required'       => '密码不能为空',
            'name.required'               => '名称不能为空',
            'loginPasswd.confirmed'      => '两次输入密码不相同',
            'loginPasswd.between'        => '请输入6-18位字符长度的密码',
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $res = $accountService->validPasswd($request->input('loginPasswd'));
        if ($res['errCode'] != 0){
            error($res['errMsg']);
        }
        if ($request->input('id') != $request->session()-get('userData')['id']){
            error('该账号不属于你！你无权修改');
        }
        $userData = Array(
            'login_password'        => bcrypt($request->input('loginPasswd')),
            'name'                   => $request->input('name'),
        );
        StaffOperLogService::write('修改账号,'.json_encode($userData));
        $res = $accountService->update($request->input('id'),$userData);
        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }

    }

	/**
	 * @auth zhangyh
	 * @Email zhangyh_private@foxmail.com
	 * @date 201704071005
	 * @desc 删除用户
	 * @param Request $request
	 */
    public function delAccount(Request $request,AccountService $accountService)
	{
		$rule = Array(
			'id'                  => 'required',
		);
		$message = Array(
			'id.required'                 => 'ID不能为空',

		);
		$validator = Validator::make($request->all(),$rule,$message);
		if ($validator->fails()){
			error($validator->errors()->first());
		}
        StaffOperLogService::write('删除账号,id='.$request->input('id'));
		$res = $accountService->del($request->input('id'));
        if ($res){
            return mysuccess();
        }else{
            return myerror();
        }

	}


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date201703161934
     * @desc 退出登陆
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/staff/login');
    }



}