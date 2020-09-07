<?php 

namespace App\Services\Lib;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use RedisService;

/**
 * 服务基类
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年1月16日 14:32:03
 */
class Service
{
    /**
     * 模型绝对命名空间
     * @var string
     */
    protected $modelNS = '\\App\\Model\\';

    /**
     * 导入的服务包或服务插件
     * @var instance
     */
    protected $vendor;

    /**
     * Eloquentmo模型
     * @var Model
     */
    public $model;

    /**
     * 主键名
     * @var string
     */
    public $idKey;

    /**
     * 数据表所有字段字符串
     * @var array
     */
    public $field;

    /**
     * 查询字段
     * @var array
     */
    public $columns;

    /**
     * 查询条件
     * @var array
     */
    public $where = [];

    /**
     * 忽略查询条件强制读取redis数据
     * @var array
     */
    public $whereIgnore;

    /**
     * 排序字符串
     * @var string
     */
    public $order;

    /**
     * 唯一标识键名
     * @var string
     */
    public $uniqueKey;

    /**
     * 唯一标识值
     * @var string
     */
    public $uniqueValue;

    /**
     * 查询关联关系数组
     * @var array
     */
    public $with;

    /**
     * 所有关联关系，插入redis时使用
     * @var array
     */
    public $withAll;

    /**
     * 每页显示的条数
     * @var array
     */
    public $perPage;

    /**
     * 解决特定类型的服务
     * 
     * 1、设置ORM模型
     * 2、设置唯一标识名称，用于redis键名组装
     * 3、设置唯一标识的值，用于redis键名组装
     * 4、设置忽略限制强制走redis的查询条件
     * 5、设置主键名
     * 
     * @param  string $model       [模型名称]
     * @param  string $uniqueKey   [唯一标识键名]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     * @param  string $uniqueValue [唯一标识值]
     * @param  string $ignore      [主键名]
     * @param  string $idKey       [主键名]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月16日 14:32:03
     * 
     * @return $this
     */
    public function make($model = '', $uniqueKey = '', $uniqueValue = '', $ignore = [], $idKey = '')
    {
        if ( empty($model) ) {
            return $this;
        } else {
            // 设置属性
            $modelName         = $this->modelNS . $model;
            $this->model       = new $modelName();
            $this->uniqueKey   = $uniqueKey;
            $this->uniqueValue = $uniqueValue;
            $this->idKey       = $idKey ?: $this->model->getKeyName();
            // 默认排序规则设为主键倒序
            $this->order       = $this->idKey . ' DESC';
            // 获取所有关联关系
            $this->withAll     = $this->model->withAll ?? [];
            // 目前数据库所有字段是查数据库的 所以不能放到构造函数中设置，后期优化再复原
            /* 获取表所有字段 */
            // $this->field       = $this->getAllColumn();
            /* 默认查询所有字段 */
            // $this->columns     = $this->field;
            // 设置强制忽略查询条件数组
            if ( !empty($ignore) ) {
                $this->whereIgnore($ignore);
            }

            /* 设置redis键名 */
            $this->setRedisKey();
        }

        return $this;
    }

    /**
     * 初始化 设置唯一标识和redis键名
     * 
     * @param  model  $model       [模型实例]
     * @param  string $uniqueKey   [唯一标识键名]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     * @param  string $uniqueValue [唯一标识值]
     * @param  string $idKey       [主键名]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月16日 14:32:03
     * 
     * @return void
     */
    public function initialize($model, $uniqueKey = '', $uniqueValue = '', $idKey = 'id', $ignore = [])
    {
        /* 设置属性 */
        $this->model       = $model;
        $this->uniqueKey   = $uniqueKey;
        $this->uniqueValue = $uniqueValue;
        $this->idKey       = $idKey;
        /* 默认排序规则设为主键倒序 */
        $this->order       = $idKey . ' DESC';
        /* 获取表名 */
        $tableName         = $model->getTable();
        // 目前数据库所有字段是查数据库的 所以不能放到构造函数中设置，后期优化再复原
        /* 获取表所有字段 */
        // $this->field       = $this->getAllColumn();
        /* 默认查询所有字段 */
        // $this->columns     = $this->field;
        // 设置强制忽略查询条件数组
        if ( !empty($ignore) ) {
            $this->whereIgnore($ignore);
        }

        /* 设置redis键名 */
        if ( !empty($uniqueKey) && !empty($uniqueValue) ) {
            if ( !empty($ignore) ) {
                $uniqueKey .= '_';
                foreach ($ignore as $key => $value) {
                    $uniqueKey .= $key . '_' . $value;
                }
            }
            $uniqueKey = ':' . $uniqueKey . ':' . $uniqueValue;
        } else {
            $uniqueKey = '';
        }
        $redisListKey = $tableName . $uniqueKey;
        $redisHashKey = $tableName . ':' . $idKey . ':';
        RedisService::key([ $redisListKey, $redisHashKey ], $idKey);
    }

