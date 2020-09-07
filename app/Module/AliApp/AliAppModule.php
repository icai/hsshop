<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/7/23
 * Time: 11:40
 */

namespace App\Module\AliApp;


use App\Lib\Redis\RedisClient;
use App\Module\MemberModule;
use App\S\AliApp\AliappConfigService;
use App\S\AliApp\AliappNotifyService;
use App\S\AliApp\AliappRefundInfoService;
use App\S\Member\MemberService;
use DB;

class AliAppModule
{


    public $redis;
    public $timeOut;
    public $request;

    public function __construct(){
        $this->request   = app('request');
        $this->redis = (new RedisClient())->getRedisClient();
        $this->timeOut = 259200;
    }


    /**
     * 添加支付宝小程序授权配置信息
     * @param $data
     * @author 张永辉 2018年7月23日
     */
    public function addAliAppConfig($data,$wid){
        $data = json_decode(json_encode($data),true);
        $aliappConfigService = new AliappConfigService();
        $nowTime = time();
        foreach ($data['tokens'] as $key=>$val){
            $expiresIn = $nowTime+$val['expires_in']-1728000;   //提前20天重新获取令牌
            $reExpiresIn = $nowTime+$val['re_expires_in']-3600; //提前一个小时失效刷新令牌
            $param = [
                'wid'                => $wid,
                'user_id'           => $val['user_id'],
                'auth_app_id'       => $val['auth_app_id'],
                'app_auth_token'    => $val['app_auth_token'],
                'app_refresh_token' => $val['app_refresh_token'],
                'expires_in'         => $expiresIn,
                're_expires_in'      => $reExpiresIn,
                'is_delete'          => 0,
            ];
            $res = $aliappConfigService->model->where('auth_app_id',$val['auth_app_id'])->get()->toArray();

            $versionManageModule = new VersionManageModule();
            if ($res){
                $aliappConfigService->update($res[0]['id'],$param);
                $param['id'] = $res[0]['id'];
            }else{
                $id = $aliappConfigService->add($param);
                $param['id'] = $id;
            }
            $versionManageModule->getAliappInfo($param);
        }
        return true;

    }


    /**
     * 小程序授权获取访问token
     * @param $code
     * @param string $grantType
     * @author 张永辉
     */
    public function getAccessToken($data,$grantType = 'authorization_code'){
        $resData = ['errCode'=>0,'errMsg'=>''];
        $aliappConfigService = new AliappConfigService();
        $configData = $aliappConfigService->model->find($data['aliappConfigId']);
        if (!$configData){
            $resData['errCode'] = -40003;
            $resData['errMsg']  = '配置信息为空';
            return $resData;
        }
        $configData = $configData->toArray();
        $aliClient = new AliClientModule();
        $request = new AlipaySystemOauthTokenRequest ();
        $aliClient->appId = $configData['auth_app_id'];
        $aliClient->alipayrsaPublicKey = $configData['ali_rsa_pub_key'];
        $request->setGrantType($grantType);
        $request->setCode($data['authCode']);
        $request->setRefreshToken('');
        $result = $aliClient->execute ( $request);
        $result = json_decode(json_encode($result),true);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(isset($result[$responseNode])){
            $userData['data']['user_id'] = $result[$responseNode]['user_id'];
            if (!empty($data['type']) && $data['type'] == 1){
                $userData = $this->getUserInfo($result[$responseNode]['access_token'],$configData);
            }
            if ( !(!empty($data['type']) && $data['type'] == 1) || $userData['errCode'] == '0'){
                $userData = $userData['data'];
                $resData['data'] = $this->getMemberInfo($userData,$data);
                if ($resData['data'] ){
                    return $resData;
                }else{
                    $result['errCode'] = 40087;
                    $result['errMsg'] = '更新用户失败';
                    return $resData;
                }
            }else{
                return $userData;
            }

        }else{
            \Log::info('获取token信息失败');
            \Log::info(json_decode(json_encode($result),true));
            $resData['errCode'] = '-4003';
            $resData['errMsg'] = '授权失败';
            return $resData;
        }
    }


