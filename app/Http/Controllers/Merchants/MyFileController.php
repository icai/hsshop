<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-3
 * Time: 下午3:45
 */

namespace App\Http\Controllers\Merchants;


use App\Http\Controllers\Controller;
use App\Jobs\LoadFile;
use App\S\File\FileClassifyService;
use App\S\File\FileInfoService;
use App\S\File\UserFileService;
use Illuminate\Http\Request;
use Upyun\Config;
use Upyun\Signature;
use Upyun\Util;
use Validator;
use Storage;

class MyFileController extends Controller
{

    public function test(Request $request,FileInfoService $fileInfoService)
    {

    }

    /**
     * @auth zhangyh
     * @desc 上传文件 上传文件name = file
     * @date 201703031554
     * @return [type] [description]
     */
    public function upfile(Request $request,FileInfoService $fileInfoService,UserFileService $userFileService){
        if ($request->hasFile('file')){
            $id = $request->session()->get('userInfo')['id'];
            //上传文件
            $result = $fileInfoService->upFile($request->file('file'), $request->input('name','') );
            if ($result['success'] == 1){
				if ($request->input('halt', 0) == 1) {
					echo json_encode($result);
					exit;
				}

                //保存用户文件信息
                $userFileData = Array(
                    'user_id'           => $id,
                    'file_info_id'      => $result['data']['id'],
                    'file_classify_id'  => $request->input('classifyId',0),
                    'weixin_id'         => $request->session()->get('wid'),
                    'file_mine'         => $result['data']['file_mine'],
                    'file_cover'         => $request->input('file_cover',''),
                );
                $userFileId = $userFileService->add($userFileData);
                if ($userFileId){
                    $where = [];
                    $where['file_classify_id'] = $userFileData['file_classify_id'];
                    $where['weixin_id'] = session('wid');
                    $where['id'] = $userFileId;
                    list($data) = $userFileService->getlistPage($where);
//                    success('上传成功','',$res,true);
                    $content = array( 'status' => 1, 'info' => '上传成功', 'url' => '', 'data' => array_pop($data['data']) );
                    echo  json_encode($content);
                    exit();

                }else{
                    error('文件上传失败');
                }
            }else{
                error('文件上传失败');
            }
        }else{
            error('请上传文件');
        }
    }

    //设置又拍云上传视频
    public function setUpxVideo(Request $request, FileInfoService $fileInfoService,UserFileService $userFileService)
    {
        $data = [
            'name'  =>  $request->input('name',''),
            'path'  =>  $request->input('path',''),
            'type'  =>  $request->input('type',''),
            'size'  =>  $request->input('size',0),
            's_path'    => '',
            'm_path'    => '',
            'l_path'    => '',
            'img_size'  => '',
            'file_mine' => 2,
        ];
        $videoArr = [
                'video/x-msvideo' => 'avi',
                'video/x-dv' =>'dv',
                'video/mp4' =>'mp4',
                'video/mpeg' =>'mpeg' ,
                'video/quicktime' =>'mov'  ,
                'video/x-ms-wmv'=>'wm'   ,
                'video/x-flv' =>'flv'  ,
                'video/x-matroska'=> 'mkv'  
            ];
        if (!isset($videoArr[$request->input('type')])) {
            error('文件类型错误');
        }
        $addResult = $fileInfoService->add($data);
        $id = $request->session()->get('userInfo')['id'];

        //保存用户文件信息
        $userFileData = Array(
            'user_id'           => $id,
            'file_info_id'      => $addResult,
            'file_classify_id'  => $request->input('classifyId',0),
            'weixin_id'         => $request->session()->get('wid'),
            'file_mine'         => 2,
            'file_cover'        => $request->input('file_cover',''),
        );
        $userFileId = $userFileService->add($userFileData);
        if ($userFileId){
            $where = [];
            $where['file_classify_id'] = $userFileData['file_classify_id'];
            $where['weixin_id'] = session('wid');
            $where['id'] = $userFileId;
            list($data) = $userFileService->getlistPage($where);
            $content = array( 'status' => 1, 'info' => '上传成功', 'url' => '', 'data' => array_pop($data['data']) );
            echo  json_encode($content);
            exit();

        }else{
            error('文件上传失败');
        }
    }