    /**
     * 获取表所有字段
     * 
     * @return array             [表所有字段]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年3月4日 16:16:20
     */
    public function getAllColumn()
    {
        $this->field = Schema::getColumnListing($this->model->getTable());

        return $this->field;
    }

    /**
     * 列表查询
     * 
     * @param  boolean $pageFlag [是否分页]
     * @param  array   $idList   [查询指定主键的数据]
     * @return array             [数据和分页]
     */
    public function getList($pageFlag = true, $idList = [])
    {
        /* 接收参数 */
        $input = app('request')->input();
        
        /* 读取配置文件中的每页多少条数据参数 */
        $this->perPageJudge();

        /* 数据和分页 */
        $list     = [];
        $pageHtml = '';
        /* redis里没有任何数据 则将所有主键存入redis */
        $this->redisEmptyJudge();

        // 有强制忽略则将查询条件置空
        if ( !empty($this->whereIgnore) ) {
            $this->where = [];
        }

        /* 查询条件为空则读取redis数据 否则查询数据库 */
        if ( empty($this->where) ) { 
            /* redis分页 */
            if ( $pageFlag === false ) {
                if ( empty($idList) ) {
                    list($list, $pageHtml, $inexistenceIds) = RedisService::getList();
                } else {
                    list($list, $pageHtml, $inexistenceIds) = RedisService::getList(0, -1, $idList);
                }
            } else {
                list($list, $pageHtml, $inexistenceIds) = RedisService::page();
            }
            /* 查询没有缓存的数据 */
            if ( !empty($inexistenceIds) ) {
                /* 查询数据库 */
                $inexistenceList = $this->model->select($this->getAllColumn())->whereIn($this->idKey, $inexistenceIds)->order($this->order)->get();
                if ( $this->withAll ) {
                    foreach ($this->withAll as $wv) {
                        $inexistenceList->load($wv);
                    }
                }
                $inexistenceList = $inexistenceList->toArray();
                /* 将数据写入redis */
                if ( !empty($this->withAll) ) {
                    RedisService::update(function () use (&$inexistenceList) {
                        foreach ($inexistenceList as $key => $value) {
                            foreach ($this->withAll as $wev) {
                                if ( isset($value[$wev]) && is_array($value[$wev]) ) {
                                    $inexistenceList[$key][$wev] = json_encode($value[$wev]);
                                }
                            }
                        }
                        return $inexistenceList;
                    });
                } else {
                    RedisService::update($inexistenceList);
                }
                /* 合并数组 */
                $list['data'] = array_merge($list['data'], $inexistenceList);
            }
        } else {
            // 查询字段判断
            $this->columnsJudge();
            
            /* 组装唯一标识并加入查询条件 */
            if ( !empty($this->uniqueKey) && !empty($this->uniqueValue) && empty($this->where[$this->uniqueKey]) ) {
                $this->where[$this->uniqueKey] = $this->uniqueValue;
            }

            /* 查询指定id */
            if ( count($idList) ) {
                $this->where[$this->idKey] = ['in', $idList];
            }

            /* 查询数据库并分页 */
            if ( $this->with ) {
                $list = $this->model->select($this->columns)->wheres($this->where)->order($this->order);
                if ( $pageFlag === false ) {
                    $list = $list->get();
                    $pageHtml = '';
                } else {
                    $list = $list->paginate($this->perPage)->appends($input);
                    $pageHtml = $list->links();
                }
                foreach ($this->with as $wv) {
                    $list->load($wv);
                }
            } else {
                if ( $pageFlag === false ) {
                    $list = $this->model->select($this->columns)->wheres($this->where)->order($this->order)->get();
                    $pageHtml = '';
                } else {
                    $list = $this->model->select($this->columns)->wheres($this->where)->order($this->order)->paginate($this->perPage)->appends($input);
                    $pageHtml = $list->links();
                }
            }
            /* 转化整个模型集合为数组 */
            $list = $list->toArray();
        }

        /* 解析数据 */
        if ( isset($list['data']) && !empty($this->withAll) ) {
            foreach ($list['data'] as $k => $v) {
                foreach ($this->withAll as $wev) {
                    if ( isset($v[$wev]) && !is_array($v[$wev]) ) {
                        $list['data'][$k][$wev] = json_decode($v[$wev], true);
                    }
                }
            }
        } elseif ( !isset($list['data']) ) {
            $temp['data'] = $list;
            $list = $temp;
            unset($temp);
        }
        return [ $list, $pageHtml ];
    }

