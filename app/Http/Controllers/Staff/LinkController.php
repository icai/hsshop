<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\S\Staff\LinkService;

class LinkController extends Controller{


    /**
     * 友情链接列表
     * @param LinkService $linkService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function index(LinkService $linkService)
	{

		list($list,$page) = $linkService->getAllList();
		return view('staff.link.index',[
			'title' => '友情链接',
			'list'  => $list,
			'page'  => $page
			
		]);
	}

    /**
     * 友情链接新增
     * @param Request $Request
     * @param LinkService $linkService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function save(Request $Request,LinkService $linkService)
	{ 
		$input = $Request->input();
		$id = $input['id'] ?? '';
		if($Request->isMethod('post')){
			if(empty($input)){
				error('数据异常');
			}

			$rule = [
				'name' => 'required',
				'url'  => 'required'
			];

			$message = [
				'name.required' => '请填写链接标题',
				'url.required'  => '请填写链接地址'
			];

			$validator = Validator::make($input,$rule,$message);
			if ($validator->fails()){
				error($validator->errors()->first());
			}
			
			if(!stripos('http',$input['url']) && stripos('http',$input['url']) != 0){
				$input['url'] = 'http://'.$input['url'];
			}
			if($id){
				$rs = $linkService->update($id,$input);
			}else{
				$rs = $linkService->add($input);
			}

			if($rs){
				success();
			}
			error();
		}

		$data = [];
		if($id){
			$data = $linkService->getRowById($id);
		}

		return view('staff.link.save',[
			'title' => $id ? '修改友情' : '添加友情',
			'data'  => $data
		]);
	}

    /**
     * 删除友链
     * @param Request $request
     * @param LinkService $linkService
     */
	public function linkDel(Request $request,LinkService $linkService)
	{
		$id = $request->input('id') ?? 0;
		if(!$id){
			error('数据异常');
		}
		if($linkService->del($id)){
			success();
		}

		error();
	}

    /**
     * 网站地图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function mapIndex()
	{ 
		return view('staff.link.mapIndex',[
			'title'    => '友情链接',
		]);
	}


}