    /**
     * @auth zhangyh
     * @desc 修改用户文件分组 传递参数UserFileids[] = 1 userFileIds[]=2  分组修改为 classifyId = 0
     * @param Request $request
     * @param UserFileService $userFileService
     * @param FileClassifyService $fileClassifyService
     */
    /**
     * @auth zhangyh 201703081514
     * @desc 修改文件名称
     */
    function modifyVedio(Request $request,FileInfoService $fileInfoService,UserFileService $userFileService)
    {
        $rule = [
            'name'    => 'required',
            'id'  => 'required',
            'classifyId'    => 'required|Integer'
        ];
        $message = [
            'id.required'  => '用户上传文件id不能为空',
            'classifyId.required'   => '分组id不能为空',
            'name.required'   => '文件名不能为空',
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        

        $fileData = Array(
            'file_cover'  => $request->input('file_cover'),
            'file_classify_id'  => $request->input('classifyId'),
        );

        $userFileData = $userFileService->model->where('id',$request->input('id'))->first()->toArray();
        if (!$userFileData){
            error('该文件不存在');
        }
        $userFileService->update($userFileData['id'],$fileData);
//        $fileInfoService->init()->where(['id' => $request->input('fileId')])->update($fileData);
        $fileData2['name'] = $request->input('name');
        $fileInfoService->update($userFileData['file_info_id'],$fileData2);
        success();

    }

    /**
     * @auth zhangyh
     * @desc 修改用户文件分组 传递参数UserFileids[] = 1 userFileIds[]=2  分组修改为 classifyId = 0
     * @param Request $request
     * @param UserFileService $userFileService
     * @param FileClassifyService $fileClassifyService
     */

    function modifyClassify(Request $request,UserFileService $userFileService,FileClassifyService $fileClassifyService)
    {

        $rule = Array(
            'userFileIds'    => 'required',
            'classifyId'    => 'required|Integer'
        );
        $message = Array(
            'userFileIds.required'  => '用户图片id不能为空',
            'classifyId.required'   => '分组id不能为空'
        );

        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails())
        {
            error($validator->errors()->first());
        }


        $userFileService->modifyClassify($request->input('userFileIds'),$request->input('classifyId'),session('userInfo')['id']);
        success();
    }


    /**
     * @auth zhangyh 201703031527
     * @desc 添加分组 添加分分组打名称， name=测试分组
     * @param Request $request
     * @param FileClassifyService $fileClassifyService
     */
    function addClassify(Request $request,FileClassifyService $fileClassifyService)
    {
        $rule = Array(
            'name'  =>'required|max:20'
        );
        $message = Array(
            'name.required'     => '分组名称不能为空',
            'name.max'          => '分组最多20个字符',
        );

        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails())
        {
            error($validator->errors()->first());
        }


        if ($request->input('classifyId')){
//            $fileClassifyService->init()->where(['id'=>$request->input('classifyId')])->update(['id'=>$request->input('classifyId'),'name'=>$request->input('name')]);
            $fileClassifyService->update($request->input('classifyId'),['name'=>$request->input('name')]);
            success("操作成功",'',['name'=>$request->input('name')]);
        }

        $data = Array(
            'user_id'       => $request->session()->get('userInfo')['id']?$request->session()->get('userInfo')['id']:1,
            'name'          => $request->input('name'),
            'weixin_id'     => $request->session()->get('wid'),
            'file_mine'     => $request->input('file_mine', 1),
        );

