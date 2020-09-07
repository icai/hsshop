<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/10
 * Time: 13:36
 */

namespace App\Http\Controllers\Staff;


use App\Http\Controllers\Controller;

use App\S\Staff\CusSerManageService;
use Validator;
use Illuminate\Http\Request;

class CustomerManageController extends Controller
{


    /**
     * 获取客服列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function index(CusSerManageService $cusSerManageService)
    {
        //获取所有客服
        $data = $cusSerManageService->getAll();
        return view('staff.customerservicemange.index',array(
            'title'     => '客服管理',
            'slidebar'      => 'index',
            'data'         => $data,
        ));
    }

    /**
     * 添加客服
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create(Request $request,CusSerManageService $cusSerManageService)
    {
        if ($request->isMethod('post')) {
            //验证数据
            $data = $request->input();
            $rule = Array(
                'name'  => 'required',
                'phone' => 'required|unique:customer_service|numeric|',
            );
            $message = Array(
                'name.required'  => '客服名称不能为空',
                'phone.required' => '电话不能为空',
                'phone.numeric'  => '电话号码格式不正确',
            );
            $validator = Validator::make($data,$rule,$message);
            if($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if($cusSerManageService->create($data))
            {
                return redirect('staff/CustomerServiceManage')->with('success', '添加成功!');
            } else {
                return redirect()->back();
            }

        }
        //引入视图
        return view('staff.customerservicemange.create',[
            'title'     => '客服管理',
            'slidebar'      => 'index'
        ]);
    }


    /** 修改操作
     * @param Request $request
     * @param $id 客服id
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function save(Request $request)
    {
        $data = \CusSerManageService::getAll();


        $fOne = \CusSerManageService::getAll((time() - 24*3600));
        $fTwo = \CusSerManageService::getAll((time() - 24*3600*2));
        $fThree = \CusSerManageService::getAll((time() - 24*3600*3));

        $wOne = \CusSerManageService::getAll((time() + 24*3600));
        $wTwo = \CusSerManageService::getAll((time() + 24*3600*2));
        $fThree = \CusSerManageService::getAll((time() + 24*3600*3));

        $show = [

            date('Y-m-d',(time() - 24*3600*3)) => $fThree,
            date('Y-m-d',(time() - 24*3600*2)) => $fTwo,
            date('Y-m-d',(time() - 24*3600)) => $fOne,
            '今天'    => $data,
            date('Y-m-d',(time() + 24*3600)) => $wOne,
            date('Y-m-d',(time() + 24*3600*2)) => $wTwo,
            date('Y-m-d',(time() + 24*3600*3)) => $fThree,
        ];

        if ($request->isMethod('post')) {
            //验证数据
            $data = $request->input();
            $rule = Array(
                'name'  => 'required',
                'phone' => 'required|numeric',
            );
            $message = Array(
                'name.required'  => '客服名称不能为空',
                'phone.required' => '电话不能为空',
                'phone.numeric'  => '电话号码格式不正确',
            );
            $validator = Validator::make($data,$rule,$message);
            if($validator->fails())
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if(\CusSerManageService::update($data))
            {
                return redirect('staff/CustomerServiceManage')->with('success', '修改成功!');
            } else {
                return redirect()->back();
            }
        }
        //获取操作日志情况 
        list($list,$html) = \CusSerManageService::getLogList(['type'=>1],'',5,'created_at','desc');
        foreach ($list['data'] as $k=>$v)
        {
            $temp = json_decode($v['content'],1);
            $list['data'][$k]['content'] = $temp;
        }
        return view('staff.customerservicemange.update',[
            'title'     => '客服管理',
            'slidebar'  => 'index',
            'data'      =>  $data,
            'list'      =>  $list,
            'html'      =>  $html,
            'show'      =>  $show
        ]);

    }

    /**
     * 删除操作
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CusSerManageService $cusSerManageService)
    {
        if ($cusSerManageService->del()) {
                return redirect('staff/CustomerServiceManage')->with('success', '删除成功!');
        }
        return redirect()->back();
    }

}