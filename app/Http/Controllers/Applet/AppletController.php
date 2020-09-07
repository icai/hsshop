<?php
namespace App\Http\Controllers\Applet;

use App\Http\Controllers\Controller;
use App\S\Wechat\WeChatShopConfService;
use Illuminate\Http\Request;
use Validator;
use App\S\Applet\AppletService;
use App\Services\Lib\JSSDK;
use Illuminate\Support\Facades\Cookie;
use Intervention\Image\ImageManager;
use App\S\Foundation\DateReturnService;

class AppletController extends Controller{

	/**
	 * 小程序邀请函首页
	 * @return [type]           [description]
	 */
	public function index(Request $Request)
	{
		$referPhone = $Request->input('phone') ?? '';

		return view('applet.index',[
			'title'      => '咨询报名小程序',
			'referPhone' => $referPhone
		]);
	}

	/**
	 * 提交报名
	 * @return [type] [description]
	 */
	public function signUp(Request $Request,AppletService $appletService)
	{
		$input = $Request->input();
		if(!$input){
			error('数据异常');
		}
		$saveData = [];
		$saveData['real_name'] = $input['real_name'];
		$saveData['mobile']    = $input['mobile'];
		$saveData['company']   = $input['email'];
		$saveData['post']	   = $input['company'];
		$saveData['industry']  = $input['title'];
		$saveData['refer']	   = $input['phone'] ?? '';
		$rs = $appletService->add($saveData);
		if($rs){
			success('报名成功');
		}
		error();
	}

