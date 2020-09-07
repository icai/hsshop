<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\S\Applet\AppletService;

class ShareController extends Controller{


    /**
     * 分享统计列表
     * @param AppletService $appletService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function shareIncome(AppletService $appletService)
	{
		$list = $appletService->getDataCountList();
		return view('staff.share.shareIncome',[
			'title'    => '分享统计',
			'sliderba' => 'shareIncome',
			'list'     => $list
		]);
	}

    /**
     * 根据手机号获取分享报名的人数
     * @param Request $request
     * @param AppletService $appletService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function showSignerList(Request $request,AppletService $appletService){

		$phone = $request->input('phone');
		if(empty($phone)){
			error('数据异常');
		}
		$list = $appletService->getSignerListByPhone($phone);
		return view('staff.share.showSignerList',[
			'title' => '分享统计',
			'sliderba' => 'shareIncome',
			'list'  => $list
		]);
	}
}