    /**
     * 获取用户信息
     * @param $aliUserId 支付宝小程序id
     * @param $wid 店铺id
     * @return int 用户id，0 创建失败
     * @author 张永辉 2018年7月23日
     */
    public function getMemberInfo($userData,$data){

        $memberService = new MemberService();
        $memberData = $memberService->getList(['ali_user_id'=>$userData['user_id']]);
        $tokenData = [
            'wid'   => $data['wid'],
            'aliappConfigId' => $data['aliappConfigId'],
        ];
        if ($memberData){
            $memberData = current($memberData);
            $this->upMemberInfo($userData,$memberData,$memberService);
            $tokenData['mid'] = $memberData['id'];
            return $this->_getToken($tokenData);
        }
        $memberModule = new MemberModule();
        DB::beginTransaction();
        if (!$memberModule->memberCheck($data['wid'],$userData['user_id'])){
            return false;
        }
        $memberData = [
            'umid'      => 0,
            'wid'       => $data['wid'],
            'source'    => '7',
            'ali_user_id'=> $userData['user_id'],
            'headimgurl' =>$userData['avatar']??'',
            'province'  => $userData['province']??'',
            'city'  => $userData['city']??'',
            'nickname'  => $userData['nick_name']??'',
            'is_student_certified'  => $userData['is_student_certified']??'',
            'user_type'  => $userData['user_type']??'',
            'user_status'  => $userData['user_status']??'',
            'is_certified'  => $userData['is_certified']??'',
            'sex'  => $userData['gender']??'' =='F'?'2':'1',
        ];
        $mid = $memberService->add($memberData);
        if (!$mid){
            DB::rollBack();
            return [];
        }
        DB::commit();
        $tokenData['mid'] = $mid;
        return $this->_getToken($tokenData);
    }


    /**
     * 更新用户信息
     * @param $nowMember
     * @param $data
     * @author 张永辉 2018年7月31日
     */
    public function upMemberInfo($nowMember,$data,MemberService $memberService)
    {

        if (!isset($nowMember['gender'])){
            $nowMember['gender'] = 'F';
        }
        if ($nowMember['gender'] == 'F'){
            $nowMember['gender'] = 2;
        }elseif ($nowMember['gender'] == 'M'){
            $nowMember['gender'] = 1;
        }else{
            $nowMember['gender'] = 0;
        }
        $keys = [
            'nick_name'=>'nickname',
            'avatar'=>'headimgurl',
            'province'=> 'province',
            'city'  => 'city',
            'is_student_certified'  => 'is_student_certified',
            'user_type'  => 'user_type',
            'user_status'  => 'user_status',
            'gender'  => 'sex',
            'is_certified'  => 'is_certified',
        ];
        $upData = [];
        foreach ($keys as $key=>$val) {
            if (!empty($nowMember[$key]) && $nowMember[$key] != $data[$val]){
                $upData[$val] = $nowMember[$key];
            }
        }
        if (!$upData){
            return true;
        }
        $res = $memberService->updateData($data['id'],$upData);
        if ($res['errCode'] == 0){
            return true;
        }else{
            return false;
        }


    }


    /**
     * 获取token,key值
     * @param $token
     * @return string
     * @author 张永辉
     */
    private function _getKey($token){
        return 'aliapp:'.$token;
    }


    /**
     * 获取访问令牌
     * @return array
     * @author 张永辉 2018年7月23日
     */
    private function _getToken($data)
    {
        $token = md5(uniqid('',true));
        $key = $this->_getKey($token);
        if ($this->redis->EXISTS($key)){
            return $this->_getToken();
        }else{
            $data['token'] = $token;
            $this->redis->SET($key,json_encode($data));
            $this->redis->EXPIRE($key, $this->timeOut);
            return $data;
        }
    }


