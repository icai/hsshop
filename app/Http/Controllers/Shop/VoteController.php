<?php
/**
 * Created by wuxiaoping.
 * User: wuxiaoping
 * Date: 2017/10/26
 */

namespace App\Http\Controllers\shop;


use App\Http\Controllers\Controller;
use App\S\Vote\ActivityVoteService;
use App\S\Vote\EnrollInfoService;
use App\S\Vote\VoteLogService;
use Validator;
use Illuminate\Http\Request;
use Captcha;
use QrCodeService;
use Intervention\Image\ImageManager;


class VoteController extends Controller
{

    /**
     * 移动端投票首页
     * @param  int $id 投票活动id
     * @return [type]     [description]
     */
    public function index($wid,$id,Request $request,ActivityVoteService $activityVoteService,EnrollInfoService $enrollInfoService,VoteLogService $voteLogService)
    {
        $mid = session('mid');
        if (empty($id)) {
            abort('404');
        }
        $voteData = $activityVoteService->getRowById($id);
        if (empty($voteData)) {
            error('该投票活动不存在或已删除');
        }else{
            if (session('wid') != $voteData['wid']) {
                error('该店铺未设置该投票活动');
            }
        }
        //活动结束剩余时间
        $remaining_time = ($voteData['end_time'] - time()) < 0 ? 0 : ($voteData['end_time'] - time());
        $input = $request->input();
        $input['vote_id'] = $id;
        $orderby = $input['orderby'] ?? 'created_at';
        if ($request->isMethod('post')) {
            $data = [];

            $rule = [
                'id'      => 'required',
            ];

            $message = [
                'id.required'     => '投票号码不能为空',
            ];
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }

            $info = $enrollInfoService->getRowById($input['id']);
            if (empty($info)) {
                error('该投票号码不存在');
            }

            //限制投票
            $voteLogDataLimit = $voteLogService->getListByWhereData($wid,['mid' => $mid,'enroll_id' => $input['id'],'created_at' => date('Y-m-d',time())],'',[],'statistics');

            $voteLogDataUserLimit = $voteLogService->getListByWhereData($wid,['mid' => $mid,'created_at' => date('Y-m-d',time())],'enroll_id',[],'statistics');
            //如果该用户对当前的参加投票用户已进行投票的情况
            if ($voteLogDataLimit) {
                if (count($voteLogDataLimit) >= $voteData['many_ticket']) {
                    error('您每天对同一活动号码只能投'.$voteData['many_ticket'].'票');
                }
            }else{
                if (count($voteLogDataUserLimit) >= $voteData['many_people']) {
                    error('您每天最多对'.$voteData['many_people'].'个人进行活动投票');
                }
            }
            
            //增加投票数
            if ($enrollInfoService->increment($input['id'],['id' => $input['id']])) {
                /*添加投票日志记录*/
                $data['mid']        = $mid;
                $data['wid']        = $wid;
                $data['enroll_id']  = $input['id'];
                $data['vote_id']    = $id;
                $data['created_at'] = date('Y-m-d',time());
                if ($voteLogService->add($data)) {
                    $enrollInfo = json_decode($info['enroll_info'],true);
                    $originalImg = public_path('shop/images/vote-up.png');
                    $qrcodeImg   = public_path('shop/images/xw_youjia.png');
                    $root = config('app.url');
                    $imgData = explode($root,$info['head_img']);
                    $headImg = public_path($imgData[1]);
                    /*判断上传的图片是否需要反转（上传的不是上下的正面图）*/
                    $headImg = $this->isReversalImg($headImg);
                    $this->mergeImages($wid,$originalImg,$headImg,$qrcodeImg,$info['id'],$enrollInfo['name'],$info['id']);
                    $imgSrc = imgUrl('vote/'.$wid.'/'.$info['id'].'/vote_'.$info['id'].'.png');
                    success('投票成功','',['imgUrl' => $imgSrc]);
                }
                error('投票失败');
            }

        }

        $enrollList = $enrollInfoService->getAllList($wid,$input,'',false); //计算参加投票总数

        $currentNumber = $maximum = 0;
        //获取参加活动的当前号码
        $dataInfo = $enrollInfoService->getListByWhereData($wid,['mid' => $mid,'vote_id' => $id]);
        if ($dataInfo) {
            $currentNumber = $dataInfo[0]['id'];
            //计算距离大奖的票数（即最高票数+1）
            $maximum = $enrollInfoService->getMaximum($wid,$id,$mid);
        }
        
        //统计投票人次
        $num = $voteLogService->getPersonTime($wid,$id);

