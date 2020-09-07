<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  17:58
 * DESC
 */

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\Controller;
use App\Model\InformationType;
use App\Module\ProductModule;
use App\S\File\FileInfoService;
use App\Services\Staff\InfoRecommendService;
use App\Services\Staff\InformationService;
use App\S\Staff\InformationTypeService;
use App\Services\Staff\RecommendService;
use DB;
use Illuminate\Http\Request;
use StaffOperLogService;
use Validator;
use App\S\Staff\InformationService as InforService;

class InformationController extends Controller
{

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703131948
     * @desc 添加资讯 修改资讯
     * @param Request $request
     * @param InformationTypeService $informationTypeService
     */
    public function  addInformation(Request $request, InforService $inforService,InfoRecommendService $infoRecommendService)
    {
        $rule = Array(
            'title'               => 'required',
            'subTitle'            => 'required',
            'infoType'            => 'required',
            'content'             => 'required',
            'auth'                => 'required',
            'keywords'            => 'required'
        );
        $message = Array(
            'title.required'            => '标题不能为空',
            'subTitle.required'         => '副标题不能为空',
            'infoType.required'         => '资讯分类不能为空',
            'content.required'         => '内容不能为空',
            'auth.required'             => '作者不能为空',
            'keywords.required'         => '关键字不能为空'
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }


        $infomationData = Array(
            'title'      => $request->input('title'),
            'sub_title'  => $request->input('subTitle'),
            'info_type'  => $request->input('infoType'),
            'attachment' => $request->input('attachment')?trim($request->input('attachment'),','):'',
            'content'    => $request->input('content'),
            'account_id' => $request->session()->get('userData')['id'],
            'auth'       => $request->input('auth'),
            'status'     => $request->input('status'),
            'meta'       => $request->input('meta'),
            'seo_title'  => $request->input('seo_title')??'',
            'keywords'   => $request->input('keywords'),
        );

        $infomationData['content'] = ProductModule::delProductContentHost($infomationData['content'],'1');
        if ($request->input('id')){
            $infomationData['id'] = $request->input('id');
            StaffOperLogService::write('修改资讯,id='.$infomationData['id']);
            $inforService->update($request->input('id'),$infomationData);
            //$informationService->init()->where(['id'=>$request->input('id')])->update($infomationData,false);
            $infoRecommendService->setRecommendInfor();
            success('操作成功','',$infomationData['id']);
        }else{
            $id = $inforService->add($infomationData);
            //$infomationData['id'] = $informationService->init()->add($infomationData,false);
            if ($id){
                StaffOperLogService::write('添加资讯,id='.$id);
                success('操作成功','',$id);
            }else{
                error();
            }
        }

    }

