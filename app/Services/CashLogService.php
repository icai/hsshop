<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/5/25
 * Time: 8:53
 */

namespace App\Services;


use App\Model\CashLog;
use App\Model\Member;
use PHPExcel;

class CashLogService
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 根据条件获取提现记录
     * @param $input
     */
    public function get($input)
    {
        $where = [];
        $where['wid'] = session('wid');
        if (isset($input['start']) && !empty($input['start']) && isset($input['end']) && !empty($input['end'])){
            $where['created_at'] = ['between', [$input['start'], $input['end']]];
        }
        if (isset($input['nickname']) && !empty($input['nickname'])){
            $tempWhere['wid'] = session('wid');
            $tempWhere['nickname'] = ['like','%'.$input['nickname'].'%'];
            $member = Member::wheres($tempWhere)->get(['id'])->toArray();
            $ids = [];
            foreach ($member as $value){
                $ids[] = $value['id'];
            }
            if (empty($ids)){
                $ids[] = 0;
            }
            $where['mid'] = ['in',$ids];
        }
        if (isset($input['status']) && $input['status'] != ''){
            $where['status'] = $input['status'];
        }
        $page = $input['page']??1;
        $pagesize = config('database.perPage');
        $offset = ($page-1)*$pagesize;
        $count = CashLog::wheres($where)->count();
        $pageNum = ceil($count/$pagesize);
        $cashData = CashLog::wheres($where)->skip($offset)->take($pagesize)->orderBy('id','desc')->get()->load('member')->toArray();
        $pageInfo = [
            'pageNow' => $page,
            'pageSize'=> $pagesize,
            'count'    => $count,
            'pageNum'  => $pageNum,
        ];
        return [
            'pageInfo'=>$pageInfo,
            'cash'    => $cashData,
        ];
    }

    /**
     * 统计每个店铺分销打款数据
     * $status 打款状态
     * 0-等待审核  1-同意提现  2-确认打款  3-拒绝提现
     * @param  integer $dateTime [时间数组：[统计日期开始时间戳,统计日期结束时间戳]]
     * 默认值：
     * [0, -1] 代表从时间戳为0今日凌晨0点的数据统计
     * 自定义时间戳示例：
     * [1486310400, 1486915200] 代表从2017-02-06 00:00:00到2017-02-13 00:00:00之间的数据统计
     * @return [type] [description]
     */
    public function statical($wid,$status=2,$dateTime=[0,-1])
    {
        /* 结束时间小于开始时间则将结束时间置为当前时间 */
        if ( !isset($dateTime[1]) || $dateTime[1] < $dateTime[0] ) {
            $dateTime[1] = mktime(0,0,0,date('m'),date('d'),date('Y'));
        }

        $where['wid'] = $wid;
        $where['status'] = $status;
        $where['updated_at'] = array('between', [date('Y-m-d H:i:s',$dateTime[0]), date('Y-m-d H:i:s',$dateTime[1])] );
        $obj = CashLog::wheres($where)->orderBy('updated_at','DESC')->get();
        $list = [];
        if($obj){
            $list = $obj->toArray();
        }
        return $list;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170525
     * @desc 更新操作
     * @param $where
     * @param $data
     * @return bool
     */
    public function up($where,$data)
    {
        if (empty($where)){
            return false;
        }
        return CashLog::where($where)->update($data);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 添加数据
     * @desc 20170527
     * @param $data
     */
    public function add($data){
        $id = CashLog::insertGetId($data);
        return $id;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171116
     * @desc 获取用户收入日志
     * @param $mid
     * @param $wid
     */
    public function getMemberCash($mid,$wid,$page=1)
    {
        $pagesize = config('database.perPage');
        $offset = ($page-1)*$pagesize;
        $cash = CashLog::where('wid',$wid)->where('mid',$mid)->orderBy('id','desc')->skip($offset)->take($pagesize)->get()->toArray();
        return $cash;
    }

    /**
     * 导出excel表格  20180113
     * @param $data array 导出的列表
     * @param $type string 导出类型
     * @author 付国维
     */
    public function exportExcel($data = [])
    {
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("导出订单")
            ->setSubject("导出订单")
            ->setDescription("导出订单")
            ->setKeywords("导出订单")
            ->setCategory("result file");
        //设置单元格宽度
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('k')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            //标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '微信ID')
                ->setCellValue('B1', '微信昵称')
                ->setCellValue('C1', '提现金额')
                ->setCellValue('D1', '提现方式')
                ->setCellValue('E1', '收款人')
                ->setCellValue('F1', '收款账号')
                ->setCellValue('G1', '申请时间')
                ->setCellValue('H1', '状态')
                ;
            foreach ($data as $k => $v) {
                $num = $k + 2;
                //对数据进行转化
                if ($v['type'] && $v['type'] == 1) {
                    $type = '银行卡';
                } elseif ($v['type'] && $v['type'] == 2) {
                    $type = '支付宝';
                } elseif ($v['type'] && $v['type'] == 3) {
                    $type = '微信';
                }
                if ($v['status'] == 1) {
                    $status = '同意提现';
                } elseif ($v['status'] == 2) {
                    $status = '确认已打款';
                } elseif ($v['status'] == 3) {
                    $status = '拒绝提现';
                } elseif($v['status'] == 0){
                    $status = '等待审核';
                }
                //Excel数据填充
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $v['member']['wechat_id'] ?? '')
                    ->setCellValue('B' . $num, $v['member']['nickname'] ?? '')
                    ->setCellValue('C' . $num, $v['money'] ?? '')
                    ->setCellValue('D' . $num, $type ?? '')
                    ->setCellValue('E' . $num, $v['name'] ?? '')
                    ->setCellValue('F' . $num, $v['account'] ?? '')
                    ->setCellValue('G' . $num, $v['created_at'] ?? '')
                    ->setCellValue('H' . $num, $status ?? '等待审核');
            }
            //准备导出
            $excelObj->setActiveSheetIndex();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="佣金信息报表_' . time() . '.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
            $objWriter->save('php://output');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180408
     * @desc 获取提现信息
     * @param $id
     */
    public function getRowById($id)
    {
        $res = CashLog::find($id);
        if ($res){
            return $res->toArray();
        }else{
            return [];
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180410
     * @desc 是否存在微信提现
     * @param $wid
     */
    public function isExist($wid)
    {
        $res = CashLog::where('wid',$wid)->whereIn('status',[0,1])->first();
        if ($res){
            return true;
        }else{
            false;
        }
    }




}