    /**
     * 获取token存储数据
     * @param $token
     * @param array $field
     * @return array|mixed
     * @author 张永辉
     */
    public function getTokenData($token,$field = [])
    {
        if (!$token){
            return [];
        }
        $key = $this->_getKey($token);
        $result = $this->redis->GET($key);
        if (!$result){
            return [];
        }
        $this->redis->EXPIRE($key, $this->timeOut);
        $result = json_decode($result,true);
        if ($field){
            $temp = [];
            foreach ($field as $item) {
                $temp[$item] = array_column($result,$item);
            }
            return $temp;
        }else{
            return $result;
        }

    }


    /**
     *  更新token到期时间
     * @param $token
     * @author 张永辉
     */
    public function updateTokenTime($token)
    {
        $key = $this->_getKey($token);
        if ($this->redis->EXISTS($key)){
            $this->redis->EXPIRE($key, $this->timeOut);
            return true;
        }
    }


    /**
     * 支付宝小程序订单退款
     * @param $oid
     * @author 张永辉
     */
    public function aliappOrderRefund($oid,$amount='')
    {

        $result = ['errCode'=>0,'errMsg'=>''];
        $aliClientModule = new AliClientModule();
        $request = new AlipayTradeRefundRequest();
        $aliappConfigService = new AliappConfigService();
        $aliappNotifyService = new AliappNotifyService();

        $aliappNotifyData = $aliappNotifyService->getList(['out_trade_no'=>$oid]);
        if (!$aliappNotifyData){
            $result['errCode'] = -40001;
            $result['errMsg']   =  '支付信息不存在';
            return $result;
        }
        $aliappNotifyData = current($aliappNotifyData);
        $configData = $aliappConfigService->getRowByAppId($aliappNotifyData['app_id']);
        $aliClientModule->appId = $configData['auth_app_id'];
        $aliClientModule->alipayrsaPublicKey = $configData['ali_rsa_pub_key'];
        $params = [
            'trade_no'      => $aliappNotifyData['trade_no'],
            'out_trade_no'  => $aliappNotifyData['out_trade_no'],
            'refund_amount' => empty($amount)?$aliappNotifyData['total_amount']:$amount
        ];
        $request->setBizContent(json_encode($params));
        $res = $aliClientModule->execute($request);
        $res = json_decode(json_encode($res),true);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(isset($res[$responseNode]) && $res[$responseNode]['code'] == '10000'){
            $insertData = [
                'trade_no'            => $res[$responseNode]['trade_no'],
                'out_trade_no'        => $res[$responseNode]['out_trade_no'],
                'buyer_logon_id'      => $res[$responseNode]['buyer_logon_id'],
                'fund_change'         => $res[$responseNode]['fund_change'],
                'refund_fee'          => $res[$responseNode]['refund_fee'],
                'gmt_refund_pay'      => $res[$responseNode]['gmt_refund_pay'],
                'info'                 => json_encode($res),
            ];
            $res = (new AliappRefundInfoService())->add($insertData);
            if ($res){
                return $result;
            }else{
                \Log::info($insertData);
                \Log::info($res);
                $result['errCode'] = -40011;
                $result['errMsg'] = '操作失败';
                return $result;
            }
        }else{
            \Log::info($result);
            $result['errCode'] = -40011;
            $result['errMsg'] = $res[$responseNode]['sub_msg'];
            return $result;
        }

    }


    /**
     * 拉取用户信息
     * @param $auth_token
     * @author 张永辉
     */
    public function getUserInfo($access_token,$configData)
    {
        $result = ['errCode'=>'0','errMsg'=>''];
        $aop = new AliClientModule();
        $request = new AlipayUserInfoShareRequest ();
//        $resData = $aop->execute ( $request , $access_token,$configData['app_auth_token']);

        $aop->appId = $configData['auth_app_id'];
        $aop->alipayrsaPublicKey = $configData['ali_rsa_pub_key'];
        $resData = $aop->execute ( $request , $access_token);

        $resData = json_decode(json_encode($resData),true);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(!empty($resData[$responseNode]['code']) && $resData[$responseNode]['code'] == '10000'){
            $result['data'] = $resData[$responseNode];
            return $result;
        } else {
            \Log::info('授权获取用户信息失败');
            \Log::info($resData);
            $result['errCode'] = 40038;
            $result['errMsg'] = '授权失败';
            return $result;
        }
    }




}