<?php 
namespace App\S\Foundation;
use App\S\S;
use App\Services\Wechat\ApiService;
use Intervention\Image\ImageManager;
/**
 * 省市区
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年1月14日 10:32:35
 */
class CreateShareImgService  extends S 
{

    /**
     * 合成图片信息
     * @param  [int] $wid [店铺id]
     * @param  [int] $mid [用户id]
     * @param  string $upImg         [用户上传的图片]
     * @param  string $backgroundImg [背景图片]
     * @param  string $headImg       [用户微信头像]
     * @param  string $nickname      [用户微信昵称]
     * @return [type]                [description]
     */
    public function createImg($wid,$mid,$backgroundImg='',$upImg='',$headImg='',$nickname='')
    {
        $manager = new ImageManager(array('driver' => 'gd'));
        //初始化背景图
        $image = $manager->make($backgroundImg)->resize(700,null,function($constraint){
            $constraint->aspectRatio();  
        });

        //$upImg = $this->isReversalImg($upImg);
        //插入上传的图片
        $upmark = $manager->make($upImg)->fit(486, 295, function ($constraint) {
            $constraint->upsize();
        });
        $image->insert($upmark,'top-left',106,575); 

        //插入用户微信头像
        $headmark = $manager->make($headImg)->fit(120, 120, function ($constraint) {
            $constraint->upsize();
        });
        $image->insert($headmark,'top',0,26);
        
        $result = $this->qrcodeCreate($wid,$mid);
        if ($result['errCode'] <> 0) {
            error($result['errMsg']);
        }
        $qrcodeImg = public_path('hsshop/image/qrcodes/meet/'.$wid.'-'.$mid).'/qrcode.png';

        //设置添加二维码图片
        $qrcodemark = $manager->make($qrcodeImg)->resize(190,200);
        $image->insert($qrcodemark,'bottom-right',44,55); 

        //处理昵称显示位置
        $image->text($nickname,350,180,function($font){
            $font->file('./font/vistaBold.ttf');
            $font->size(30);
            $font->color('#f9d9aa');
            $font->align('center');
            $font->valign('middle');
            $font->angle(0);

        });

        //定义一个保存图片的目录
        $path       = public_path('hsshop/image/qrcodes/meet/'.$wid.'-'.$mid);
        if(!file_exists($path)){
            mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
        //保存合并的图片
        $fileName = $path.'/shareImg.png';
        $image->save($fileName);  
        $image->destroy(); 
    }


    /**
     * 生成带参数的临时二维码
     * @param  [int] $wid [店铺id]
     * @param  [int] $mid [用户id]
     * @return [type]      [description]
     */
    public function qrcodeCreate($wid,$mid)
    {
        $returnData = ['errCode' => 0,'errMsg' => '','data' => []];
        $data  = [
            'expire_seconds'    => '2592000',
            'action_name'       => 'QR_STR_SCENE',
        ];
        $data['action_info']['scene']['scene_str'] = 'meeting_'.$mid;
        $apiService = new ApiService();
        $re = $apiService->tempQrcodeCreated($wid,$data);
        if(isset($re['errcode'])){
            $returnData['errCode'] = 1;
            $returnData['errMsg'] = '获取二维码失败，请确保已绑定店铺微信号（服务号）';
            return $returnData;
        }

        $content = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$re['ticket'];
        $path = public_path('hsshop/image/qrcodes/meet/'.$wid.'-'.$mid);
        //创建目录
        if(!file_exists($path)) {
            mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
        $file = $path.'/qrcode.png';
        $content = file_get_contents($content);
        //把图数据写入到相应的文件中
        file_put_contents($file, $content);

        return $returnData;
    }

    /**
     * 是否对图片进行反转处理
     * @param  [type]  $path [description]
     * @return boolean       [description]
     */
    public function isReversalImg($path)
    {
        $image = imagecreatefromstring(file_get_contents($path));
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($path);
            if(isset($exif['Orientation']) && !empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 8:
                        $image = imagerotate($image,90,0);
                        break;
                    case 3:
                        $image = imagerotate($image,180,0);
                        break;
                    case 6:
                        $image = imagerotate($image,-90,0);
                        break;
                }
                imagejpeg($image,$path);
                imagedestroy($image);
            }
        }
        return $path;
    }

    /**
     * 读取微信头像流数据
     * @param  [int] $wid        [店铺id]
     * @param  [int] $mid        [用户id]
     * @param  [string] $headimgurl [微信头像url]
     * @return [type]             [description]
     */
    public function weixinHeadImg($wid,$mid,$headimgurl)
    {
        $path       = public_path();
        $logo       = 'hsshop/image/weixin/headimg/'.$wid.'/'.$mid.'/logo.png'; //设置水印logo
        if (!file_exists($path.'/'.$logo)) {
            $memberLogo = $headimgurl;
            //读取远程文件
            $content    = http_get_imgData($memberLogo); 
            //创建目录
            if(!file_exists($path.'/hsshop/image/weixin/headimg/'.$wid.'/'.$mid)){
                mkdir(iconv("UTF-8", "GBK", $path.'/hsshop/image/weixin/headimg/'.$wid.'/'.$mid),0777,true);
            }
            //定义文件名
            $filename   = iconv("UTF-8", "GBK",$path.'/hsshop/image/weixin/headimg/'.$wid.'/'.$mid.'/logo.png');
            $fp         = @fopen($filename,"w+"); //将文件绑定到流 (以读写的模式写入)
            fwrite($fp,$content); //写入文件  
        }

        return $logo;
    }


}