    /**
     * 单条数据查询
     * 1、读取redis数据
     * 2、第一步失败则查数据库并写入redis
     * 
     * @param  integer $id [主键值]
     * @return array       [查询结果]
     */
    public function getInfo($id = -1)
    {
        if ( empty($this->where) ) {
            if ( RedisService::exists($id) ) {
                // redis中存在该键 获取单条数据
                $info = RedisService::getInfo($id);
                if ( empty($info) ) {
                    // 查询数据库
                    if ( empty($this->withAll) ) {
                        $info = $this->model->select($this->getAllColumn())->find($id);
                    } else {
                        $info = $this->model->select($this->getAllColumn())->with($this->withAll)->find($id);
                    }
                    if ( count($info) ) {
                        $info = $info->toArray();
                        $redisDatas = $info;
                        // 数据处理
                        if ( !empty($this->withAll) ) {
                            foreach ($this->withAll as $value) {
                                if ( isset($redisDatas[$value]) && is_array($redisDatas[$value]) ) {
                                    $redisDatas[$value] = json_encode($redisDatas[$value]);
                                }
                            }
                        }
                        // 将数据写入redis
                        RedisService::update($redisDatas);
                    } else {
                        return [];
                    }
                } else {
                    // 数据处理
                    if ( !empty($this->withAll) ) {
                        foreach ($this->withAll as $value) {
                            if ( isset($info[$value]) ) {
                                $info[$value] = json_decode($info[$value], true);
                            }
                        }
                    }
                }
            } else {
                // 查询字段判断
                $this->columnsJudge();

                // redis里没有对应键值 则查询数据库
                if ( empty($this->withAll) ) {
                    $info = $this->model->select($this->columns)->find($id);
                } else {
                    $info = $this->model->select($this->columns)->with($this->withAll)->find($id);
                }
                if ( count($info) ) {
                    // redis里没有任何数据 则将所有主键存入redis
                    $this->redisEmptyJudge();

                    $info = $info->toArray();
                    $redisDatas = $info;
                } else {
                    return [];
                }

                // 数据处理
                if ( !empty($this->withAll) ) {
                    foreach ($this->withAll as $value) {
                        if ( isset($redisDatas[$value]) && is_array($redisDatas[$value]) ) {
                            $redisDatas[$value] = json_encode($redisDatas[$value]);
                        }
                    }
                }
                
                RedisService::update([$redisDatas]);
            }
        } else {
            // 查询字段判断
            $this->columnsJudge();
            
            $info = $this->model->select($this->columns)->wheres($this->where)->first();
            
            if ( count($info) ) {
                $info = $info->toArray();
            } else {
                return [];
            }
        }
        

        return $info;
    }

    /**
     * 更新数据库，仅支持单条记录更新
     * 
     * @param  array  $datas     [要更新的数据数组]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * 
     * @return [json|boolean] [ajax标识为真则返回json，否则更新成功则返回true，更新失败返回false]
     */
    public function updateD($datas, $ajaxFlag = true)
    {
        // 防止失误更新整表 必须加条件更新
        if ( empty($this->where) ) {
            $ajaxFlag && error();
            return false;
        }

        $dbResult = $this->model->wheres($this->where)->update($datas);

        return $this->dealDbResult($dbResult, $ajaxFlag);
    }