    public function editInformation(Request $request,InformationService $informationService,InformationTypeService $informationTypeService)
    {
        $data = $informationTypeService->getAllList();
        $categoryData = [];
        foreach ($data as $val)
        {
            $categoryData[$val['parent_id']][] = $val;
        }
        $informationData = [];
        if ($id = $request->input('id')){
            $informationData = $informationService->get(['id'=>$id])[0]['data'];
            $informationData?$informationData=$informationData[0]:'';
                $path = $informationTypeService->getRowById($informationData['info_type'])['type_path'];
                $informationData['path'] = explode(',',$path);
                $informationData['content'] = ProductModule::addProductContentHost($informationData['content'],'1');
            }
        return view('staff.infomation.editInformation',array(
            'title'     => '资讯管理',
            'sliderbar' => 'editInformation',
            'categoryData'=> json_encode($categoryData),
            'informationData'   => $informationData,
        ));
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703132008
     * @desc 添加资讯分类
     * @param Request $request
     * @param InformationTypeService $informationTypeService
     */
    public function  addInfoType(Request $request,InformationTypeService $informationTypeService)
    {
        $input = $request->input();
        $rule = Array(
            'name'               => 'required',
        );
        $message = Array(
            'name.required'            => '分类名称不能为空',
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $infoTypeData = Array(
            'parent_id'         => isset($input['parent_id'])?$input['parent_id']:0,
            'type_path'         => trim($input['type_path'],','),
            'name'               => $input['name'],
            'status'            => isset($input['status'])?$input['status']:1,
            'sort'              => isset($input['sort'])?$input['sort']:10,
        );

        //查看是否存在分类
        list($res) = $informationTypeService->getlistPage(['name'=>$input['name']]);
        if (!empty($res['data'])){
            error('该分类已存在请不要重复') ;
        }


       //修改资讯
        if (isset($input['id'])){
            $infoTypeData['type_path'] = trim($infoTypeData['type_path'].','.$input['id'],',');
            $informationTypeService->update($input['id'],['name'=>$input['name']]);
            StaffOperLogService::write('修改资讯分类,id='.$input['id']);
            success();
        }

        //添加资讯
        $id = $informationTypeService->add($infoTypeData,false);
        if ($id){
            $upData = [
                'type_path'=>trim($infoTypeData['type_path'].','.$id,','),
            ];
            $informationTypeService->update($id,$upData);
            StaffOperLogService::write('添加资讯分类,id='.$id);
            success();
        }else{
            error();
        }

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703231552
     * @desc 编辑资讯分类
     * @param Request $request
     * @param InformationTypeService $informationTypeService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEditType(Request $request,InformationTypeService $informationTypeService)
    {
        $id = $request->input('id');
        $infoTypeData = [];
        if ($id){
            $infoTypeData = $informationTypeService->getRowById($id);
            $infoTypeData['type_path'] = explode(',',$infoTypeData['type_path']);
            $infoTypeData['type_count'] = count($infoTypeData['type_path']);
        }
        $data['data'] = $informationTypeService->getAllList();
        $categoryData = [];
        foreach ($data['data'] as $val)
        {
            $categoryData[$val['parent_id']][] = $val;
        }

        return view('staff.infomation.showEditType',array(
            'title'          => '资讯管理',
            'sliderbar'     => 'getInfoType',
            'infoTypeData'  => $infoTypeData,
            'categoryData'       => json_encode($categoryData),
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703132022
     * @desc 获取分类
     * @param Request $request
     * @param InformationTypeService $informationTypeService
     */
    public  function getInfoType(Request $request,InformationTypeService $informationTypeService)
    {
        $infoTypeData = $informationTypeService->get();
        return view('staff.infomation.getInfoType',array(
            'title'     => '资讯管理',
            'sliderbar' => 'getInfoType',
            'infoTypeData'  => $infoTypeData,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703132039
     * @desc 根据条件获取资讯
     * @param Request $request
     * @param InformationService $informationService
     * @update 张永辉 2018年6月28日 根据资讯状态搜索
     */
    public function  getInformation(Request $request,InformationService $informationService,InformationTypeService $informationTypeService)
    {
        $status = $request->input('status',0);
        $where = [];
        if (!empty($status)){
            $where['status'] = $status;
        }
        if ($request->input('keywords')) {
            $title = $request->input('keywords');
            $where['_closure'] = function($query) use ($title){
                $query->where('keywords', 'like', '%'.$title.'%')->orWhere('title', 'like', '%'.$title.'%')->orWhere('content', 'like', '%'.$title.'%');
            };
        }
        $infoData = $informationService->get($where);
        $data = $informationTypeService->getAllList();
        $categoryData = [];
        foreach ($data as $val)
        {
            $categoryData[$val['parent_id']][] = $val;
        }
        return view('staff.infomation.getInfomation',array(
            'title'     => '资讯管理',
            'sliderbar' => 'getInformation',
            'infoData'  => $infoData,
            'categoryData'=> json_encode($categoryData)
        ));
    }

    /**
     * 修改排序
     * @return [type] [description]
     */
    public function saveInformationSort(Request $request,InformationService $informationService,InformationTypeService $informationTypeService,$type="information")
    {
        $id = $request->input('id') ?? 0;
        $type = $request->input('type') ?? 'information';
        if (!$id) {
            error('请先确认要修改的排序资讯');
        }
        $saveData['sort'] = $request->input('sort') ?? 0;
        if ($type == 'information') {
            $result = $informationService->init()->where(['id'=>$id])->update($saveData,false);  
        }else if($type == 'infortype') {
            $result = $informationTypeService->update($id,$saveData);
        }else {
            $result = false;
        }
        if ($result) {
            success();
        }
        error();
    }
    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703140936
     * @desc 删除资讯
     * @param Request $request
     * @param InformationService $informationService
     */
    public function  delInfomation(Request $request,InformationService $informationService,InfoRecommendService $infoRecommendService)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'            => '删除资讯ID不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $res = $informationService->init()->where(['id'=>$input['id']])->delete($input['id'],false);
        $infoRecommendService->setRecommendInfor();
        if ($res){
            StaffOperLogService::write('删除资讯,id='.$input['id' ]);
            success();
        }else{
            error();
        }

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141339
     * @desc  获取推荐列表
     * @param Request $request
     * @param RecommendService $recommendService
     */
    public function getRecomment(Request $request,RecommendService $recommendService)
    {
        $recommentData = $recommendService->init()->getList();
        return view('staff.infomation.recommend',array(
            'title'     => '资讯管理',
            'sliderbar' => 'getRecomment',
            'recommend'  => $recommentData,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141401
     * @desc 添加资讯推荐 name=id 资讯ID，recommentIds[] = 1,recommentIds[]=2 …………
     * @param Request $request
     * @param InfoRecommendService $infoRecommendService
     */
    public function addRecomment(Request $request,InfoRecommendService $infoRecommendService)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'                       => '资讯ID不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $id = $request->input('id');
        //判断是否已推荐
        $data = $infoRecommendService->init()->model->where(['info_id'=>$id])->select('id')->get()->toArray();
        $tmp = [];
        foreach ($data as $val)
        {
            $tmp[] = $val['id'];
        }
        $infoRecommendService->init()->where(['info_id'=>$id])->delete($tmp,false);
        if (!isset($input['recommentIds']) || empty($input['recommentIds'])){
            StaffOperLogService::write('推荐资讯,'.json_encode($input));
            success();
        }
        $recommentIds =$input['recommentIds'];
        if (!is_array($recommentIds)){
            error('推荐ID必须是数组');
        }
        $res = $infoRecommendService->addInfoRecomment($id,$recommentIds);
        if ($res['success'] == 0){
            StaffOperLogService::write('推荐资讯,'.json_encode($input));
            error($res['message']);
        }else{
            success();
        }
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703150915
     * @desc 获取推荐资讯，recommentId 推荐资讯的ID
     * @param Request $request
     * @param InfoRecommendService $infoRecommendService
     */
    public function getRecommentInfo(Request $request,InfoRecommendService $infoRecommendService)
    {
        $input = $request->input();
        $rule = Array(
            'recommentId'               => 'required',
        );
        $message = Array(
            'recommentId.required'     => '推荐类型ID不能为空'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $recommendData = $infoRecommendService->getRecommentInfo($input['recommentId']);
        success('','',$recommendData);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703221336
     * @desc 上传文件
     * @param Request $request
     * @param FileInfoService $fileInfoService
     */
    public function fileUpload(Request $request,FileInfoService $fileInfoService)
    {
        if ($request->hasFile('file')){
            $prefix = 'staff';
            $result = $fileInfoService->upFile($request->file('file'));
            if ($result['success'] == 1){
                $content = array( 'status' => 1, 'info' => '上传成功', 'url' => '', 'data' => $result['data'] );
                echo  json_encode($content);
                exit();
            }else{
                error('文件上传失败');
            }
        }else{
            error('请上传文件');
        }
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703221418
     * @desc 资讯详情
     * @param Request $request
     * @param InformationService $informationService
     */
    public function informationDetal(Request $request,InformationService $informationService,RecommendService $recommendService,InfoRecommendService $infoRecommendService)
    {
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'            => '资讯ID不能为空',
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $infoData = $informationService->get(['id'=>$request->input('id')])[0]['data'];
        $infoData?$infoData = $infoData[0]:error('资讯不存在或已删除');
        //获取推荐列表
        list($recommendData) = $recommendService->init()->getList(false);
        //资讯已推荐信息
        $data = $infoRecommendService->init()->model->where(['info_id'=>$request->input('id')])->select('rec_id')->get()->toArray();
        $infoRecommendData = [];
        foreach ($data as $val)
        {
            $infoRecommendData[] = $val['rec_id'];
        }
        return view('staff.infomation.informationDetal',array(
            'title'     => '资讯管理',
            'sliderbar' => 'getInformation',
            'infoData'  => $infoData,
            'recommendData' => $recommendData,
            'infoRecommendData' => $infoRecommendData,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170327
     * @desc 删除资讯分类
     * @param Request $request
     * @param InformationType $informationType
     */
    public function delInfoType(Request $request,InformationTypeService $informationTypeService)
    {
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'            => '资讯分类ID不能为空',
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $id = $request->input('id');
        $informationTypeService->delInfoType($id);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703280942
     * @desc 添加修改推荐
     * @param Request $request
     * @param RecommendService $recommendService
     */
    public function addRecommend(Request $request,RecommendService $recommendService)
    {
        $input = $request->input();
        if ($request->isMethod('post')){
            //添加推荐
            $rule = Array(
                'name'      => 'required',
                'content'   => 'required',
            );
            $message = Array(
                'name.required'            => '推荐名称不能为空',
                'content.required'         => '推荐描述不能为空',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $recommentData = [
                'name'      => $input['name'],
                'content'   => $input['content'],
                'uri'       => $input['uri'],
            ];
            if (isset($input['id'])){
                $res = $recommendService->init()->where(['id'=>$input['id']])->update($recommentData,false);
                if ($res){
                    StaffOperLogService::write('修改推荐,id='.$input['id']);
                    success();
                }else{
                    error();
                }
            }else{
                $id = $recommendService->init()->add($recommentData,false);
                if ($id){
                    StaffOperLogService::write('添加推荐,id='.$id);
                    success();
                }else{
                    error();
                }
            }


        }
        $recommentData = [];
        if (isset($input['id'])){
            $recommentData = $recommendService->init()->getInfo($input['id']);
        }
        return view('staff.infomation.addRecommend',array(
            'title'     => '资讯管理',
            'sliderbar' => 'getRecomment',
            'recommend'  => $recommentData,
        ));
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703281039
     * @desc 删除推荐
     * @param Request $request
     * @param RecommendService $recommendService
     */
    public function delRecommend(Request $request,RecommendService $recommendService)
    {
        $input = $request->input();
        $rule = Array(
            'id'      => 'required',
        );
        $message = Array(
            'id.required'            => '请选择推荐',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $recommendService->init()->where(['id'=>$input['id']])->delete($input['id']);


    }




}






















