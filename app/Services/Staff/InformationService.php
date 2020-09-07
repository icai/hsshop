<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/13  11:39
 * DESC
 */

namespace App\Services\Staff;


use App\Model\Information;
use App\S\File\FileInfoService;
use App\Services\Service;
use DB;
use Illuminate\Http\Request;

class InformationService extends Service
{
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
        $this->field = ['id','title','sub_title','attachment','content','account_id','info_type','created_at','auth','status','meta','seo_title','keywords','sort'];
        $this->with(['account']);
        $this->withAll = ['account'];

    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new Information(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  201703132106
     * @desc  根据条件查询资讯
     * @param array $where
     */
    public  function get($where = array())
    {
        $input = $_REQUEST;//dd($input);
//        $where = Array();
        /*组装查询条件*/
        $where['1'] = 1;
        if (isset($input['id']) && !empty($input['id'])){
            $where['id'] = $input['id'];
        }
        if (isset($input['title'])  && !empty($input['title'])){
            $where['title'] = ['like','%'.$input['title'].'%'];
        }
        if (isset($input['subTitle']) && !empty($input['subTitle'])){
            $input['subTitle'] = trim($input['subTitle']);
            $where['sub_title'] = ['like','%'.$input['subTitle'].'%'];
        }
        if (isset($input['content']) && !empty($input['content'])){
            $input['content'] = trim($input['content']);
            $where['title'] = ['like','%'.$input['content'].'%'];
        }
        //根据时间查询
        if ((isset($input['startTime']) && !empty($input['startTime'])) && (isset($input['endTime']) && !empty($input['endTime']))){
            $where['created_at'] = array('between', [$input['startTime'], $input['endTime']]);
        }else{
            if (isset($input['startTime']) && !empty($input['startTime'])){
                $where['created_at'] = ['>=',$input['startTime']];
            }
            if (isset($input['endTime']) && !empty($input['endTime'])){
                $where['created_at'] = ['<=',$input['endTime']];
            }
        }
        //根据分类查询 可以根据一级分类，二级分类，三级分类查询
        if (isset($input['threeCategory']) && !empty($input['threeCategory'])){
            $where['info_type'] = $input['threeCategory'];
        }elseif(isset($input['secCategory']) && !empty($input['secCategory'])){
            //根据二级分类查询
            $infoTypeService = new InformationTypeService();
            //$tmpWhere['type_path'] = ['like','%,'.$input['secCategory'].',%'];
            $input['secCategory'] = intval($input['secCategory']);
            $input['secCategory']  = addslashes(strip_tags($input['secCategory']));
            $tmpWhere['_string'] = ' find_in_set('.$input['secCategory'].',type_path)';
            $data = $infoTypeService->init()->where($tmpWhere)->getList(false)[0]['data'];
            if ($data){
                $ids = [];
                foreach ($data as $val)
                {
                    $ids[] = $val['id'];
                }
                $where['info_type'] = ['in',$ids];
            }else{
                $where['info_type'] = $input['secCategory'];
            }
        }elseif (isset($input['oneCategory']) && !empty($input['oneCategory'])){
            //根据一级分类查询
            $infoTypeService = new InformationTypeService();
            //$tmpWhere['type_path'] = ['like','%'.$input['oneCategory'].',%'];
            $input['oneCategory'] = intval($input['oneCategory']);
            $input['oneCategory']  = addslashes(strip_tags($input['oneCategory']));
            $tmpWhere['_string'] = ' find_in_set('.$input['oneCategory'].',type_path)';
            $data = $infoTypeService->init()->where($tmpWhere)->getList(false)[0]['data'];
            if ($data){
                $ids = [];
                foreach ($data as $val)
                {
                    $ids[] = $val['id'];
                }
                $where['info_type'] = ['in',$ids];
            }
        }
        $infoData = $this->init()->where($where)->order('sort desc,created_at desc')->getList();
        if ($infoData['0']['data']){
            $this->dealInfoData($infoData[0]['data']);
        }
        return $infoData;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703132111
     * @desc 处理资讯数据
     * @param $data
     */
    public function dealInfoData(&$data)
    {
        $infoTypeService = new InformationTypeService();
//        $infoTypeData = $infoTypeService->init()->getList(false)[0]['data'];dd($infoTypeData);
        $infoTypeData = $infoTypeService->init()->model->get()->toArray();//dd($infoTypeData);
        /*处理信息分类*/
        $infoTypeTmp = array();
        $typePathTmp = array();
        foreach ($infoTypeData as $key=>$val)
        {
            $infoTypeTmp[$val['id']] = $val['name'];
        }
        //类型路径
        foreach ($infoTypeData as $val)
        {
            if ($val['type_path']){
                $path = explode(',',$val['type_path']);
                foreach ($path as &$v)
                {
                    $v = $infoTypeTmp[$v];
                }
                $typePathTmp[$val['id']] = implode('->',$path);
            }
        }
        if ($data){
          foreach($data as &$val)
          {
              if (isset($infoTypeTmp[$val['info_type']]) && isset($typePathTmp[$val['info_type']])) {
                $val['type_name'] = $infoTypeTmp[$val['info_type']];
                $val['type_path'] = $typePathTmp[$val['info_type']];
              }
              //获取资讯资源
              $source = [];
              if ($val['attachment']){
                  $fileInfoService = new FileInfoService();
                  $tmp = explode(',',$val['attachment']);
                  foreach ($tmp as $value){
                    $source[] = $fileInfoService->getRowById($value);
                  }
              }
              $val['source'] = $source;
              //end section
              $this->replace($val['content']);
          }
        }
    }


    public function replace(&$content)
    {

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170413
     * @desc 获取经营建议和学习交流资讯
     */
    public function getFirstPageInfo()
    {
//        $inforTypeService = new InformationTypeService();
//        $inforType = $inforTypeService->init()->model->where(['name'=>'经营建议','parent_id'=>0])->orWhere(['name'=>'学习交流','parent_id'=>0])->orderBy('id','asc')->get()->toArray();
//        $id1 = '';
//        $id2 = '';
//        foreach ($inforType as $val)
//        {
//            if ($val['name'] == '经营建议'){
//                $id1 = $val['id'];
//            }elseif ($val['name'] == '学习交流'){
//                $id2 = $val['id'];
//            }
//        }
//        $result = [
//            'suggest'       => '',
//            'study'         => '',
//        ];
//        if ($id1){
//            $result['suggest'] = DB::table('information as i')->leftJoin('information_type as it','i.info_type','=','it.id')->where('type_path','like','%'.$id1.',%')->whereNull('i.deleted_at')->orderBy('i.id','desc')->limit(5)->get(['i.id','i.title','i.sub_title'])->toArray();
//        }
//        if($id2){
//            $result['study'] = DB::table('information as i')->leftJoin('information_type as it','i.info_type','=','it.id')->where('type_path','like','%'.$id2.',%')->whereNull('i.deleted_at')->orderBy('i.id','desc')->limit(5)->get(['i.id','i.title','i.sub_title'])->toArray();
//        }
        $_REQUEST['oneCategory'] = 19;
        list($suggest) = $this->get();
        if ($suggest['data']){
            $suggest = array_slice($suggest['data'],0,4);
        }else{
            $suggest = [];
        }
        $_REQUEST['oneCategory'] = 41;
        list($study) = $this->get();
        if ($study['data']){
            $study = array_slice($study['data'],0,4);
        }else{
            $study = [];
        }
        return $result = [
            'suggest'   => $suggest,
            'study'     => $study,
        ];
    }

}
