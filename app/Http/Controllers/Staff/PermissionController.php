<?php

/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 2017/3/9
 * Time: 16:09
 */
namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use App\Module\PermissionModule;
use App\S\Staff\AccountService;
use App\Services\Permission\AdminRoleService;
use App\Services\Permission\AdrolePermissionService;
use App\Services\Permission\PermissionService;
use App\Services\Permission\RolePermissionService;
use App\Services\Permission\RoleService;
use App\Services\Permission\WeixinRoleService;
use Illuminate\Http\Request;
use StaffOperLogService;
use Validator;


class PermissionController extends Controller
{


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201005
     * @desc 获取总后台权限
     * @param Request $request
     * @param AdminRoleService $adminRoleService
     */
    public function getAdminRole(Request $request,AdminRoleService $adminRoleService)
    {
        $adminRoleData = $adminRoleService->init()->where(['1'=>1])->getList();
        return view('staff.permission.adminrole',array(
            'title'     => '权限管理',
            'sliderba' => 'adminrole',
            'roleData'    => $adminRoleData,
        ));
    }

    /**
     * @auth zhangyh201703091612
     * @desc 添加后台角色
     * @param name = name //角色名称 name = content //角色描述
     * @param \Request $request
     */
    public  function addAdminRole(Request $request,AdminRoleService $adminRoleService)
    {
        //添加角色
        if ($request->isMethod('post')){
            $rule = Array(
                'name'      => 'required|max:20',
                'content'   => 'required'
            );
            $message = Array(
                'name.required'         => '角色名称不能为空',
                'name.max'               => '角色名称最长不能超过20个字符',
                'content.required'      => '角色描述不能为空'
            );

            $validator = Validator::make($request->all(),$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }

            $adminRoleData = Array(
                'name'      => $request->input('name'),
                'content'   => $request->input('content')
            );
            //判断是添加还是修改
            $str = '';
            if ($request->input('id')){
                $id = $request->input('id');
                $res = $adminRoleService->init()->where(['id'=>$id])->update($adminRoleData,false);
                $str = '修改权限,id='.$id;
            }else{
                $res = $id = $adminRoleService->init()->add($adminRoleData,false);
                $str ='添加权限,id='.$id;
            }
            if ($res){
                StaffOperLogService::write($str);
                success();
            }else{
                error();
            }
        }//add or udpate end
        //显示页面
        $roleData = [];
        if ($request->input('id')){
            $roleData = $adminRoleService->init()->getInfo($request->input('id'));
        }
        return view('staff.permission.addadminrole',array(
            'title'     => '权限管理',
            'sliderba' => 'adminrole',
            'roleData' => $roleData,
        ));


    }

