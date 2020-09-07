<?php

namespace App\Services\Wechat;

class LinkService {

    private $Token      = '';
    private $isdebug    = true;
    private $resultStr  = '';
    private $content    = '';
    private $receiveArr = array();

    private $listevent = array(
        "text"      => "文本信息",
        "image"     => "图片消息",
        "voice"     => "语音消息",
        "video"     => "视频消息",
        "location"  => "位置消息",
        "link"      => "链接消息",
        "event"     => array(
            "subscribe"     => "订阅账号",
            "unsubscribe"   => "取消订阅",
            "scan"          => "订阅扫描",
            "LOCATION"      => "位置上报",
            "CLICK"         => "菜单点击",
        ),
    );


    public function init($tk = '', $para = array()) {
        $this->Token      = $tk;
        foreach ($para as $key=> $val) {
            $this->$key = $val;
        }
    }

    public function _get($keyname='') {
        if($keyname=='') return $this->receiveArr;
        else             return $this->receiveAr[$keyname];
    }

    public function _send() {
        $this->Debug(4, $this->content);
        echo $this->xml_encode($this->resultStr, "xml");exit;
    }

    public function _sendone() {
        $this->Debug(4, $this->content);
        echo $this->xml_encode($this->resultStr, "xml", '');exit;
    }

    public function OneValid() {
        $echoStr = $_GET["echostr"];
        $this->Debug('1.5', $echoStr);
        if($this->checkSignature()){
            $this->Debug(2);
            echo $echoStr;
            exit;
        }
        $this->Debug(-1);
    }

    /*接收信息*/
    public function Receive() {     
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];  
        if (!empty($postStr)) {
            $postObj = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //$postArr = $this->objectToArray($postObj);
            $postArr = $postObj;    
            $this->receiveArr = $postArr;
            $this->Debug(3, $postArr);
            return $this;
        }else {
            echo -1;exit;
        }
    }

    /*回复信息*/
    public function ResponseMsg($msg) {     
        $this->content = $msg;
        $temp =  array(
                'ToUserName'   => $this->receiveArr['FromUserName'],
                'FromUserName' => $this->receiveArr['ToUserName'],
                'CreateTime'   => time()
            );
        $this->resultStr = array_merge($temp, $msg);
        return $this;
    }

    /*回复信息*/
    /*直接传入XML格式,无需再次调用_send()方法*/
    public function ResponseMsgXml($msg) {      
        $this->Debug(4, $msg);
        $resultStr = sprintf($msg, $this->receiveArr['FromUserName'], $this->receiveArr['ToUserName'], time());
        echo $resultStr;exit;
    }

    /*数据有效性验证*/
    public function Valid() {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            return true;
        }else{
            $this->Debug(-1);
            return false;
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];    
        $token = $this->Token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        $date['signature'] = $_GET["signature"];
        $date['timestamp'] = $_GET["timestamp"];
        $date['nonce']     = $_GET["nonce"];
        $date['token']     = $token;
        $date['tmpStr']    = $tmpStr;
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function Debug($leixid, $debugarr) {
        $jieshou = array("-1"=>"验证失败", "1"=>"开始验证", "1.5"=>"验证中...", "2"=>"验证成功", "3"=>"接收信息", "4"=>"回复信息");
        if($this->isdebug) {
            $temp['time']   = date("Y-m-d H:i:s");
            $temp['code']   = $leixid;
            $temp['cname']  = $jieshou[$leixid];
            if($leixid=='-1' || $leixid=='1' || $leixid=='2') {
                $temp['text'] = $jieshou[$leixid];
            }else if($leixid=='1.5') {
                $temp['text']     = $debugarr;
            }else if($leixid=='3') {
                $temp['text']       = $debugarr;
                if(!is_array($this->listevent[$this->receiveArr['MsgType']]))
                    $temp['cname']  = "接收".$this->listevent[$this->receiveArr['MsgType']];
                else 
                    $temp['cname']  = "接收".$this->listevent['event'][$this->receiveArr['Event']];
            }else {
                $temp['text'] = $debugarr;
            }
            $log  = $this->encode_json($temp);
            file_put_contents("logs.txt", $log."\n", FILE_APPEND);
        }
        return;
    }

    function arrayToObject($e){
        if( gettype($e)!='array' ) return;
        foreach($e as $k=>$v){
            if( gettype($v)=='array' || getType($v)=='object' )
                $e[$k]=(object)arrayToObject($v);
        }
        return (object)$e;
    }
     
    function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)objectToArray($v);
        }
        return $e;
    }

    function encode_json($str) {
        $json = json_encode($str);
        //linux
        return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $json);
        //windows
        //return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2LE', 'UTF-8', pack('H4', '\\1'))", $json);
    }

    function decode_json($str) {
        return json_decode($str);
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    function xml_encode($data, $root='xml', $item='item', $attr='', $id='id', $encoding='utf-8') {
        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml   .= "<{$root}{$attr}>";
        $xml   .= $this->data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    function data_to_xml($data, $item='item', $id='id') {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if(is_numeric($key)){
                $id && $attr = " {$id}=\"{$key}\"";
                $key  = $item;
            }
            if($key) $xml    .=  "<{$key}{$attr}>";
            $xml    .=  (is_array($val) || is_object($val)) ? $this->data_to_xml($val, $item, $id) : $this->xmlSafeStr($val);
            if($key) $xml    .=  "</{$key}>";
        }
        return $xml;
    }

    function xmlSafeStr($str){
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }
}
?>