        $id = $fileClassifyService->add($data);
        if ($id) {
            $data['id'] = $id;
            success("操作成功",'',$data);
        }else{
            error("操作失败");
        }

    }


    /**
     * @auth zhangyh
     * @desc 2017030051344 删除文件 传递参数为array userFileIds[]=1 userFileIds[]=2 …………
     * @param Request $request
     * @param UserFileService $userFileService
     */
    function  delFile(Request $request,UserFileService $userFileService)
    {
        $rule = Array(
            'userFileIds'    => 'required',
        );
        $message = Array(
            'userFileIds.required'   => '用户文件ID不能为空',
        );


        $validator = Validator::make($request->only(['userFileIds']),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        //删除用户图片
        $userFileIds = $request->input('userFileIds');
        if (is_array($userFileIds)){
            $userFileService->delUserFile($userFileIds);
            success('删除成功');
        }else{
            error('请输入用户文件id');
        }

    }

    /**
     * @desc 根据文件分组获取文件
     * @zhangyh 201703061709
     * @param Request $request
     * @param UserFileService $userFileService
     */
    function getUserFileByClassify(Request $request,UserFileService $userFileService)
    {
        $classifyId = $request->input('classifyId', 0);
        $file_mine = $request->input('file_mine', 1);
        $res = $userFileService->getUserFileByClassify($classifyId, $file_mine);
        success('','',$res);
    }


    /**
     * @auth zhangyh 201703061740
     * @desc 删除文件分组 传递分组id classifyId = 9
     * @param Request $request
     * @param FileClassifyService $fileClassifyService
     */
    function delClassify(Request $request,FileClassifyService $fileClassifyService)
    {
        $rule = [
            'classifyId'    => 'required|integer'
        ];
        $message = [
            'classifyId.required'   => '分组ID不能为空',
            'classifyId.integer'    => '分组ID必须为整数'
        ];
        $validator = Validator::make($request->only('classifyId'),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $classifyId = $request->input('classifyId');
        //获取用户信息
        $userId = $request->session()->get('userInfo')['id'];
        //获取分类信息
        $fileClassifyServiceData = $fileClassifyService->getRowById($classifyId);
        if ($fileClassifyServiceData){
            if ($fileClassifyServiceData['user_id'] == $userId){
                $fileClassifyService->delClassify($classifyId);
            }else{
                error('该分组不属于你！你无权先删除');
            }
        }else{
            error('删除失败！请稍候重试.');
        }
    }


    /**
     * @auth zhangyh 201703081514
     * @desc 修改文件名称
     */
    function modifyFileName(Request $request,FileInfoService $fileInfoService,UserFileService $userFileService)
    {
        $rule = [
            'name'    => 'required',
            'fileId'  => 'required'
        ];
        $message = [
            'name.required'   => '名称不能为空',
            'fileId.required'    => '文件ID不能为空'
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $fileData = Array(
            'id'    => $request->input('fileId'),
            'name'  => $request->input('name'),
        );
        $userFileData = $userFileService->model->where('file_info_id',$fileData['id'])->first()->toArray();
        if (!$userFileData){
            error('该文件不存在');
        }
//        $fileInfoService->init()->where(['id' => $request->input('fileId')])->update($fileData);
        $fileInfoService->update($request->input('fileId'),$fileData);

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201704011002
     * @desc 获取文件分组
     * @param Request $request
     */
    public function getClassify(Request $request,FileClassifyService $fileClassifyService)
    {
        $uid = session('userInfo')['id'];
        $file_mine = $request->input('file_mine', 1);
        $request->input('name');
        $fileClassifyData = $fileClassifyService->getMyClassify($uid, $file_mine);
        success('','',$fileClassifyData);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @update 何书哲 2019年05月20日 size参数作除数为0处理
     */
    public function getImgList(Request $request)
    {
        $wid = session('wid');

        $fileInfoService = new FileInfoService();
        $userFileService = new UserFileService();
        $where = [
            'weixin_id' => session('wid'),
            'file_mine' => 1,
            'file_mine' => '4',
        ];
        $page = ($request->input('start')/($request->input('size')??20))+1;
        $pageSize = $request->input('size')??20;
        $request->offsetSet('page',$page);
        $res = $userFileService->getListWithPage($where, $orderBy = '', $order = '', $pageSize);
        $ids = array_column($res[0]['data'],'file_info_id');
        $fileData = $fileInfoService->getListById($ids);
        $list = [];
        foreach ($fileData as $val){
            $temp['mtime'] = strtotime($val['updated_at']);
            $temp['url']   = config('app.cdn_img_url').$val['path'];
            $list[] = $temp;
        }
        $result = array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $request->input('start'),
            "total" => $res[0]['total'],
        );
        header("Content-Type: text/html; charset=utf-8");
        echo json_encode($result);
        exit();

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180615
     * @desc 获取CDN上传信息
     * @param Request $request
     * @param FileInfoService $fileInfoService
     */
    public function getCDNInfo(Request $request,FileInfoService $fileInfoService)
    {
        $input = $request->input();
        $bucket = config('app.cdn_bucket');
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $config->setFormApiKey('Mv83tlocuzkmfKKUFbz2s04FzTw=');
        $data['save-key'] = '/'.$fileInfoService->getCDNFilePath($input);
        $data['expiration'] = time() + 120;
        $data['bucket'] = $bucket;
        $data['notify-url'] = 'https://hsshop.huisou.cn/merchants/myfile/notify/';
        $data['content-length-range'] = '0,31240000';
        $policy = Util::base64Json($data);
        $method = 'POST';
        $uri = '/' . $bucket;
        $signature = Signature::getBodySignature($config, $method, $uri, null, $policy);
        echo json_encode(array(
            'policy' => $policy,
            'authorization' => $signature,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201850615
     * @desc 异步回调接口
     */
    public function notify(Request $request)
    {
        try{

        }catch (\Exception $e){
            throw new \Exception();
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180601
     * @desc 设置图片
     */
    public function setFile(Request $request,UserFileService $userFileService)
    {
        if ($request->path() == 'staff/myfile/setFile'){
            success();
        }
        $input = $request->input();
        $rule = [
            'name'    => 'required',
            'path'    => 'required',
            'type'    => 'required',
            'img_size' => 'required',
            'size' => 'required',
        ];
        $message = [
            'name.required'   => '文件名不能为空',
            'path.required'   => '路径不能为空',
            'type.required'   => '类型不能为空',
            'img_size.required'   => '尺寸不能为空',
            'size.required'   => '请上传图片尺寸',
        ];
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $fileData = [
            'name'          => $input['name'],
            'path'          => $input['path'],
            's_path'      => $input['path'],
            's_path'      => $input['path'],
            'm_path'      => $input['path'],
            'l_path'      => $input['path'],
            'type'          => $input['type'],
            'size'          => $input['size'],
            'img_size'      => $input['img_size'],
        ];

        $fileId = (new FileInfoService())->add($fileData);

        //保存用户文件信息
        $id = $request->session()->get('userInfo')['id'];
        $userFileData = Array(
            'user_id'           => $id,
            'file_info_id'      => $fileId,
            'file_classify_id'  => $input['classifyId']??0,
            'weixin_id'         => session('wid'),
            'file_mine'         => '4',
        );

        $userFileId = $userFileService->add($userFileData);
        $job = (new LoadFile($input['path']))->onQueue('LoadFile');
        $this->dispatch($job);
        success('操作成功','',$fileData);
    }





}
















