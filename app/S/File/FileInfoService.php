<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-2
 * Time: 下午8:52
 */

namespace App\S\File;


use App\Lib\Redis\FileInfoRedis;
use App\Model\FileInfo;
use App\S\S;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use RedisPagination;
use Storage;
use Validator;

use Upyun\Upyun;
use Upyun\Config;
class FileInfoService extends S
{


    /**
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');
        parent::__construct('FileInfo');

    }

    public function upUpyunRegister($file,$name = "")
    {
        $bucketConfig = new Config('huisoucn', 'phpteam', 'phpteam123456');
        $client = new Upyun($bucketConfig);
        $f = fopen($file, 'rb');
        return $client->write($name, $f);
    }

    public function upUpyun($file,$name = "")
    {
        $bucketConfig = new Config('huisoucn', 'phpteam', 'phpteam123456');
        $client = new Upyun($bucketConfig);
        $f = fopen($file, 'rb');
        $client->write($name, $f);
        return $client->info($name);
    }

    public function hasUpyun($fileName)
    {
        $bucketConfig = new Config('huisoucn', 'phpteam', 'phpteam123456');
        $client = new Upyun($bucketConfig);
        return $client->has($fileName);
    }

    /**
     * @auth zhangyh 928013258@qq.com
     * @version 201703022115
     * @param Request $request
     * @ todo 上传文件
     */
    public function  upFile($file, $name = "")
    {
        $result = Array(
            'success'   =>0,
            'message'   =>'',
        );
        if (!$file){
            error('上传文件不能为空');
        }
        //如果是图片生成缩略图
        $s_path = '';
        $m_path = '';
        $l_path = '';
        $img_size = '';


        $type = $file->getMimeType();
        $mine = $this->getFileType($type);
        $size = $file->getSize();
        $path = $this->getFilePath($file);

        if ($mine != 'image') {
            $bucketConfig = new Config('huisoucn', 'phpteam', 'phpteam123456');
            $client = new Upyun($bucketConfig);
            $f = fopen($file, 'rb');
            $client->write($path, $f);
            $res=$client->info($path);
        } else {
            //获取文件前缀
            if (Storage::exists($path))
            {
                $path = $this->getFilePath($file);

            }
            //存储文件
            $bytes = Storage::put(
                $path,
                file_get_contents($file->getRealPath())
            );
            if (!Storage::exists($path))
            {
                error();
            }
        }
        //处理图片
       if ($this->isImage($type)){
           $resArr = $this->dealImage($file);
           $s_path = $resArr[0];
           $m_path = $resArr[1];
           $l_path = $resArr[2];
           $img_size = $resArr[3];
       }
       if ($mine == 'image') {
           $file_mine = 1;
       } elseif ($mine == 'video') {
           $file_mine = 2;
       }elseif ($mine == 'audio') {
           $file_mine = 3;
       }else{
            error(); 
       }
       if (empty($name)) {
           $name = $this->getFileName($file->getClientOriginalName());
       }
        //插入文件信息
        $data  = Array(
            'name'      => $name,
            'path'      => $path,
            'type'      => $file->getMimeType(),
            'size'      => $file->getSize(),
            's_path'    => $s_path,
            'm_path'    => $m_path,
            'l_path'    => $l_path,
            'img_size'  => $img_size,
            'file_mine' => $file_mine
        );
        if ($this->add($data))
        {
            $result['success'] = 1;
            $result['data'] = $data;
            return $result;
        }else{
            error();
        }
    }


