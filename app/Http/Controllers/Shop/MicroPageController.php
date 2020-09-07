<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/13
 * Time: 14:26
 */

namespace App\Http\Controllers\shop;
use App\Http\Controllers\Controller;
use App\Module\LiShareEventModule;
use App\S\File\UserFileService;
use App\S\Foundation\VerifyCodeService;
use App\S\Member\MemberService;
use MemberCardService;
use Illuminate\Http\Request;
use MallModule as MicroPageStoreService;
use WeixinService;
use Bi;
use App\S\PublicShareService;
use  MicroPageService;

use App\S\ShareEvent\LiRegisterService;
use Validator;
use App\S\Weixin\ShopService;

class MicroPageController extends Controller
{
    /**
     * todo 移动端微页面
     * @param Request $request
     * @param StoreService $storeService
     * @param int $wid
     * @param int $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-13
     * @update 张永辉 2018年6月29日  如果发现微页面已经设置成主页了，直接显示主页不显示微页面
     * @update 张永辉 2018年7月9日   微页面是否是主页面修改
     * @update 吴晓平 2018年09月月10 微信页分享内容优化
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function index(Request $request,ShopService $shopService,$wid=0,$id=0) {

        $resut = MicroPageService::getRowById($id);
        if ($resut['errCode'] == 0 && !empty($resut['data']['is_home'])){
            return redirect('/shop/index/'.$wid);
        }

        //add by 吴晓平 2018.09.10 微信页分享内容优化
        $shareData = [];
        if ($resut['errCode'] == 0 && $resut['data'] && $resut['data']['share_title']) {
            $shareData['share_title'] = $resut['data']['share_title'];
            $shareData['share_desc']  = $resut['data']['share_desc'];
            $shareData['share_img']   = imgUrl().$resut['data']['share_img'];
        }
        //获取店铺名 logo等
        //$shop = WeixinService::getStageShop($wid);
        $shop = $shopService->getRowById($wid);
        //add by zhangyh 20180415
        if (in_array($wid,config('app.li_wid'))){
            return view('shop.groupsmeeting.index', [
                'wid'       =>$wid,
                'id'        =>$id,
                'shop'      => $shop,
                'shareData' => $shareData ? $shareData : (new PublicShareService())->publicShareSet($wid)
            ]);
        } //end zhangyh
        return view('shop.micropage.index', [
            'wid'       =>$wid,
            'id'        =>$id,
            'shop'      => $shop,
            'shareData' => $shareData ? $shareData : (new PublicShareService())->publicShareSet($wid)
        ]);
    }

    /**
     * 无需微信访问微页面
     * @param Request $request 参数类
     * @param int $wid 店铺id
     * @param int $id 微页面id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @author 陈文豪 2018年05月05日
     */
    public function showPage(Request $request,$wid=0,$id=0,$type=0)
    {
        $li_register_service = new LiRegisterService();
        if ($request->isMethod('post')) {
            $input = $request->input();
            //验证参数
            $rule = Array(
                'name'         => 'required|between:1,10',
                'phone'        => 'required|regex:mobile',
                'company_name' => 'between:1,26',
                'company_position' => 'between:1,20',
            );
            $message = Array(
                'name.required'    => '请输入姓名',
                'name.between'     => '姓名长度为1-10个字符',
                'phone.required'    => '请输入手机号码',
                'phone.regex'       => '手机号码格式不正确',
                'company_name.required'    => '请输入公司名称',
                'company_position.required' => '请输入公司地址',
                'company_name.between' => '公司名称长度为1-26个字符',
                'company_position.between' => '职务长度为1-20个字符',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            $data = [
                'type' => $type,
                'umid' => 0,
                'mid' => 0,
                'name' => $input['name'],
                'phone' => $input['phone'],
                'company_name' => $input['company_name'],
                'company_position' => $input['company_position']
            ];
            if (!$li_register_service->model->insertGetId($data)) {
                error('领取失败');
            } else {
                success('领取成功');
            }
        }

        return view('shop.web.index', [
            'wid'       =>$wid,
            'id'        =>$id,
            'shop'      => [],
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ]);
    }

    /**
     * 7月9日活动提交页
     * @param Request $request 参数类
     * @param int $wid 店铺id
     * @param int $id 微页面id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @return json|view
     * @author 许立 2018年07月09日
     * @update 许立 2018年07月10日 判断是否注册过了 注册商家账号 发送短信
     * @update 许立 2018年07月10日 手机号和公司名唯一 注册过的邀请人才发送短信
     * @update 许立 2018年07月10日 保存店铺id
     * @update 许立 2018年07月10日 更新已注册已发送短信字段
     * @update 许立 2018年07月11日 增加微信号字段 非必填;是否已注册
     * @update 许立 2018年07月11日 短信发送成功标记
     * @update 许立 2018年07月12日 短信发送模板修改
     */
    public function freeApply(Request $request, $wid, $id, $type)
    {
        $li_register_service = new LiRegisterService();
        $mid = session('mid');
        if ($request->isMethod('post')) {
            // 验证参数
            $input = $request->input();
            $rule = Array(
                'name'         => 'required|between:1,10',
                'phone'        => 'required|regex:mobile',
                'company_name' => 'between:1,26',
                'company_position' => 'between:1,20',
            );
            $message = Array(
                'name.required'    => '请输入姓名',
                'name.between'     => '姓名长度为1-10个字符',
                'phone.required'    => '请输入手机号码',
                'phone.regex'       => '手机号码格式不正确',
                'company_name.required'    => '请输入公司名称',
                'company_position.required' => '请输入公司地址',
                'company_name.between' => '公司名称长度为1-26个字符',
                'company_position.between' => '职务长度为1-20个字符',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }

            // 判断是否注册过了
            if ($li_register_service->getRow($mid, $wid, $type)) {
                error('你在该已经注册过了');
            }
            // 手机号和公司名唯一
            if ($li_register_service->isRegisteredByPhone($wid, $type, $input['phone'])) {
                error('您已领取过，请勿重复领取');
            }
            if ($li_register_service->isRegisteredByCompany($wid, $type, $input['company_name'])) {
                error('您已领取过，请勿重复领取');
            }

            // 获取当前用户的上级mid
            $member_service = new MemberService();
            $parent_mid = $member_service->getRowById($mid)['pid'] ?? 0;

            // 入库数据
            $data = [
                'wid' => $wid,
                'type' => $type,
                'umid' => session('umid'),
                'mid' => $mid,
                'name' => $input['name'],
                'phone' => $input['phone'],
                'company_name' => $input['company_name'],
                'company_position' => $input['company_position'],
                'parent_mid' => $parent_mid,
                'wechat_name' => $input['wechat_name'] ?? '', // 微信号 非必填
            ];
            $new_id = $li_register_service->model->insertGetId($data);
            if (!$new_id) {
                error('领取失败');
            } else {
                $message_service = new VerifyCodeService();
                // 注册商家后台账号并发送短信
                $user_result = (new LiShareEventModule())->registerByMobile([$input['phone']]);
                $message_result = $message_service->groupPurchaseNoitice($input['phone'], [$input['phone'], '12345678'], 16);
                // 更新已注册已发送短信字段
                $update = [];
                $user_result && $update['is_register'] = 1;
                // 短信发送成功标记
                $message_result->statusCode == '000000' && $update['is_sms'] = 2;
                if ($update) {
                    $li_register_service->model
                        ->where('id', $new_id)
                        ->update($update);
                }
                if ($type == 9) {
                    // 增加类型$type=9 7月9日活动 上级邀请人数达到2 则发送短信
                    $row = $li_register_service->getRow($parent_mid, $wid, $type);
                    if ($row && $li_register_service->inviteCount($parent_mid, $wid, $type) == 2) {
                        $message_service->groupPurchaseNoitice($row['phone'], [$row['phone'], '12345678'], 17);
                    }
                }
                success('领取成功');
            }
        }

        return view('shop.activity.freeApply', [
            'wid'       =>$wid,
            'id'        =>$id,
            'shop'      => [],
            'shareData' => (new PublicShareService())->publicShareSet($wid),
            'isRegistered' => $li_register_service->getRow($mid, $wid, $type) ? 1 : 0, // 是否已注册
        ]);
    }

    /**
     * 免费领会搜云活动推广结果页
     * @param int $wid 店铺id
     * @return view
     * @author 许立 2018年07月09日
     */
    public function freeApplyResult($wid, $id, $type)
    {
        return view('shop.activity.freeApplyResult', [
            'wid'       =>$wid,
            'id'        =>$id,
            'shop'      => [],
            'shareData' => (new PublicShareService())->publicShareSet($wid)
        ]);
    }

    /**
     * 免费领会搜云活动用户滚动列表接口
     * @param int $wid 店铺id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @return json
     * @author 许立 2018年07月09日
     */
    public function freeApplyUserList($wid, $type)
    {
        if (empty($wid) || empty($type)) {
            error('参数不完整');
        }
        success('', '', (new LiRegisterService())->sliderList($wid, $type));
    }

    /**
     * 邀请的好友页面
     * @param int $wid 店铺id
     * @param int $id 微页面id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @return view
     * @author 许立 2018年07月10日
     * @update 许立 2018年07月10日 返回邀请列表
     * @update 许立 2018年07月11日 返回图片
     */
    public function freeApplyInviteList($wid, $id, $type)
    {
        // 返回图片
        $image_path_list = [];
        // 我的文件分组id
        $file_group_id = 105;
        if (config('app.env') == 'dev') {
            $file_group_id = 32;
        } elseif (config('app.env') == 'prod') {
            $file_group_id = 287;
        }
        $files = (new UserFileService())->getUserFileByClassify($file_group_id);
        if ($files[0]['total']) {
            foreach ($files[0]['data'] as $image) {
                if (!empty(imgUrl($image['FileInfo']['path']))) {
                    $image_path_list[] = imgUrl($image['FileInfo']['path']);
                }
            }
        }

        return view('shop.activity.freeApplyInviteList', [
            'wid'       => $wid,
            'id'        => $id,
            'shop'      => [],
            'shareData' => (new PublicShareService())->publicShareSet($wid),
            'list'      => (new LiRegisterService())->inviteList(session('mid'), $wid, $type), // 邀请列表
            'image_path_list' => $image_path_list,
        ]);
    }

    /**
     * todo 获取自定义微页面信息
     * @param Request $request
     * @param StoreService $storeService
     * @param int $wid
     * @param int $id
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-08
     */
    public function indexPage(Request $request,$wid=0,$id=0)
    {
        $filter=$request->input('filter')??true;
        $returnData = array('errCode' => 0, 'errMsg' => '');
        $errMsg='';
        if(empty($wid))
        {
            $errMsg.='$wid为空';
        }
        if(empty($id))
        {
            $errMsg.='$id为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data=[];
        $data['wid']=$wid;
        $data['id']=$id;
        $return = MicroPageStoreService::processMicroPageData($data,$filter);
        if (isset($return['data']['container'])) {
            $return['data']['container'] = str_replace('videoreplace',   '<video width=\"100%\" height=\"280\" controls=\"controls\" poster=\"/ueditor/php/upload/image/20171103/1509689299520580.jpg\"><source src=\"https://upx.cdn.huisou.cn/wscphp/music/aa.mp4 \" type=\"video/mp4\"></video>', $return['data']['container']);
        }

        if ($return['errCode'] == 0) {
            Bi::micPageView($wid, session('mid'), $id); 
        }
        return $return;
    }

    /**
     *  无需微信访问微页面
     * @param Request $request
     * @param StoreService $storeService
     * @author cwh
     * @date 2018年05月05日
     */
    public function showIndexPage(Request $request,$wid=0,$id=0)
    {
        $filter=$request->input('filter')??true;
        $returnData = array('errCode' => 0, 'errMsg' => '');
        $errMsg='';
        if(empty($wid))
        {
            $errMsg.='$wid为空';
        }
        if(empty($id))
        {
            $errMsg.='$id为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data=[];
        $data['wid']=$wid;
        $data['id']=$id;
        $return = MicroPageStoreService::processMicroPageData($data,$filter);
        return $return;
    }

    /**
     * todo 微页面预览
     * @param Request $request
     * @param int $wid
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author jonzhang
     * @date 2017-06-27
     */
    public function preview(Request $request,$wid=0,$id=0) {
        return view('shop.micropage.preview', [
            'wid'=>$wid,
            'id'=>$id
        ]);
    }

    /**
     * todo 预览请求信息
     * @param Request $request
     * @param StoreService $storeService
     * @param int $wid
     * @param int $id
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-05-08
     */
    public function previewPage(Request $request,$wid=0,$id=0)
    {
        $filter=$request->input('filter')??true;
        $returnData = array('errCode' => 0, 'errMsg' => '');
        $errMsg='';
        if(empty($wid))
        {
            $errMsg.='$wid为空';
        }
        if(empty($id))
        {
            $errMsg.='$id为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data=[];
        $data['wid']=$wid;
        $data['id']=$id;
        return MicroPageStoreService::processMicroPageData($data,$filter);
    }
}