    /**
     * 更新redis缓存数据
     * 支持多条更新和单条更新
     * 多条记录更新需要把所有字段传入
     * 
     * @param  array|string  $datas    [要更新redis缓存数据数组 - 每条必须包含主键 | 一个闭包 | 字符串 - 更新数据的id ]
     * 数组示例：更新商品1，商品2，商品3的数据
     * $datas = [
     *     [ 'id' => '1', 'title' => '商品1', 所有字段要写全... ],
     *     [ 'id' => '2', 'title' => '商品2', 所有字段要写全... ],
     *     [ 'id' => '3', 'title' => '商品3', 所有字段要写全... ]
     * ];
     * 
     * 
     * 字符串示例： 要将商品id为1的商品的标题更新为商品2，库存更新为5000
     * $datas = '1';
     * $field = [
     *     'title' => '商品2',
     *     'stock' => 5000
     * ];
     * @return [json|boolean] [ajax标识为真则返回json，否则返回true，redis操作失败没做处理！]
     */
    public function updateR($datas, $field = [], $ajaxFlag = true)
    {
        if ( !empty($this->withAll) ) {
            if ( is_array($datas) ) {
                foreach ($datas as $key => $value) {
                    foreach ($this->withAll as $wev) {
                        if ( isset($value[$wev]) && is_array($value[$wev]) ) {
                            $datas[$key][$wev] = json_encode($value[$wev]);
                        }
                    }
                }
            } else {
                foreach ($this->withAll as $wev) {
                    if ( isset($field[$wev]) && is_array($field[$wev]) ) {
                        $field[$wev] = json_encode($field[$wev]);
                    }
                }
            }
        }

        RedisService::update($datas, $field);
        
        $ajaxFlag && success();
        return true;
    }

    /**
     * 更新redis缓存数据
     * 支持多条更新和单条更新
     * 多条记录更新需要把所有字段传入
     * 防止失误更新整表 必须加条件更新
     * 
     * @param  array  $datas [要更新的数据数组]
     * @param  boolean  $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * 
     * @return boolean [成功true，失败false]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月28日 10:51:25
     */
    public function update($datas, $ajaxFlag = true)
    {
        if ( is_array(current($datas)) ) {
            // batch
            foreach ($datas as $key => $value) {
                $save = $this->model->wheres($this->where)->update($value);
                if ( !$save ) {
                    $ajaxFlag && error();
                    return false;
                }
                if ( !empty($this->withAll) ) {
                    foreach ($this->withAll as $wev) {
                        if ( isset($value[$wev]) && is_array($value[$wev]) ) {
                            $datas[$key][$wev] = json_encode($value[$wev]);
                        }
                    }
                }
            }
            $redisDatas = $datas;
            $redisField = [];
        } else {
            // only one
            if ( empty($this->where) ) {
                // 防止失误更新整表 必须加条件更新
                $ajaxFlag && error();
                return false;
            }
            $save = $this->model->wheres($this->where)->update($datas);
            if ( !$save ) {
                $ajaxFlag && error();
                return false;
            }
            if ( !empty($this->withAll) ) {
                foreach ($this->withAll as $wev) {
                    if ( isset($datas[$wev]) && is_array($datas[$wev]) ) {
                        $datas[$wev] = json_encode($datas[$wev]);
                    }
                }
            }
            $redisDatas = isset($this->where[$this->idKey]) ? $this->where[$this->idKey] : $datas[$this->idKey];
            $redisField = $datas;
        }

        if ( $save ) {
            // 更新redis
            RedisService::update($redisDatas, $redisField);
            $ajaxFlag && success();
            return true;
        }
        $ajaxFlag && error();
        return false;
    }

    /**
     * 插入数据库 并返回主键
     * @param  array  $datas     [要插入的数据库]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * 
     * @return [json|boolean|integer] [ajax标识为真则返回json，否则插入成功则返回主键值，插入失败返回false]
     */
    public function addD($datas, $ajaxFlag = true)
    {
        $id = $this->model->insertGetId($datas);

        if ( !$id ) {
            $ajaxFlag && error();
            return false;
        }

        $ajaxFlag && success();
        return $id;
    }

    /**
     * 插入redis
     * @param  array  $datas     [要插入的数据库]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * 
     * @return [boolean] [成功true；失败false]
     */
    public function addR($datas, $ajaxFlag = true)
    {
        if ( !empty($this->withAll) ) {
            foreach ($this->withAll as $wev) {
                if ( isset($datas[$wev]) && is_array($datas[$wev]) ) {
                    $datas[$wev] = json_encode($datas[$wev]);
                }
            }
        }

        RedisService::save([$datas]);

        $ajaxFlag && success();
        return true;
    }

