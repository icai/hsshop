<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Jobs\ExportMallData;
use App\Module\ExportModule;
use App\Module\GroupsRuleModule;
use App\Module\MeetingGroupsRuleModule;
use App\S\Groups\GroupsDetailService;
use App\S\Product\RemarkService;
use App\S\ShareEvent\LiSalesmanService;
use App\S\ShareEvent\SalesmanStatisticService;
use App\S\Staff\AccountService;
use Illuminate\Http\Request;
use App\S\ShareEvent\AdminSellerkpiService;
use App\S\ShareEvent\LiRegisterService;
use App\Model\AdminSellerkpi;
use DB;

class SellerController extends Controller {
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $input = $request->input();
        $lisalesmanService = new LiSalesmanService();
        $where = [];
        if (!empty($input['name'])){
            $where['name'] = $input['name'];
        }
        if (!empty($input['mobile'])){
            $where['mobile'] = $input['mobile'];
        }

        $sqlwhere = '1=1 ';
        if (!empty($input['starttime']) && !empty($input['endtime'])){
            $sqlwhere .= ' AND intime>= "'.$input['starttime'].'" AND intime<= "'.$input['endtime'].'"';
        }

        $sql = 'SELECT topid, COUNT(id) as num FROM ds_salesman_statistic WHERE '.$sqlwhere.' GROUP BY topid';
        $res = DB::select($sql);
        $topdata = json_decode(json_encode($res),true);
        $sql = 'SELECT topid, COUNT(id) as num FROM ds_salesman_statistic WHERE '.$sqlwhere.' AND is_open_groups=1 GROUP BY topid ';
        $groupsData = DB::select($sql);
        $groupsData = json_decode(json_encode($groupsData),true);
        $groupsModule = new GroupsRuleModule();
        $groupsData =  $groupsModule->dealKey($groupsData,'topid');
        $topdata = $groupsModule->dealKey($topdata,'topid');

        if (!empty($input['tag']) && $input['tag'] == 1){
            $data = $lisalesmanService->getList($where);
            $this->exportSellerMan($groupsData,$topdata,$data);
        }

        $data = $lisalesmanService->getlistPage($where);
        foreach ($data[0]['data'] as $key=>&$val){
            $val['num'] = $topdata[$val['wx_mid']]['num']??0;
            $val['groupsNum'] = $groupsData[$val['wx_mid']]['num']??0;
        }
        $salesmanStatisticService = new SalesmanStatisticService();
        $countWhere = [];
        if (!empty($input['starttime']) && !empty($input['endtime'])){
            $countWhere['intime'] = ['between', [$input['starttime'],$input['endtime'] ]];
        }