    /**
     * @auth zhangyh
     * @desc 处理图片缩略图
     * @param $file
     * @param $id
     * @update 张永辉 2018年10月16日 当宽高小于1时不压缩
     */
    function dealImage($file)
    {
        //生成图片缩略图
        $manager = new ImageManager();
        $img_size = config('filesystems.image');
        $imgObj =  $manager->make($file->getRealPath());
        $width = $imgObj->width();
        $height = $imgObj->height();
        //获取图片比例
        $ratio = $imgObj->height()/$imgObj->width();
        //大图存储位置
        $l_path = $this->getFilePath($file,"_l");
        $l_width = $img_size['l']*$width/100;
        if ($l_width < 1 || $l_width * $ratio < 1) {
            $image = $imgObj->resize($width, $height);
        } else {
            $image = $imgObj->resize($l_width, $l_width * $ratio);
        }
        $image->save($l_path);
        //中图
        $m_path = $this->getFilePath($file,"_m");
        $m_width = $img_size['m']*$width/100;
        if ($m_width < 1 || $m_width * $ratio < 1) {
            $image = $imgObj->resize($width, $height);
        } else {
            $image = $imgObj->resize($m_width, $m_width*$ratio);
        }
        $image->save($m_path);
        //小图
        $s_width = $img_size['s']*$width/100;
        if ($s_width < 1 || $s_width * $ratio < 1) {
            $image = $imgObj->resize($width, $height);
        } else {
            $image = $imgObj->resize($s_width, $s_width*$ratio);
        }
        $s_path = $this->getFilePath($file,"_s");
        $image->save($s_path);
        return [$s_path,$m_path,$l_path,$width.'x'.$height];
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @desc 保存图片
     * @param $taget
     * @param $path
     */
    function saveFile($taget,$path)
    {
        Storage::put(
            $taget,
            file_get_contents($path)
        );
        @unlink($path);
    }

    /**
     * @desc 获取文件路径
     * @param $file
     * @param $id
     * @param string $extend
     * @update 张永辉 2019年9月26日 如果后缀为空重新生成后缀
     */
    function getFilePath($file, $extend = null)
    {
        $type = $file->getMimeType();
        $prefix = $this->getFileType($type);
        $fileName = date('His') . rand(0, 99999) . rand(0, 99999);
        $path = config('filesystems.file_path') . '/' . $prefix . '/' . date('Y/m/d') . '/' . $fileName . $extend . '.' . $file->getClientOriginalExtension();
        if (!$file->getClientOriginalExtension()) {
            $path .= $this->getFileType($file->getMimeType(), '1');
        }
        return $path;
    }

    /**
     * @auth zhangyh 201703031102
     * @param $type
     * @return mixed
     * @desc 获取文件类型
     * @update 张永辉 2019年9月26日 返回文件后缀
     */
    function getFileType($type, $flag = '')
    {
        $types = Array(
            'image' => [
                'image/jpeg' => 'jpg',
                'image/gif'  => 'gif',
                'image/png'  => 'png',
                'image/bmp'  => 'bmp',
                'psd'        => 'application/octet-stream',
                'ico'        => 'image/x-icon',
            ],
            'audio' => [
                'audio/mpeg'     => 'mp3',
                'audio/midi'     => 'mid',
                'audio/ogg'      => 'ogg',
                'audio/mp4'      => 'mp4a',
                'audio/wav'      => 'wav',
                'audio/x-ms-wma' => 'wma',
            ],
            'video' => [
                'video/x-msvideo'  => 'avi',
                'video/x-dv'       => 'dv',
                'video/mp4'        => 'mp4',
                'video/mpeg'       => 'mpeg',
                'video/quicktime'  => 'mov',
                'video/x-ms-wmv'   => 'wm',
                'video/x-flv'      => 'flv',
                'video/x-matroska' => 'mkv'
            ]
        );
        if ($flag == '1') {
            foreach ($types as $val) {
                if (isset($val[$type])) {
                    return $val[$type];
                }
            }
            return 'png';
        }
        if (isset($types['image'][$type])) {
            return 'image';
        } elseif (isset($types['audio'][$type])) {
            return 'audio';
        } else if (isset($types['video'][$type])) {
            return 'video';
        } else {
            return 'other';
        }
    }

    /**
     * @auth zhangyh
     * @desc 判断是否为图片
     * @param $type
     */
    function isImage($type)
    {
        $tmp = Array();
        $tmp = explode('/',$type);
        if ($tmp[0] == 'image') {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取文件名
     * @param $fileName
     * @return string
     */
    function getFileName($fileName)
    {
        $tmp = Array();
        $tmp = explode(".",$fileName);
        array_pop($tmp);
        return implode($tmp,'.');

    }

    /**
     * @auth zhangyh
     * @date 201703031336
     * @param $data
     * @todo 插入文件信息
     */
    function insert(&$data)
    {
//        /*插入数据 */
//        try{
//            $insertGetId= $this->model->insertGetId($data);
//        }catch (exception $e)
//        {
//            return false;
//        }
//        $insertDatas = array();
//        /*redis插入 */
//        if($insertGetId) {
//            $data['id'] = $insertGetId;
//            $insertDatas[$insertGetId] = $data;
//            /*redis插入 */
//            RedisPagination::save($insertDatas);
//            return true;
//        }
//        return false;

        $res = $this->add($data);
        if ($res){
            $data['id'] = $res;
            return true;
        }else{
            error();
        }
    }

    //新service
    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id获取列表
     * @param $id
     * @return array
     */
    public function getRowById($id)
    {
        $result = [];
        $redis = new FileInfoRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->addArr($result);
        }
        return $result;
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new FileInfoRedis();
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170626
     * @desc 根据id更新数据
     * @param $id
     * @param $data
     */
    public function update($id,$data){
        $res = $this->model->where('id',$id)->update($data);
        if ($res){
            $storeRedis = new FileInfoRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new FileInfoRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add(&$data)
    {
        $id =  $this->model->insertGetId($data);
        $data['id'] = $id;
        return $id;
    }

    public function getlistPage($where)
    {
        return $this->getListWithPage($where, '', '');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function getCDNFilePath($input)
    {
        $fileName = date('His').rand(0,99999).rand(0,99999);
        if (isset($input['type'])){
            $type = $this->getFileType($input['type']);
        }else{
            $type = 'image';
        }
        if (!empty($input['flag']) && $input['flag'] == '1'){
            $path = config('filesystems.file_path').'/ueditor/'.$type.'/'.date('Y/m/d').'/'.$fileName.'{.suffix}';
        }else{
            $path = config('filesystems.file_path').'/'.$type.'/'.date('Y/m/d').'/'.$fileName.'{.suffix}';
        }
        return $path;
    }

}
