<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\S\Weixin\ShopService;
use App\S\Weixin\WeixinCaseService;
use App\S\Weixin\WeixinBusinessService as WXBusinessService;
use Excel;

class CaseController extends Controller
{
	/**
	 * 推存的商户同步到案例
	 * @author 吴晓平 <2018.11.22>
	 * @param  ShopService $shopservice [description]
	 * @return [type]                   [description]
	 */
    public function syncWeixinCase(ShopService $shopservice)
    {
    	$returnData = ['errCode' => 0,'msg' => ''];
    	$result = $shopservice->syncWeixinCase();
    	if ($result) {
    		$returnData['msg'] = '同步更新成功';
    		return $returnData;
    	}
    	$returnData['errCode'] = -1;
    	$returnData['msg'] = '同步更新失败,商家案例已存在';
    	return $returnData;
    }

    /**
     * 商户案例列表
     * @author 吴晓平 <2018.11.27>
     * @return [type] [description]
     */
    public function weixinCaselist(Request $request,WeixinCaseService $weixinCaseService)
    {
        $keyword = $request->input('name') ?? '';
        if ($keyword) {
            $where['title'] = $keyword;
        }
        list($list,$pageHtml) = $weixinCaseService->getAllCaseList($where ?? []);
        foreach ($list['data'] as $key => &$value) {
            $value['belongsToBusiness'] = json_decode($value['belongsToBusiness'],true);
            switch ($value['type']) {
                case '1':
                    $value['type_title'] = '微商城';
                    break;
                case '2':
                    $value['type_title'] = '小程序';
                    break;
                case '3':
                    $value['type_title'] = 'APP';
                    break;
                default:
                    $value['type_title'] = '其他';
                    break;
            }
        }
        return view('staff.weixin.case.list',[
            'title'    => '行业解决方案',
            'list'     => $list,
            'pageHtml' => $pageHtml
        ]);
    }

    /**
     * 新建商家案例
     * @author 吴晓平 <2018.11.27>
     * @return [type] [description]
     */
    public function caseCreate()
    {
        return view('staff.weixin.case.create',[
            'title' => '行业解决方案'
        ]);
    }

    /**
     * 修改案例分类
     * @author 吴晓平 <2018.11.27>
     * @param  Request               $request               [description]
     * @param  WXBusinessService $weixinBusinessService [description]
     * @param  WeixinCaseService     $weixinCaseService     [description]
     * @return [type]                                       [description]
     */
    public function caseEdit(Request $request,WXBusinessService $weixinBusinessService,WeixinCaseService $weixinCaseService)
    {
        $id = $request->input('id') ?? 0;
        $data = $weixinCaseService->getRowById($id);
        if (empty($data)) {
            error('该案例不存在或已被删除');
        }
        if ($request->isMethod('post')) {
            $business_id = $request->input('business_id') ?? 0;
            $id = $request->input('id') ?? 0;
            $title = $request->input('title') ?? '';
            $qrcode = $request->input('qrcode') ?? '';
            if (strpos('data:image/png;base64,',$qrcode)) {
                $qrcode = str_replace('data:image/png;base64,','',$qrcode);
            }
            if (empty($business_id)) {
                error('请选择要修改的分类');
            }
            //更新关联表的数据到redis
            $redisRelationData = ['belongsToBusiness' => json_encode(['id' => $business_id,'title' => $title],JSON_UNESCAPED_UNICODE)];
            if ($weixinCaseService->update($id,['business_id' => $business_id,'qrcode' => $qrcode],$redisRelationData)) {
                (new ShopService())->update($data['wid'],['business_id' => $business_id]);
                success('修改成功');
            }
            error('修改失败');
        }
        //所有一级分类列表
        $result = $weixinBusinessService->getAllCategory();
        $categoryList = [];
        foreach ($result as $key => $value) {
            $categoryList[$value['pid']][] = $value;
        }
        //处理分类数据（把未包含二级分类的移除掉）
        $childCatesIds = array_values(array_filter(array_keys($categoryList)));
        $firstCates = [];
        foreach ($categoryList[0] as $key => $value) {
            if (in_array($value['id'],$childCatesIds)) {
                $firstCates[] = $value;
            }
        }
        //重新赋于一级分类
        $categoryList[0] = $firstCates;

        $businessData = $weixinBusinessService->getRowById($data['business_id']);
        if (empty($businessData)) {
            error('该商户案例所属分类已不存在或已被删除');
        }
        // 获取所选案例的所有二级分类列表
        $secondCates = $categoryList[$businessData['pid']];
        return view('staff.weixin.case.edit',[
            'title'        => '行业解决方案',
            'categoryList' => $categoryList,
            'secondCates'  => $secondCates,
            'caseData'     => $data,
            'pid'          => $businessData['pid'],
        ]);
    }

