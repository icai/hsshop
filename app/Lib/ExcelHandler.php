<?php
/**
 * 文件导入，导出公共类
 * @author 吴晓平 2019年09月26日
 */

namespace App\Lib;

use Maatwebsite\Excel\Facades\Excel;

class ExcelHandler
{

    /**
     * excel文件数据导入
     * @param  [string] $filePath [excel文件绝对路径]
     * @return collection
     * @author 吴晓平[wuxiaoping1559@dingtalk.com] 2019年09月26日
     */
    public function import($filePath = '')
    {
        Excel::load($filePath, function ($reader) use (&$res) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
        }, 'GBK');
        return $res;
    }

    /**
     * 上传excel文件到本地
     * @author 吴晓平[wuxiaoping1559@dingtalk.com] 2019年09月26日
     * @param  [obj] $file [上传的图片对象]
     * @param  [string]  $folder      [自定义的存放的路径]
     * @param  [string]  $filePrefix [前缀，如当前的id值]
     * @return array 上传excel后的绝对路径
     */
    public function saveExcelFile($file, $folder, $filePrefix)
    {
        // 构建存储的文件夹规则，值如：file/excel/201904/01/
        $folderName = "hsshop/$folder/" . date("Ym/d", time());
        // 值如：/home/vagrant/Code/larabbs/public/file/excel/201904/01/
        $uploadPath = base_path('public') . '/' . $folderName;
        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'xls';

        // 拼接文件名，值如：1_1493521050_7BVc9v9ujP.png
        $filename = $filePrefix . '_' . time() . '_' . str_random(10) . '.' . $extension;
        // 如果上传的不是图片将终止操作
        if (!in_array($extension, ['xls', 'xlsx', 'csv', 'zip'])) {
            return false;
        }
        // 将图片移动到我们的目标存储路径中
        $file->move($uploadPath, $filename);

        return [
            'path' => base_path('public') . "/$folderName/$filename"
        ];
    }


}
