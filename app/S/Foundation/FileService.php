<?php
namespace App\S\Foundation;

/**
 * 文件类
 */
class FileService
{
    /*
     * 将文件以 UTF-8 编码格式打开
     * @param $filename
     * @return resource  返回处理资源类型
     */
    public static function fopen_utf8($filename){
        $encoding='';
        $handle = fopen($filename,'r');
        $bom = fread($handle, 2);
        rewind($handle);
        if($bom === chr(0xff).chr(0xfe) || $bom === chr(0xfe).chr(0xff)){
            $encoding = 'UTF-16';
        }else{
            $file_sample = fread($handle,1000)+'e'; // + e is a workaround for mb_string bug
            rewind($handle);
            $encoding = mb_detect_encoding($file_sample,'UTF-8,UTF-7,ASCII,EUC-JP,SJIS,eucJP-win,SJIS-win,JIS,ISO-2022-JP');
        }
        if($encoding){
            stream_filter_append($handle,'convert.iconv.'.$encoding.'/UTF-8');
        }
        return $handle;
    }

    /**
     * 获取指定路径下的文件列表
     * 如果第二个参数为true 则会递归的列出子目录下的文件
     * @param String $dir 目录
     * @param String $recursion 是否递归
     */
    public static function getFileList($dir, $recursion = false){
        $fileList = [];
        $real_path = realpath($dir);
        if (is_dir($real_path)) {
            if ($dh = opendir($real_path)) {
                while (($file = readdir($dh)) !== false) {
                    if (strpos($file, '.') === 0) {
                        continue;
                    }
                    $full_path = $real_path . DIRECTORY_SEPARATOR . $file;
                    $filetype = filetype($full_path);
                    $is_dir = $filetype == 'dir';
                    $relative_path = str_ireplace(base_path('public'), '', $full_path);
                    $relative_path = str_replace('\\', '/', $relative_path);
                    $fileList[] = array(
                        'name'=>$file,
                        'path'=>$full_path,
                        'relative_path'=>$relative_path,
                        'is_dir'=>$is_dir,
                    );
                    if($is_dir == true && $recursion == true){
                        $subdir = self::getFileList($real_path . DIRECTORY_SEPARATOR . $file, true);
                        $fileList = array_merge($fileList, $subdir);
                    }
                }
                closedir($dh);
            }
        }
        return $fileList;
    }

    /**
     * 删除整个文件夹
     * 若第二个参数为true，则连同文件夹一同删除（包括自身）
     * @param string $path
     * @param string $del_dir
     * @param number $level
     * @return boolean
     */
    public static function deleteFiles($path, $del_dir = false, $level = 0){
        // Trim the trailing slash
        $path = rtrim($path, DIRECTORY_SEPARATOR);

        if (!$current_dir = @opendir($path)){
            return false;
        }

        while(false !== ($filename = @readdir($current_dir))){
            if ($filename != "." and $filename != ".."){
                if (is_dir($path.DIRECTORY_SEPARATOR.$filename)){
                    // Ignore empty folders
                    if (substr($filename, 0, 1) != '.'){
                        self::deleteFiles($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
                    }
                }else{
                    unlink($path.DIRECTORY_SEPARATOR.$filename);
                }
            }
        }
        @closedir($current_dir);

        if ($del_dir == true){
            return @rmdir($path);
        }
        return true;
    }
}