<?php
/**
 * Created by zhangyh.
 * User: 张永辉 [zhangyh_private@foxmail.com]
 * Date: 2019/9/26 14:48
 */

namespace App\Http\Controllers\ByteDance;

use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Module\ByteDance\PayModule;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Validator;

/**
 * Class PayController
 * @package App\Http\Controllers\ByteDance
 * @todo 字节跳动小程序支付接口
 * @date 2019年9月26日14:49:10
 * @author 张永辉
 */
class PayController extends Controller
{

    /**
     * @desc获取订单支付信息
     * @param Request $request
     * @param PayModule $payModule
     * @throws \Exception
     * @author 张永辉 [zhangyh_private@foxmail.com] at 2019 年 09 月 29 日
     * @line http://192.168.0.239:10000/html/web/share/share.html#5d9050a0f2d4b508261069d1
     */
    public function getPay(Request $request, PayModule $payModule)
    {
        $input = $request->input();
        $rule = [
            'id' => 'required|integer',
        ];
        $message = [
            'id.required' => '订单id不能为空',
            'id.integer'  => '订单id必须是整数',
        ];
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            xcxerror($validator->errors()->first());
        }
        $res = $payModule->setInput($input)->getPay();
        xcxsuccess('操作成功', $res);
    }
}