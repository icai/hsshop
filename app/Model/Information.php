<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\InformationType;

class Information extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='information';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function account()
    {
        return $this->belongsTo('App\Model\Account','account_id')->select();
    }

    /**
     * 获取某篇文章的上下文
     * @param  [int] $id        [资讯详情id]
     * @param  [int] $info_type [资讯分类]
     * @return [type]            [description]
     */
    public function context($id,$info_type)
    {
        $data = $returnData = [];
        $obj = $this->wheres(['info_type' => $info_type])->where(function($query) use($id){
            $query->where('id','>',$id)
            ->orWhere(function($query) use($id){
                $query->where('id','<',$id);
            });
        })->order('id ASC')->get();

        $nextArr = $preArr = [];
        if($obj){
            $data = $obj->toArray();
            foreach($data as $key=>$val){
                if($val['id'] > $id){
                    $greaterKeys[] = $key;
                }else{
                    $lessKeys[] = $key;
                }
            }
            //下一篇资讯
            if (isset($greaterKeys) && $greaterKeys) {
                $nextArr['id']    = $data[$greaterKeys[0]]['id'];
                $nextArr['title'] = $data[$greaterKeys[0]]['title'];
            }
            //上一篇资讯
            if (isset($lessKeys) && $lessKeys) {
                $preArr['id']    = $data[$lessKeys[0]]['id'];
                $preArr['title'] = $data[$lessKeys[0]]['title'];
            }

        }
        $returnData['pre']  = $preArr;
        $returnData['next'] = $nextArr;
        return $returnData;

    }

    public function getNewsFromTree()
    {
        $informationType = new InformationType();
        $informationTypeList = $informationType->wheres(['parent_id' => 0])->get()->toArray();
        foreach($informationTypeList as $key=>$category){
            if($category['name'] == '帮助中心'){
                array_splice($informationTypeList, $key,1);
            }
        }

        $result = $ids = [];
        foreach($informationTypeList as $val){
            $val['id'] = addslashes(strip_tags($val['id']));
            $where['_string'] = ' find_in_set('.$val['id'].',type_path)';
            $result[] = $informationType->wheres($where)->get()->toArray();
        }

        foreach($result as $items){
            $name = $items[0]['name'];
            foreach($items as $item){
                $ids[$name][] = $item['id'];
            }
        }
        

        foreach($ids as $key=>$idArr){
            $newWhere['id'] = ['in',$idArr];
            $data[$key][] = $this->wheres($newWhere)->orderBy('id','desc')->get()->toArray();
        }
        
        return $data;
        
    }

    /**
     * 根据关键词获取相关新闻,助排除掉当前的新闻
     * @author 吴晓平 update 2018.07.11
     * @param  [string]  $keywords  [新闻所属相同的关键词]
     * @param  integer $currentId [当前新闻资讯的id]
     * @return [type]             [description]
     */
    public function getNewsFromKeywords($keywords,$currentId=0)
    {
        $where['keywords'] = ['like','%'.$keywords.'%'];
        if ($currentId) {
            $where['id'] = ['<>',$currentId];
        }
        $data = [];
        $obj = $this->wheres($where)->OrderBy('id','desc')->limit(5)->get();
        if($obj){
            $data = $obj->toArray();
        }
        return $data;
    }

}
