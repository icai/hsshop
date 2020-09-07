<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/2/23
 * Time: 19:01
 */

namespace App\Module;

use Log;
/**
 * Class RSAModule
 * @package App\Module
 */
class RSAModule
{
    private $pubKey = null;
    private $priKey = null;
    private $pubKeyPath = 'hsshop/rsakey/pub.pem';
    private $priKeyPath = 'hsshop/rsakey/pri.pem';
    private $keyLen;

    public function __construct()
    {
        $this->_getKey();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180224
     * @desc 获取加密文件
     */
    private function _getKey()
    {
        $this->pubKeyPath = 'file://'.public_path($this->pubKeyPath);
        $this->priKeyPath = 'file://'.public_path($this->priKeyPath);
        $this->pubKey = openssl_get_publickey($this->pubKeyPath);
        $this->priKey = openssl_get_privatekey($this->priKeyPath);
        $this->keyLen = openssl_pkey_get_details($this->pubKey)['bits'];
        if (!$this->pubKey || !$this->priKey){
            Log::info(__FILE__.'文件,第'.__LINE__.'行，错误：公钥或秘钥文件读取失败');
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180224
     * @desc 加密
     * @param $data
     * @param string $code
     * @param int $padding
     * @return bool
     */
    public function encrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING)
    {
        $result = '';
        if ($data){
            $partLen = $this->keyLen/8-11;
            foreach (str_split($data,$partLen) as $value){
                $tmp = '';
                openssl_private_encrypt($value, $tmp, $this->priKey,$padding);
                $result .=$tmp;
            }
        }
        if ($result){
            $result = $this->_encode($result,$code);
        }
        return $result;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${20180305
     * @desc 私钥解密
     * @param $data
     * @param string $code
     * @param int $padding
     * @param bool $rev
     * @return string
     */
    public function decrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING, $rev = false)
    {
        $result='';
        $partLen = $this->keyLen/8;
        $data = $this->_decode($data, $code);
        foreach (str_split($data,$partLen) as $value){
            $tmp = '';
            openssl_private_decrypt($value, $tmp, $this->priKey, $padding);
            $result .= $tmp;
        }
        return json_decode($result,true);
    }


    private function _encode($data, $code)
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_encode($data);
                break;
            case 'hex':
                $data = bin2hex($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }

    private function _decode($data, $code)
    {
        switch (strtolower($code)) {
            case 'base64':
                $data = base64_decode($data);
                break;
            case 'hex':
                $data = $this->_hex2bin($data);
                break;
            case 'bin':
            default:
        }
        return $data;
    }





    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180224
     * @desc 加密
     * @param $data
     * @param string $code
     * @param int $padding
     * @return bool
     */
    public function pubEncrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING)
    {
        $result = '';
        if ($data){
            $partLen = $this->keyLen/8-11;
            foreach (str_split($data,$partLen) as $value){
                $tmp = '';
                openssl_public_encrypt($value, $tmp, $this->pubKey,$padding);
                $result .=$tmp;
            }
        }
        if ($result){
            $result = $this->_encode($result,$code);
        }
        return $result;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${20180305
     * @desc 私钥解密
     * @param $data
     * @param string $code
     * @param int $padding
     * @param bool $rev
     * @return string
     */
    public function pubDecrypt($data, $code = 'base64', $padding = OPENSSL_PKCS1_PADDING, $rev = false)
    {
        $result='';
        $partLen = $this->keyLen/8;
        $data = $this->_decode($data, $code);
        foreach (str_split($data,$partLen) as $value){
            $tmp = '';
            openssl_public_decrypt($value, $tmp, $this->pubKey, $padding);
            $result .= $tmp;
        }
        return json_decode($result,true);
    }


}