    /**
     * @auth zhangyh 201703091724
     * @desc 绑定添加角色权限
     * @param  name = adroleId 角色ID permissionId[] = 1 permissionId[] = 2 …………   权限ID
     * @param Request $request
     * @param AdrolePermissionService $adrolePermissionService
     */
    public  function bindAdminRolePermission(Request $request,AdrolePermissionService $adrolePermissionService,AdminRoleService $adminRoleService,PermissionService $permissionService)
    {
        //绑定后台角色与权限关系
        if ($request->isMethod('post')){
            $rule = Array(
                'adroleId'          => 'required|integer',
                'permissionId'      => 'required'
            );
            $message = Array(
                'adroleId.required'         => '角色ID不能不能为空',
                'adroleId.integer'          => '角色ID整数',
                'permissionId.required'  => '权限信息不能为空'
            );

            $validator = Validator::make($request->all(),$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            //角色绑定权限
            $permissionData = $request->input('permissionId');
            $adroleId = $request->input('adroleId');
            if (!is_array($permissionData)){
                error('权限信息应为数组');
            }
            $adrolePermissionService->bindPermission($adroleId,$permissionData);
        }//bind admin role end
        //show page start
        if (!$id = $request->input('id')){
            error('管理员ID不能为空');
        }
        $permissionData = $permissionService->getPermission('adrole',$id);
        $roleData = $adminRoleService->init()->getInfo($id);
        return view('staff.permission.bindAdminrolePermission',array(
            'title'                 => '权限管理',
            'sliderba'              => 'adminrole',
            'permissionData'       => $permissionData,
            'roleData'              => $roleData,
        ));

    }


    /**
     * @auth zhangyh 20170309
     * @desc 添加权限
     * @param name='',route='',content=''
     * @param Request $request
     * @param PermissionService $permissionService
     */
    public function addPermission(Request $request,PermissionService $permissionService)
    {
        $rule = Array(
            'name'          => 'required',
            'route'          => 'required',
            'content'      => 'required'
        );
        $message = Array(
            'name.required'         => '权限名称不能为空',
            'route.required'          => '路由不能为空',
            'content.required'  => '权限描述不能为空'
        );

        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }

        $permissionData = Array(
            'name'      => $request->input('name'),
            'route'     => $request->input('route'),
            'content'   => $request->input('content'),
            'type'      => $request->input('type')??1,
        );
        $id = $permissionService->init()->add($permissionData,false);
        if ($id){
            StaffOperLogService::write('添加权限，id='.$id);
            success();
        }else{
            error();
        }
    }

