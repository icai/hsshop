<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2017/10/26
 * Time: 19:39
 */

namespace App\Services\Wechat;


use App\S\Wechat\WeChatShopConfService;


class CustomService
{

    private $original_id;
    private $accessToken;

    const AddCustomServiceUrl       = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token";
    const InviteCustomServiceUrl    = "https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token";
    const CreateSessionUrl          = "https://api.weixin.qq.com/customservice/kfsession/create?access_token";
    const UpdateCustomServiceUrl    = "https://api.weixin.qq.com/customservice/kfaccount/update?access_token";
    const DeleteCustomServiceUrl    = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token";
    const UploadHeadImg             = "https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token";
    const OnlineCustomServiceUrl    = "https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token";
    const ListCustomServiceUrl      = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token";
    const SendMsgUrl                = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token";
    const CloseSessionUrl           = "https://api.weixin.qq.com/customservice/kfsession/close?access_token";
    const GetWaitCaseUrl            = "https://api.weixin.qq.com/customservice/kfsession/getwaitcase?access_token";
    const GetSessionStatusUrl       = "https://api.weixin.qq.com/customservice/kfsession/getsession?access_token";



    public function __construct($wid)
    {
        $this->accessToken = (new ApiService())->getAccessToken($wid);
        $conf = (new WeChatShopConfService())->getConfigByWid($wid);
        $this->original_id    =  $conf['original_id'];
    }

    private function http_post($url = '', $post = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect: '")); //头部要送出'Expect: '
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        $data['kf_account'] = $post['kf_account'];
        //检查判断PHP版本
        if (class_exists('\CURLFile')) { //php版本大于5.5
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            $data['media'] = new \CURLFile($post['fileName']);
        } // php版本5.5以下
        else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data['media'] =  '@' . realpath($post['fileName']);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'TEST');
        $result = curl_exec($curl);
        if($result == FALSE){
            return curl_error($curl);
        }
        return json_decode($result, true);
    }

    //创建客服
    public function addCustomService($data)
    {
        $post['kf_account'] = $data['kf_account'].'@'.$this->original_id;
        $post['nickname']   = $data['kf_nick'];
        $url = self::AddCustomServiceUrl."=".$this->accessToken;
        $result = $this->post($url, json_encode($post, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    //邀请绑定
    public function inviteCustomService($data)
    {
        $post['kf_account'] = $data['kf_account'];
        $post['invite_wx']  = $data['invite_wx'];
        $url = self::InviteCustomServiceUrl."=".$this->accessToken;
        $result = $this->post($url, json_encode($post, JSON_UNESCAPED_UNICODE));
        return $result;
    }


    public function updateCustomService($data = [])
    {
        $post['kf_account'] = $data['kf_account'];
        $post['nickname']   = $data['kf_nick'];
        $url = self::UpdateCustomServiceUrl."=".$this->accessToken;
        $result = $this->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }


    public function deleteCustomService($data = [])
    {
        $url = self::DeleteCustomServiceUrl."=".$this->accessToken."&kf_account=".$data['kf_account'];
        $result = $this->get($url);
        return $result;
    }

    public function getOnlineCustomService()
    {
        $url = self::OnlineCustomServiceUrl."=".$this->accessToken;
        $result = $this->get($url);
        return $result;
    }


    public function getListCustomService()
    {
        $url = self::ListCustomServiceUrl."=".$this->accessToken;
        $result = $this->get($url);
        return $result;
    }

    //上传客服头像
    public function uploadHeadImg($data)
    {
        $url = self::UploadHeadImg."=".$this->accessToken."&kf_account=".$data['kf_account'];
        $result = $this->http_post($url,$data);
        return $result;
    }


    //创建会话
    public function createSession($openId)
    {
        $kf_account = $this->chooseCustom();
        if(isset($kf_account['errcode']) || empty($kf_account)){
            return $kf_account;
        }
        $data['kf_account'] = $kf_account;
        $data['openid'] = $openId;
        $url = self::CreateSessionUrl."=".$this->accessToken;
        $result = $this->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        $result['kf_account'] = $kf_account;
        return $result;
    }

    //筛选客服
    public function chooseCustom()
    {
        $onlineData = $this->getOnlineCustomService();
        if(isset($onlineData['errcode'])){
            return $onlineData;
        }
        if(empty($onlineData['kf_online_list'])){
            return [];
        }
        $kf_online_list = $onlineData['kf_online_list'];
        foreach ($kf_online_list as $key => $val) {
            $tmp[$key] = $val['accepted_case'];
        }
        array_multisort($tmp,SORT_ASC,$kf_online_list);
        $custom = $kf_online_list[0];
        return $custom['kf_account'];
    }

    //关闭会话
    public function closeSession($data)
    {
        $url = self::CloseSessionUrl.'='.$this->accessToken;
        $result = $this->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    public function getWaitCase()
    {
        $url = self::GetWaitCaseUrl."=".$this->accessToken;
        $result = $this->get($url);

        return $result;
    }

    public function getSessionStatus($openid)
    {
        $url = self::GetSessionStatusUrl."=".$this->accessToken."&openid=".$openid;
        $result = $this->get($url);
        return $result;
    }


    public function sendMsg($data)
    {
        $url = self::SendMsgUrl.'='.$this->accessToken;
        $result = $this->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    public function post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect: '")); //头部要送出'Expect: '
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_TIMEOUT,1);
        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);
        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        if ( intval($info['http_code']) == 200 ) {
            return json_decode($result, true);
        } else {
            \Log::info($info);
            //L($info, 'curl_getinfo');
            error('请求失败');
        }
        return $result;
    }

    public function get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect: '")); //头部要送出'Expect: '
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        if ( intval($info['http_code']) == 200 ) {
            return json_decode($result, true);
        } else {
            \Log::info($info);
            //L($info, 'curl_getinfo');
            error('请求失败');
        }
        return $result;
    }

}