<?php 

namespace App\Services\Foundation;
use QrCode;

/**
 * 二维码服务类
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月28日 17:36:55
 */
class QrCodeService {
    /**
     * 生成二维码 【png格式】
     * 
     * @param  string $url  [二维码链接地址]
     * @param  string $logo [二维码logo水印] 定义为相对地址 /public/mctsource/images/merchants_logo.png
     * @param  int  $size [二维码图片大小]: 如果$size=100 代表宽高均为100像素的二维码图片
     * @param  mix  $param [递归目录] 可以为字符串如：用户01/会员卡01/...;也可以为数字如：02....
     * @param  int  $waterSize [logo水印的占比，默认占二维码的20%]
     * @return string       [二维码图片绝对路径]
     */
    public function create( $url, $logo = '', $size = 100 ,$param='',$waterSize=20) {
        $qrcodeUrl = '';

        //（生成的路径指定为 pulic/qrcodes目录下，中文转码）
        if(!file_exists(iconv("UTF-8", "GBK", public_path('hsshop/image/qrcodes/'.$param))))
        {
            mkdir(iconv("UTF-8", "GBK", public_path('hsshop/image/qrcodes/'.$param)),0777,true); 
        }

        // 生成绝对路径（文件名）
        $path = public_path('hsshop/image/qrcodes/'.$param);
        $qrcodeUrl  = iconv("UTF-8", "GBK",$path.'/qrcode.png');

        // 根据logo生成是否带有水印的二维码
        if($logo)
        {
            Qrcode::format('png')->size($size)->merge($logo, ".$waterSize")->margin(0.5)->generate($url,$qrcodeUrl);  //水印占比固定为20%
        }else{
            Qrcode::format('png')->size($size)->margin(0.5)->generate($url,$qrcodeUrl);
        }

        return $qrcodeUrl;
    }

    public function createShopQrCode($url,$wid)
    {
        $path = public_path('qrcodes/shopQrcode'.$wid.'.png');
        Qrcode::format('png')->size(150)->margin(1)->generate($url,$path);
        return 'qrcodes/shopQrcode'.$wid.'.png';
    }

}
