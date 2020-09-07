<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

use App\S\Weixin\WeixinBusinessService;
use App\S\Weixin\WeixinCaseService;
use QrCodeService;
use App\Module\CommonModule;
use App\Model\WeixinCase;

class CaseController extends Controller
{

	/**
	 * 获取二级分类
	 * @param  Request               $request               [description]
	 * @param  WeixinBusinessService $weixinBusinessService [description]
	 * @return [type]                                       [description]
	 */
    public function categoryList(Request $request,WeixinBusinessService $weixinBusinessService)
    {
        $dir = public_path().'/home/image/categoryIcon';
        $icons = readFiles($dir);
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.cate.list' : 'home.cate.list';
    	// 支持筛选一级分支下的二级分类，同时也支持对二级分类的标题进行模糊搜索
        $pid = $request->input('pid') ?? 0;
        $type = $request->input('type') ?? 1; //案例类型
    	//一级分类列表(去除掉没有二级分类人数据)
    	$firstCateList = $weixinBusinessService->getFirstCategory();
        //dd($firstCateList->toArray());
        foreach ($firstCateList as $key => $value) {
            if ($value['has_many_childsCount'] == 0) {
                continue;
            }
            $result[$value['id']] = $value; 
        }
        $firstCateList = $result ?? [];
    	// 二级分类列表
    	$list = $weixinBusinessService->getSecondCategory($pid,$whereData ?? []);
        $cateList = [];
        if ($list) {
            foreach ($list as $key => $value) {
                $value['caseCount'] = WeixinCase::where('business_id',$value['id'])->where('type',$type)->count();
                $value['icon'] = config('app.source_url').'home/image/case_hslogo.png';
                foreach ($icons as $icon) {
                    $mix = explode('-',$icon);
                    $num = str_replace('.png', '', $mix[1]);
                    if ($num == $value['id']) {
                        $value['icon'] = config('app.source_url').str_replace(public_path().'/', '', $icon);
                    }
                }
                //过滤二级分类下没有案例的情况
                if ($value['caseCount'] == 0) {
                    continue;
                }
                $cateList[$value['pid']][] = $value;
            }
        }
    	return view($view_html,[
			'title'    => '商家案例分类列表',
            'pid'      => $pid,
			'list'     => $cateList,
			'firtList' => $firstCateList,
            'type'      => $type
    	]);
    }

    /**
     * 获取某个二级分类下的所有商家案例列表
     * @param  [type]                $id                    [description]
     * @param  WeixinBusinessService $weixinBusinessService [description]
     * @return [type]                                       [description]
     */
    public function caseList(Request $request,WeixinCaseService $weixinCaseService,WeixinBusinessService $weixinBusinessService)
    { 
        $id = $request->route('id') ?? 0;
        $type = $request->route('type') ?? 1;
        $is_mobile = $request->attributes->get('is_mobile');
        $view_html = $is_mobile ? 'home.mobile.cate.caseList' : 'home.cate.caseList';
        $data = $weixinBusinessService->getRowById($id);
        /*if (empty($data)) {
            error('该分类不存在或已被删除');
        }*/
        $keyword = $request->input('keyword') ?? '';
        $where['type'] = $type;
        $where['business_id'] = $id;
        if ($keyword) {
            $where['title'] = $keyword;
        }
        $list = $weixinCaseService->getAllCaseList($where ?? [],false);
        $result = [];
        foreach ($list as $key => $value) {
            $result[$value['type']][] = $value;
        }
        $result = array_values($result);
    	return view($view_html,[
            'title'   => $data ? $data['title'].'案例' : '所有分类案例',
            'list'    => $list,
            'result'  => $result,
            'type'    => $type,
            'keyword' => $keyword
    	]);
    	
    }
}
