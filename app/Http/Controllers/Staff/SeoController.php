<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\S\Staff\SeoService;

class SeoController extends Controller{

    /**
     * seo列表
     * @param SeoService $seoService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index(SeoService $seoService)
	{
		list($list,$page) = $seoService->getAllList();
		return view('staff.seo.index',[
			'title' => 'SEO管理',
			'list'  => $list,
			'page'  => $page
		]);
	}

    /**
     * seo保存
     * @param Request $request
     * @param SeoService $seoService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function save(Request $request,SeoService $seoService)
	{
		$input = $request->input();
		$id = $input['id'] ?? '';
		if($request->isMethod('post')){
			if(empty($input)){
				error('数据异常');
			}
			$rule = [
				'keywords' => 'required',
				'title'	   => 'required',
				'descript' => 'required',
				'page_url' => 'required' 	
			];

			$message = [
				'keywords.required' => '请填写关键词',
				'title.required'    => '请填写标题',
				'descript.required' => '请填写描述',
				'page_url.required' => '请填写关联链接'
			];

			$validator = Validator::make($input,$rule,$message);
			if ($validator->fails()){
				error($validator->errors()->first());
			}
			if($id){
				$rs = $seoService->update($id,$input);
			}else{
				$rs = $seoService->add($input);
			}
			//成功处理
			if($rs){
				success();
			}
			error();
		}
		$data = [];
		if($id){
			$data = $seoService->getRowById($id);	
		}
		return view('staff.seo.save',[
			'title'    => $id ? '编辑SEO' : '新增SEO',
			'data' => $data
		]);
	}

    /**
     * 删除
     * @param Request $request
     * @param SeoService $seoService
     */
	public function seoDel(Request $request,SeoService $seoService)
	{
		$id = $request->input('id') ?? 0;
		if(!$id){
			error('数据异常');
		}
		if($seoService->del($id)){
			success();
		}

		error();
	}
	


}
