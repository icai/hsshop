<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/14  14:58
 * DESC
 */

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\Controller;
use App\Jobs\ExportMallData;
use App\Model\SaleAchieve;
use App\Module\BindMobileModule;
use App\Module\CommonModule;
use App\S\Foundation\RegionService;
use App\S\Member\MemberService;
use App\S\Staff\WeixinTemplateService;
use App\S\Wechat\WeChatShopConfService;
use App\Services\Permission\AdminRoleService;
use App\Services\Permission\WeixinRoleService;
use App\Services\WeixinBusinessService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use Validator;
use StaffOperLogService;
use Storage;
use App\S\Staff\AfficheService;
use OrderService;
use App\Model\User;
use App\Services\UserService;
use App\S\User\UserService as UService;
use App\S\Weixin\ShopService;
use App\S\Weixin\WeixinCaseService;


class BusinessManageController extends Controller
{

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703171020
     * @desc 总后台首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('staff.businessmanage.index',array(
            'title'     => '首页',
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141500
     * @desc 企业分类管理 ,获取分类，获取顶级一级分类pid=0,不传递Pid默认获取所有二级分类
     * @param Request $request
     * @param WeixinBusinessService $weixinBusinessService
     * @update 许立 2018年09月26日 返回店铺数
     * @update 梅杰 2018年09月27日 增加每个分类下已忽略店里列表
     */
    public function BusinessCategory(Request $request,WeixinBusinessService $weixinBusinessService, WeixinService $weixinService,ShopService $shopService)
    {
        $input = $request->input();
        $pid = isset($input['pid'])?$input['pid']:null;
        $weixinBusinessData = $weixinBusinessService->getCategory($pid);
        $pCategory = $weixinBusinessService->init()->where(['pid'=>0])->getList(false)[0]['data'];

        foreach ($weixinBusinessData[0]['data'] as $k => $v) {
            // 是否有子分类
            $businessData = $weixinBusinessService->init()->where(['pid' => $v['id']])->getList(false)[0]['data'];
            $businessIdArr = [$v['id']];
            $businessData && $businessIdArr = array_merge($businessIdArr, array_column($businessData, 'id'));
            $where['business_id'] = ['in',$businessIdArr];
            $countList = $shopService->getListWithoutPage($where);
            $allCount = count($countList);

            $ignore = array_filter($countList,function ($val) {
               return $val['is_ignore']  == 1;
            });


            $ignoreCount = count($ignore);

            $weixinBusinessData[0]['data'][$k]['shopCount'] = $allCount;
            $weixinBusinessData[0]['data'][$k]['ignoreCount'] = $ignoreCount;

        }


        return view('staff.businessmanage.businesscategory',array(
            'title'     => '店铺管理',
            'sliderba' => 'businesscategory',
            'weixinBusinessData'    => $weixinBusinessData,
            'pCategory'             => $pCategory,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703171735
     * @desc 获取单个分类信息
     * @param Request $request
     */
    public function getModifyInfo(Request $request,WeixinBusinessService $weixinBusinessService)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required|integer',
        );
        $message = Array(
            'id.required'     => '分类ID不能为空',
            'id.integer'      => 'ID必须为整数'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $pCategory = $weixinBusinessService->init()->where(['pid'=>0])->getList(false)[0]['data'];
        $data = $weixinBusinessService->init()->where([])->getInfo($input['id']);
        return view('staff.businessmanage.modifyCategory',array(
            'pCategory' => $pCategory,
            'data'      => $data,
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141631
     * @desc 添加分类,修改分类
     * @param Request $request
     * @param WeixinBusinessService $weixinBusinessService
     */
    public function addCategory(Request $request,WeixinBusinessService $weixinBusinessService)
    {
        $input = $request->input();
        $tag = isset($input['tag'])?$input['tag']:2; //tag=1添加二级分类，tag=2添加一级分类
        //验证数据
        $rule = Array(
            'title'               => 'required',
        );
        $message = Array(
            'title.required'     => '分类名称不能为空'
        );
        if($tag == 1){
            $rule['pid'] = 'required';
            $message['pid.required'] = '请选择一级分类';
        }
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $weixinBusinessData = [
            'pid'       =>  $tag==1?$input['pid']:0,
            'title'     => $input['title'],
            'sort'      => isset($input['sort'])?$input['sort']:0,
            'status'    => isset($input['status'])?$input['status']:1
        ];
        if (isset($input['id']) && !empty($input['id'])){
            //修改分类
            $oneData = $weixinBusinessService->init()->getInfo($input['id']);
            if (!$oneData && $oneData['pid']!=0){
                error('分类不存在');
            }
            $weixinBusinessData['id'] = $input['id'];
            $res = $weixinBusinessService->init()->where(['id'=>$input['id']])->update($weixinBusinessData,false);
            if ($res){
                StaffOperLogService::write('修改企业分类,id='.$input['id']);
                success();
            }else{
                error();
            }
        }else{
            $id = $weixinBusinessService->init()->add($weixinBusinessData,false);
            if ($id){
                StaffOperLogService::write('添加企业分类,id='.$id);
                success();
            }else{
                error();
            }
        }


    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703151101
     * @desc 删除分类，如果删除的是一级分类则把该分类下面的二级分类一同删除，如果有店铺已选择该分类则不能删除
     * @param Request $request
     * @param WeixinBusinessService $weixinBusinessService
     */
    public function delCategory(Request $request,WeixinBusinessService $weixinBusinessService)
    {
        $input = $request->input();
        $rule = Array(
            'id'               => 'required|integer',
        );
        $message = Array(
            'id.required'     => '分类ID不能为空',
            'id.integer'      => 'ID必须为整数'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
       /*删除分类*/
       $res = $weixinBusinessService->delWeixinBusiness($input['id']);
       if ($res['success'] == 1){
           StaffOperLogService::write('删除企业分类,id='.$input['id']);
           success();
       }else{
           error($res['message']);
       }
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703151109
     * @desc 获取企业店铺信息
     * @param Request $request
     * @param WeixinService $weixinService
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
	 * @update 梅杰 2018年9月26日 返回数据增加角色信息
     * @update 许立 2018年09月28日 过期时间直接使用过期字段
     */
    public  function getShop(Request $request,UService $user,ShopService $shopService)
    {
        /*@update 吴晓平 2018年09月17日 店铺会员数据重构*/
        $input = $request->input() ?? [];
        $uid = $request->route('uid') ?? 0;
        $input['uid'] = $uid;
        list($shopData,$pageHtml) = $shopService->getShopList($input);
        if ($shopData['data']) {
            foreach ($shopData['data'] as $key => $value) {
                $flag = 0;
                strtotime($value['shop_expire_at']) < time() + 30 *84600 && $flag = 2; //30天后过期
                strtotime($value['shop_expire_at']) < time()  && $flag = 1; //已过期
                $shopData['data'][$key]['dueFlag'] = $flag;
            }
        }else {
            return redirect()->back()->withInput()->withErrors('该帐号还未创建店铺！');
        }
        //店铺主营行业一级分类
        $category = ( new WeixinBusinessService())->init()->where(['pid'=>0])->getList(false)[0]['data'];

        //获取地区 Herry
        $regions = (new RegionService())->getAll();
        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach($regions as $value){
            $regionList[$value['pid']][] = $value;
        }
        //对省份进行排序
        $provinceList = $regionList[-1];
        return view('staff.businessmanage.getShop',array(
            'title'     => '店铺管理',
            'sliderba' => 'getShop',
            'shopData'    => $shopData['data'],
            'regions_data' => json_encode($regionList),
            'regionList'   => $regionList,
            'provinceList' => $provinceList,
            'pageHtml'     => $pageHtml,
            'category'     => $category
        ));
    }

    /**
     * 清除商家后台登录限制
     * @author 吴晓平 <2018年09月06日>
     * @param  Request       $request       [description]
     * @param  WeixinService $weixinService [description]
     * @return [type]                       [description]
     */
    public function relieveLogin(Request $request,UserService $userService)
    {
        $account = $request->input('account') ?? '';
        if (empty($account)) {
            error('参数为空');
        }
        $key = 'limit-'.$account;
        $userService->cleanErrLogins($key);
        success();
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703152050
     * @desc 删除店铺
     * @param Request $request
     * @param WeixinService $weixinService
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function  delShop(Request $request,ShopService $shopService)
    {
        error('该功能暂停使用');
        $input = $request->input();
        $rule = Array(
            'id'               => 'required',
            'uid'              => 'required'
        );
        $message = Array(
            'id.required'     => '店铺ID不能为空',
            'uid.required'    => '店铺所有者ID不能为空',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $res = $shopService->delShopWithRole($input['id'],$input['uid']);
        if ($res['success'] == 1){
            StaffOperLogService::write('删除店铺,id='.$input['id']);
            success();
        }else{
            error($res['message']);
        }

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703160826
     * @desc 修改店铺
     * @param Request $request
     * @param WeixinService $weixinService
     * @update 张永辉 2018年7月13日 设置绑定小程序的数量
     * @update 何书哲 2018年8月23日 底部logo是否显示
     * @update 何书哲 2018年9月25日 底部logo自定义设置
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年11月19日 过期时间没保存bug修复
     */
    public function modifyShop(Request $request,ShopService $shopService,WeixinRoleService $weixinRoleService)
    {
        $input = $request->input();
        $rules = array(
            'id'           => 'required',
            'shop_name'    => 'required|between:1,50',
            'business_id'  => 'required',
            'company_name' => 'required|between:1,50',
            'address'      => 'required|between:1,200',
            'role_id'      => 'required',
            'start_time'   => 'required',
            'end_time'     => 'required',
            'xcx_num'      => 'required|integer',
            'is_logo_show' => 'required|in:0,1',
            'is_logo_open' => 'required|in:0,1',
            'logo_type'    => 'required|in:0,1',
        );
        /* 错误消息 */
        $messages = array(
            'id.required'           => '修改店铺的ID不能为空',
            'shop_name.required'    => '请填写店铺名称',
            'shop_name.between'     => '店铺名称最多填写50个字符',
            'business_id.required'  => '请选择主营商品',
            'company_name.required' => '请填写公司名称',
            'company_name.between'  => '公司名称最多填写50个字符',
            'province_id.required'  => '请选择省份',
            'city_id.required'      => '请选择城市',
            'area_id.required'      => '请选择地区',
            'address.required'      => '请填写联系地址',
            'address.between'       => '联系地址最多填写200个字符',
            'role_id.required'      => '请选择店铺角色',
            'start_time.required'   => '请选择开始时间',
            'end_time.required'     => '请选择截止时间',
            'is_logo_show.required' => '是否显示底部logo不能为空',//何书哲 2018年8月23日 底部logo是否显示
            'is_logo_show.in'       => '是否显示底部logo值不正确',//何书哲 2018年8月23日 底部logo是否显示
            'is_logo_open.required' => '是否开启底部logo链接不能为空',//何书哲 2018年9月10日 是否开启底部logo链接
            'is_logo_open.in'       => '是否开启底部logo链接值不正确',//何书哲 2018年9月10日 是否开启底部logo链接
            'logo_type.required'    => '底部logo类型为空',//何书哲 2018年9月25日 底部logo类型
            'logo_type.in'          => '底部logo类型值不正确',//何书哲 2018年9月25日 底部logo类型
        );
        if ($request->input('logo_type')) {
            $rules['logo_path'] = 'required';
            $messages['logo_path.required'] = '底部logo图片不能为空';
        } else {
            $input['logo_path'] = '';
        }
        $validator = Validator::make($input,$rules,$messages);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        try{
            $res = $weixinRoleService->init()->model->where(['wid'=>$input['id']])->first()->toArray();
        }catch (Exception $exception){
            error($exception->getMessage());
        }

        $input['shop_expire_at'] = $input['end_time'];

        $weixinRoleData = [
            'id'                => $res['id'],
            'wid'               => $input['id'],
            'admin_role_id'    => array_pull($input,'role_id'),
            'start_time'        => array_pull($input,'start_time'),
            'end_time'          => array_pull($input,'end_time'),
        ];
        StaffOperLogService::write('修改店铺,'.json_encode($input));
        if (isset($input['is_sms']) && $input['is_sms'] == 1){
            $input['is_sms'] = 0;
        }else{
            unset($input['is_sms']);
        }
        //$dbResult = $weixinService->init()->where(['id' => $input['id']])->update($input,false);
        $dbResult = $shopService->update($input['id'],$input);
        $weixinRoleService->init()->where(['wid'=>$input['id']])->update($weixinRoleData);

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201949
     * @desc 修改店铺页面
     * @param Request $request
     * @param WeixinService $weixinService
     * @update 吴晓平 2018年09月12日 把weixinService中的操作迁移到S/ShopService
     */
    public function showEditShop(Request $request,ShopService $shopService,WeixinBusinessService $weixinBusinessService,AdminRoleService $adminRoleService,WeixinRoleService $weixinRoleService)
    {
        $rule = Array(
            'id'               => 'required|integer',
        );
        $message = Array(
            'id.required'     => '店铺ID不能为空',
            'id.integer'      => '店铺ID必须为整数',
        );
        $validator = Validator::make($request->only('id'),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        //获取地区全部数据
        $regionService = new RegionService();
        $regions = $regionService->getAllWithoutDel();
        foreach ($regions as $key=>$item){
            $regionList[$item['pid']][] = $item;
        }
        //单独列出省份列表信息
        $provinceList = $regionList[-1];
        //show page
        $id = $request->input('id');
        $shopData = $shopService->getRowById($id);
        if ($shopData['province_id']) {
           $cityList = $regionList[$shopData['province_id']];
        }
        if ($shopData['city_id']) {
            $areaList = $regionList[$shopData['city_id']];
        }
        $data = $weixinBusinessService->init()->getList(false)[0]['data'];
        $categoryData = [];
        $tmp = '';
        foreach ($data as $val)
        {
            if ($val['id'] == $shopData['business_id']){
                $tmp = $val['pid'];
            }
            $categoryData[$val['pid']][] = $val;
        }
        if ($tmp == 0){
            $shopData['oneCategory'] = $shopData['business_id'];
            $shopData['secCategory'] = 0;
        }else{
            $shopData['oneCategory'] = $tmp;
            $shopData['secCategory'] = $shopData['business_id'];
        }
        //get role
        $shopData['roleData'] = $adminRoleService->init()->getList(false)[0]['data'];
        $shopRole = $weixinRoleService->init()->where(['wid'=>$id])->getList(false)[0]['data'];
        if (!$shopRole){
        	error('角色错误');
		}
        empty($shopRole)?'':$shopData['shopRole']=$shopRole[0];

        //add by wuxiaoping 2018.05.29
        $user = new User();
        $userInfo = $user->where(['id' => $shopData['uid']])->first()->toArray();
        return view('staff.businessmanage.showEditShop',array(
            'title'        => '店铺管理',
            'sliderba'     => 'getShop',
            'shopData'     => $shopData,
            'categoryData' => json_encode($categoryData),
            'userInfo'     => $userInfo,
            'provinceList' => $provinceList,
            'regions'      => json_encode($regionList,JSON_UNESCAPED_UNICODE),
            'cityList'     => $cityList ?? [],
            'areaList'     => $areaList ?? [],
        ));

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703161439
     * @desc 推荐店铺，取消推荐
     * @param Request $request
     * @param WeixinService $weixinService
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function recommend(Request $request,ShopService $shopService)
    {
        $rule = Array(
            'id'               => 'required',
        );
        $message = Array(
            'id.required'     => '店铺ID不能为空',
        );
        $validator = Validator::make($request->only('id'),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        //$weixinData = $weixinService->init()->getInfo($request->input('id'));
        $weixinData = $shopService->getRowById($request->input('id'));
        $upData = [
            'id'    => $weixinData['id'],
        ];
        if ($weixinData['is_recommend'] == 1){
            $upData['is_recommend']=0;
            $logstr = '取消推荐店铺';
        }else{
            $upData['is_recommend']=1;
            $logstr = '推荐店铺';
        }

        //$res = $weixinService->init()->where(['id'=>$upData['id']])->update($upData,false);
        $res = $shopService->update($upData['id'],$upData);
        if ($res){
            if ($weixinData['is_recommend'] == 1) {
                (new WeixinCaseService())->delByCondition(['wid' => $weixinData['id']]);
            }
            StaffOperLogService::write($logstr.',id='.$upData['id']);
            success();
        }else{
            error();
        }
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703161933
     * @desc 获取用户模板
     * @param Request $request
     * @param WeixinTemplateService $weixinTemplateService
     */
    public function getTemplate(Request $request,WeixinTemplateService $weixinTemplateService)
    {
        $weixinTemplateData = $weixinTemplateService->getListAll();
        return view('staff.businessmanage.getTemplate',array(
            'title'          => '店铺管理',
            'sliderba'      => 'getTemplate',
            'template'      => $weixinTemplateData,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170427
     * @desc 添加通用模板
     * @param Request $request
     * @param WeixinTemplateService $weixinTemplateService
     */
    public function addTemplate(Request $request,WeixinTemplateService $weixinTemplateService)
    {
        $input = $request->input();
        if ($request->isMethod('post')){
            $rule = Array(
                'title'               => 'required|max:12',
                'desc'                => 'required|max:100',
                'img'                 => 'required',
                'qrcode'              => 'required|integer',
                'sort'                => 'required|integer',
                'status'              => 'required|in:0,1',
            );
            $message = Array(
                'title.required'     => '模板名称不能为空',
                'desc.required'      => '描述不能为空',
                'img.required'       => '请上传图片',
                'qrcode.required'    => '请填写店铺ID',
                'qrcode.integer'     => '店铺ID必须是整数',
                'sort.integer'       => '请上传排序值',
                'status.integer'     => '请选择状态',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $template = [
                'title'         => $input['title'],
                'desc'          => $input['desc'],
                'img'           => $input['img'],
                'qrcode'        => $input['qrcode'],
                'sort'          => $input['sort'],
                'status'        => $input['status'],
            ];
            if (isset($input['id']) && $input['id']) {
                $dbResult = $weixinTemplateService->update($input['id'],$template);
            }else{
                $dbResult = $weixinTemplateService->add($template);
            }
            if($dbResult){
                success();
            }

            error();
        }

        $templateData = [];
        if (isset($input['id']) && $input['id']){
            $templateData = $weixinTemplateService->getRowById($input['id']);
        }
        return view('staff.businessmanage.addTemplate',array(
            'title'          => '店铺管理',
            'sliderba'      => 'getTemplate',
            'template'      => $templateData,
        ));
    }

    public function delTemplate(WeixinTemplateService $weixinTemplateService,$id)
    {
        if($weixinTemplateService->del($id)){
            success('删除成功');
        }
    }

    /**
     * [微信公众号文件上传]
     * @return [type] [description]
     * @update 何书哲 2018年9月18日 只允许上传.txt和.pem后缀格式的文件
     */
    public function uploadFile(Request $request)
    {
        if($request->isMethod('post')){
            $file = $request->file('file');
            if(!$file){
                error('未选择上传文件');
            }
            //判断文件是否上传成功
            if($file->isValid()){
                //何书哲 2018年9月18日 只允许上传.txt和.pem后缀格式的文件
                $extension = $file->getClientOriginalExtension();
                if (strtolower($extension) != 'txt' && strtolower($extension) != 'pem') {
                    error('只允许上传.txt和.pem后缀格式的文件');
                }

                //获取原文件名
                $originalName = $file->getClientOriginalName();
                //上传的文件名中不能有中文（防止乱码）
                $pattern = '/[^\x00-\x80]/';
                if(preg_match($pattern,$originalName)){
                    error('上传的文件格式不正确！(含有中文)');
                }
                //临时绝对路径
                $realPath = $file->getRealPath();
                $path = config('filesystems.file_path').'/mpverify/'.$originalName;
                $bool = Storage::disk('local')->put($path, file_get_contents($realPath));


                if($bool){
                    success('文件上传成功');
                }else{
                    error('文件上传失败');
                }

            }else{
                error();
            }
        }
        return view('staff.businessmanage.uploadFile',[
            'title'    => '店铺管理',
            'sliderba' => 'uploadFile'

        ]);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171219
     * @desc 总后台店铺客户管理
     * @param Request $request
     */
    public function customer(Request $request)
    {
        $umService = new MemberService();
        $data = $umService->getListByConditionWithPage($request->input());
        return view('staff.businessmanage.customer',[
            'title'    => '店铺管理',
            'sliderba' => 'customer',
            'data'      => $data,
        ]);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param Request $request
     */

    public function changeMobile(Request $request)
    {
        $input = $request->input();
        $rule      = Array(
            'id'     => 'required',
            'mobile' => 'required|max:11',
        );
        $message   = Array(
            'id.required'   => '用户id不能为空',
            'mobile.required' => '手机号码不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $res = (new BindMobileModule())->changeMobile($input['id'],$input['mobile']);
        $res?success():error();

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180123
     * @desc 清空店铺分销关系
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function cleanDistribute(Request $request,ShopService $shopService)
    {
        $wid = $request->input('wid');
        /*$shopData = (new WeixinService())->getStore($wid);
        if ($shopData['errCode'] != 0){
            error('店铺不存在');
        }*/
        $shopData = $shopService->getRowById($wid);
        if (empty($shopData)) {
            error('店铺不存在');
        }

        $memberService = new MemberService();
        $where = [
            'wid'       => $wid,
            'pid'       => ['<>',0],
        ];
        $memberData = $memberService->getList($where);
        if (!$memberData){
            success();
        }

        $ids = array_column($memberData,'id');
        $memberService->batchUpdate($ids,['pid'=>0])?success():error();

    }

    /**
     * 导出店铺信息(目前主要是商品及相关信息)
     */
    public function export(Request $request)
    {
        $input = $request->input();
        if (empty($input['wid_from']) || empty($input['wid_to'])) {
            error('店铺ID不能为空');
        }

        dispatch((new ExportMallData(['wid_from' => $input['wid_from'], 'wid_to' => $input['wid_to']]))->onQueue('ExportMallData'));

        success('后台导入中, 实际进度请去目标店铺查看');
    }

    /**
     * 公告 用于店铺显示
     * @param Request $request
     * @author fuguowei
     * @date  20180205
     */
    public function affiche(Request $request,AfficheService $afficheService,WeixinService $weixinService)
    {

        if($request->isMethod('post'))
        {
            $affiche = $request->input('content') ?? '';
            $affiche = trim($affiche);
            $id = $request->input('id');
            $where['content'] = $affiche;
            if($id)
            {
                $rs = $afficheService->update($id,$where);
            }else{
                $rs = $afficheService->add($where);
            }
            //处理成功
            if($rs){
                success();
            }
            error();
        }
        $obj = $afficheService->getRowById('1');
        if(isset($obj['content']) && $obj['content'])
        {
            $obj['content'] = $this->pictures($obj['content']);
        }
        return view('staff.businessmanage.affiche',array(
            'title'     => '店铺管理',
            'sliderba' => 'affiche',
            'obj'      => $obj ?? [],
        ));
    }

    /**
     * @author fuguowei
     * @date 20180105
     * @desc  商品详情的图片加域名处理
     */
    public  function  pictures($str='')
    {
        preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/",$str,$matches);
        $img_src_arr = $matches[3];
        $url =config('app.source_img_url');
        if($img_src_arr)
        {
            foreach($img_src_arr as $k=>$v)
            {
                $http =strpos($v,'ttp:');
                $https =strpos($v,'ttps:');
                if($http != 1 && $https !=1)
                {
                    $str = str_replace($v,$url."$v",$str);
                }
            }
        }
        return $str;
    }

    /**
     * 导出店铺列表
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月11日 导出店铺修复
     */
    public function shopExport(Request $request, ShopService $shopService)
    {
        $shops = $shopService->getShopList($request->input(), true);
        OrderService::exportExcel($shops, 'shopExport');
    }

    /**
     * todo 修改店铺是否收费
     * @param Request $request
     * @author jonzhang
     * @date 2018-05-30
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function updateStoreForFee(Request $request,ShopService $shopService)
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        $isFee=$request->input('isFee')??0;
        $isFee=intval($isFee);
        $ids=$request->input('ids');
        //全部开启底部logo链接
        if ($isFee == 5) {
            $weixinService->allUpdateData(['is_logo_open'=>1]);
            return $returnData;
        } elseif ($isFee == 6) {
            $weixinService->allUpdateData(['is_logo_open'=>0]);
            return $returnData;
        }
        if(empty($ids)||$ids=="[]")
        {
            $returnData['errCode']=-10001;
            $returnData['errMsg']="ids为空";
            return $returnData;
        }
        $ids=json_decode($ids,true);
        if(empty($ids))
        {
            $returnData['errCode']=-10002;
            $returnData['errMsg']="ids不符合要求";
            return $returnData;
        }
        $i=0;
        foreach($ids as $id)
        {
            if (in_array($isFee, [0,1,2])) {
                $result=$shopService->update($id,['is_fee'=>$isFee]);
            } else if ($isFee == 3) {
                $result=$shopService->update($id,['is_logo_open'=>1]);
            } else if ($isFee == 4) {
                $result=$shopService->update($id,['is_logo_open'=>0]);
            }
            if($result)
            {
                $i++;
            }
        }
        $returnData['errMsg']="更新了".$i."条数据";
        return $returnData;
    }


    /**
     * 地区列表
     * @param Request $request
     * @param RegionService $regionService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张永辉
     */
    public function regionManage(Request $request,RegionService $regionService)
    {
        $input = $request->input();
        $regions = $regionService->getAll();
        foreach($regions as $value){
            $regionList[$value['id']] = $value;
        }
        $where = ['level'=>2];
        if (!empty($input['title'])){
            $where['title'] = $input['title'];
        }
        $res = $regionService->getListWithPage($where,'id', 'desc');
        foreach ($res[0]['data'] as &$val){
            $val['pname'] = $regionList[$val['pid']]['title']??'';
            $val['gid'] = $regionList[$val['pid']]['pid']??'';
            $val['gpname'] = $regionList[$val['gid']]['title'];
        }

        foreach ($regions as $key=>$item){
            if ($item['status'] == -1){
                unset($regions[$key]);
            }
        }
        foreach ($regions as $value) {
            $regionData[$value['pid']][] = $value;
        }

        return view('staff.businessmanage.regionManage',array(
            'title'     => '店铺管理',
            'sliderba' => 'regionManage',
            'data'      => $res,
            'regionData'    => $regionData,
        ));
    }

    /**
     * 隐藏地区
     * @param $id
     * @author 张永辉  2018年8月23日
     */
    public function hideRegion($id,RegionService $regionService)
    {
        $res = $regionService->getRowById($id);
        if (!$res){
            error('地区不存在');
        }
        if ($res['status'] == -2){
            $data = ['status'=>0];
        }else{
            $data = ['status'=>-2];
        }
        $res = $regionService->update($id,$data);
        if ($res){
            $regionService->delRedisAll();
            success('操作成功','',$data);
        }else{
            error();
        }
    }



    public function addRegion(Request $request,RegionService $regionService)
    {
        $input = $request->input();
        $rule      = Array(
            'gid'     => 'required',
            'pid' => 'required',
            'title' => 'required',
        );
        $message   = Array(
            'gid.required'   => '省不能为空',
            'pid.required' => '市不能为空',
            'title.required' => '请填写地区',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $insertData = [
            'title'     => $input['title'],
            'pid'       => $input['pid'],
            'level'       => 2,
        ];
       $res =  $regionService->add($insertData);
        if ($res){
            $regionService->delRedisAll();
            success();
        }else{
            error();
        }

    }


    /**
     * 查看日志
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function allOperateLog() {
        $logData = StaffOperLogService::getListWithPage([], 'created_at', 'desc', 50);
        $actionArr = [
            '1' => '设置域名', '2' => '上传代码', '3' => '获取页面',
            '4' => '获取类目', '5' => '提交审核', '6' => '绑定体验者',
            '7' => '提交发布', '8' => '解绑体验者', '9' => '查看消息模板',
            '10' => '朕要体验', '11' => '设置消息模板', '12' => '取消审核',
            '13' => '获取二维码', '14' => '获取小程序码', '15' => '添加备注',
            '16' => '设置业务域名', '17' => '版本回退', '18' => '作废',
            '19' => '下架', '20' => '审核成功', '21' => '审核失败',
            '22' => '授权成功',
        ];
        if ($logData[0]['data']) {
            foreach ($logData[0]['data'] as &$item) {
                $item['action_name'] = '';
                if ($item['type'] == 0) {
                    $content = preg_replace("/(，)/" ,',' , $item['content']);
                    $content = explode(',', $content, 2);
                    preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $content[0], $matches);
                    $item['action_name'] = join('', $matches[0]);
                    continue;
                }
                $item['action_name'] = $actionArr[$item['type']];
            }
        }
        return view('staff.businessmanage.operateLog',array(
            'title'    => '日志管理',
            'sliderba' => 'index',
            'list'     => $logData[0]['data'],
            'pageHtml' => $logData[1]
        ));
    }


    /**
     * 店铺二维码与小程序码
     * @param Request $request
     * @author: 梅杰 2018年9月25日
     * @update 梅杰 2018年10月22日 增加是否绑定公众号判断
     */
    public function getShopCode(Request $request)
    {

        if ($wid = $request->input('id')) {

            $module = new CommonModule();
            $code = '';
            if ((new WeChatShopConfService())->getRowByWid($wid)) {
                $code =  $module->qrCode($wid,config('app.url').'shop/index/'.$wid);
            }
            $xcxCode = $module->qrCode($wid,'pages/index/index',1);
            success('操作成功','',[
                'code' => $code,
                'xcxCode' => $xcxCode
            ]);
        }
        error('店铺id为空');
    }
    /**
     * 会员列表备注功能
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年09月26日
     */
    public function userRemark(Request $request, ShopService $shopService)
    {
        // 参数
        $input = $request->input();
        (empty($input['uid']) || empty($input['wid'])) && error('参数不完整');
        if (empty($input['salesman']) && empty($input['achievement']) && empty($input['trade'])) {
            error('请至少填写一项数据');
        }

        // 信息是否已经存在
        $remarkModel = new SaleAchieve();
        $row = $remarkModel->where('uid', $input['uid'])->first();
        $updateData = [
            'salesman' => $input['salesman'],
            'achievement' => $input['achievement'],
            'trade' => $input['trade']
        ];

        $shopService->update($input['wid'], [
            'achievement' => $input['achievement'],
            'remark' => $input['trade'],
        ]);

        if ($row) {
            // 更新
            $result = $remarkModel->where('id', $row->id)->update($updateData);

            // 更新店铺表redis
            $shops = $shopService->model
                ->with(['user' => function($query){
                    $query->with('saleAchieve');
                }])->get()->toArray();
            foreach ($shops as $shop) {
                $shop['user'] = json_encode($shop['user']);
                $shopService->redis->updateRow($shop);
            }
        } else {
            // 新增
            $updateData['uid'] = $input['uid'];
            $result = $remarkModel->insertGetId($updateData);
        }

        $result ? success() : error();
    }
    /**
     * 忽略
     * @param Request $request
     * @param ShopService $service
     * @author: 梅杰 2018年9月26日
     */
    public function ignoreShop(Request $request,ShopService $service)
    {
        if ($wid = $request->input('id')) {
            $ignore = $request->input('ignore',0);
            $service->ignoreShop($wid,$ignore) &&  success('操作成功');
            error();
        }
        error('店铺id为空');
    }

    /**
     * 批量忽略
     * @author: 梅杰 2018年9月26日
     */
    public function batchIgnore(ShopService $service)
    {
        $service->batchIgnore() && success('正在处理');
        error();
    }

}
