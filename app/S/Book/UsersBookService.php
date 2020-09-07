<?php
namespace App\S\Book;
use App\S\S;
use App\Lib\Redis\UsersBookRedis;
use PHPExcel;
class UsersBookService extends S{

    protected $redis;

    public function __construct()
    {
        parent::__construct('UsersBook');
        $this->redis = new UsersBookRedis();
    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-08-21
     */
    public function getListById($idArr = [])
    {
        $redisData = $mysqlData = [];
        $redisId = [];

        $result = $this->redis->getArr($idArr);

        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }

        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    //添加数据
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 处理编辑
     * @param  [int] $id   [主键id]
     * @param  [array] $data [要更新的数组数据]
     * @return [type]       [description]
     */
    public function update($id,$data)
    {
        $rs = $this->model->wheres(['id' => $id])->update($data);
        if($rs){
            $this->redis->updateHashRow($id,$data);
            return true;
        }

        return false;
    }

    /**
     * 删除数据
     * @param  [int] $id   [主键id]
     * @return [type]     [description]
     */
    public function del($id)
    {
        $rs = $this->model->wheres(['id' => $id])->delete();
        if($rs){
            $this->redis->del($id);
            return true;
        }

        return false;
    }

    /**
     * 获取单条数据
     * @param  [int] $id   [主键id]
     * @return [type]     [description]
     */
    public function getRowById($id)
    {
        if(empty($id)){
            error('数据异常');
        }
        $data = [];
        $data = $this->redis->getRow($id);
        if(empty($data)){
            $obj = $this->model->wheres(['id' => $id])->first();
            if ($obj) {
                $data = $obj->toArray();
                $this->redis->setRow($id,$data);
            } 
        }
        return $data;

    }

    /**
     * 获取全部预约列表信息
     * @param  string $orderBy [description]
     * @return [type]          [description]
     */

    public function getAllList($wid,$whereData=[],$orderBy='',$pageSize=16,$is_page=true)
    {
        $where['wid'] = $wid;
        if ($whereData) {
            foreach ($whereData as $key => $value) {
                switch ($key) {
                    case 'status':
                        if ($value){
                            $where['status'] = $value;
                        }
                        break;
                    case 'book_date':
                        if ($value) {
                            $where['book_date'] = $value;
                        }
                        break;
                    case 'book_time':
                        if ($value) {
                            $where['book_time'] = $value;
                        }
                        break;
                    case 'id':
                        if ($value) {
                            $where['id'] = $value;
                        }
                        break;
                    case 'book_id':
                        if ($value) {
                            $where['book_id'] = $value;
                        }
                        break;
                    case 'mid':
                        if ($value) {
                            $where['mid'] = $value;
                        }
                        break;
                    case 'is_delete':
                        $where['is_delete'] = $value;
                        break;
                    case 'created_at':
                        $where['created_at'] = ['like', '%' .$value. '%'];
                        break;

                    default:
                        # code...
                        break;
                }
            }
        }
        $order = $orderBy ?? 'created_at';
        if ($is_page){
            $list = $this->getListWithPage($where,'created_at','DESC',$pageSize);
        }else {
            $list = $this->getList($where);
        }


        return $list;

    }

    /**
     * 统计预约总数，待处理总数
     * @param  [int] $book_id [预约表的主键id]
     * @return [type]          [description]
     */
    public function statistics($wid,$mid=0,$book_id,$book_date=0)
    {
        if ($mid) {
            $bookTotal = $this->model->wheres(['wid' => $wid,'mid'=> $mid,'book_id' => $book_id,'is_delete' => 0])->count(); //预约总数
            $pendingTotal = $this->model->wheres(['wid' => $wid,'mid'=> $mid,'book_id' => $book_id,'status' => 1,'is_delete' => 0])->count();
            $whereData['mid'] = $mid;
        }else {
            $bookTotal = $this->model->wheres(['wid' => $wid,'book_id' => $book_id])->count(); //预约总数
            $pendingTotal = $this->model->wheres(['wid' => $wid,'book_id' => $book_id,'status' => 1,'is_delete' => 0])->count(); //待处理总数
        }
        $timeLimit = [];
        $obj = $this->model->orderBy('book_date','desc')->wheres(['wid' => $wid,'book_id' => $book_id,'is_delete' => 0])->first(); //预约总数
        if($obj)
        {
            $timeLimit = $obj->toArray();
        }
        $returnData['timeLimit'] = $timeLimit;

        if ($book_date) {
            $whereData['book_date'] = $book_date;
        }
        $whereData['wid'] = $wid;
        $whereData['book_id'] = $book_id;
        $whereData['is_delete'] = 0;
        $currentTotal = $this->model->wheres($whereData)->count();  //当天的预约总数

        $returnData['bookTotal']    = $bookTotal;
        $returnData['pendingTotal'] = $pendingTotal;
        $returnData['currentTotal'] = $currentTotal;
        return $returnData;
    }


    /**
     * 导出excel表格
     * @param $data array 导出的列表
     * @param $type string 导出类型
     * @author 付国维
     * @since 2017/03/06 14:30
     */
    public function exportExcel1($data = [], $type = 'order')
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
        if ($type == 'order') {
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(20);

            //标题行
            foreach ($data as $k => $v) {
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A1', '序号')
                    ->setCellValue('B1', '真实姓名')
                    ->setCellValue('C1', '电话')
                    ->setCellValue('D1', '提交时间')
                    ->setCellValue('E1', '预约状态')
                    ->setCellValue('F1', '处理时间')
                    ->setCellValue('G1', '用户是否删除');
                    if (isset($v['fields']) && !empty($v['fields'])) {
                        $chrVal = 71; //chr(71)十进制表示大写字母G
                        foreach ($v['fields'] as $key => $value) {
                            $chrVal++;
                            $excelObj->setActiveSheetIndex()->setCellValue(chr($chrVal).'1', $value['ykey']);
                        }
                    }

                for($i='A';$i<='F';$i++){
                    for($j=1;$j<=16;$j++){
                        //设置水平，垂直居中
                        $excelObj->getActiveSheet()->getStyle($i.$j)->getAlignment()->setHorizontal('center');
                        $excelObj->getActiveSheet()->getStyle($i.$j)->getAlignment()->setVertical('center');
                    }}
            }

            foreach ($data as $k => $v) {
                $num = $k + 2;
                //start 对数据库保存的数字改为对应的文字内容
                if($v['is_delete'] == '0')
                {
                    $v['is_delete'] = '否';
                }else{
                    $v['is_delete'] = '是';
                }

                if($v['status'] == '1')
                {
                    $v['status'] = '等待客服处理';
                }elseif($v['status'] == '2')
                {
                    $v['status'] = '已确定';
                }else{
                    $v['status'] = '已拒绝';
                }
                if($v['shop_updated'])
                {
                    $v['shop_updated'] = date('Y-m-d H:i:s',$v['shop_updated']);
                }else{
                    $v['shop_updated'] = '';
                }

                //end
                //Excel数据填充
                //先对确定字段的信息处理
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $k+'1')
                    ->setCellValue('B' . $num, $v['name'])
                    ->setCellValue('C' . $num, $v['phone'])
                    ->setCellValue('D' . $num, $v['created_at'])
                    ->setCellValue('E' . $num, $v['status'])
                    ->setCellValue('F' . $num, $v['shop_updated'])
                    ->setCellValue('G' . $num, $v['is_delete']);
                if (isset($v['fields']) && !empty($v['fields'])) {
                    $chrVal = 71; //chr(71)十进制表示大写字母G
                    foreach ($v['fields'] as $key => $value) {
                        $chrVal++;
                        $excelObj->setActiveSheetIndex()->setCellValue(chr($chrVal).$num, $value['yval']);
                    }
                }

            }
            //准备导出
            $excelObj->setActiveSheetIndex();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="预约列表_' . time() . '.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
            $objWriter->save('php://output');


        }
    }

    /**
     * 批量更新
     * @param $ids
     * @param $data
     * @return bool
     * @author 张永辉
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redisUpData = [];
            foreach ($ids as $val){
                $redisUpData[] = array_merge($data,['id'=>$val]);
            }
            return $this->redis->updateArr($redisUpData);
        }else{
            return false;
        }
    }
  

}