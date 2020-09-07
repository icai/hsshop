<?php
namespace App\S\ShareEvent;
use App\Jobs\SendTplMsg;
use App\Lib\Redis\LiSalesmanRedis;
use App\S\Member\MemberGarbageService;
use App\S\S;
use App\Lib\Redis\LiDetail;
use PHPExcel;

//1、访问自己页面，其他人页面，须 初始化自己的集赞 即查询自己的享立减
//2、分享后, 查询数据，未分享，则调用接口 改为已分享
//3、有人帮我赞后，记录立减时间，更新集赞人数，传递是否已经满
//4、领取成功，修改我的集赞
class LiSalesmanService extends S
{
	public function __construct()
    {
        parent::__construct('LiSalesman');
    }

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
        $redis = new LiSalesmanRedis();
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
        $redis = new LiSalesmanRedis();
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
            $storeRedis = new LiSalesmanRedis();
            return $storeRedis->update($id,$data);
        }
    }

    public function del($id)
    {
        $res = $this->model->where('id',$id)->delete();
        if ($res){
            $storeRedis = new LiSalesmanRedis();
            return $storeRedis->del($id);
        }else{
            return false;
        }
    }

    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    public function getlistPage($where=[])
    {
        return $this->getListWithPage($where, '', '');
    }

    public function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        return parent::getList($where, $skip, $perPage, $orderBy, $order); // TODO: Change the autogenerated stub
    }

    //导出销售表
    public function exportSalesman($data)
    {
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("导出销售")
            ->setSubject("导出销售")
            ->setDescription("导出销售")
            ->setKeywords("导出销售")
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
        $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        //标题行
        $excelObj->setActiveSheetIndex()
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '业务员姓名')
            ->setCellValue('C1', '手机号')
            ->setCellValue('D1', '总单量')
            ->setCellValue('E1', '总有效单量')
            ->setCellValue('F1', '当月单量')
            ->setCellValue('G1', '当月有效单量')
        ;
        //对一个订单多条数据分行显示
        foreach($data as $k => $v)
        {
            $num = $k + 2;
            //Excel数据填充
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A'.$num, "$k"+1)
                ->setCellValue('B'.$num, $v['name'] ?? '')
                ->setCellValue('C'.$num, $v['mobile'] ?? '')
                ->setCellValue('D'.$num, $v['total'] ?? 0)
                ->setCellValue('E'.$num, $v['totalValid'] ?? 0)
                ->setCellValue('F'.$num, $v['month'] ?? 0)
                ->setCellValue('G'.$num, $v['monthValid'] ?? 0)
            ;
        }
        //准备导出
        $excelObj->setActiveSheetIndex();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="销售表_' . time() . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
        $objWriter->save('php://output');
    }

}
