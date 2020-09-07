<?php

namespace App\Http\Controllers\WXXCX;

use App\Http\Controllers\Controller;
use App\Module\LiShareEventModule;
use App\S\ShareEvent\LiRegisterService;
use Illuminate\Http\Request;
use Validator;

class FreeXCXController extends Controller
{
    /**
     * 申请报名
     * @param Request $request
     * @return mixed
     */
    public function apply(Request $request)
    {
        //参数
        $input = $request->input();
        $mid = $input['mid'];
        $wid = $input['wid'];
        $li_register_service = new LiRegisterService();

        if ($request->isMethod('post')) {
            //提交报名信息
            if ($li_register_service->isAppliedByXCX($mid, $wid)) {
                xcxsuccess('领取成功');
            } else {
                //验证参数
                $rule = Array(
                    'name'         => 'required|between:1,10',
                    'phone'        => 'required|regex:mobile',
                    'company_name' => 'required|between:1,26',
                    'company_position' => 'between:1,20',
                );
                $message = Array(
                    'name.required'    => '请输入姓名',
                    'name.between'     => '姓名长度为1-10个字符',
                    'phone.required'    => '请输入手机号码',
                    'phone.regex'       => '手机号码格式不正确',
                    'company_name.required'    => '请输入公司名称',
                    'company_position.required' => '请输入公司地址',
                    'company_name.between' => '公司名称长度为1-26个字符',
                    'company_position.between' => '职务长度为1-20个字符',
                );
                $validator = Validator::make($input,$rule,$message);
                if ($validator->fails()){
                    xcxerror($validator->errors()->first());
                }
                $data = [
                    'wid' => $wid,
                    'type' => 2,
                    'mid' => $mid,
                    'name' => $input['name'],
                    'phone' => $input['phone'],
                    'company_name' => $input['company_name'],
                    'company_position' => $input['company_position']
                ];
                if (!$li_register_service->model->insertGetId($data)) {
                    xcxerror('领取失败');
                } else {
                    (new LiShareEventModule())->registerSuccess($mid);
                    xcxsuccess('领取成功');
                }
            }
        }

        xcxsuccess('', ['is_applied' => $li_register_service->isAppliedByXCX($mid, $wid)]);
    }

    /**
     * 报名成功页
     */
    public function applySuccess(Request $request)
    {
        //参数
        $input = $request->input();
        $mid = $input['mid'];
        $wid = $input['wid'];

        xcxsuccess('', ['is_applied' => (new LiRegisterService())->isAppliedByXCX($mid, $wid)]);
    }
}