    /**
     * 上传案例二维码图片
     * @param  Request           $request           [description]
     * @param  WeixinCaseService $weixinCaseService [description]
     * @return [type]                               [description]
     */
    public function caseQrcodeUpload(Request $request,WeixinCaseService $weixinCaseService)
    {
        $file = $request->image;
        $id   = $request->input('id') ?? 0;
        $data = $weixinCaseService->getRowById($id);
        if (empty($data)) {
            error('该案例不存在或已被删除');
        }
        //设置不同的文件上传目录
        switch ($data['type']) {
            case '1':
                $source = 'shop';
                break;
            case '2':
                $source = 'xcx';
                break;
             case '3':
                $source = 'app';
                break;
            default:
                $source = 'other';
                break;
        }
        // 构建存储的文件夹规则
        $folder_name = "hsshop/caseImage/qrcodes/$source" . date("Ym/d", time());
        $result = $weixinCaseService->fileUpload($file,$folder_name,'image'); 
        return $result;
    }

    /**
     * 删除商户案例
     * @author 吴晓平 <2018.11.27>
     * @param  Request           $request           [description]
     * @param  WeixinCaseService $weixinCaseService [description]
     * @return [type]                               [description]
     */
    public function caseDel(Request $request,WeixinCaseService $weixinCaseService)
    {
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            error('请选择要删除的案例');
        }
        if ($weixinCaseService->del($id)) {
            success('删除成功');
        }
        error('删除失败');
    }

    /**
     * 上传商家案例excel文件
     * @return [type] [description]
     */
    public function caseFileUp(Request $request,WeixinCaseService $weixinCaseService)
    {
        $file = $request->excelFile;
        // 构建存储的文件夹规则
        $folder_name = "hsshop/import/excel/" . date("Ym/d", time());
        $result = $weixinCaseService->fileUpload($file,$folder_name,'file');
        return $result;
    }

    /**
     * 商家案例导入
     * @author 吴晓平 <2018.11.27>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function caseImport(Request $request,WeixinCaseService $weixinCaseService)
    {
        $filePath = $request->input('file_path') ?? '';
        if (empty($filePath)) {
            error('请上传要导入的商家案例excel文件');
        }
        $res = []; 
        Excel::load($filePath, function($reader) use( &$res ) {  
            $reader = $reader->getSheet(0); 
            $res = $reader->toArray();  
        },'GBK');

        if ($res) {
            $result = array_shift($res); //把第一列数据单独拿出来
            if (count($result) <> 3) {
                error('导入格式不正确，请根据下载的模板导入数据');
            }
            if (empty($res)) {
                error('该上传的文件内容为空');
            }else { 
                $data = [];
                foreach ($res as $key => $value) {
                    $data[$key]['title'] = $value[0];
                    $data[$key]['business_id'] = (int)$value[1];
                    $data[$key]['type'] = (int)$value[2];
                }
                // 批量插入数据
                if (!$weixinCaseService->batchInsert($data)) {
                    error('导入数据失败');
                }
                success('导入成功');
            }
        }
        error();
    }

    /**
     * 下载模板文件
     * @author 吴晓平 <2018年11月27日>
     * @return [type] [description]
     */
    public function downExcelTemp()
    {
        $filePath = storage_path().'/exports/caseTemp.csv';
        return response()->download($filePath, '商家案例'.'.csv');  //下载
    }
}
