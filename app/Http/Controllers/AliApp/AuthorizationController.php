<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/7/23
 * Time: 9:07
 */

namespace App\Http\Controllers\AliApp;


use App\Http\Controllers\Controller;
use App\Jobs\LoginStatistics;
use App\Module\AliApp\AliAppModule;
use App\Module\AliApp\AliClientModule;
use App\Module\AliApp\AlipayOpenAuthTokenAppRequest;
use App\S\AliApp\AliappConfigService;
use Illuminate\Http\Request;
use Validator;

class AuthorizationController extends Controller
{

    /**
     * 商户授权回调接口
     * @param Request $request
     * @author 张永辉 2018年7月23日
     */
    public function callBack(Request $request)
    {
        $appAuthCode = $request->input('app_auth_code');
        $wid = $request->input('state');
        \Log::info($request->input());
        $aliClientModule = new AliClientModule();
        $requestParam = new AlipayOpenAuthTokenAppRequest();
        $aliAppModule = new AliAppModule();
        $param = [
            'grant_type'    => 'authorization_code',
            'code'           => $appAuthCode,
        ];
        $requestParam->setBizContent(json_encode($param));
        $result = $aliClientModule->execute($requestParam);
        \Log::info(json_decode(json_encode($result),true));
        $responseNode = str_replace(".", "_", $requestParam->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000){
            $res = $aliAppModule->addAliAppConfig($result->$responseNode,$wid);
            if ($res){
                success('授权成功');
            }else{
                error('授权失败');
            }
        } else {
            \Log::info(json_decode(json_encode($result),true));
        }
    }



    public function AsynchronousNotification(Request $request,AliappConfigService $aliappConfigService)
    {
        \Log::info('异步回调接口');
        \Log::info($request->input());
        $input = $request->input();
        if (!empty($input['msg_method']) && $input['msg_method'] == 'alipay.open.auth.appauth.cancelled'){
            $params = json_decode($input['biz_content'],true);
            $res = $aliappConfigService->getList(['auth_app_id'=>$params['auth_app_id']]);
            if (!$res){
                echo 'success';
                exit();
            }
            $res = current($res);
            //$result = $aliappConfigService->del($res['id']);
            $result = $aliappConfigService->update($res['id'],['is_delete'=>1]);
            \Log::info('商家解除授权');
            \Log::info($res);
            if ($result){
                echo 'success';
                exit();
            }else{
                echo 'fail';
                exit();
            }
        }
    }

    public function getUrl(){
        $url = config('app.url').'aliapp/callBack';
        $returnurl = urlencode($url);
        $url = 'https://openauth.alipay.com/oauth2/appToAppBatchAuth.htm?app_id='.config('app.ali_app_id').'&application_type=TINYAPP,WEBAPP&redirect_uri='.$returnurl.'&state='.session('wid');
        return redirect($url);
    }


    /**
     * 用户授权登陆
     * @param Request $request
     * @return mixed
     * @author 张永辉
     * @update 何书哲 2018年9月19日 登录日志发送数据中心
     */
    public function login(Request $request){
        if ($request->isMethod('post')){
            $input = $request->input();
            $rule = Array(
                'authCode'          => 'required',
                'wid'                => 'required',
                'aliappConfigId'    => 'required',

            );
            $message = Array(
                'authCode.required'     => '授权code不能为空',
                'wid.required'           => 'wid不能为空',
                'aliappConfigId.required'=> '配置id不能为空',
            );
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                xcxerror($validator->errors()->first());
            }

            $data = (new AliAppModule())->getAccessToken($input);
            if ($data['errCode'] != 0){
                xcxerror($data['errMsg']);
            }else{
                //何书哲 2018年9月19日 登录日志发送数据中心
                isset($data['data']['mid']) && dispatch((new LoginStatistics($data['data']['mid'], getIP(), 5))->onQueue('LoginStatistics'));
                xcxsuccess('登陆成功',$data['data']);
            }
        }

        return view('aliapp.authorization.login',array(
            'title'     => '登陆页面',
        ));

    }

}