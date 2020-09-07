<?php
namespace App\S\Wechat;

use App\S\S;

/**
 * 店铺数据导入记录表Service
 */
class WeixinImportService extends S
{
    //状态枚举常量
    const SCHEDULE_STATUS_PRODUCT = 0; //仅商品导入完成
    const SCHEDULE_STATUS_FREIGHT = 1; //商品和运费模板导入完成
    const SCHEDULE_STATUS_GROUP = 2; //商品和运费模板和分组导入完成
    const SCHEDULE_STATUS_COMPLETE = 3; //全部信息导入完成

    public function __construct()
    {
        parent::__construct('WeixinImport');
    }

    /**
     * 根据商品ID和目标店铺ID获取导入记录表信息
     * @param $pid_from
     * @param $wid_to
     * @return array
     */
    public function getRow($pid_from, $wid_to)
    {
        $row = $this->model->wheres(['pid_from' => $pid_from, 'wid_to' => $wid_to])->get()->toArray();
        return $row ? $row[0] : [];
    }

    /**
     * 生成导入记录表
     * @param $wid_from int 源店铺ID
     * @param $wid_to int 目标店铺ID
     * @param $pid_from int 源商品ID
     * @param $pid_to int 目标商品ID
     * @return int | false
     */
    public function createImportRecord($wid_from, $wid_to, $pid_from, $pid_to)
    {
        $data = [
            'wid_from' => $wid_from,
            'wid_to' => $wid_to,
            'pid_from' => $pid_from,
            'pid_to' => $pid_to,
            'schedule_status' => self::SCHEDULE_STATUS_PRODUCT, //仅导入商品信息完成
        ];

        return $this->model->insertGetId($data);
    }

    /**
     * 查询导入记录
     * @param $where array 查询条件
     * @return array
     */
    public function getOneByWhere($where)
    {
        $row = $this->model->wheres($where)->first();
        return $row ? $row->toArray() : [];
    }

    /**
     * 更新导入进度
     */
    public function update($id, $update_array)
    {
        $this->model->wheres(['id' => $id])->update($update_array);
    }
}