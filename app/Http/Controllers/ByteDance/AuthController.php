<?php

namespace App\Http\Controllers\ByteDance;

use App\Http\Controllers\Controller;
use App\Module\ByteDance\BaseModule;
use App\S\Weixin\ShopService;
use Illuminate\Http\Request;
use Validator;

/**
 * Created by zhangyh.
 * User: 张永辉 [zhangyh_private@foxmail.com]
 * Date: 2019/9/20 13:32
 * @desc 登陆相关控制器
 */
class AuthController extends Controller
{


    /**
     * @desc 登陆接口
     * @param Request $request
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 20 日
     * @link http://192.168.0.239:10000/html/web/share/share.html#5d881b8bf2d4b50826106970
     */
    public function login(Request $request, BaseModule $baseModule, ShopService $shopService)
    {
        $input = $request->input();
        $rule = [
            'wid'  => 'required|integer',
            'code' => 'required',
        ];

        $message = [
            'wid.required'  => '店铺id不能为空',
            'code.required' => '登陆code不能为空',
        ];
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }
        $result = $baseModule->login($input);
        $result['shopId'] = $result['wid'];
        $shopData = $shopService->getRowById($result['wid']);
        $result['is_logo_show'] = isset($shopData['is_logo_show']) ? $shopData['is_logo_show'] : 1;
        $result['is_logo_open'] = isset($shopData['is_logo_open']) ? $shopData['is_logo_open'] : 0;
        $result['logo_type'] = isset($shopData['logo_type']) ? $shopData['logo_type'] : 0;
        $result['logo_path'] = !empty($shopData['logo_path']) ? imgUrl() . $shopData['logo_path'] : config('app.url') . '/static/images/footer_new_logo11.png';
        $result['is_official_account'] = 1;
        xcxsuccess($result);
    }

}