    /**
     * 插入db和redis
     * 
     * @param  array  $datas [要插入的数据]
     * 
     * 批量插入（二维数组）
     * $datas = [
     *     [ 'id' => '1', 'title' => '商品1', 所有字段要写全... ],
     *     [ 'id' => '2', 'title' => '商品2', 所有字段要写全... ],
     *     [ 'id' => '3', 'title' => '商品3', 所有字段要写全... ]
     * ];
     * 
     * 
     * 单条记录插入（一维数组）
     * $datas = [
     *     'title' => '商品2',
     *     'stock' => 5000,
     *     所有字段要写全...
     * ];
     * @param  boolean  $ajaxFlag ajax标识，默认调用ajax返回数据函数
     * 
     * @return array ajax标识为真则返回json；单条记录插入失败返回false，批量插入返回失败是第几条数据插入失败，插入成功返回id或id数组
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月28日 10:51:25
     */
    public function add($datas, $ajaxFlag = true) {
        if ( is_array(current($datas)) ) {
            // batch 事务没写！！！
            $datas = array_values($datas);
            foreach ($datas as $key => $value) {
                $id[$key] = $this->model->insertGetId($value);
                if ( $id[$key] === false ) {
                    $ajaxFlag && error();
                    return $key;
                }
            }
            $query = $this->model->whereIn($this->idKey, $id)->get();
        } else {
            // only one
            $id = $this->model->insertGetId($datas);
            if ( $id === false ) {
                $ajaxFlag && success();
                return false;
            }
            $query = $this->model->where($this->idKey, $id)->get();
        }

        if ( !empty($this->withAll) ) {
            foreach ($this->withAll as $val) {
                $query->load($val);
            }
        }
        $redisDatas = $query->toArray();

        foreach ($redisDatas as $k => $val) {
            if ( !empty($this->withAll) ) {
                foreach ($this->withAll as $v) {
                    if ( !isset($redisDatas[$k][$v]) ) {
                        $redisDatas[$k][$v] = json_encode([]);
                    } else {
                        $redisDatas[$k][$v] = json_encode($redisDatas[$k][$v]);
                    }
                }
            }
        }

        // 插入redis
        RedisService::save($redisDatas);
        $ajaxFlag && success();
        return $id;
    }

    /**
     * 删除db+redis
     * 
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  [integer|string|array] $id [要删除数据的主键值或主键值数组]
     * 
     * @return [json|boolean] [ajax标识为真则返回json，否则删除成功则true，删除失败返回false]
     */
    public function delete($id, $ajaxFlag = true)
    {
        if ( empty($this->where) ) {
            $this->where[$this->idKey] = $id;
        }

        $dbResult = $this->deleteD(false);

        $dbResult && $dbResult = $this->deleteR($id, $ajaxFlag);

        return $this->dealDbResult( $dbResult, $ajaxFlag );
    }

    /**
     * 删除db
     * 
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * 
     * @return [json|boolean] [ajax标识为真则返回json，否则删除成功则true，删除失败返回false]
     */
    public function deleteD($ajaxFlag = true)
    {
        if ( empty($this->where) ) {
            $ajaxFlag && error();
            return false;
        }

        $dbResult = $this->model->wheres($this->where)->delete();

        return $this->dealDbResult( $dbResult, $ajaxFlag );
    }

    /**
     * 删除redis
     * 
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  [integer|string|array] $id [要删除数据的主键值或主键值数组]
     * 
     * @return [json|boolean] [ajax标识为真则返回json，否则删除成功则true，删除失败返回false]
     */
    public function deleteR($id, $ajaxFlag = true)
    {
        if ( !RedisService::exists($id) ) {
            $ajaxFlag && success();
            return true;
        }

        $dbResult = RedisService::delete($id);
        if ( $dbResult ) {
            $ajaxFlag && success();
            return boolval($dbResult);
        }

        $ajaxFlag && error();
        return boolval($dbResult);
    }

