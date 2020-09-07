<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\S\Staff\CaseService;
use App\S\Staff\IndustryService;
use App\S\Staff\CaseCommentService;
use QrCode;

class ExampleController extends Controller{


	//案例列表
	public function index(Request $request,CaseService $caseService)
	{
		$input = $request->input() ?? [];
		list($list,$page) = $caseService->getAllList($input,'sort');

		/*获取行业的值**/
		$industryService = new IndustryService();
		foreach($list['data'] as &$val){
			$industryList = $industryService->getAllList(false,explode(',',$val['industry_ids']));
			foreach($industryList as $item){
				$val['industrys'] = $item['name'].',';
			}
		}
		return view('staff.example.index',[
			'title' => '案例管理',
			'sliderbar' => 'index',
			'list'  => $list,
			'page'  => $page
		]);
	}

	/**
	 * [save description]
	 * @param  Request     $request     [description]
	 * @param  CaseService $caseService [description]
	 * @return [type]                   [description]
	 * @update 吴晓平  2019年05月22日 添加案例时对行业类型进行标识用于区分会搜云新零售系统与其他分类
	 */
	public function save(Request $request,CaseService $caseService)
	{		
		$input = $request->input();
		$id = $input['id'] ?? 0; 
		if($request->isMethod('post')){

			if(empty($input)){
				error('数据异常');
			}
			$rule = [
				'title'    => 'required',
				'subtitle' => 'required',
				'auth'     => 'required',
				'keywords' => 'required',
				'meta'     => 'required',
				'logo'     => 'required',
				'show_img' => 'required',
			];
			$message = [
				'title.required'    => '请填写案例标题',
				'subtitle.required' => '请填写案例类型',
				'auth.required'     => '请填写作者',
				'keywords.required' => '请填写行业分类',
				'meta.required'     => '请填写产品介绍',
				'logo.required'     => '请上传logo图',
				'show_img.required' => '请上传商品展示图',
			];
			$validator = Validator::make($input,$rule,$message);
			if ($validator->fails()){
				error($validator->errors()->first());
			}
			$industryIds = $input['keywords'];
			$sign = $input['subtitle'] == '会搜云新零售系统' ? 1 : 0;
			$saveData                 = [];
			$saveData['name']         = $input['title'];
			$saveData['type']         = $input['subtitle'];
			$saveData['author']       = $input['auth'];
			$saveData['industry_ids'] = join(',' , $industryIds);
			$saveData['intruduce']    = $input['meta'];
			$saveData['logo']         = $input['logo'];
			$saveData['show_img']     = $input['show_img'];
			$saveData['desc']         = $input['content'];
			$saveData['sort']         = $input['sort'];
            $saveData['code']         = $input['code'] ?? '';  //add fuguowei
            $case = [];
			if($id){
				$case = $caseService->getRowById($id);
				$rs = $caseService->update($id,$saveData);
			}else{
				$rs = $caseService->add($saveData);
			}
			//处理成功
			if($rs){
				// @update 吴晓平  2019年05月22日
				$industryService = new IndustryService();
				if ($input['subtitle'] == '会搜云新零售系统') {
					if ($case) {
						$industryService->updateHaveMany(explode(',', $case['industry_ids']), ['sign' => 0]);
						$industryService->updateHaveMany($industryIds,['sign' => $sign]);
					}else {
						$industryService->updateHaveMany($industryIds,['sign' => $sign]);
					}
				}
				success();
			}
			error();
		}
		$data = $industryArr = $imgArr = [];
		if($id){
			$data = $caseService->getRowById($id);
			if($data['show_img']){
				$imgArr = explode(';',$data['show_img']);
			}
			$industryArr = explode(',',$data['industry_ids']);
		}


		
		$industryService = new IndustryService();
		$industryList = $industryService->getAllList(false);

		foreach($industryList as &$val){
			if(in_array($val['id'],$industryArr)){
				$val['check'] = 'checked=checked';
			}else{
				$val['check'] = '';
			}
		}
		// 在总后台添加案例时，增加了会搜云新零售系统分类
		// update by 吴晓平 2019年5月17日
		$caseArr = ['APP定制', '微信商城', '分销系统', '微信小程序', '微营销总裁班', '会搜云新零售系统'];
		return view('staff.example.save',[
			'title'        => '案例管理',
			'sliderbar'    => 'index',
			'data'         => $data,
			'caseArr'      => $caseArr,
			'industryList' => $industryList, 
			'imgArr'	   => $imgArr
		]);
	}

