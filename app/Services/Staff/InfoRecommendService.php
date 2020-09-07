<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/14  13:28
 * DESC
 */

namespace App\Services\Staff;


use App\Model\InfoRecommend;
use App\Services\Service;
use Redisx;

class InfoRecommendService extends Service
{

    public static $recommendInforData;

    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id','info_id','rec_id','created_at'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new InfoRecommend(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141407
     * @desc 添加推荐
     * @param $id
     * @param $recommentIds
     */
    public function addInfoRecomment($id,$recommentIds)
    {
        $result = Array('success'=>0,'message'=>'');
        $infomationService = new InformationService();
        $infomationData = $infomationService->init()->getInfo($id);
        if (!$infomationData){
            $result['message'] = '该咨询不存在';
            return $result;
        }

        $infoRecommentData = Array(
            'info_id'       => $id,
        );
        foreach ($recommentIds as $val)
        {
            $infoRecommentData['rec_id'] = $val;
            $res = $this->init()->add($infoRecommentData,false);
            if (!$res){
                $result['message'] = '推荐失败！请稍后重试';
            }
        }
        $this->setRecommendInfor();
        $result['success'] = 1;
        return $result;

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141718
     * @desc 根据推荐ID获取推荐资讯
     * @param $recommentId
     */
    public function getRecommentInfo($param,$flag='id')
    {
        if ($flag == 'id'){
            $where = Array(
                'rec_id'    => $param,
            );
        }elseif ($flag == 'uri'){
            $recomemndService = new RecommendService();
            $res = $recomemndService->init()->model->where(['uri'=>$param])->select('id')->get()->toArray();
            $ids = [];
            if ($res){
                foreach ($res as $val)
                {
                    $ids[] = $val['id'];
                }
            }
            $where = Array(
                'rec_id'    => ['in',$ids],
            );
        }

        list($infoIds) = $this->init()->where($where)->select('info_id')->getList(false);
        $infoIds = $infoIds['data'];
        if ($infoIds){
            $whereids = [];
            foreach ($infoIds as $val)
            {
                $whereids[] = $val['info_id'];
            }
            $informationService = new InformationService();
            $informationData = $informationService->get(['id'=>['in',$whereids]]);
            return $informationData;

        }else{
            return [];
        }
    }

//    public function  getInfoByUri($uri)
//    {
//        $this->with(['information','recommend']);
//        $data = $this->init()->where(['1'=>1])->getList(false);
//        $infoData = $this->getRecommentInfo($uri,'uri');
//        if ($infoData){
//            foreach ($infoData[0]['data'] as &$val){
//                $val['content'] = substr($val['content'],0,100);
//            }
//        }
//        return $infoData;
//    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703271942
     * @desc 写资讯内容到redis
     */
    public function setRecommendInfor()
    {
        $this->with(['information','recommend']);
        list($data) = $this->init()->where(['1'=>1])->getList(false);
        $inforData = [];
        foreach ($data['data'] as $val)
        {
            if (!empty($val['information'])){
                $val['information']['content'] = $this->intercept(strip_tags($val['information']['content']),40);
                $tmp = [
                    'id'            =>$val['information']['id'],
                    'uri'           => $val['recommend']['uri'],
                    'title'         => str_limit($val['information']['title'],20),
                    'sub_title'    => $val['information']['sub_title'],
                    'content'       => $val['information']['content'],
                ];
                $inforData[$tmp['uri']][]=$tmp;
            }
        }
        //推荐资讯加入到redis
//        self::$recommendInforData = $inforData;
        Redisx::SET($this->getKey(),json_encode($inforData));

    }

    public function getInfor()
    {
        if (!Redisx::EXISTS($this->getKey())){
            $this->setRecommendInfor();
        }
        return json_decode(Redisx::GET($this->getKey()),true);
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703271945
     * @desc 截取字符串
     * @param $str
     * @param $len
     * @param string $charset
     * @return string
     */
    function intercept($str, $len, $charset="utf-8")
    {
        //如果截取长度小于等于0，则返回空
        if( !is_numeric($len) or $len <= 0 )
        {
            return "";
        }

        //如果截取长度大于总字符串长度，则直接返回当前字符串
        $sLen = strlen($str);
        if( $len >= $sLen )
        {
            return $str;
        }
        //判断使用什么编码，默认为utf-8
        if ( strtolower($charset) == "utf-8" )
        {
            $len_step = 3; //如果是utf-8编码，则中文字符长度为3
        }else{
            $len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
        }

        //执行截取操作
        $len_i = 0;
        //初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
        $substr_len = 0; //初始化应该要截取的总字节数

        for( $i=0; $i < $sLen; $i++ )
        {
            if ( $len_i >= $len ) break; //总截取$len个字符串后，停止循环
            //判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
            if( ord(substr($str,$i,1)) > 0xa0 )
            {
                $i += $len_step - 1;
                $substr_len += $len_step;
            }else{ //否则，为英文字符，加1个字节
                $substr_len ++;
            }
            $len_i ++;
        }
        $result_str = substr($str,0,$substr_len );
        return $result_str;
    }

    public function getKey()
    {
        return 'Recommend:Information';
    }


}




