    /**
     * 删除db+redis以及关联关系的db+redis数据
     * 
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  [integer|string|array] $id [要删除数据的主键值或主键值数组]
     * 
     * @return [json|boolean] [ajax标识为真则返回json，否则删除成功则true，删除失败返回false]
     */
    public function deleteAllWith($id, $ajaxFlag = true)
    {
        $this->delete($id, $ajaxFlag = true);
    }

    /**
     * redis自增自减
     * 
     * @param  integer $id       [主键值]
     * @param  string  $field    [自增字段名]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  mixed   $num      [增量，支持整型和浮点型，支付负数自减]
     * @return [type]            [description]
     */
    public function incrementR($id, $field, $ajaxFlag = true, $num = 1)
    {
        $dbResult = RedisService::increment($id, $field, $num);

        if ( $dbResult !== false ) {
            $ajaxFlag && success();
            return $dbResult;
        }

        $ajaxFlag && error();
        return false;
    }

    /**
     * db自增自减
     * 
     * @param  integer $id       [主键值]
     * @param  string  $field    [自增字段名]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  mixed   $num      [增量，支持整型和浮点型，支持负数自减]
     * @return [type]            [description]
     */
    public function incrementD($id, $field, $ajaxFlag = true, $num = 1)
    {
        $this->where[$this->idKey] = $id;

        if ( $num > 0 ) {
            $dbResult = $this->model->wheres($this->where)->increment($field, $num);
        } elseif ( $num < 0 ) {
            $dbResult = $this->model->wheres($this->where)->decrement($field, $num);
        }

        return $this->dealDbResult( $dbResult, $ajaxFlag );
    }

    /**
     * db+redis自增
     * 
     * @param  integer $id       [主键值]
     * @param  string  $field    [自增字段名]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  mixed   $num      [增量，支持整型和浮点型]
     * @return [type]            [description]
     */
    public function increment($id, $field, $ajaxFlag = true, $num = 1)
    {
        if ( empty($id) ) {
            if ( empty($this->where) ) {
                $ajaxFlag && error();
                return false;
            }
            $id = $this->model->wheres($this->where)->value($this->idKey);
        } else {
            $this->where[$this->idKey] = $id;
        }

        $dbResult = $this->incrementD($id, $field, false, $num);
        
        if ( $dbResult ) {
            return $this->incrementR( $id, $field, $ajaxFlag, $num );
        }

        $ajaxFlag && error();
        return false;
    }

