<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/3/7
 * Time: 13:52
 */

namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Module\LiShareEventModule;
use App\S\ShareEvent\ActivityRegisterService;
use App\S\ShareEvent\LiRegisterService;
use App\S\ShareEvent\LiSalesmanService;
use Illuminate\Http\Request;
use App\S\Foundation\CreateShareImgService;
use App\S\Member\MemberService;
use Validator;

use App\S\File\FileInfoService;
use App\Services\Wechat\ApiService;

use Upyun\Config;
use Upyun\Signature;
use Upyun\Util;
use DB;

use App\Lib\Redis\MeetingRedis;

class MeetingController extends Controller
{

    public function getVideoSign(Request $request, FileInfoService $fileInfoService)
    {
        $config = new Config('huisoucn', 'phpteam', 'phpteam123456');
        $config->setFormApiKey('Mv83tlocuzkmfKKUFbz2s04FzTw=');
        $data['save-key'] = $request->input('save_key');
        $data['expiration'] = time() + 120;
        $data['bucket'] = 'huisoucn';
        $policy = Util::base64Json($data);
        $method = 'POST';
        $uri = '/' . $data['bucket'];
        $signature = Signature::getBodySignature($config, $method, $uri, null, $policy);
        echo json_encode(array(
            'policy' => $policy,
            'authorization' => $signature,
            'headimgUrl' => 'hsshop/' . config('app.env') . '/head' . session("mid") . '.jpg',
            'qrcode' => 'hsshop/' . config('app.env') . '/qrcodenew' . session("mid") . '.png'
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date $20180307
     * @desc 用户注册页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function register(Request $request, ActivityRegisterService $activityRegisterService, FileInfoService $fileInfoService)
    {
        $input = $request->input();
        $createShareImgService = new CreateShareImgService();
        $branch = config('app.env');
        if ($request->isMethod('post')) {
            $rules = array(
                'name' => 'required',
                'company' => 'required',
                'img' => 'required',
            );
            $messages = array(
                'company.required' => '请输入姓名',
                'company.required' => '请输入公司名称',
                'img.required' => '请上传图片',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return myerror($validator->errors()->first());
            }
            $data = [
                'mid' => session('mid'),
                'name' => $input['name'],
                'company' => $input['company'],
                'img' => $input['img'],
                'headimgUrl' => $input['headimgUrl'] ?? '',
                'qrcode' => $input['qrcode'] ?? '',
            ];
            if (empty($input['id'])) {
                $activityRegisterService->add($data) ? success() : error();
            } else {
                $activityRegisterService->update($input['id'], ['img' => $input['img']]) ? success() : error();
            }

        }
        $register = $activityRegisterService->getRegister(session('mid'));

        return view('shop.meeting.register', [
            'title' => '注册',
            'branch' => $branch,
            'register' => $register,
        ]);
    }

    public function defaulRegister(Request $request, ActivityRegisterService $activityRegisterService, FileInfoService $fileInfoService)
    {
        $input = $request->input();
        $createShareImgService = new CreateShareImgService();
        $branch = config('app.env');
        $register = $activityRegisterService->getRegister(session('mid'));

        if ($request->isMethod('post')) {
            $rules = array(
                'name' => 'required',
                'company' => 'required',
                'img' => 'required',
            );
            $messages = array(
                'company.required' => '请输入姓名',
                'company.required' => '请输入公司名称',
                'img.required' => '请上传图片',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return myerror($validator->errors()->first());
            }
            if (!isset($register['headimgUrl']) || empty($register['headimgUrl'])) {
                return myerror('头像不存在');
            }
            if (!isset($register['qrcode']) || empty($register['qrcode'])) {
                return myerror('二维码图片不存在');
            }

            $headimgStatus = $fileInfoService->hasUpyun($register['headimgUrl']);
            if ($headimgStatus !== true) {
                return myerror('头像不存在');
            }
            $qrcodeStatus = $fileInfoService->hasUpyun($register['qrcode']);
            if ($qrcodeStatus !== true) {
                return myerror('二维码图片不存在');
            }
            $imgStatus = $fileInfoService->hasUpyun($input['img']);
            if ($imgStatus !== true) {
                return myerror('上传图片不存在');
            }

            $data = [
                'name' => $input['name'],
                'company' => $input['company'],
                'img' => $input['img'],
            ];
            $register['name'] = $input['name'];
            $register['company'] = $input['company'];
            $register['img'] = $input['img'];

            $activityRegisterService->updateBymid(session('mid'), $data) ? success() : error();

        }

        return view('shop.meeting.defaultRegister', [
            'title' => '注册',
            'branch' => $branch,
            'register' => $register,
        ]);
    }

    /**
     * 图片上传
     * @update 何书哲 2019年06月20日 添加存在头像判断，否则使用默认头像
     */
    public function defaultUpload(ActivityRegisterService $activityRegisterService, FileInfoService $fileInfoService)
    {
        $branch = config('app.env');
        $mid = session("mid");
        $wid = session('wid');
        $uploadTime = time();

        $redis = new MeetingRedis();
        $status = $redis->get($mid);

        if ($status === true) {
            error('图片处理中');
        }
        $redis->set($mid); //设置重复上传时间设置

        $register = $activityRegisterService->getRegister($mid);
        $update = true;
        if (empty($register)) {
            $update = false;
        }

        $updateData = [];
        $headimgStatus = $qrcodeStatus = true;
        if (isset($register['headimgUrl']) && !empty($register['headimgUrl'])) {
            $headimgStatus = $fileInfoService->hasUpyun($register['headimgUrl']);
        }
        if (isset($register['qrcode']) && !empty($register['qrcode'])) {
            $qrcodeStatus = $fileInfoService->hasUpyun($register['qrcode']);
        }

        //上传头像
        if ($headimgStatus !== true || !isset($register['headimgUrl']) || empty($register['headimgUrl'])) {
            $headimgurl = 'https://www.huisou.cn/static/images/member_default.png';
            $headPath = 'hsshop/' . $branch . '/head' . $mid . '_' . $uploadTime . '.jpg';

            $memberService = new MemberService();
            $memberInfo = $memberService->getRowById(session('mid'));
            if ($memberInfo) {
                // update 何书哲 2019年06月20日 添加存在头像判断，否则使用默认头像
                if ($memberInfo['headimgurl'] && ($memberInfo['headimgurl'] <> 'http://127.0.0.1/public/static/images/member_default.png')) {
                    $headimgurl = $memberInfo['headimgurl'];
                }
            }

            $fileInfoService->upUpyunRegister($headimgurl, $headPath);
            $updateData['headimgUrl'] = $headPath;
        }

        //上传二维码
        if ($qrcodeStatus !== true || !isset($register['qrcode']) || empty($register['qrcode'])) {
            $qrcodePath = 'hsshop/' . $branch . '/qrcodenew' . $mid . '_' . $uploadTime . '.png';

            $createShareImgService = new CreateShareImgService();
            $result = $createShareImgService->qrcodeCreate($wid, $mid);
            if ($result['errCode'] <> 0) {
                error($result['errMsg']);
            }
            $qrcode = 'hsshop/image/qrcodes/meet/' . session('wid') . '-' . session('mid') . '/qrcode.png';

            $fileInfoService->upUpyunRegister($qrcode, $qrcodePath);
            $updateData['qrcode'] = $qrcodePath;
        }

        if ($update === true) {
            $activityRegisterService->updateBymid($mid, $updateData) ? success() : error();
        }
        $updateData['mid'] = $mid;
        $activityRegisterService->add($updateData) ? success() : error();
    }

    /**
     * 获取二维码并上传到又拍云
     * @param  FileInfoService $fileInfoService [description]
     * @return [type]                           [description]
     */
    public function getQrcode(FileInfoService $fileInfoService)
    {
        $branch = config('app.env');
        if ($branch <> 'local') {
            $createShareImgService = new CreateShareImgService();
            $result = $createShareImgService->qrcodeCreate(session('wid'), session('mid'));
            if ($result['errCode'] <> 0) {
                error($result['errMsg']);
            }
            $qrcode = 'hsshop/image/qrcodes/meet/' . session('wid') . '-' . session('mid') . '/qrcode.png';
            $fileInfoService->upUpyun($qrcode, 'hsshop/' . config('app.env') . '/qrcodenew' . session("mid") . '.png');
            success('', '', $qrcode);
        } else {
            success('', '', 'qrcodes/store/82');
        }
    }

    public function upWeixinHeadImg(FileInfoService $fileInfoService)
    {
        $headimgurl = 'https://www.huisou.cn/static/images/member_default.png';
        $memberService = new MemberService();
        $memberInfo = $memberService->getRowById(session('mid'));
        if ($memberInfo) {
            if ($memberInfo['headimgurl'] <> 'http://127.0.0.1/public/static/images/member_default.png') {
                $headimgurl = $memberInfo['headimgurl'];
            }
        }
        $fileInfoService->upUpyun($headimgurl, 'hsshop/' . config('app.env') . '/head' . session("mid") . '.jpg');
        success('', '', 'hsshop/' . config('app.env') . '/head' . session("mid") . '.jpg');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180307
     * @desc 获取战况信息和页面
     * @param Request $request
     * @param ActivityRegisterService $activityRegisterService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fighting(Request $request, ActivityRegisterService $activityRegisterService)
    {
        if ($request->isMethod('post')) {
            $registerData = $activityRegisterService->getRanking();
            success('操作成功', '', $registerData);
        }

        return view('shop.meeting.fighting', [
            'title' => '战况',
        ]);
    }

    /**
     * 生成海报页面
     * @return [type] [description]
     */
    public function posterPage(Request $request, $wid)
    {
        $data = ['img' => '', 'headimgUrl' => '', 'qrcode' => '', 'nickname' => ''];
        $mid = session('mid');
        $activityRegisterService = new ActivityRegisterService();
        $res = $activityRegisterService->getRegister($mid);
        if ($res) {

            $data['img'] = $res['img']; //上传的会场图
            $data['headimgUrl'] = $res['headimgUrl']; //微信头像
            $data['qrcode'] = $res['qrcode']; //二维码
            $data['nickname'] = base64_encode($res['name']);
            $data['nickname'] = str_replace('/', '|', $data['nickname']);

        } else {
            return redirect('/shop/meeting/register/' . $wid);
        }
        //获取用户昵称
        $memberService = new MemberService();
        $memberInfo = $memberService->getRowById($mid);
        $img = "https://upx.cdn.huisou.cn/huisoucn/background2.jpg!";
        $img .= "/watermark/url/" . base64_encode($data['headimgUrl'] . "!/format/webp/roundrect/100/fw/124") . "/align/north/margin/0x28";
        $img .= "/watermark/text/" . $data['nickname'] . "/font/simhei/align/north/margin/0x185/color/f9d9aa";
        $img .= "/watermark/url/" . base64_encode($data['img'] . "!/format/webp/roundrect/10/both/646x375") . "/align/south/margin/0x380";
        $img .= "/watermark/url/" . base64_encode($data['qrcode'] . "!/fwfh/218x218") . "/align/southeast/margin/43x58";
        \Log::info('=======' . $img);
        return view('shop.meeting.posterPage', [
            'title' => '生成海报',
            'img' => $img
        ]);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180307
     * @desc 推广用户注册
     */
    public function extensionRegister(Request $request, $wid)
    {
        $input = $request->input();
        $mid = session('mid');
        $umid = session('umid');
        $li_register_service = new LiRegisterService();

        if ($li_register_service->isApplied($umid, 3)) {
            //已经提交过了
            return redirect('/shop/meeting/registerSuccess/' . $wid);
        }

        if (!empty($input['shareMid'])) {
            //保存分享参数 避免前端提交遗漏
            $request->session()->put('shareMid', $input['shareMid']);
        }
        if ($request->isMethod('post')) {
            //提交报名信息
            if ($li_register_service->isApplied($umid, 3)) {
                success('领取成功');
            } else {
                //验证参数
                $rule = Array(
                    'name' => 'required|between:1,10',
                    'phone' => 'required|regex:mobile',
                    'company_name' => 'required|between:1,26',
                    'company_position' => 'between:1,20',
                );
                $message = Array(
                    'name.required' => '请输入姓名',
                    'name.between' => '姓名长度为1-10个字符',
                    'phone.required' => '请输入手机号码',
                    'phone.regex' => '手机号码格式不正确',
                    'company_name.required' => '请输入公司名称',
                    'company_position.required' => '请输入公司地址',
                    'company_name.between' => '公司名称长度为1-26个字符',
                    'company_position.between' => '职务长度为1-20个字符',
                );
                $validator = Validator::make($input, $rule, $message);
                if ($validator->fails()) {
                    error($validator->errors()->first());
                }
                $data = [
                    'type' => 3,
                    'umid' => $umid,
                    'mid' => $mid,
                    'name' => $input['name'],
                    'phone' => $input['phone'],
                    'company_name' => $input['company_name'],
                    'company_position' => $input['company_position']
                ];
                if (!$li_register_service->model->insertGetId($data)) {
                    error('领取失败');
                } else {
                    (new LiShareEventModule())->courseGetSuccess($mid);
                    success('领取成功');
                }
            }
        }

        $mid = (new LiShareEventModule())->getShareMid($umid, $mid);
        $share_link = $mid ? $mid : '';

        return view('shop.meeting.extensionRegister', array(
            'title' => '免费领取课程',
            'share_link' => $share_link
        ));
    }


    public function registerSuccess(Request $request, $wid)
    {
        return view('shop.meeting.registerSuccess', array(
            'title' => '领取成功',
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function getInvitationData(Request $request)
    {
        $sql = 'SELECT ls.id,ls.`name`,ls.mobile,COUNT(*) as num FROM ds_li_salesman as ls LEFT JOIN ds_admin_sellerkpi as ads  ON ls.mobile=ads.manage_mid LEFT JOIN ds_li_register as lr ON ads.mid=lr.phone WHERE ads.id IS NOT NULL AND lr.id IS NOT NULL GROUP BY ls.mobile  ORDER BY num DESC,ls.id ASC';
        $res = DB::select($sql);
        $data = json_decode(json_encode($res), true);
        return view('shop.meeting.getInvitationData', array(
            'title' => '邀请数据',
            'data' => $data,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function invitationDetail(Request $request)
    {
        $sql = 'SELECT lr.id,lr.`name`,lr.phone,lr.company_name,lr.company_address,lr.company_position FROM ds_admin_sellerkpi as ads LEFT JOIN ds_li_register AS lr ON ads.mid=lr.phone WHERE ads.manage_mid=' . $request->input('mobile');
        $res = DB::select($sql);
        $data = json_decode(json_encode($res), true);
        return view('shop.meeting.invitationDetail', array(
            'title' => '邀请详情',
            'data' => $data,
            'name' => $request->input('name'),
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function seeDetail()
    {
        $umid = session('umid');
        $res = (new LiSalesmanService())->getList(['umid' => $umid]);
        if (!$res) {
            error('您还没有注册，赶紧去注册吧');
        }
        $salesManData = current($res);
        return redirect('/shop/meeting/invitationDetail?mobile=' . $salesManData['mobile'] . '&name=' . $salesManData['name']);
    }

}