        $sum = $salesmanStatisticService->count($countWhere);
        $isJoinNum = $salesmanStatisticService->count(array_merge($countWhere,['is_open_groups'=>'1']));
        return view('staff.seller.index',array(
            'title'     => '业务员跟单',
            'sliderba' => 'account',
            'data'     => $data,
            'sum'       => $sum,
            'isJoinNum'=>$isJoinNum,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180522
     * @desc 导出业务员信息
     * @param $groupsData
     * @param $topdata
     */
    public function exportSellerMan($groupsData,$topdata,$data)
    {
        foreach ($data as $key=>&$val){
            $val['num'] = $topdata[$val['wx_mid']]['num']??0;
            $val['groupsNum'] = $groupsData[$val['wx_mid']]['num']??0;
        }
        $title = [
            'name'      => '业务员姓名',
            'mobile'    => '业务员电话',
            'num'       => '邀请数量',
            'groupsNum'    => '参团数量',
        ];
        (new ExportModule())->derive(['data'=>$data,'title'=>$title],'业务员邀请信息');
        exit();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180516
     * @desc 刷新拼团信息
     */
    public function refresh()
    {
        $sql = 'SELECT ads.mid FROM ds_salesman_statistic as ads LEFT JOIN  ds_groups_detail as gd ON ads.mid = gd.member_id WHERE ads.is_open_groups=0 AND gd.id IS NOT NULL GROUP BY mid';
        $data = DB::select($sql);
        $data = json_decode(json_encode($data),true);
        $ids = array_column($data,'mid');
        if ($ids){
            $salesmanService = new SalesmanStatisticService();
            $res = $salesmanService->getList(['mid'=>['in',$ids]]);
            if ($res){
                $upids = array_column($res,'id');
                foreach ($upids as $val){
                    $salesmanService->update($val,['is_open_groups'=>'1']);
                }
            }
        }
        return redirect('/staff/seller/index');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180516
     * @desc 获取邀请详情
     */
    public function getSaleManDetail(Request $request)
    {
        $input = $request->input();
        $salesmanStatisticService = new SalesmanStatisticService();
        $id = $input['id']??'';
        $where = [];
        if (!empty($id)){
            $where['topid'] = $id;
        }
       $tmpwhere = $this->getWhere($input);
       $where = array_merge($where,$tmpwhere);
       if (!empty($input['tag']) && $input['tag'] == 1){
            $this->export($where);
       }
        $data = $salesmanStatisticService->getlistPage($where);
        $salesmanData  = (new LiSalesmanService())->getList();
        $salesmanData = (new GroupsRuleModule())->dealKey($salesmanData,'wx_mid');
        foreach ($data[0]['data'] as &$val){
            $val['salesman'] = $salesmanData[$val['topid']]??'';
        }
        return view('staff.seller.getSaleManDetail',array(
            'title'     => '业务员跟单',
            'sliderba' => 'account',
            'data'     => $data,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180522
     * @desc 导出功能
     */
    public function export($where)
    {
        $salesmanStatisticService = new SalesmanStatisticService();
        $data = $salesmanStatisticService->getList($where);
        $salesmanData  = (new LiSalesmanService())->getList();
        $salesmanData = (new GroupsRuleModule())->dealKey($salesmanData,'wx_mid');
        foreach ($data as &$val){
            $val['saller_name'] = $salesmanData[$val['topid']]['name']??'';
            $val['saller_mobile'] = $salesmanData[$val['topid']]['mobile']??'';
        }
        $mid = array_column($data,'mid');
        $result = $this->dealRemark($mid);
        foreach ($data as &$item){
            $item['remark']  = $result[$item['mid']]??'';
        }

        $title = [
            'nickname'      => '昵称',
            'headimgurl'    => '头像',
            'sex'            => '性别：0未知；1男；2女',
            'intime'        => '注册时间',
            'created_at'    => '邀请时间',
            'mid'            => '用户编号',
            'is_open_groups'=> '是否参团',
            'saller_name'   => '业务员姓名',
            'saller_mobile' => '业务员电话',
            'remark'         => '留言信息',
        ];
        $data = ['data'=>$data,'title'=>$title];
        $name = '邀请人信息';
        (new ExportModule())->derive($data,$name);

    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  2018
     * @desc
     * @param $mid
     */
    public function dealRemark($mid)
    {
        $data = (new MeetingGroupsRuleModule())->getRemark(0,[],$mid);
        $result = [];
        foreach ($data['data'] as $val){
            $result[$val['member_id']] = $result[$val['member_id']]??'';
            foreach ($val['remark'] as $item){
                $result[$val['member_id']] .= $item['title'].':'.$item['content'].",";
            }
            if ($val['remark']){
                $result[$val['member_id']] .= ';';
            }

        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180516
     * @desc  获取搜索条件
     */
    public function getWhere($input)
    {

        $where = [];
        if (!empty($input['nickname'])){
            $where['nickname'] = $input['nickname'];
        }
        if (!empty($input['is_open_groups'])){
            $where['is_open_groups'] = $input['is_open_groups']-1;
        }
        if (!empty($input['mobile'])){
            $remarkService = new RemarkService();
            $res = $remarkService->getList(['content'=>$input['mobile'],'type'=>'7']);
            if (!$res){
                $where['mid'] = '';
            }else{
                $remakNOs = array_column($res,'remark_no');
                $groupsDetailData = (new GroupsDetailService())->getListByWhere(['remark_no'=>['in',$remakNOs]]);
                if (!$groupsDetailData){
                    $where['mid'] = '';
                }else{
                    $mids = array_column($groupsDetailData,'member_id');
                    $where['mid'] = ['in',$mids];
                }
            }
        }
        if (!empty($input['name'])){
            $remarkService = new RemarkService();
            $res = $remarkService->getList(['content'=>$input['name'],'type'=>'0']);
            if (!$res){
                $where['mid'] = '';
            }else{
                $remakNOs = array_column($res,'remark_no');
                $groupsDetailData = (new GroupsDetailService())->getListByWhere(['remark_no'=>['in',$remakNOs]]);
                if (!$groupsDetailData){
                    $where['mid'] = '';
                }else{
                    $mids = array_column($groupsDetailData,'member_id');
                    $where['mid'] = ['in',$mids];
                }
            }
        }
        if (!empty($input['starttime']) && !empty($input['endtime']) ){
            $where['intime'] = ['between', [$input['starttime'],$input['endtime'] ]];
        }
        return $where;
    }


    /**
     * 修改分组  用户id  分组名称name
     * @param Request $request
     * @param AdminSellerkpiService $adminSellerkpiService
     * @param LiSalesmanService $liSalesmanService
     */
    public function updateGroup(Request $request,AdminSellerkpiService $adminSellerkpiService,LiSalesmanService $liSalesmanService)
    {
        if($request->isMethod('post'))
        {
            $ids =$request->input('ids') ?? [];
            $sellerId =$request->input('sellerId') ?? '';
            if(!$ids)
            {
                error('请勾选要修改的客户');
            }
            if(!$sellerId)
            {
                error('请勾选要修改的分组');
            }
            $where['id'] = ['in',$ids];
            $sellerkpi = $adminSellerkpiService->getList($where);
            $sellerData = $liSalesmanService->model->where(['id'=>$sellerId])->first();
            $re = '';
            if($sellerData)
            {
                $sellerData = $sellerData->toArray();
                if($sellerkpi)
                {
                    foreach($sellerkpi as &$v)
                    {
                        $v['manage_mid'] = $sellerData['mobile'];
                        $v['SellerkpiSalesman'] = json_decode($v['SellerkpiSalesman'],true);
                        $v['SellerkpiRegister'] = json_decode($v['SellerkpiRegister'],true);
                        $v['SellerkpiSalesman']['id'] = $sellerData['id'];
                        $v['SellerkpiSalesman']['umid'] = $sellerData['umid'];
                        $v['SellerkpiSalesman']['wx_mid'] = $sellerData['wx_mid'];
                        $v['SellerkpiSalesman']['name'] = $sellerData['name'];
                        $v['SellerkpiSalesman']['mobile'] = $sellerData['mobile'];
                        $re = $adminSellerkpiService->update($v['id'],$v);
                    }
                }
            }
            if($re)
            {
                success();
            }else{
                error();
            }
        }
        list($data,$page) = (new LiSalesmanService())->getlistPage();
        success('','',$data);
    }

    /**
     * @auth fuguowei
     * @date 20180305
     * @desc 修改有效单字段数据
     * @param Request $request
     */
    public function  updateValid(Request $request,AdminSellerkpiService $adminSellerkpiService)
    {
        $id =$request->input('id') ?? 0;
        $valid =$request->input('valid') ?? 1;
        if(!$id)
        {
            error('请选择要修改的数据');
        }
        if($valid)
        {
            //改为无效单
            $result = $adminSellerkpiService->update($id,['is_valid'=>'0']);
        }else{
            //改为有效单
            $result = $adminSellerkpiService->update($id,['is_valid'=>'1']);
        }
        if($result){
            success();
        }else{
            error();
        }
    }

    /**
     * @auth fuguowei
     * @date 20180305
     * @desc 销售列表
     * @param Request $request
     */
    public function sellerIndex(Request $request,AdminSellerkpiService $adminSellerkpiService)
    {
        $liSales = new LiSalesmanService();
        list($liSalesMan,$page) = $liSales->getlistPage();
        //获取本月起始时间
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        //获取今天结束时间
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $where['addtime'] = ['in',['$beginThismonth','$endToday']];
        if($liSalesMan['data'])
        {
            foreach($liSalesMan['data'] as &$value)
            {
                $data = $adminSellerkpiService->getList(['manage_mid'=>$value['mobile']]);
                if($data)
                {
                    $value['total'] = count($data);
                    $value['totalValid'] = 0;
                    $value['monthValid'] = 0;
                    $value['month'] = 0;
                    foreach($data as $v)
                    {
                        //总有效单数
                        if($v['is_valid'])
                        {
                            $value['totalValid']++;
                        }
                        //当月有效单数
                        if($v['is_valid'] && $beginThismonth<= $v['addtime'] && $v['addtime']<= $endToday)
                        {
                            $value['monthValid']++;
                        }
                        //当月总单数
                        if($beginThismonth<= $v['addtime'] && $v['addtime']<= $endToday)
                        {
                            $value['month']++;
                        }
                    }
                }
            }
        }
          return view('staff.seller.sellerIndex',array(
              'title'       => '业务员跟单',
              'sliderba'    => 'account',
              'liSalesMan' 	=> $liSalesMan['data'] ?? [],
              'page'        => $page,
          ));
    }

    /**
     * @auth fuguowei
     * @date 20180305
     * @desc 删除销售
     * @param Request $request
     */
    public function del(Request $request,LiSalesmanService $liSalesmanService,AdminSellerkpiService $adminSellerkpiService)
    {
        $id =$request->input('id') ?? 0;
        $mobile =$request->input('mobile') ?? '';
        if(!$id)
        {
            error('请选择要删除的选项');
        }
        $data = $adminSellerkpiService->getList(['manage_mid'=>$mobile]);
        $re = '';
        foreach($data as $v)
        {
            $v['grade'] = 1;
            $v['prev_mid'] = 0;
            $v['SellerkpiRegister'] = json_decode($v['SellerkpiRegister'],true);
            $v['top_mid'] = 0;
            $v['SellerkpiSalesman'] = '';
            $v['manage_mid'] = 0;
            $re = $adminSellerkpiService->update($v['id'],$v);
        }
        $res = $liSalesmanService->del($id);
        if($re)
        {
            success();
        }else{
            error();
        }
    }


    /**
     * 销售管理客户导出
     * @param AdminSellerkpiService $adminSellerkpiService
     */
    public function exportSellerkpi(AdminSellerkpiService $adminSellerkpiService)
    {
        $sellerkpi = $adminSellerkpiService->getList();
        if($sellerkpi)
        {
            $ids=[];
            foreach($sellerkpi as $value)
            {
                if($value['SellerkpiRegister'])
                {
                    $ids[] =$value['id'];
                }
            }
            $datas['id']=['in',$ids];
            $sellerkpi = $adminSellerkpiService->getList($datas);
            foreach ($sellerkpi as &$seller){
                if($seller['addtime'])
                {
                    $seller['addtime']=date('Y-m-d h:i:s',$seller['addtime']);
                }else{
                    $seller['addtime']=0;
                }
                $seller['SellerkpiRegister'] = json_decode($seller['SellerkpiRegister'],true);
                $seller['SellerkpiSalesman'] = json_decode($seller['SellerkpiSalesman'],true);
            }
        }
            $adminSellerkpiService->export($sellerkpi);
    }

    /**
     * 销售导出
     * @param AdminSellerkpiService $adminSellerkpiService
     */
    public function exportSalesman(AdminSellerkpiService $adminSellerkpiService)
    {
        $liSales = new LiSalesmanService();
        $liSalesMan = $liSales->getList();
        //获取本月起始时间
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        //获取今天结束时间
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $where['addtime'] = ['in',['$beginThismonth','$endToday']];
        if($liSalesMan)
        {
            foreach($liSalesMan as &$value)
            {
                $data = $adminSellerkpiService->getList(['manage_mid'=>$value['mobile']]);
                if($data)
                {
                    $value['total'] = count($data);
                    $value['totalValid'] = 0;
                    $value['monthValid'] = 0;
                    $value['month'] = 0;
                    foreach($data as $v)
                    {
                        //总有效单数
                        if($v['is_valid'])
                        {
                            $value['totalValid']++;
                        }
                        //当月有效单数
                        if($v['is_valid'] && $beginThismonth<= $v['addtime'] && $v['addtime']<= $endToday)
                        {
                            $value['monthValid']++;
                        }
                        //当月总单数
                        if($beginThismonth<= $v['addtime'] && $v['addtime']<= $endToday)
                        {
                            $value['month']++;
                        }
                    }
                }
            }
        }
        $liSales->exportSalesman($liSalesMan);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180521
     * @desc 按照参与信息查询
     */
    public function joinInfo(Request $request)
    {
        $mid = $request->input('mid');
        $groupsDetailService = new GroupsDetailService();
        $res = $groupsDetailService->getListByWhere(['member_id'=>$mid]);
        if (!$res){
            error('该用户未参加拼团活动');
        }
        $remarkNo = [];
        foreach ($res as $val){
            $remarkNo[] = $val['remark_no'];
        }
        $data = (new MeetingGroupsRuleModule())->getRemark(0,[],$mid);
        return view('staff.seller.joinInfo',array(
            'title'     => '业务员跟单',
            'sliderba' => 'account',
            'data'     => $data,
        ));
    }



}