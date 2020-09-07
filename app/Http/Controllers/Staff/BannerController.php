<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\S\Staff\SellerappAdService;
use Illuminate\Http\Request;
use Validator;
use App\S\Staff\BannerService;
use App\S\Staff\AdService;

class BannerController extends Controller{


	//banner图列表
	public function index(BannerService $bannerService)
	{
		list($list,$page) = $bannerService->getAllList();
		return view('staff.banner.index',[
			'title'     => 'Banner管理',
			'list'      => $list,
			'page'      => $page,
			'sliderbar' => 'index',
		]);
	}

	/**
	 * 新建，编辑banner页面
	 * 同时也是提交接口页面
	 * @param  Request       $request       [description]
	 * @param  BannerService $bannerService [description]
	 * @return [type]                       [description]
	 */
	public function save(Request $request,BannerService $bannerService)
	{

		$input = $request->input();
		$id = $input['id'] ?? '';
		if($request->isMethod('post')){
			if($id){  //编辑
				$msg = '编辑Banner成功';
				$rs = $bannerService->update($id,$input);
			}else{
				$msg = '新建Banner成功';
				$rs = $bannerService->add($input);
			}

			//处理结果
			if($rs){
				success($msg);
			}

			error();
		}
		$data = [];
		if($id){
			$data = $bannerService->getRowById($id);
		}

		$posArr = ['全站','会搜云首页','APP定制','微信小程序','微信商城','分销系统','微营销总裁班','案例展示','帮助中心','会搜云资讯','关于我们'];
		
		return view('staff.banner.save',[
			'title' => 'Banner管理',
			'data'	=> $data,
			'posArr'    => $posArr,
			'sliderbar' => 'index'
		]);
	}

	/**
	 * 状态更改 多条更新与单条更新
	 * @param  Request       $request       [description]
	 * @param  BannerService $bannerService [description]
	 * @return [type]                       [description]
	 */
	public function statusSave(Request $request,BannerService $bannerService)
	{
		$input = $request->input();
		if(empty($input)){
			error('数据异常');
		}
		$id = $input['id'];
		$state = true;
		switch ($input['type']) {
			case 'del':
				if(is_array($input['id'])){
					foreach($input['id'] as $id){
						if(!$bannerService->del($id)){
							$state = false;
						}
					}
				}else{
					if(!$bannerService->del($id)){
						$state = false;
					}
				}
				break;
			case 'enable':
				$data['status'] = 1;
				if(is_array($input['id'])){
					foreach($input['id'] as $id){
						if(!$bannerService->update($id,$data)){
							$state = false;
						}
					}
				}else{
					if(!$bannerService->update($id,$data)){
						$state = false;
					}
				}
				break;
			case 'disable':
				$data['status'] = 0;
				if(is_array($input['id'])){
					foreach($input['id'] as $id){
						if(!$bannerService->update($id,$data)){
							$state = false;
						}
					}
				}else{
					if(!$bannerService->update($id,$data)){
						$state = false;
					}
				}
				break;
			default:
				# code...
				break;
		}

		if(!$state){
			error();
		}
		success();

	}

		
	public function ad(AdService $adService)
	{
		list($list,$page) = $adService->getAllList();
		return view('staff.banner.ad',[
			'title'     => 'Banner管理',
			'sliderbar' => 'ad',
			'list'		=> $list,
			'page'		=> $page
		]);
	}

	public function adSave(Request $request,AdService $adService)
	{
		$input = $request->input();
		$id = $input['id'] ?? '';
		if($request->isMethod('post')){
			if(!stripos('http',$input['url']) && stripos('http',$input['url']) != 0){
				$input['url'] = 'http://'.$input['url'];
			}
			if($id){  //编辑
				$msg = '编辑广告成功';
				$rs = $adService->update($id,$input);
			}else{
				$msg = '新建广告成功';
				$rs = $adService->add($input);
			}

			//处理结果
			if($rs){
				success($msg);
			}

			error();
		}
		$data = [];
		if($id){
			$data = $adService->getRowById($id);
		}
		$posArr = ['全站','会搜云首页','APP定制','微信小程序','微信商城','分销系统','微营销总裁班','案例展示','帮助中心','会搜云资讯','关于我们'];
		return view('staff.banner.adSave',[
			'title'     => 'Banner管理',
			'sliderbar' => 'ad',
			'data'		=> $data,
			'posArr'	=> $posArr
		]);
	}

	//删除广告
	public function adDel(Request $request,AdService $adService)
	{
		$id = $request->input('id') ?? 0;
		if(!$id){
			error('数据异常');
		}
		if($adService->del($id)){
			success();
		}

		error();
	}


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201890502
     * @desc 获取APP团购列表
     * @param Request $request
     */
	public function sellerappad(Request $request,SellerappAdService $sellerappAdService)
    {
        $data = $sellerappAdService->getlistPage();
        return view('staff.banner.sellerappad',[
            'title'     => 'Banner管理',
            'sliderbar' => 'ad',
            'data' => $data,
        ]);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201890502
     * @desc 获取APP团购列表
     * @param Request $request
     */
    public function sellerappadAdd(Request $request,SellerappAdService $sellerappAdService)
    {
        $input = $request->input();
        $id = $input['id'] ?? '';
        if($request->isMethod('post')){
            $data = [
                'title'=> $input['title'],
                'url' => $input['url'],
                'img' => $input['img'],
                'sec' => $input['sec'],
            ];
            if (!$id){
                $res = $sellerappAdService->add($data);
            }else{
                $res = $sellerappAdService->update($input['id'],$data);
            }
            if ($res){
                success();
            }else{
                error();
            }
        }
        $data = [];
        if($id){
            $data = $sellerappAdService->getRowById($id);
        }
        return view('staff.banner.sellerappadAdd',[
            'title'     => 'Banner管理',
            'sliderbar' => 'ad',
            'data'		=> $data,
        ]);
    }

    public function selleradDel(Request $request,SellerappAdService $sellerappAdService)
    {
        $id = $request->input('id');
        if (is_array($id)){
            foreach ($id as $value){
                $sellerappAdService->del($value);
            }
            success();
        }else{
            $sellerappAdService->del($id)?success():error();
        }
    }

    public function selleradOpen(Request $request,SellerappAdService $sellerappAdService)
    {
        $id = $request->input('id');
        $sellerappAdService->update($id,['is_open'=>$request->input(['status'])])?success():error();
    }

}
