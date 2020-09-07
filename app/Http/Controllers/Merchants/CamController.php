<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\S\Cam\CamActivityService;
use App\S\Cam\CamListService;
use App\S\Market\SeckillService;
use App\S\Product\ProductService;
use App\Module\GroupsRuleModule;
use App\Module\ShareEventModule;
use Validator;
use OrderService;
use Excel;
use Storage;

class CamController extends Controller
{

    public function __construct()
    {
        $this->leftNav = 'shareEvent';
    }

    /**
     * 发卡密活动列表
     * @author 吴晓平 <2018年08月06日>
     * @param  CamActivityService $camActivityService [description]
     * @param  CamListService $camListService [description]
     * @return [type]                                 [description]
     */
    public function list(Request $request, CamActivityService $camActivityService, CamListService $camListService)
    {
        $title = $request->input('title') ?? '';
        $type = $request->input('type');
        $where = [];
        if ($title) {
            $where['title'] = $title;
        }
        if (isset($type)) {
            $where['type'] = $type;
        }
        $wid = session('wid');
        list($list, $pageHtml) = $camActivityService->getAllList($wid, $where);
        if ($list['data']) {
            foreach ($list['data'] as $key => &$value) {
                $value['count'] = $camListService->countStock($value['id']);
            }
        }

        return view('merchants.cam.list', [
            'title' => '发卡密',
            'leftNav' => $this->leftNav,
            'slidebar' => 'list',
            'pageHtml' => $pageHtml,
            'list' => $list['data'],
        ]);
    }

    /**
     * 发卡密活动添加、编辑页
     * @author 吴晓平 <2018年08月06日>
     * @param  Request $request [description]
     * @param  CamActivityService $camActivityService [description]
     * @return [type]                                 [description]
     */
    public function create(Request $request, CamActivityService $camActivityService)
    {
        $id = $request->input('id') ?? 0;
        $data = [];
        if ($id) {
            $data = $camActivityService->getRowById($id);
        }

        return view('merchants.cam.create', [
            'title' => $id ? '编辑发卡密' : '新建发卡密',
            'leftNav' => $this->leftNav,
            'slidebar' => 'create',
            'id' => $id,
            'data' => $data
        ]);

    }

    /**
     * 发卡密活动保存添加，编辑数据
     * @author 吴晓平 <2018年08月06日>
     * @param  Request $request [description]
     * @param  CamActivityService $camActivityService [description]
     * @return [type]                                 [description]
     * @update 梅杰 2018年8月9号 发布活动时创建队列
     */
    public function save(Request $request, CamActivityService $camActivityService, CamListService $camListService)
    {
        $input = $request->input();
        $id = $input['id'] ?? 0;
        $filePath = $input['file'] ?? ''; //上传的文件路径
        $rule = [
            'title' => 'required|string|max:50',
        ];
        $message = [
            'title.required' => '发卡密名称不能为空',
            'title.string' => '发卡密名称填写有误，只能输入字符',
            'title.max' => '最多支持10个字符',
        ];
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }
        /*if (!isset($input['begin_time']) || $input['begin_time'] == "") {
            error('请设置生效时间');
        }

        if (!isset($input['end_time']) || $input['end_time'] == "") {
            error('请设置过期时间');
        }
        if ($input['end_time'] < $input['begin_time']) {
            error('过期时间设置有问题');
        }*/

