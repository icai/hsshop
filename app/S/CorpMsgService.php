<?php

namespace App\S;

use App\Lib\Redis\RedisClient;

class CorpMsgService
{

    const TOKEN_URL = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?';
    const SEND_URL = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=';

    //企业微信id
    private $corpid;

    //企业微信应用secret
    private $corpsecret;

    //企业微信应用agentId
    private $agentId;

    //发送对象
    private $uids;

    public $redis;


    /**
     * 构造函数
     * CorpMsgService constructor.
     */
    public function __construct()
    {
        $this->corpid = config('app.corp_id');
        $this->corpsecret = config('app.corp_secret');
        $this->agentId = config('app.agent_id');
        $this->uids = config('app.corp_uids');
        $this->redis = (new RedisClient())->getRedisClient();
    }

    /**
     * 获取企业微信token
     * @return mixed
     * @throws \Exception
     * @author 何书哲 2019年01月10日
     */
    private function _getToken()
    {
        $key = $this->_getKey();

        if ($access_token = $this->redis->GET($key)) {
            return $access_token;
        }

        $url = self::TOKEN_URL . 'corpid=' . $this->corpid . '&corpsecret=' . $this->corpsecret;
        $result = jsonCurl($url);

        if ($result['errcode']) {
            throw new \Exception('获取企业微信token失败');
        }

        $access_token = $result['access_token'];
        $this->redis->SET($key, $access_token);
        $this->redis->EXPIRE($key, 7000);

        return $access_token;
    }

    private function _getKey()
    {
        return 'corp_msg';
    }

    /**
     * 发送企业微信通知
     * @param string $uids
     * @param string $content
     * @throws \Exception
     * @author 何书哲 2019年01月10日
     * @update 何书哲 2019年09月30日 检测该报错信息是否存在，不存在则会发送，错误信息保持30分钟不会重发
     */
    public function sendMsg($datas = [], $type = 0)
    {
        $url = self::SEND_URL . $this->_getToken();

        $data = [
            'touser' => $this->uids,
            'msgtype' => 'markdown',
            'agentid' => $this->agentId,
        ];

        // update 何书哲 2019年09月30日 检测该报错信息是否存在，不存在则会发送，错误信息保持30分钟不会重发
        if ($this->contentExists($datas['content'])) {
            return true;
        }

        if ($type == 0) {
            $data['markdown']['content'] = "`错误日志` 
>文件：{$datas['file_name']} 
>
>行数：<font color=\"info\">第{$datas['line']}行</font>
>
>详细信息：<font color=\"warning\">{$datas['content']}</font>";
        }

        if ($type == 1) {
            $data['markdown']['content'] = "`错误日志`
<font color=\"warning\">{$datas['content']}</font>";
        }

        $res = jsonCurl($url, json_encode($data));
        \Log::info('企业微信发送结果：' . json_encode($res));
    }

    /**
     * 错误信息是否存在
     * @param string $content 错误信息
     * @return bool true:已存在 false:不存在
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2019年09月30日 14:54:39
     */
    private function contentExists(string $content = '')
    {
        $redisKey = 'wework:error_log:content:' . md5($content);
        if ($this->redis->EXISTS($redisKey)) {
            return true;
        } else {
            $this->redis->SET($redisKey, $content, 'EX', intval(config('app.wework_error_log_monitor_interval', 1800)));
        }
        return false;
    }

}