	//案例删除
	public function caseDel(Request $request,CaseService $caseService)
	{
		$id = $request->input('id') ?? 0;
		if(!$id){
			error('数据异常');
		}
		if($caseService->del($id)){
			success();
		}

		error();
	}

	/**
	 * 行业分类 
	 * @author wuxiaoping <2017.08.22>
	 * @return [type] [description]
	 */
	public function industry(IndustryService $industryService)
	{
		list($list,$page) = $industryService->getAllList();
		return view('staff.example.industry',[
			'title'     => '案例管理',
			'sliderbar' => 'industry',
			'list' 		=> $list,
			'page'		=> $page

		]);
	}

	/**
	 * 行业分类新建、编辑处理
	 * @author wuxiaoping <2017.08.22>
	 * @return [type] [description]
	 */
	public function industrySave(Request $request,IndustryService $industryService)
	{
		$input = $request->input();
		$id = $input['id'] ?? 0; 
		if($request->isMethod('post')){

			if(empty($input)){
				error('数据异常');
			}
			$rule = [
				'name'    => 'required'	
			];
			$message = [
				'name.required'    => '请填写行业名称'
			];
			$validator = Validator::make($input,$rule,$message);
			if ($validator->fails()){
				error($validator->errors()->first());
			}
			if($id){
				$rs = $industryService->update($id,$input);
			}else{
				$rs = $industryService->add($input);
			}
			//处理成功
			if($rs){
				success();
			}
			error();
		}
		$data = [];
		if($id){
			$data = $industryService->getRowById($id);
		}
		return view('staff.example.industrySave',[
			'title'     => $id ? '编辑行业分类' : '添加行业分类',
			'sliderbar' => 'industry',
			'data'      => $data
		]);
	}

	/**
	 * 行业分类删除
	 * @param  Request         $request         [description]
	 * @param  IndustryService $industryService [description]
	 * @return [type]                           [description]
	 */
	public function industryDel(Request $request,IndustryService $industryService)
	{
		$id = $request->input('id') ?? 0;
		if(!$id){
			error('数据异常');
		}
		if($industryService->del($id)){
			success();
		}

		error();
	}

	/**
	 * 对应案例的评论
	 * @param  CaseCommentService $caseCommentService [description]
	 * @return [type]                                 [description]
	 */
	public function commentList(Request $request,CaseCommentService $caseCommentService)
	{
		$caseId = $request->input('id');
		list($list,$page) = $caseCommentService->getAllList($caseId);
		$caseService = new CaseService();
		foreach($list['data'] as &$val){
			$data = $caseService->getRowById($val['case_id']);
			$val['name'] = $data['name'];
		}
		return view('staff.example.commentList',[
			'title'     => '案例管理',
			'sliderbar' => 'index',
			'list'      => $list,
			'page'      => $page
		]);
	}

	//删除评论
	public function commentDel(Request $request,CaseCommentService $caseCommentService)
	{
		$id = $request->input('id') ?? 0;
		if(!$id){
			error('数据异常');
		}
		if($caseCommentService->del($id)){
			success();
		}

		error();
	}


	/**
     * 生成二维码
     */
    public function createQrcode(Request $request)
    {
        $id = $request->input('id'); //数据库中保存的会员卡id
        $url = config('app.url').'home/index/caseDetails?id='.$id;
        $result['show_qrcode_url'] = $url; 
        $qrcodeStr = QrCode::size(150)->generate(URL($url)); 
        $result['qrcodeStr'] = $qrcodeStr;
        echo json_encode($result);
        exit;
    }

}