        $data['wid'] = session('wid');
        $data['title'] = $input['title'];
        $data['type'] = $input['type'] ?? 1;
        //$data['begin_time'] = $input['begin_time'];
        //$data['end_time']   = $input['end_time']'';
        $data['remark'] = $input['remark'] ?? '';
        if ($id) {
            $rs = $camActivityService->update($id, $data);
            $msg = '发卡密修改成功';
        } else {
            if (empty($filePath)) {
                error('请先上传卡密');
            }
            $rs = $camActivityService->add($data);
            $msg = '发卡密添加成功';
        }
        if ($rs) {
            $insertNum = 0;
            if ($filePath) {
                $camId = $id ? $id : $rs;
                $returnData = $this->handleImportData($filePath);
                if ($returnData['errCode'] == 0 && !empty($returnData['data'])) {

                    foreach ($returnData['data'] as $key => $value) {
                        $insertData[$key]['cam_id'] = $camId;
                        $insertData[$key]['name'] = json_encode($value['name'], JSON_UNESCAPED_UNICODE);
                        $insertData[$key]['attr'] = json_encode($value['attr'], JSON_UNESCAPED_UNICODE);
                    }
                    if ($camListService->insertBatch($insertData)) { //批量插入数据
                        $insertNum = count($returnData['data']);
                        $camListService->addList($camId);
                    }
                } else {
                    $msg .= '<br>' . $returnData['errMsg'];
                }
            }
            $msg = $insertNum ? $msg . "<br>已有" . $insertNum . '条记录导入到卡密库' : $msg;
            success($msg);
        }
        error();
    }

    /**
     * 使失效发卡密活动
     * @author 吴晓平 <2018年08月06日>
     * @param  Request $request [description]
     * @param  CamActivityService $camActivityService [description]
     * @return [type]                                 [description]
     * @update 许立 2018年09月20日 删除多余注释代码
     */
    public function invalid(Request $request, CamActivityService $camActivityService)
    {
        $camId = $request->input('id') ?? 0;
        if (empty($camId)) {
            error('请先选择要失效的发卡密');
        }
        $where['wid'] = session('wid');
        $where['status'] = 1;
        $where['cam_id'] = $camId;
        $data = (new ProductService())->listWithoutPage($where);
        $is_in_activity = 0;
        $camPid = []; //定义卡密商品id
        if ($data[0]['data']) {
            error('该发卡密有关联的商品，不能失效');
        }
        if ($is_in_activity) {
            error('该发卡密有关联的商品在参数秒杀或拼团或享立减活动，不能失效');
        }
        // 判断订单是否有已支付未发货的发卡密订单，如果有不能失效
        OrderService::init('wid', session('wid'))->buildWhere();
        OrderService::whereAdd(['wid' => session('wid')]);
        OrderService::whereAdd(['status' => 1]);
        OrderService::whereAdd(['type' => 12]);
        // 关联关系
        $with = ['orderDetail'];
        /* 获取数据 */
        list($list, $pageHtml) = OrderService::with($with)->getList();
        $orderPid = []; //定义已支付订单id
        if ($list['data']) {
            foreach ($list['data'] as $val) {
                foreach ($val['orderDetail'] as $v) {
                    $orderPid[] = $v['product_id'];
                }
            }
        }
        if (array_intersect($camPid, $orderPid)) {
            error('该发卡密有关联的商品支付成功未发货，不能失效');
        }

        if ($camActivityService->update($camId, ['invalid' => 1])) {
            success();
        }
        error();
    }

    /**
     * 删除已失效的发卡密活动
     * @author 吴晓平 <2018年08月06日>
     * @param  Request $request [description]
     * @param  CamActivityService $camActivityService [description]
     * @return [type]                                 [description]
     */
    public function delCam(Request $request, CamActivityService $camActivityService)
    {
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            error('请先选择要删除的发卡密');
        }
        if ($camActivityService->del($id)) {
            success();
        }
        error();
    }

    /**
     * 卡密库列表
     * @author 吴晓平 <2018年08月06日>
     * @param  Request $request [description]
     * @param  CamListService $camListService [description]
     * @return [type]                         [description]
     * @update 何书哲 2019年09月30日 卡密attr为空key，value不存在处理
     */
    public function camStockList(Request $request, CamListService $camListService)
    {
        $id = $request->input('id') ?? 0;
        if (empty($id)) {
            error('请先选择发卡密活动');
        }
        list($list, $pageHtml) = $camListService->getAllList(['cam_id' => $id]);
        if ($list['data']) {
            foreach ($list['data'] as $key => &$value) {
                if (isset($value['name']) && $value['name']) {
                    $names = json_decode($value['name'], true);
                    $value['name_key'] = $names['key'];
                    $value['name_val'] = $names['value'];
                }
                if (isset($value['attr']) && $value['attr']) {
                    $attrs = json_decode($value['attr'], true);
                    $value['attr_key'] = $attrs['key'] ?? '';
                    $value['attr_val'] = $attrs['value'] ?? '';
                }
                $value['getMember'] = json_decode($value['getMember'], true);
            }
        }
        return view('merchants.cam.stockList', [
            'title' => '卡密库',
            'leftNav' => $this->leftNav,
            'slidebar' => 'camStockList',
            'pageHtml' => $pageHtml,
            'list' => $list['data'],
            'id' => $id
        ]);
    }

    /**
     * 处理导入的excel文件数据
     * @author 吴晓平 2018年08月06日
     * @param  string $filePath [上传后的文件路径]
     * @return [type]           [description]
     */
    public function handleImportData($filePath = '')
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $res = [];
        Excel::load($filePath, function ($reader) use (&$res) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
        }, 'GBK');
        if ($res) {
            $result = array_shift($res); //把第一列数据单独拿出来
            if (count($result) > 2) {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '请控制导入2列数据，或根据下载Excel模板导入数据';
                return $returnData;
            }
            if (empty($res)) {
                $returnData['errCode'] = -1;
                $returnData['errMsg'] = '该上传的文件内容为空';
                return $returnData;
            } else {
                $data = [];
                foreach ($res as $key => $value) {
                    if (count($value) == 2) {
                        $data[$key]['name'] = ['key' => $result[0], 'value' => $value[0]];
                        $data[$key]['attr'] = ['key' => $result[1], 'value' => $value[1]];
                    } else if (count($value) == 1) {
                        $data[$key]['name'] = ['key' => $result[0], 'value' => $value[0]];
                        $data[$key]['attr'] = '';
                    } else {
                        $returnData['errCode'] = -4;
                        $returnData['errMsg'] = '暂时只支持导入最多2列数据';
                        return $returnData;
                    }
                    if (!preg_match('/^[0-9A-Za-z]{6,16}$/i', $value[0])) {
                        $returnData['errCode'] = -3;
                        $returnData['errMsg'] = '发卡卡号格式由6~16位数字和字母组成,请设置正确格式';
                        return $returnData;
                    }
                }
                $returnData['data'] = $data;
            }
        } else {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '该上传的文件内容为空';
            return $returnData;
        }

        return $returnData;
    }

    /**
     * 上传excel文件
     * @author 吴晓平 2018年08月06日
     * @return [type] [description]
     */
    public function upExcel(Request $request)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if ($request->hasFile('file')) {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '请选择上传文件';
            return $returnData;
        }
        $file = $request->file('info');
        $filePath = $_FILES['info']['tmp_name'];
        if (filesize($filePath) / 1024 > 3072) { //限制只能上传为3M的文件（1万条数据）
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '上传的文件过大，请重新编辑后上传';
            return $returnData;
        }
        //获取后缀名
        $extension = pathinfo($_FILES['info']['name'], PATHINFO_EXTENSION);
        if ($extension != 'csv') {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '请上传.csv格式的文件';
            return $returnData;
        }
        $path = config('filesystems.file_path') . '/excel/' . date('Y/m/d') . '/' . date('His') . rand(0, 99999) . rand(0, 99999) . '.' . $file->getClientOriginalExtension();
        //存储文件
        $bytes = Storage::put(
            $path,
            file_get_contents($file->getRealPath())
        );
        $returnData['data'] = $path;
        return $returnData;
    }

    /**
     * Excel文件导出功能
     * @author 吴晓平 <2018年08月08日>
     * @param  Request $request [description]
     * @param  CamListService $camListService [description]
     * @return [type]                         [description]
     */
    public function export(Request $request, CamListService $camListService)
    {
        $ids = $request->input('ids') ?? [];
        if (empty($ids)) {
            error('请先选择要导出的发卡密');
        }
        $list = $camListService->getListById($ids);
        $data = $cellFirstData = [];
        if ($list) {
            $attrs = $names = [];
            $cellFirstData = ['创建时间', '发送时间', '购买者']; //定义导出的第一行头部信息
            foreach ($list as $key => $value) {
                if (isset($value['name']) && $value['name']) {
                    $names = json_decode($value['name'], true);
                    $data[$key]['name_val'] = $names['value'];
                }
                if (isset($value['attr']) && $value['attr']) {
                    $attrs = json_decode($value['attr'], true);
                    $data[$key]['attr_val'] = $attrs['value'];
                }
                $data[$key]['create_at'] = $value['created_at'];
                $data[$key]['send'] = $value['is_send'] == 0 ? '未发送' : $value['send_time'];
                $data[$key]['user'] = '-';
                if (!empty($value['getMember'])) {
                    $member = json_decode($value['getMember'], true);
                    $data[$key]['user'] = $member['truename'] ? $member['truename'] : $member['nickname'];
                }
                //$data[$key]['use_time'] = empty(intval($value['use_time'])) ? '未使用' : $value['use_time'];
            }
            if ($attrs) {
                array_unshift($cellFirstData, $attrs['key']);
            }
            if ($names) {
                array_unshift($cellFirstData, $names['key']);
            }
        }
        array_unshift($data, $cellFirstData);
        $cellData = $data;
        if (empty($cellData)) {
            error('数据异常，请重新操作');
        }
        Excel::create(iconv('UTF-8', 'GBK', '卡密库'), function ($excel) use ($cellData) {
            $excel->sheet('score', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    /**
     * 批量删除
     * @author 吴晓平 <2018年08月08日>
     * @param  Request $request [description]
     * @param  CamListService $camListService [description]
     * @return [type]                         [description]
     */
    public function delbatch(Request $request, CamListService $camListService)
    {
        $ids = $request->input('ids') ?? [];
        if (empty($ids)) {
            error('请先选择要删除的发卡密');
        }
        if ($camListService->delBatch($ids)) {
            success();
        }
        error();
    }

    /**
     * 添加发卡密库存
     * @author 吴晓平 <2018年08月08日>
     * @param Request $request [description]
     * @param CamListService $camListService [description]
     */
    public function addStock(Request $request, CamListService $camListService)
    {
        $camId = $request->input('id') ?? 0;
        if (!$camId) {
            error('请选择发卡密活动');
        }
        $lelftCount = $camListService->leftStock($camId);

        return view('merchants.cam.addStock', [
            'title' => '添加卡密库存',
            'leftNav' => $this->leftNav,
            'slidebar' => 'addStock',
            'lelftCount' => $lelftCount
        ]);
    }

    /**
     * 处理添加导入库存
     * @author 吴晓平 <2018年08月08日>
     * @param  Request $request [description]
     * @param  CamListService $camListService [description]
     * @return [type]
     */
    public function doAddStock(Request $request, CamListService $camListService)
    {
        $camId = $request->input('id') ?? 0;
        $filePath = $request->input('file') ?? '';
        if (!$camId) {
            error('请先确定要导入的发卡密活动');
        }
        if (empty($filePath)) {
            error('请先上传卡密文件');
        }

        $returnData = $this->handleImportData($filePath);
        if ($returnData['errCode'] == 0 && !empty($returnData['data'])) {
            foreach ($returnData['data'] as $key => $value) {
                $insertData[$key]['cam_id'] = $camId;
                $insertData[$key]['name'] = json_encode($value['name'], JSON_UNESCAPED_UNICODE);
                $insertData[$key]['attr'] = json_encode($value['attr'], JSON_UNESCAPED_UNICODE);
            }
            if ($camListService->insertBatch($insertData)) { //批量插入数据
                $insertNum = count($returnData['data']);
                $camListService->addList($camId);
                success("已有" . $insertNum . '条记录导入到卡密库');

            }
        }
        error($returnData['errMsg']);

    }

    /**
     * 下载发卡密
     * @author 吴晓平 <2018年08月09日>
     * @return [type] [description]
     */
    public function downExcelTemp()
    {

        $filePath = storage_path() . '/exports/case.csv';
        return response()->download($filePath, '发卡密' . '.csv');  //下载
    }
}
