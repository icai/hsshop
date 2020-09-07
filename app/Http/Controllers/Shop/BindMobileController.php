<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/9/25
 * Time: 11:58
 */

namespace App\Http\Controllers\shop;


use App\Lib\Redis\SMSKeys;
use App\Module\BindMobileModule;
use App\S\Foundation\VerifyCodeService;
use App\S\Member\MemberService;
use App\S\Member\UnifiedMemberService;
use App\S\Wechat\WeixinSmsConfService;
use App\Services\Lib\RestService;
use App\Services\ReserveService;
use Illuminate\Http\Request;
use Validator;
use App\Model\WeixinAddress;

class BindMobileController
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170925
     * @desc 绑定手机验证码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $url = $request->input('url');
        if ($request->isMethod('post')){
            $input = $request->input();
            $rule = Array(
                'phone'               => 'required',
                'code'                => 'required',
            );
            $message = Array(
                'phone.required'     => '手机号码不能为空',
                'code.required'      => '验证码不能为空',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $phone = $request->input('phone');
            $codeSms = (new SMSKeys('bindmobile'.session('mid').'phone'.$phone))->get();
            $codeSms = json_decode($codeSms,true)??[];
            $code = $request->input('code');
            if (!in_array($code,$codeSms)){
                error('验证码错误');
            }
            //判断该电话号码是否可以绑定
            $umService = new UnifiedMemberService();
            $umData = $umService->getList(['mobile'=>$phone]);
            if ($umData){
                error('该号码已被其他用户绑定');
            }
            //处理绑定手机号码
            $res = (new BindMobileModule())->bindMobile(session('umid'),$phone,session('wid'),session('mid'));
            if ($res){
                success('绑定成功');
            }else{
                error('绑定失败，请重新操作');
            }
        }
        //判断是修改手机号码还是绑定
        $umData = (new UnifiedMemberService())->getRowById(session('umid'));
        if (empty($umData['mobile'])){
            return view('shop.bindmobile.index',array(
                'title'=>'绑定号码',
                'url'  => $url,
            ));
        }else{
            return view('shop.bindmobile.change',array(
                'title'=>'更改手机号码',
                'url'  => $url,
                'mobile'=> $umData['mobile'],
            ));
        }


    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170925
     * @desc
     */
    public  function  sendCode(Request $request,VerifyCodeService $verifyCodeService)
    {
        $phone = $request->input('phone');
        $sms_code = 2;
        if(!$phone || !$sms_code){
            error('手机号码或短信码不能为空');
        }
        $bindMobileModule = new BindMobileModule();
        if (!$bindMobileModule->isAccessSendCode(session('mid'))){
            error('您发送验证码已达到上线');
        }
        if ($request->input('flag','')){
            $umService = new UnifiedMemberService();
            $umData = $umService->getList(['mobile'=>$phone]);
            if ($umData){
                error('该号码已被其他用户绑定');
            }
        }

        //生成验证码 随机生成4位
        $code = rand(1000,9999);
        //获取店铺退货电话号码
        $contactPhone = '商家客服';
        $datas = [$code,1,$contactPhone];
        $result = $verifyCodeService->sendCode($phone,$datas,$sms_code);
        if($result->statusCode!=0) {
            error((string)$result->statusMsg);
        }else{
            $smsKeys = new SMSKeys('bindmobile'.session('mid').'phone'.$phone);
            $codeSms = $smsKeys->get();
            $codeSms = json_decode($codeSms,true)??[];
            array_push($codeSms,$code);
            $smsKeys->set(json_encode($codeSms));
            success('验证码发送成功');
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170929
     * @desc 换绑手机号吗
     * @param Request $request
     */
    public function changeMobile(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'mobile'               => 'required',
            'code1'                => 'required',
            'code2'                => 'required',
        );
        $message = Array(
            'mobile.required'     => '手机号码不能为空',
            'code1.required'     => '验证码不能为空',
            'code2.required'     => '验证码不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $mid = session('mid');
        $code2Sms = (new SMSKeys('bindmobile'.$mid.'phone'.$input['mobile']))->get();
        $code2Sms = json_decode($code2Sms,true)??[];
        $memberData = (new UnifiedMemberService())->getRowById(session('umid'));
        $code1Sms = (new SMSKeys('bindmobile'.$mid.'phone'.$memberData['mobile']))->get();
        $code1Sms = json_decode($code1Sms,true)??[];
        if (!in_array($input['code1'],$code1Sms) || !in_array($input['code2'],$code2Sms)){
            error('验证码错误');
        }

        $res = (new BindMobileModule())->changeMobile(session('mid'),$input['mobile']);
        if ($res){
            success();
        }else{
            error();
        }

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 当前用户是否需要绑定手机号码
     */
    public function isBind()
    {
        return 1;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 绑定手机号码
     * @param Request $request
     */
    public function bindMobile(Request $request)
    {
        $input = $request->input();
        $rule = Array(
            'phone'               => 'required',
            'code'                => 'required',
        );
        $message = Array(
            'phone.required'     => '手机号码不能为空',
            'code1.required'     => '验证码不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $phone = $request->input('phone');
        $code = $request->input('code');
        $codeSms = (new SMSKeys('bindmobile'.session('mid').'phone'.$phone))->get();
        $codeSms = json_decode($codeSms,true)??[];
        if (!in_array($code,$codeSms)){
            error('验证码错误');
        }
        //判断该电话号码是否可以绑定
        $umService = new UnifiedMemberService();
        $umData = $umService->getList(['mobile'=>$phone]);
        if ($umData){
            error('该手机号码已经绑定过了');
        }
        //处理绑定手机号码
        $res = (new BindMobileModule())->bindMobile(session('umid'),$phone,session('wid'),session('mid'));
        if ($res){
            success('绑定成功');
        }else{
            error();
        }
    }





}