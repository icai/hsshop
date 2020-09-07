<?php
namespace App\S\ShareEvent;

use App\Lib\Redis\LiRegister;
use App\S\S;
use PHPExcel;
use DB;

/**
 * 注册信息Service
 */
class LiRegisterService extends S
{
    public function __construct()
    {
        parent::__construct('LiRegister');
    }

    /**
     * 获取非分页列表
     * @return array
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new LiRegister();
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
     * 检查某用户是否注册过信息
     * @param $mid
     * @return int
     */
    public function isRegistered($mid)
    {
        return $this->model->where('mid', $mid)->get()->toArray() ? 1 : 0;
    }

    /**
     * 检查某用户是否申请过免费小程序
     * @param $umid
     * @return int
     */
    public function isApplied($umid,$type=1)
    {
        return $this->model->where('umid', $umid)->where('type', $type)->get()->toArray() ? 1 : 0;
    }

    /**
     * 检查某用户是否申请过免费小程序[适用于小程序]
     * @param $mid
     * @return int
     */
    public function isAppliedByXCX($mid, $wid)
    {
        return $this->model->where('mid', $mid)->where('wid', $wid)->where('type', 2)->get()->toArray() ? 1 : 0;
    }

    /**
     * 导出excel表格
     * @param $data array 准备导出的数据
     * @author 付国维
     * @since 2018/01/24 14:30
     */
    public function exportExcelXls($data = [])
    {
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("导出注册信息")
            ->setSubject("导出注册信息")
            ->setDescription("导出注册信息")
            ->setKeywords("导出注册信息")
            ->setCategory("result file");
        //设置单元格宽度
        $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $excelObj->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        //标题行
        $excelObj->setActiveSheetIndex()
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '提交时间')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '手机号')
            ->setCellValue('E1', '公司名称')
            ->setCellValue('F1', '职务')
        ;

        foreach ($data as $k => $v)
        {
            $num = $k + 2;
            //Excel数据填充
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A' . $num, "$k"+1)
                ->setCellValue('B' . $num, $v['created_at'])
                ->setCellValue('C' . $num, $v['name'] ?? '')
                ->setCellValue('D' . $num,$v['phone'] ?? '')
                ->setCellValue('E' . $num, $v['company_name'] ?? '')
                ->setCellValue('F' . $num, $v['company_position'] ?? '')
            ;
        }
        //准备导出
        $excelObj->setActiveSheetIndex();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="注册信息表_' . time() . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
        $objWriter->save('php://output');
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180227
     * @desc 批量更新
     * @param $ids
     * @param $data
     * @return bool
     */
    public function batchUpdate($ids,$data)
    {
        $res = $this->model->whereIn('id',$ids)->update($data);
        if ($res){
            $redis = new LiRegister();
            return $redis->batchUpdate($ids,$data);
        }else{
            return false;
        }
    }

    /**
     * 免费领会搜云活动用户滚动列表
     * @param int $wid 店铺id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @return array
     * @author 许立 2018年07月09日
     * @update 许立 2018年07月10日 封装匿名处理方法
     */
    public function sliderList($wid, $type)
    {
        $default_list = [
            [
                'name' => '张一',
                'phone' => 13800003223
            ],
            [
                'name' => '陈一一',
                'phone' => 15800005433
            ],
            [
                'name' => '李一一',
                'phone' => 13700004632
            ],
            [
                'name' => '吴一一',
                'phone' => 13200008086
            ],
            [
                'name' => '梅一',
                'phone' => 18900004947
            ],
            [
                'name' => '许一一',
                'phone' => 15100002422
            ],
            [
                'name' => '魏一一',
                'phone' => 15200006733
            ],
            [
                'name' => '张一一',
                'phone' => 13600008867
            ],
            [
                'name' => '陈一',
                'phone' => 13300008983
            ],
            [
                'name' => '周一一',
                'phone' => 13900003686
            ],
        ];
        $list = $this->getListWithPage(['wid' => $wid, 'type' => $type], '', '', 30);
        // 姓名和手机号匿名处理 例: 许**  188****1111  1分钟前领取了
        // 如果注册信息少于10条 使用默认数据 否则返回30条数据
        if ($list[0]['total'] < 10) {
            $list = $default_list;
        } else {
            $list = $list[0]['data'];
        }

        return $this->_getAnonymityList($list);
    }

    /**
     * 获取某用户总共邀请人数
     * @param int $parent_mid 邀请人id
     * @param int $wid 店铺id 许立 2018年07月10日
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @return int
     * @author 许立 2018年07月09日
     */
    public function inviteCount($parent_mid, $wid, $type)
    {
        return $this->model->wheres(['parent_mid' => $parent_mid, 'wid' => $wid, 'type' => $type])->count();
    }

    /**
     * 获取某用户邀请列表
     * @param int $parent_mid 邀请人id
     * @param int $wid 店铺id 许立 2018年07月10日
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @return array
     * @author 许立 2018年07月10日
     * @update 许立 2018年07月11日 连表字段修改
     * @update 许立 2018年07月11日 注册表用户id连表用户表id
     */
    public function inviteList($parent_mid, $wid, $type)
    {
        $list = DB::table('li_register as r')
            ->select(['r.name', 'r.phone', 'm.headimgurl'])
            ->leftJoin('member as m','m.id','=','r.mid')
            ->where('r.parent_mid', $parent_mid)
            ->where('r.wid', $wid)
            ->where('r.type', $type)
            ->get()
            ->toArray();

        return $this->_getAnonymityList($list);
    }

    /**
     * 判断手机号是否已经注册过
     * @param int $mid 用户id 许立 2018年07月11日 删除参数
     * @param int $wid 店铺id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @param int $phone 填写的手机号
     * @return bool true:已注册,false:未注册
     * @author 许立 2018年07月09日
     * @update 许立 2018年07月10日
     */
    public function isRegisteredByPhone($wid, $type, $phone)
    {
        return !!$this->model
            ->where('wid', $wid)
            ->where('type', $type)
            ->where('phone', $phone)
            ->first();
    }

    /**
     * 判断公司名是否已经注册过
     * @param int $mid 用户id 许立 2018年07月11日 删除参数
     * @param int $wid 店铺id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动
     * @param string $company 填写的公司名
     * @return bool true:已注册,false:未注册
     * @author 许立 2018年07月09日
     */
    public function isRegisteredByCompany($wid, $type, $company_name)
    {
        return !!$this->model
            ->where('wid', $wid)
            ->where('type', $type)
            ->where('company_name', $company_name)
            ->first();
    }

    /**
     * 获取用户一条注册信息
     * @param int $mid 用户id
     * @param int $wid 店铺id
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动 许立 2018年07月10日
     * @return array
     * @author 许立 2018年07月10日
     * @update 许立 2018年07月10日 修改成获取一条记录
     * @update 许立 2018年07月10日 返回一维数组
     */
    public function getRow($mid, $wid, $type)
    {
        $row = $this->model
            ->where('mid', $mid)
            ->where('wid', $wid)
            ->where('type', $type)
            ->get()
            ->toArray();

        return $row ? $row[0] : [];
    }

    /**
     * 根据手机号获取用户一条注册信息
     * @param int $phone 手机号
     * @param int $wid 店铺id 许立 2018年07月11日
     * @param int $type 渠道参数 0:默认,1:免费送小程序,2:小程序3月3日活动,8:小程序5月5日活动,9:7月9日活动 许立 2018年07月10日
     * @return array
     * @author 许立 2018年07月10日
     */
    public function getRowByPhone($phone, $wid, $type = 0)
    {
        $row = $this->model
            ->where('phone', $phone)
            ->where('wid', $wid)
            ->where('type', $type)
            ->get()
            ->toArray();

        return $row ? $row[0] : [];
    }

    /**
     * 返回邀请手机号
     * @param array $list 原始数据
     * @return array
     * @author 许立 2018年07月11日
     * @update 许立 2018年07月11日 返回邀请手机号
     */
    public function dealWithParentPhone($list)
    {
        foreach ($list as $k => $v) {
            $list[$k]['parent_phone'] = $this->inviteCount($v['mid'], $v['wid'], $v['type']) ? $v['phone'] : 0;
        }

        return $list;
    }

    /**
     * 用户名和手机号匿名处理
     * @param array $list 原始数据
     * @return array
     * @author 许立 2018年07月10日
     * @update 许立 2018年07月11日 兼容数组和对象处理
     */
    private function _getAnonymityList($list)
    {
        $object_to_array = [];
        foreach ($list as $k => $v) {
            // 处理姓名
            if (is_array($v)) {
                $name_length = mb_strlen($v['name'], 'utf-8');
                $name_first_string = mb_substr($v['name'], 0, 1, 'utf-8');
                $v['name'] = $name_first_string . str_repeat('*', $name_length - 1);
                // 处理电话
                $phone_first_string = mb_substr($v['phone'], 0, 3, 'utf-8');
                $phone_last_string = mb_substr($v['phone'], -4, 4, 'utf-8');
                $v['phone'] = $phone_first_string . str_repeat('*', 4) . $phone_last_string;
                // 处理时间
                if ($k < 8) {
                    $v['created_at'] = '1分钟前领取了';
                } elseif ($k < 16) {
                    $v['created_at'] = '2分钟前领取了';
                } else {
                    $v['created_at'] = '3分钟前领取了';
                }
                $list[$k] = $v;
            } else {
                // 处理姓名
                $name_length = mb_strlen($v->name, 'utf-8');
                $name_first_string = mb_substr($v->name, 0, 1, 'utf-8');
                $object_to_array[$k]['name'] = $name_first_string . str_repeat('*', $name_length - 1);
                // 处理电话
                $phone_first_string = mb_substr($v->phone, 0, 3, 'utf-8');
                $phone_last_string = mb_substr($v->phone, -4, 4, 'utf-8');
                $object_to_array[$k]['phone'] = $phone_first_string . str_repeat('*', 4) . $phone_last_string;
                $object_to_array[$k]['headimgurl'] = $v->headimgurl;
            }
        }

        return $object_to_array ? $object_to_array : $list;
    }

}
