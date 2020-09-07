<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/1/18
 * Time: 14:12
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\S\WXXCX\WXXCXCollectFormIdService;
use Illuminate\Http\Request;
use CommonModule;

class CollectFormController extends Controller
{

    /**
     * formId 收集
     *
     * @param Request $request
     * @param WXXCXCollectFormIdService $formIdService
     *
     * @return array
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月24日 16:32:36
     */
    public function index(Request $request, WXXCXCollectFormIdService $formIdService)
    {
        $returnData = ['errCode' => 0, 'msg' => 'success', 'data' => []];
        $formData = $request->input(['formIds']);
        $returnData['data'] = $formData;
        if (!$formIdService->save($request->input(['mid']), $formData)) {
            $returnData['code'] = -2;
            $returnData['msg'] = '收集失败';
        }
        return $returnData;
    }


}