    /**
     * 设置查询字段
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月22日 14:54:38
     * 
     * @param  array|mixed  $columns [字段名]
     * 
     * @return $this
     */
    public function select($columns)
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }
    
    /**
     * 设置查询条件
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月22日 14:21:25
     * 
     * @param  array  $where [条件数组]
     * 
     * @return $this
     */
    public function where(array $where)
    {
        $this->where = $where;

        return $this;
    }
    
    /**
     * 获取当前查询条件
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月22日 14:21:25
     * 
     * @param  array  $where [条件数组]
     * 
     * @return $this
     */
    public function whereGet()
    {
        return $this->where;
    }

    /**
     * 追加设置查询条件
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月7日 17:10:48
     * 
     * @param  array  $where [追加条件数组]
     * @param  array  $flag  [覆盖标识：如果原条件数组和追加条件数组有相同字段的条件时是否覆盖原条件数组中的元素，默认不覆盖]
     * 
     * @return $this
     */
    public function whereAdd(array $where, $flag = false)
    {
        $this->where = $flag === false ? array_merge($where, $this->where) : array_merge($this->where, $where);
        return $this;
    }

    /**
     * 设置强制忽略查询条件数组
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年3月6日 11:16:05
     * 
     * @param  array  $where [条件数组]
     * 
     * @return $this
     */
    public function whereIgnore(array $where)
    {
        $this->whereIgnore = $where;

        return $this;
    }

    /**
     * 设置排序字符串
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月22日 14:33:03
     * 
     * @param  string  $order [排序字符串]
     * 
     * @return $this
     */
    public function order($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * 设置关联关系
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月7日 14:55:04
     * 
     * @param  array  $with [关联关系名称]
     * 
     * @return $this
     */
    public function with($with)
    {
        $this->with = $with;

        return $this;
    }


    /**
     * redis数据为空的情况处理
     * 
     * redis里没有任何数据 则将所有主键存入redis
     * 否则不进行任何操作
     * 
     * @return $this
     */
    public function redisEmptyJudge()
    {
        if ( !RedisService::count() ) {
            $wherePush = [];
            if ( !empty($this->whereIgnore) ) {
                $wherePush = $this->whereIgnore;
            }
            if ( !empty($this->uniqueKey) && !empty($this->uniqueValue) && empty($wherePush[$this->uniqueKey]) ) {
                $wherePush[$this->uniqueKey] = $this->uniqueValue;
            }
            $this->model->select([$this->idKey])->where($wherePush)->order($this->order)->chunk(100, function($totalIds) {
                RedisService::push($totalIds);
            });
        }

        return $this;
    }

    /**
     * 设置页数
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月27日 10:04:47
     * 
     * @param  string  $perPage [每页数据条数]
     * 
     * @return $this
     */
    public function perPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * 查询前页数判断
     * 
     * 已设置则知道redis页数
     * 未设置则读取配置文件中的每页多少条数据参数（redis默认也会读取配置文件中的每页多少条数据参数，故不需要再单独设置）
     * 
     * @return $this
     */
    public function perPageJudge()
    {
        if ( $this->perPage ) {
            RedisService::per($this->perPage);
        } else {
            $this->perPage = config('database')['perPage'];
        }

        return $this;
    }

    /**
     * 查询前的查询字段判断
     * 
     * 由于目前数据库所有字段是查数据库获取的 所有逻辑有改动，暂时先使用才判断没有设置字段则查所有字段
     * 
     * @return $this
     */
    public function columnsJudge()
    {
        if ( empty($this->columns) ) {
            $this->columns = $this->field ? $this->field : $this->getAllColumn();
        }
    }

    /**
     * 数组转树形结构
     * 
     * @param  array   $list  [数据数组]
     * @param  string  $pk    [主键字段名称]
     * @param  string  $pid   [父级字段名称]
     * @param  string  $child [子级下标]
     * @param  integer $root  [顶级id]
     * @return array          [处理后的数组]
     */
    public function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = [];

        if ( is_array($list) && count($list) ) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $value) {
                $refer[$value[$pk]] =& $list[$key];
                $refer[$value[$pk]][$child] = [];
            }
            foreach ($list as $key => $value) {
                // 判断是否存在parent
                $parentId = $value[$pid];
                if ( $root == $parentId ) {
                    $tree[] =& $list[$key];
                } else {
                    if ( isset( $refer[$parentId] ) ) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }

        return $tree;
    }

    /**
     * db或redis操作返回结果处理
     * 
     * @param  [mixed]        $dbResult [db或redis操作返回结果]
     * @param  [boolean]      $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @return [json|boolean]           [ajax标识为真则返回json，否则删除成功则true，删除失败返回false]
     */
    public function dealDbResult($dbResult, $ajaxFlag)
    {
        if ( $dbResult ) {
            $ajaxFlag && success();
            return true;
        }

        $ajaxFlag && error();
        return false;
    }

    /**
     * 获取属性值
     * 
     * @param  string $key [属性名]
     * @return mixed       [属性值]
     */
    public function get($key)
    {
        if ( isset($this->$key) ) {
            return $this->$key;
        }

        error('属性不存在');
    }

    /**
     * 设置redis键
     * 
     * @return $this
     */
    public function setRedisKey()
    {
        $uniqueKey   = $this->uniqueKey;
        $uniqueValue = $this->uniqueValue;
        $tableName   = $this->model->getTable();

        if ( !empty($uniqueKey) && !empty($uniqueValue) ) {
            if ( !empty($this->whereIgnore) ) {
                $uniqueKey .= '_';
                foreach ($this->whereIgnore as $key => $value) {
                    $uniqueKey .= $key . '_' . $value;
                }
            }
            $uniqueKey = ':' . $uniqueKey . ':' . $uniqueValue;
        } else {
            $uniqueKey = '';
        }
        $redisListKey = $tableName . $uniqueKey;
        $redisHashKey = $tableName . ':' . $this->idKey . ':';
        RedisService::key([ $redisListKey, $redisHashKey ], $this->idKey);

        return $this;
    }

    /**
     * 调用Model方法
     * 
     * @param  string $method     [方法名称]
     * @param  mixed  $parameters [参数]
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->model->$method(...$parameters);
    }
}
