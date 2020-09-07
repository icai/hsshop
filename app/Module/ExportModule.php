<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/8/10
 * Time: 10:11
 */

namespace App\Module;

use  PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Writer_CSV;

class ExportModule
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170810
     * @desc 导出数据
     * @param $data 数据
     * @param $style 导出类型
     * @param $name
     * @param $title
     * @param $width_array array 单元格宽度数组
     * @update 许立 2019年02月14日 emoji表情用特殊字符串替换
     */
    public function  derive($data,$name,$style='xlsx', $width_array = [])
    {
        $title = $data['title'];
        $data = $data['data'];
        $name = $name.date('Y-m-d His');

        header("Pragma:no-cache");
        header("Expires:0");
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("会搜科技");
        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
        $objPHPExcel->getProperties()->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $tmp = Array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        //设置单元格宽度 Herry
        foreach ($width_array as $k => $width) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($tmp[$k])->setWidth($width);
        }

        $i=1;
        foreach ($title as $key=>$val) {
            $objPHPExcel->getActiveSheet()->setCellValue($tmp[$i-1].'1', $val);
            $i++;
        }

        $j=2;
        foreach ($data as $key=>$val) {
            $i=0;
            foreach ($title as $k=>$v) {

                $val[$k] = filterEmoji($val[$k] ?? '', '[:emoji:]');

                $objPHPExcel->getActiveSheet()->setCellValue($tmp[$i].$j, $val[$k]);
                $i++;
            }
            $j++;
        }
        //Save Excel 2007 file 保存 或CSV
        header("Content-Type:application/vnd.ms-excel");
        if ($style == 'xlsx') {
            header("Content-Disposition:attachment;filename=$name.xls");
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');

        }else {
            header("Content-Disposition:attachment;filename=$name.csv");
            $objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
        }

        $objWriter->save("php://output");
    }

}