        return view('shop.vote.index',[
            'title'          => $voteData['act_title'] ?? '投票活动',
            'voteData'       => $voteData,
            'enrollList'     => $enrollList,
            'num'            => $num,
            'currentNumber'  => $currentNumber,
            'remaining_time' => $remaining_time,
            'maximum'        => $maximum,
            'id'             => $id,   //投票活动id
        ]);
    }

    /**
     * 移动端首页搜索，排序返回数据
     * 请求该地址须带上vote_id参数   例：/shop/vote/getSearchList?vote_id=9
     * @param  Request           $request           [description]
     * @param  EnrollInfoService $enrollInfoService [description]
     * @return [type]                               [description]
     */
    public function getEnrollListBySearch(Request $request,ActivityVoteService $activityVoteService,EnrollInfoService $enrollInfoService,VoteLogService $voteLogService)
    {
        $wid = session('wid');
        $input = $request->input();
        if (!isset($input['vote_id'])) {
            error('该投票活动不存在或已删除');
        }
        $orderby = isset($input['orderby']) ? $input['orderby'] : 'created_at';
        //参加投票活动列表
        list($enrollList,$pageHtml) = $enrollInfoService->getAllList($wid,$input,$orderby);

        if ($enrollList) {
            foreach ($enrollList['data'] as $key => &$value) {
                //保存name值
                if ($value['enroll_info']) {
                    $enrollInfo = json_decode($value['enroll_info'],true);
                    $value['name'] = $enrollInfo['name'];
                }else{
                    $value['name'] = '';
                }
            }
        }
        $returnData = $enrollList;
        success('','',$returnData);
    }

    /**
     * 投票报名页面
     * @param  Request             $request             [description]
     * @param  EnrollInfoService   $enrollInfoService   [参加投票活动Service]
     * @param  ActivityVoteService $activityVoteService [报名活动Service]
     * @return [type]                                   [description]
     */
    public function enroll(Request $request,EnrollInfoService $enrollInfoService,ActivityVoteService $activityVoteService)
    {
        $data = $enrollInfo = [];
        $headImg = '';
        $enrollData = $enrollInfoService->getListByWhereData(session('wid'),['vote_id' => $request->input('id'),'mid' => session('mid')]);
        if ($enrollData) {
            $headImg = imgUrl('vote/'.session('wid').'/'.$enrollData[0]['id'].'/enroll_'.$enrollData[0]['id'].'.png');
        }
        if(!$request->input('id')){
            error('数据异常');
        }
        $actVoteInfo = $activityVoteService->getRowById($request->input('id'));
        if ($request->isMethod('post')) {
            $input = $request->input();

            $rule = [
                'img'     => 'required',
                'id'      => 'required',
                'name'    => 'required',
                'contact' => 'required',
                'phone'   => 'required'
            ];
            $message = [
                'id.required'      => '数据异常',
                'img.required'     => '参加投票照片不能为空',
                'name.required'    => '参加投票姓名不能为空',
                'contact.required' => '参加投票联系人不能为空',
                'phone.required'   => '联系电话不能为空',   
            ];
            $validator = Validator::make($input,$rule,$message);
            if ($validator->fails()){
                error($validator->errors()->first());
            }
            
            if(empty($actVoteInfo)){
                error('该设票活动不存在或已被删除');
            }else{
                if (session('wid') != $actVoteInfo['wid']) {
                    error('该店铺未设置该投票活动');
            }
            }

            if ($enrollData) {
                error('您已报名成功，不能重复提交');
            }

            $data['mid']           = session('mid');
            $data['wid']           = session('wid');
            $data['vote_id']       = $input['id'];
            $data['head_img']      = $input['img'];
            $data['created_at']    = time();
            $enrollInfo['name']    = $input['name'] ?? ''; //参加投票活动姓名
            $enrollInfo['sex']     = $input['sex'] ?? '1'; // 1表示男 2表示女
            $enrollInfo['contact'] = $input['contact'] ?? ''; //联系人
            $enrollInfo['phone']   = $input['phone'] ?? '';
            $data['enroll_info']   = json_encode($enrollInfo,JSON_UNESCAPED_UNICODE);
            if($enrollId = $enrollInfoService->add($data)){
                $originalImg = public_path('shop/images/vote-up.png');
                $qrcodeImg   = public_path('shop/images/xw_youjia.png');
                $root = config('app.url');
                $imgData = explode($root,$input['img']);
                $headImg = public_path($imgData[1]);
                /*判断上传的图片是否需要反转（上传的不是上下的正面图）*/
                $headImg = $this->isReversalImg($headImg);
                $this->mergeImages(session('wid'),$originalImg,$headImg,$qrcodeImg,$enrollId,$input['name'],'enroll');
                $imgSrc = imgUrl('vote/'.session('wid').'/'.$enrollId.'/enroll_'.$enrollId.'.png');
                success('报名成功','',['imgUrl' => $imgSrc]);
            }
            error();
        }
        return view('shop.vote.enroll',[
            'title'          => $actVoteInfo['act_title'] ?? '投票活动',
            'headImg' => $headImg,
        ]);
    }

    /**
     * 获取报名页面的数据信息
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getEnrollData($id,Request $request,EnrollInfoService $enrollInfoService)
    {
        $returnData = [];
        $enrollData = $enrollInfoService->getListByWhereData(session('wid'),['vote_id' => $id,'mid' => session('mid')]);
        if ($enrollData) {
            foreach ($enrollData as $key => $value) {
                $enrollInfo = json_decode($value['enroll_info'],true);
                $returnData['id']       = $value['id'];
                $returnData['mid']      = $value['mid'];
                $returnData['wid']      = $value['wid'];
                $returnData['vote_id']  = $value['vote_id'];
                $returnData['head_img'] = $value['head_img'];
                $returnData['name']     = $enrollInfo['name'];
                $returnData['sex']      = $enrollInfo['sex'];
                $returnData['contact']  = $enrollInfo['contact'];
                $returnData['phone']    = $enrollInfo['phone'];
            }
        }
        return $returnData;
    }

    /**
     * 奖项设置页面
     * @param  [int]              $id                   [投票活动表id]
     * @param  ActivityVoteService $activityVoteService [description]
     * @return [type]                                   [description]
     */
    public function prizes($id,ActivityVoteService $activityVoteService)
    {
        $voteData = $activityVoteService->getRowById($id);
        if (empty($voteData)) {
            error('该投票活动不存在或已删除');
        }else{
            if (session('wid') != $voteData['wid']) {
                error('该店铺未设置该投票活动');
            }
        }
        return view('shop.vote.prizes',[
            'title'          => $voteData['act_title'] ?? '投票活动',
            'voteData' => $voteData
        ]);
    }

    /**
     * 拉票秘籍页面
     * @param  [int]              $id                   [投票活动表id]
     * @param  ActivityVoteService $activityVoteService [description]
     * @return [type]                                   [description]
     */
    public function canvass($id,ActivityVoteService $activityVoteService)
    {
        $voteData = $activityVoteService->getRowById($id);
        if (empty($voteData)) {
            error('该投票活动不存在或已删除');
        }else{
            if (session('wid') != $voteData['wid']) {
                error('该店铺未设置该投票活动');
            }
        }
        return view('shop.vote.canvass',[
            'title'          => $voteData['act_title'] ?? '投票活动',
            'voteData' => $voteData
        ]);
    }

    /**
     * 合并图片
     * @param  [string] $originalImg [原图地址]
     * @param  [string] $insertImg   [要插入的图片地址]
     * @return [type]              [description]
     */
    public function mergeImages($wid,$originalImg,$insertImg,$qrcodeImg,$num=123,$name='宋国明',$type='vote')
    {
        $manager = new ImageManager(array('driver' => 'gd'));
        $image = $manager->make($originalImg)->resize(700,null,function($constraint){
            $constraint->aspectRatio();  
        });
        $headmark = $manager->make($insertImg)->fit(473, 480, function ($constraint) {
            $constraint->upsize();
        });
        $bottom = 200;
        if ($type == 'enroll') {
            $bottom = 200;
        }
        $image->insert($headmark,'top',0,$bottom); 
        //设置添加二维码图片
        $qrcodemark = $manager->make($qrcodeImg)->resize(120,120);
        $image->insert($qrcodemark,'bottom-right',30,30); 

        $this->setFontShow($image,$num,$name,$num,$type);

        //定义一个保存图片的目录
        $path       = public_path('./vote/'.$wid.'/'.$num.'/');
        if(!file_exists($path)){
            mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
        //保存合并的图片
        if ($type == 'enroll') {
            $fileName = $path.'enroll_'.$num.'.png';
        }else{
            $fileName = $path.'vote_'.$num.'.png';
        }
        $image->save($fileName);  
        $image->destroy();                            
        
    }

    /**
     * 设置文体显示
     */
    public function setFontShow($image,$num1,$name,$num2,$type)
    {
        //设置文字显示位置
        $left = $right = 0;
        if (strlen($num1) == 1) {
            $left = 60;
            $right = 460;
        }else if (strlen($num1) == 2){
            $left = 50;
            $right = 450;
        }else if (strlen($num1) == 3) {
            $left = 40;
            $right = 440;
        }else if (strlen($num1) == 4) {
            $left = 30;
            $right = 430;
        }else{
            $left = 20;
            $right = 420;
        }
        $n1_bottom = 50;
        $name_bottom = 35;
        $n2_bottom = 830;
        if ($type == 'enroll') {
            $n1_bottom = 60;
            $name_bottom = 35;
            $n2_bottom = 830;
        }
        $image->text($num1,$left,$n1_bottom,function($font){
            $font->file('./font/huakang_W7-GB.TTC');
            $font->size(30);
            $font->color('#fff');
            $font->align('left');
            $font->valign('top');
            $font->angle(10);

        });

        $image->text($name,350,$name_bottom,function($font){
            $font->file('./font/jianti.ttf');
            $font->size(30);
            $font->color('#ff0000');
            $font->align('left');
            $font->valign('top');
            $font->angle(0);

        });
        $image->text($num2,$right,$n2_bottom,function($font){
            $font->file('./font/jianti.ttf');
            $font->size(30);
            $font->color('#fff');
            $font->align('left');
            $font->valign('top');
            $font->angle(-1);

        });
    }

    /**
     * 是否对图片进行反转处理
     * @param  [type]  $path [description]
     * @return boolean       [description]
     */
    public function isReversalImg($path)
    {
        $image = imagecreatefromstring(file_get_contents($path));
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

        return $path;
    }

}