	/**
     * todo 获取微信公众号的密钥
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-06-22
     */
    public function getWeixinSecretKey(Request $request)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid = session('wid');
        $conf = (new WeChatShopConfService())->getConfigByWid($wid);
        //微信分享
        if(!empty($conf['app_id']) && !empty($conf['app_secret'])){
            $appId  = $conf['app_id'];
            $secret = $conf['app_secret'];
        }else{
            $appId  = config('app.public_auth_appid');
            $secret = config('app.public_auth_secret');
        }
        $url = $request->input('url');
        try
        {
            $jssdk = new JSSDK($appId, $secret,62);
            $signPackage = $jssdk->GetSignPackage($url);
            if (!empty($signPackage))
            {
                $returnData['data'] = $signPackage;
            }
            else
            {
                $returnData['errCode']=-2;
                $returnData['errMsg']='没有获取到微信api数据';
            }
        }
        catch(\Exception $ex)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$ex->getMessage();
        }
        return $returnData;
    }	


    //亲情大考验
    public function kinShip(Request $request)
    {
        return view('applet.ship',[
            'title'      => '亲情大考验',
        ]);
    }

    /**
     * 统计相关案例详情页的访问人数
     * @param  [string]  $ip        [访问用户ip]
     * @param  [int]     $caseId    [案例id]
     * @param  [int]     $num       [设置默认的浏览数]
     * @return [type]               [description]
     */
    public function statistics($ip,$num = 0)
    {
        //计算当时时间与24点之前还几个小时（多少秒）
        $seconds = strtotime(date('Y-m-d').' 23:59:59')-time();
        //计算小时（取整）
        $hours = floor($seconds/3600);

        //设置存储cookie的键值（key）
        $key = 'user:'.$ip;
        Cookie::queue($key, $num, $hours*3600);  //把参数保存到cookie,设置过期时间
        
    }
     
    /**
     * 统计相关案例详情页的访问人数
     * @param  [string]  $ip        [访问用户ip]
     * @param  [int]     $caseId    [案例id]
     * @param  [int]     $num       [设置默认的浏览数]
     * @return [type]               [description]
     */
    public function h5()
    {
        return view('applet.h5.index',[
            'title'      => 'h5'
        ]);
        
    }

    /**
     * 请柬
     * @author wuxiaoping <2017.10.12>
     * @return [type] [description]
     */
    public function invitation(Request $request)
    {
        $manager = new ImageManager(array('driver' => 'gd'));
        if ($request->isMethod('post')) {
            $input = $request->input();
            $filePrefix = substr(md5($input['man'].$input['woman']),0,5);
            //定义一个保存图片的目录
            $path       = public_path('applet/invitation/static/images/');
            if(!file_exists($path)){
                mkdir(iconv("UTF-8", "GBK", $path),0777,true);
            }
            switch ($input['type']) {
                case '1':
                    $path = config('app.source_url').'applet/invitation/public/images/zheng_qinjian.jpg';
                    //调整图像的宽到600，并约束宽高比(高自动)
                    $image = $manager->make($path)->resize(600,null,function($constraint){
                        $constraint->aspectRatio();  
                    });
                    $dateArr = explode('-',$input['jiehun']);
                    //拼接成最后的字符串
                    $licensingDate = $dateArr[0].'年'.$dateArr[1].'月'.$dateArr[2].'日';

                    $dateTime = $input['hunli'];
                    $dateTimeArr = explode(' ',$dateTime);
                    //生成农历的日期
                    $noliDate = (new DateReturnService())->returnNongli($dateTimeArr[0]);
                    $hunDateArr = explode('-',$dateTimeArr[0]);
                    //生成星期几
                    $weekArray = ["日","一","二","三","四","五","六"];
                    $week = $weekArray[date('w',strtotime($dateTimeArr[0]))];
                    //拼接成最后的字符串
                    $dateTimeStr = '公元'.$hunDateArr[0].'年'.$hunDateArr[1].'月'.$hunDateArr[2].'号'.' 星期'.$week.' '.$dateTimeArr[1];
                    $this->getMergeCard($image,$licensingDate,$dateTimeStr,$noliDate,$input['man'],$input['woman'],$input['hotel'],$input['manifesto'],$mNationality='中国',$wNationality='中国');
                    $fileName = './applet/invitation/static/images/'.$filePrefix.'_zheng_qinjian.jpg';
                    $image->save($fileName);  
                    $image->destroy();
                    break;
                case '2': //生成结婚证图片
                    $path = config('app.source_url').'applet/invitation/public/images/licensing.jpg';
                    //调整图像的宽到600，并约束宽高比(高自动)
                    $image = $manager->make($path)->resize(600,null,function($constraint){
                        $constraint->aspectRatio();  
                    });
                    $dateArr = explode('-',$input['jiehun']);
                    //拼接成最后的字符串
                    $dateStr = $dateArr[0].'年'.$dateArr[1].'月'.$dateArr[2].'日';
                    $this->getMarriage($image,$input['man'],$input['woman'],$dateStr);
                    //保存图像
                    $fileName = './applet/invitation/static/images/'.$filePrefix.'_licensing.jpg';
                    $image->save($fileName);  
                    $image->destroy();  
                    break;
                case '3':
                    $path = config('app.source_url').'applet/invitation/public/images/invitation.jpg';
                    //调整图像的宽到600，并约束宽高比(高自动)
                    $image = $manager->make($path)->resize(600,null,function($constraint){
                        $constraint->aspectRatio();  
                    });
                    $dateTime = $input['hunli'];
                    $dateTimeArr = explode(' ',$dateTime);
                    //生成农历的日期
                    $noliDate = (new DateReturnService())->returnNongli($dateTimeArr[0]);
                    $dateArr = explode('-',$dateTimeArr[0]);
                    //生成星期几
                    $weekArray = ["日","一","二","三","四","五","六"];
                    $week = $weekArray[date('w',strtotime($dateTimeArr[0]))];
                    //拼接成最后的字符串
                    $dateTimeStr = '公元'.$dateArr[0].'年'.$dateArr[1].'月'.$dateArr[2].'号'.' 星期'.$week.' '.$dateTimeArr[1];
                    $this->getInvitationCard($image,$dateTimeStr,$noliDate,$input['man'],$input['woman'],$input['hotel'],$input['manifesto']);
                    //保存图像
                    $fileName = './applet/invitation/static/images/'.$filePrefix.'_invitation.jpg';
                    $image->save($fileName);  
                    $image->destroy();
                default:
                    # code...
                    break;
            }

            success('','/applet/invitation/showImg',['fileName' => substr($fileName,1)]);
        }
        
        return view('applet.invitation.index',[
            'title' => '请柬'
        ]);
    }

    public function showImg(Request $request)
    {
        $imgSrc = $request->input('imgSrc') ?? ''; 
        return view('applet.invitation.showImg',[
            'title'  => '我们结婚啦',
            'imgSrc' => $imgSrc,
        ]);
    }

    /**
     * 生成结婚证图片
     * 对应显示的内容
     * 持证人姓名、领证日期
     *
     * 男女姓名，性别，国籍
     * @param  $man [男士姓名]
     * @param  $woman [女士姓名]
     * @param  $date 登记日期
     * @param  $mNationality [男士国籍]
     * @param  $wNationality [女士国籍]
     */
    public function getMarriage($image,$man,$woman,$date,$mNationality='中国',$wNationality='中国')
    {    
        $image->text($man,115,85,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        $image->text($date,127,130,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        $image->text($man,138,298,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        $image->text($mNationality,140,318,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(10);

        });
        $image->text('男',295,262,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        $image->text('女',435,254,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);

        });
        $image->text('女',313,339,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        $image->text($woman,150,379,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        $image->text($wNationality,153,401,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(12);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(10);

        });
    }

    /**
     * 生成请柬图片
     * @param  $man [男士姓名]
     * @param  $woman [女士姓名]
     * @param  $dateTime 举行婚礼的详细时间
     * @param  $noliDate [农历日期]
     * @param  $hotel [举办婚礼的酒店名称].
     * @param  $keyword [爱情宣言]
     * @return [type] [description]
     */
    public function getInvitationCard($image,$dateTime,$noliDate,$man,$woman,$hotel,$keyword)
    {
        if(mb_strlen($keyword) > 16) {
            $word1 = mb_substr($keyword, 0,16);
            $word2 = mb_substr($keyword, 16);
            $image->text($word1,262,530,function($font){
                $font->file('./applet/invitation/static/font/jianti.ttf');
                $font->size(14);
                $font->color('#962800');
                $font->align('left');
                $font->valign('top');
                $font->angle(-3);

            });

            $image->text($word2,262,548,function($font){
                $font->file('./applet/invitation/static/font/jianti.ttf');
                $font->size(14);
                $font->color('#962800');
                $font->align('left');
                $font->valign('top');
                $font->angle(-3);

            });
        }else{
            $image->text($keyword,270,530,function($font){
                $font->file('./applet/invitation/static/font/jianti.ttf');
                $font->size(14);
                $font->color('#962800');
                $font->align('left');
                $font->valign('top');
                $font->angle(-3);

            });
        } 
        //日期，时间显示
        $image->text($dateTime,265,340,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        }); 
        //农历日期显示
        $image->text($noliDate,302,362,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        }); 
        //新郎姓名显示
        $image->text($man,325,426,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-3);
        }); 

        //新娘姓名显示
        $image->text($woman,430,431,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        });
        //酒店名称显示
        $image->text($hotel,355,462,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        });
        
    }

    /**
     * 获取结婚证与请柬合并的图片
     * @param  $man [男士姓名]
     * @param  $woman [女士姓名]
     * @param  $licensingDate [领证日期]
     * @param  $dateTime 举行婚礼的详细时间
     * @param  $noliDate [农历日期]
     * @param  $hotel [举办婚礼的酒店名称].
     * @param  $keyword [爱情宣言]
     * @param  $mNationality [男士国籍]
     * @param  $wNationality [女士国籍]
     * @return [type] [description]
     */
    public function getMergeCard($image,$licensingDate,$dateTime,$noliDate,$man,$woman,$hotel,$keyword,$mNationality='中国',$wNationality='中国')
    {
        //显示持证人姓名（男士）
        $image->text($man,118,83,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });
        //领证日期
        $image->text($licensingDate,127,129,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });

        //显示男士姓名
        $image->text($man,138,298,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(14);

        });

        //显示男士国籍
        $image->text($mNationality,140,318,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });

        //显示男士性别
        $image->text('男',296,261,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });

        //显示女士姓名
        $image->text($woman,150,378,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(14);

        });

        //显示女士国籍
        $image->text($wNationality,153,401,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });

        //显示女士性别
        $image->text('女',435,253,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);

        });

        //显示女士性别
        $image->text('女',313,340,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(13);
            $font->color('#48464B');
            $font->align('left');
            $font->valign('top');
            $font->angle(12);

        });

        if(mb_strlen($keyword) > 16) {
            $word1 = mb_substr($keyword, 0,16);
            $word2 = mb_substr($keyword, 16);
            $image->text($word1,290,990,function($font){
                $font->file('./applet/invitation/static/font/jianti.ttf');
                $font->size(14);
                $font->color('#962800');
                $font->align('left');
                $font->valign('top');
                $font->angle(-3);

            });

            $image->text($word2,290,1010,function($font){
                $font->file('./applet/invitation/static/font/jianti.ttf');
                $font->size(14);
                $font->color('#962800');
                $font->align('left');
                $font->valign('top');
                $font->angle(-3);

            });
        }else{
            $image->text($keyword,290,990,function($font){
                $font->file('./applet/invitation/static/font/jianti.ttf');
                $font->size(14);
                $font->color('#962800');
                $font->align('left');
                $font->valign('top');
                $font->angle(-3);

            });
        }

        //日期，时间显示
        $image->text($dateTime,310,780,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        }); 
        //农历日期显示
        $image->text($noliDate,333,800,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        }); 
        //新郎姓名显示
        $image->text($man,349,874,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        }); 

        //新娘姓名显示
        $image->text($woman,470,878,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        });
        //酒店名称显示
        $image->text($hotel,378,915,function($font){
            $font->file('./applet/invitation/static/font/jianti.ttf');
            $font->size(14);
            $font->color('#962800');
            $font->align('left');
            $font->valign('top');
            $font->angle(-2);
        }); 
    }

    
}