    /**
     * 删除权限 包括批量
     */
    public function deletePermission(Request $request,PermissionService $permissionService,RolePermissionService $rolePermissionService,AdrolePermissionService $adrolePermissionService)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            error('参数不完整');
        }

        foreach ($ids as $id) {
            $permissionService->init()->where(['id' => $id])->delete($id, false);
            $rolePermissionService->init()->model->where('permission_id', $id)->delete();
            $adrolePermissionService->init()->model->where('permission_id', $id)->delete();
            StaffOperLogService::write('删除权限，id='.$id);
        }

        success();
    }

    /**
     * @auth zhangyh 20170309
     * @desc 绑定店铺角色
     * @param Request $request
     * @param  name = wid ,name=adminRoleId,name=>startTime,name=endTime
     * @param WeixinRoleService $weixinRoleService
     */
    public function addWeixinRole(Request $request,WeixinRoleService $weixinRoleService)
    {
        $rule = Array(
            'wid'               => 'required|integer',
            'adminRoleId'    => 'required|integer',
            'startTime'        => 'required',
            'endTime'        => 'required'
        );
        $message = Array(
            'wid.required'               => '店铺ID不能为空',
            'wid.integer'                => '店铺ID必须为数值',
            'adminRoleId.required'      => '角色ID不能为空',
            'adminRoleId.integer'       => '角色ID为整数',
            'startTime.required'        => '开始时间不能为空',
            'endTime.required'          => '结束时间不能为空'
        );
        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        //绑定微信
        $weixinRoleService->bindWeixinRole();
    }


    /**
     * @auth zhangyh
     * @date 201703092106
     * @Email zhangyh_private@foxmail.com
     * @desc 获取权限列表
     * @param Request $request
     * @param PermissionService $permissionService
     */
    public  function getPermission(Request $request,PermissionService $permissionService)
    {
        $permissionData = $permissionService->init()->where()->getList(false);
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703100904
     * @desc 绑定前台角色和权限
     * @param Request $request
     * @param RolePermissionService $
     */
    public  function bindRolePermission(Request $request,RolePermissionService $rolePermissionService,PermissionService $permissionService,RoleService $roleService)
    {
        //bind role
        if ($request->isMethod('post')){
            $rule = Array(
                'roleId'          => 'required|integer',
                'permissionIds'      => 'required'
            );
            $message = Array(
                'roleId.required'         => '角色ID不能不能为空',
                'roleId.integer'          => '角色ID整数',
                'permissionIds.required'  => '权限信息不能为空'
            );

            $validator = Validator::make($request->all(),$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            //角色绑定权限
            $permissionIds = $request->input('permissionIds');
            $roleId = $request->input('roleId');
            if (!is_array($permissionIds)){
                error('权限信息应为数组');
            }
            $rolePermissionService->bindRolePermission($roleId,$permissionIds);
        }

        //show page
        if (!$id = $request->input('id')){
            error('管理员ID不能为空');
        }

        $permissionData = $permissionService->getPermission('role',$id);
        $roleData = $roleService->init()->getInfo($id);
        return view('staff.permission.bindRolePermission',array(
            'title'                 => '权限管理',
            'sliderba'              => 'role',
            'permissionData'       => $permissionData,
            'roleData'              => $roleData,
        ));

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703200940
     * @desc 开启禁用前台角色
     * @param Request $request
     * @param RoleService $roleService
     */
    public function openRole(Request $request,RoleService $roleService)
    {
        $rule = Array(
            'roleId'          => 'required|integer',
        );
        $message = Array(
            'roleId.required'         => '角色ID不能不能为空',
            'roleId.integer'          => '角色ID整数',
        );

        $validator = Validator::make($request->all(),$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $roleData = $roleService->init()->getInfo($request->input('roleId'));
        if ($roleData){
            $data  = [];
            $str = '';
           if ($roleData['status'] == 1){
                $data['status'] = 0;
                $str = '禁用角色'.$request->input('roleId');
           }else{
               $data['status'] = 1;
               $str = '启用角色'.$request->input('roleId');
           }
           $roleService->init()->where(['id'=>$request->input('roleId')])->update($data);

        }else{
            error('分组不存在');
        }


    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201539
     * @desc 添加前台角色
     * @param Request $request
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addRole(Request $request,RoleService $roleService)
    {
        //add role
        if ($request->isMethod('post')){
            $input = $request->input();
            $rule = Array(
                'name'      => 'required|max:20',
                'content'   => 'required',
                'status'    => 'required|in:0,1'
            );
            $message = Array(
                'name.required'         => '角色名称不能为空',
                'name.max'               => '角色名称最长不能超过20个字符',
                'content.required'      => '角色描述不能为空',
                'status.required'       => '是否开始不能为空',
                'status.in'             => '是否开始只能是是或否'
            );

            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }

            $roleData = Array(
                'name'      => $input['name'],
                'content'   => $input['content'],
                'status'    => $input['status'],
            );
            $roleService->init()->add($roleData);

        }

        return view('staff.permission.addRole',array(
            'title'     => '权限管理',
            'sliderba' => 'role',
        ));
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703201537
     * @desc 获取权限列表
     * @param Request $request
     * @param RoleService $roleService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRole(Request $request,RoleService $roleService)
    {
        $roleData = $roleService->init()->where(['1'=>1])->getList();
        return view('staff.permission.role',array(
            'title'     => '权限管理',
            'sliderba' => 'role',
            'roleData'    => $roleData,
        ));
    }


	/**
	 * @auth zhangyh
	 * @Email zhangyh_private@foxmail.com
	 * @date 20170406
	 * @desc 总后台管理员列表
	 */
    public function account(AccountService $accountService)
	{
		$accountData = $accountService->getlistPage();
		return view('staff.permission.account',array(
			'title'     => '权限管理',
			'sliderba' => 'account',
			'account'	=> $accountData,
		));
	}


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
	public function staffPermission(Request $request,PermissionModule $permissionModule)
    {
        $accountData = (new AccountService())->getRowById($request->input('id'));
        $data = $permissionModule->getPermission($request->input('id'));
        return view('staff.permission.bindStaffPermission',array(
            'title'                 => '权限管理',
            'data'              => $data,
            'account'           => $accountData,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180111
     * @desc 绑定权限
     */
    public function bindStaffPermission(Request $request)
    {
        $input = $request->input();
        $permissionModule = new PermissionModule();
        $permissionModule->bindStaffPermission($input);
        success();

    }



}













