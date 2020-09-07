<?phP

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Module\LiShareEventModule;
use App\S\ShareEvent\LiRegisterService;
use Illuminate\Http\Request;
use Validator;

class FreeXCXController extends Controller
{
    /**
     * 申请报名
     * @param Request $request
     * @return mixed
     */
    public function apply(Request $request, $wid)
    {
        error('接口暂时弃用');
        //参数
        $input = $request->input();
        $mid = session('mid');
        $umid = session('umid');
        $li_register_service = new LiRegisterService();

        if ($li_register_service->isApplied($umid)) {
            //已经提交过了
            return redirect('/shop/freeXCX/applySuccess/' . $wid);
        }

        if (!empty($input['shareMid'])) {
            //保存分享参数 避免前端提交遗漏
            $request->session()->put('shareMid', $input['shareMid']);
            $request->session()->save();
        }

        if ($request->isMethod('post')) {
            //提交报名信息
            if ($li_register_service->isApplied($umid)) {
                success('领取成功');
            } else {
                //验证参数
                $rule = Array(
                    'name'         => 'required|between:1,10',
                    'phone'        => 'required|regex:mobile',
                    'company_name' => 'required|between:1,26',
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
                    'type' => 1,
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
                    $liSareEvent = new LiShareEventModule();
                    $liSareEvent->addsSellerkpi($input['phone'], session('shareMid') ?? 0);
                    $liSareEvent->registerSuccess($mid);
                    success('领取成功');
                }
            }
        }

        $mid = (new LiShareEventModule())->getShareMid($umid, $mid);
        $share_link =  $mid ?  $mid : '';

        return view('shop.freexcx.apply',array(
            'title' => '免费领小程序',
            'share_link' => $share_link
        ));
    }

    /**
     * 报名成功页
     */
    public function applySuccess(Request $request, $wid)
    {
        //参数
        $input = $request->input();
        $mid = session('mid');
        $umid = session('umid');

        if (!(new LiRegisterService())->isApplied($umid)) {
            //没注册过 重定向到注册页 加上分享参数
            $url = '/shop/freeXCX/apply/' . $wid;
            if (!empty($input['shareMid'])) {
                $url .= '?shareMid=' . $input['shareMid'];
            }
            return redirect($url);
        }

        $share_link =  $mid;

        return view('shop.freexcx.applySuccess',array(
            'title' => '成功领取小程序',
            'share_link' => $share_link
        ));
    }
}