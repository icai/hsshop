<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/10/27
 * Time: 8:57
 */

namespace App\Http\Controllers\Merchants;


use App\Http\Controllers\Controller;
use App\S\Wechat\WxKfService;
use App\Services\Wechat\CustomService;
use Illuminate\Http\Request;
use Validator;


class WeChatCustomServiceController extends Controller
{

    private $wid;
    private $request;
    private $apiService;


    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->leftNav  = 'currency';
        $this->middleware(function ($request, $next) {
            $this->wid = session('wid');
            $this->apiService = new CustomService($this->wid);
            return $next($request);
        });
    }


    public function getList()
    {
        $data  = $this->apiService->getListCustomService();
        if (!isset($data['errcode'])) {
            $kfService = new WxKfService();
            foreach ($data['kf_list'] as &$v){
                $url = $kfService->getRowByWhere(['kf_account'=>$v['kf_account']]);
                if($url){
                    $v['kf_headimgurl']  = config('app.url').$url;
                }
            }
            success('','',$data['kf_list']);
        }
        error('','',$data);

    }

    public function addCustom()
    {
        if($this->request->isMethod('post')){
            $input = $this->request->only(['kf_account', 'kf_nick']);
            $rules = [
                'kf_account'     => 'required|max:10|regex:/^\w+$/',
                'kf_nick'        => 'required|max:16',
            ];
            $messages = [
                'kf_account.required' => '请填写客服账户',
                'kf_nick.required'    => '请填写客服昵称',
                'kf_account.regex'    => '客服账户必须是英文、数字字符或者下划线组成',
                'kf_account.max' => '客服账户长度不能超过10字符',
                'kf_nick.max'    => '客服昵称长度不能超过16字符',
            ];
            $validator = Validator::make($input, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            $re = $this->apiService->addCustomService($input);
            if ($re['errcode'] == 0) {
                success();
            }
            error('','',$re);
        }
    }


    public function inviteCustom()
    {
        if($this->request->isMethod('post')){
            $input = $this->request->only(['kf_account', 'invite_wx']);
            $rules = [
                'kf_account'     => 'required',
                'invite_wx'        => 'required|regex:/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}$/',
            ];
            $messages = [
                'kf_account.required' => '请填写客服账户',
                'invite_wx.required'    => '请填写邀请者微信号',
                'invite_wx.regex'       => '微信账号仅支持6-20个字母、数字、下划线或减号，以字母开头'
            ];
            $validator = Validator::make($input, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            $re = $this->apiService->inviteCustomService($input);
            if ($re['errcode'] == 0) {
                success();
            }
            error('','',$re);
        }
        error('非法请求');
    }

    public function updateCustom()
    {
        if($this->request->isMethod('post')){
            $data = $input = $this->request->only(['kf_account', 'kf_nick']);
            $rules = [
                'kf_account'     => 'required|max:10|regex:/^\w+$/',
                'kf_nick'        => 'required|max:16',
            ];
            $messages = [
                'kf_account.required' => '请填写客服账户',
                'kf_account.regex'    => '客服账户必须是英文、数字字符或者下划线组成',
                'kf_nick.required'    => '请填写客服昵称',
                'kf_account.max' => '客服账户长度不能超过10字符',
                'kf_nick.max'    => '客服昵称长度不能超过16字符',
            ];
            $data['kf_account'] = explode('@',$data['kf_account'])[0];
            $validator = Validator::make($data, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            $re = $this->apiService->updateCustomService($input);
            if ($re['errcode'] == 0) {
                success();
            }
            error('','',$re);
        }
    }

    public function deleteCustom()
    {
        if($this->request->isMethod('post')){
            $input = $this->request->only(['kf_account']);
            if(!$input){
                error('请选择客服');
            }
            $re = $this->apiService->deleteCustomService($input);
            $kfService = new WxKfService();
            if ($re['errcode'] == 0) {
                $kfService->delete($input);
                success();
            }
            error('','',$re);
        }
        error('非法请求');
    }


    public function uploadHeadImg()
    {
        if($this->request->isMethod('post')){
            $input = $this->request->only(['kf_account','fileName']);
            if(!$input['kf_account']){
                error('请选择客服');
            }
            if(!$input['fileName']){
                error('请选择头像');
            }
            $re = $this->apiService->uploadHeadImg($input);
            $kfService = new WxKfService();
            if (isset($re['errcode']) && $re['errcode'] == 0 && $kfService->save($this->wid,$input)) {
                success();
            }
            error('','',$re);
        }
    }














}