<?php

namespace App\Services;

use App\Model\MemberLabel;
use PHPExcel;
use RedisPagination;
use Validator;

class MemberLabelService extends Service
{
    /**
     * 构造方法
     *
     *
     * @return void
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id', 'wid', 'w_name_num', 'mobile_name_num', 'rule_name', 'trade_limit', 'amount_limit',
            'points_limit', 'created_at'];

        /* 设置闭包标识 */
        // $this->closure('member');
    }

    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new MemberLabel(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }


    public function verify( $verifyField = ['id'] )
    {
        /* 接收数据 */
        $input = $this->request->only($verifyField);

        /* 获取验证数据和提示消息 */
        $rules = [];
        $messages = [];

        foreach ($verifyField as $value) {
            switch ($value){
                /* 标签名 */
                case 'rule_name':
                    $rules['rule_name'] = 'required|between:6,30';
                    $messages['rule_name.required'] = '请输入标签';
                    $messages['rule_name.between'] = '请输入6-30位字符长度的标签';
                    break;
                default :
                    // code ...
                    break;
            }
        }

        /* 调用验证器执行验证方法 */
        $validator = Validator::make($input, $rules, $messages);

        /* 验证不通过则提示错误信息 */
        if ( $validator->fails() ) {
            error( $validator->errors()->first() );
        }

        return $input;
    }

    /*
    * 写入
    */
    public function insert($data){

        /*插入数据 */
        $insertGetId= $this->model->insertGetId($data);
        $insertDatas = array();
        /*redis插入 */
        if($insertGetId) {
            $data['id'] = $insertGetId;
            foreach ($data as $k=>$d) {
                $insertDatas[$insertGetId][$k] = $d;
            }
            $insertDatas[$insertGetId] = $data;
            /*redis插入 */
            RedisPagination::save($insertDatas);
            return true;
        }
        return false;
    }

    /*
     * 删除
     */
    public function del($id){
        $data = ['isDelete'=>0];
        $where = array('id'=>$id);
        $query= $this->model->where($where)->update($data);
        if($query) {
            /*redis删除单个数据 */
            //RedisPagination::del($id);
            return true;
        }
        return false;
    }

    /**
     * 导出excel表格
     * @author 许立
     * @since 2017/03/03 14:30
     */
    public function exportExcel($data = [])
    {
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("客户标签")
            ->setSubject("客户标签")
            ->setDescription("客户标签")
            ->setKeywords("客户标签")
            ->setCategory("result file");
        //设置单元格宽度
        $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(80);
        //标题行
        $excelObj->setActiveSheetIndex()
            ->setCellValue('A1', '标签名')
            ->setCellValue('B1', '微信会员数量')
            ->setCellValue('C1', '手机会员数量')
            ->setCellValue('D1', '标签条件');
        foreach($data as $k => $v){
            $num = $k + 2;
            //Excel数据填充
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A'.$num, $v['rule_name'])
                ->setCellValue('B'.$num, $v['w_name_num'])
                ->setCellValue('C'.$num, $v['mobile_name_num'])
                ->setCellValue('D'.$num, $this->getTagRule($v));
        }
        //准备导出
        $excelObj->setActiveSheetIndex();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="客户标签_' . time() . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 拼接标签条件
     * @author 许立
     * @since 2017/03/03 14:54
     * @param $row MemberLabelRow
     * @return string
     */
    private function getTagRule($row)
    {
        $whereTag = ['trade_limit'=>$row['trade_limit'],'amount_limit'=>$row['amount_limit'],'points_limit'=>$row['points_limit']];
        if(!empty($row['trade_limit'])){
            $whereTag['trade_limit'] = '累计成功交易 '.$row['trade_limit'].' 笔';
        }else{
            unset($whereTag['trade_limit']);
        }
        if(!empty($row['amount_limit'])){
            $whereTag['amount_limit'] = '累计购买金额 '.$row['amount_limit'].' 元';
        }else{
            unset($whereTag['amount_limit']);
        }

        if(!empty($row['points_limit'])){
            $whereTag['points_limit'] = '累计积分达到 '.$row['points_limit'];
        }else{
            unset($whereTag['points_limit']);
        }
        return implode(" 或 ",$whereTag);
    }
}