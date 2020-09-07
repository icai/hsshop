<?php

namespace App\Http\Controllers\BaiduApp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module\BaiduApp\BaiduClientModule;
use Validator;
use App\S\Member\MemberService;
use App\Module\MemberModule;
use App\Lib\BLogger;
use App\Lib\Redis\BaiduRedisClient;

class AuthorizeController extends Controller
{

    public $redis;

    public function __construct() {
        $this->redis = (new BaiduRedisClient())->getRedisClient();
    }

    /**
     * 用登录code换取session_key的值
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function login(Request $request)
    {   
    	$returnData = ['errCode' => 0,'errMsg' => '','data' => []]; 
        if ($request->Method('post')) {
            $input = $request->input();
            $baiduClientModule = new BaiduClientModule(); 
            $result = $baiduClientModule->getSessionKey($input);
            if (isset($result['errCode']) && $result['errCode']) {
                $returnData['errCode'] = $result['errCode'];
                $returnData['errMsg']  = $result['errMsg'];
                return $returnData;
            }
            $returnData['data'] = $result;
            return $returnData;
        }
        return view('baiduapp.login',array(
            'title'     => '登陆页面',
        ));
    }

    /**
     * 检查token是否过期
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function checkToken(Request $request)
    {
        $returnData = ['errCode' => 0,'errMsg' => '','data' =>[]];
        $token = $request->input('token') ?? '';
        if (empty($token)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'token为空';
            return $returnData;
        }
        $baiduClientModule = new BaiduClientModule();
        $result = $baiduClientModule->getTokenData($token);
        if (empty($result)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = 'token已过期';
            return $returnData;
        }
        $returnData['data'] = $result;
        return $returnData;
    }

    /**
     * 解密获取用户信息
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getUserInfo(Request $request,MemberService $memberService)
    {
        $iv = $request->input('iv') ?? '';       //加密向量
        $data = $request->input('data') ?? '';   //待解密数据
        $session_key = $request->input('session_key');
        $wid = $request->input('wid');
        $app_key = config('baiduapp.app_key');
        /*$sessionKeyData = $this->redis->GET('sessionKeyResult');
        \Log::info('sessionKeyData:'.$sessionKeyData);
        $sessionKeyData = json_decode($sessionKeyData,true);
        BLogger::getLogger('info','baidu')->info('sessionKeyData',$sessionKeyData);
        $session_key = $sessionKeyData['session_key'];
        if ($request->input('session_key')) {
            $session_key = $request->input('session_key');
        }*/
        $baiduClientModule = new BaiduClientModule();
        $content = $baiduClientModule->decrypt($data, $iv, $app_key, $session_key); //解密数据
        $memberInfo['wid'] = $wid;
        if ($content) {
            $memberInfo = json_decode($content,true);
            $memberData = $memberService->getList(['openid'=>$memberInfo['openid']]);
            // 根据openid判断是否存在该用户，存在则更新
            if ($memberData) {
                $memberData = current($memberData);
                $memberService->updateData($memberData['id'],$memberInfo);
                $tokenData['mid'] = $memberData['id'];
                $tokenData['wid'] = $wid;
                return $baiduClientModule->getToken($tokenData);
            }
        }
        BLogger::getLogger('info','baidu')->info('memberInfo',$memberInfo);
        // 新增member表信息
        try {
            $tokenData = \DB::transaction(function () use ($memberInfo,$memberService,$baiduClientModule,$wid) {
                if (!(new MemberModule())->memberCheck($wid,$memberInfo['openid'])){
                    return false;
                }
                $memberInfo['source'] = 7;
                $mid = $memberService->add($memberInfo);
                $tokenData['mid'] = $mid;
                $tokenData['wid'] = $wid;
                return $baiduClientModule->getToken($tokenData);
            }); 
        }catch (\Exception $e){
            error($e->getMessage());
        }
        BLogger::getLogger('info','baidu')->info('tokenData',$tokenData);
        return $tokenData;  
    }

    
}
