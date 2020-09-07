<?php 

namespace App\Services\Foundation;

use Illuminate\Pagination\LengthAwarePaginator;
use Redisx;

/**
* Redis(Hash)操作类服务类
* 功能简介：
* 1、获取redis数据总数；实现该功能必须调用的方法：count；可能需要调用的方法：key
* 2、获取redis数据列表并分页；实现该功能必须调用的方法：page；可能需要调用的方法：key、per
* 3、获取redis数据列表；实现该功能必须调用的方法：getList；可能需要调用的方法：key、per
* 4、获取redis一条数据；实现该功能必须调用的方法：getInfo；可能需要调用的方法：key
* 5、将数据存入redis（支持批量操作）；实现该功能必须调用的方法：save；可能需要调用的方法：key、push
* 6、更新redis数据（支持批量操作）；实现该功能必须调用的方法：update；可能需要调用的方法：key
* 
* @author 黄东 406764368@qq.com
* @version 2017年1月19日 13:57:18
*/
class RedisService {
    /**
     * 每页展示多少条数据
     *
     * @var integer
     */
    protected $perPage;

    /**
     * list键名
     *
     * @var string
     */
    protected $listKey;

    /**
     * hash键名
     *
     * @var string
     */
    protected $hashKey;

    /**
     * 主键
     *
     * @var string
     */
    protected $idKey = 'id';

    /**
     * 当前页码数，默认第一页
     *
     * @var integer
     */
    protected $page = 1;

    /**
     * 分页数据
     *
     * @var array
     */
    protected $list;

    /**
     * 数据总数
     *
     * @var integer
     */
    protected $count;

    /**
     * 分页html
     *
     * @var HtmlString类
     */
    protected $pageHtml;

    /**
     * 构造函数
     * 
     * @param integer $perPage [每页展示多少条数据]
     * @return void
     */
    public function __construct( $perPage ) {
        $this->perPage = $perPage;
    }

    /**
     * 设置redis键名
     * 
     * @param string|array $redisKey [redis键名]
     * @return this
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月21日 09:20:57
     */
    public function key( $redisKey, $idKey = 'id' ) {
        /* 设置redis键名 */
        if ( is_array($redisKey) ) {
            $this->listKey = $redisKey[0];
            $this->hashKey = $redisKey[1];
        } else {
            $this->listKey = $redisKey;
            $this->hashKey = $redisKey;
        }
        /* 设置主键 */
        $this->idKey = $idKey;

        return $this;
    }

    /**
     * 设置每页展示多少条数据
     * 
     * @param integer [每页展示多少条数据]
     * @return this
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月21日 09:43:09
     */
    public function per( $perPage ) {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * redis分页
     * 
     * @return array  ['数据数组', '分页html', '在redis中没有数据的id数组']
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月19日 21:08:10
     */
    public function page() {
        /* 查询redix数据总数 */
        $this->count = Redisx::LLEN($this->listKey);
        /* http请求类 */
        $request = app('request');
        /* 接收参数 */
        $input = $request->input();
        /* 设置当前页码数 */
        !empty($input['page']) && $this->page = $input['page'];
        /* 调用自定义分页类 */
        $redisPage = new LengthAwarePaginator([], $this->count, $this->perPage, null, ['path' => $request->url()]);
        /* 添加参数到分页 */
        $list = $redisPage->appends($input);
        /* 分页html */
        $this->pageHtml = $list->links();
        /* 转化整个模型集合为数组 */
        $list = $list->toArray();
        /* 计算当前页展示第几条数据至第几条数据 */
        $list['from'] = ( $this->page - 1 ) * $this->perPage;
        $list['to']   = ( $this->page - 1 ) * $this->perPage + $this->perPage - 1;
        /* 设置数据 */
        $this->list = $list;

        /* 从redis里取出数据 */
        return $this->getList($list['from'], $list['to']);
    }

    /**
     * 查询redis数据总数
     * 
     * @return array [redis数据总数]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月21日 10:47:49
     */
    public function count() {
        return Redisx::LLEN($this->listKey);
    }

    /**
     * 返回列表 key 中指定区间内的元素，区间以偏移量 start 和 stop 指定。
     * 下标(index)参数 start 和 stop 都以 0 为底，也就是说，以 0 表示列表的第一个元素，以 1 表示列表的第二个元素，以此类推。
     * 你也可以使用负数下标，以 -1 表示列表的最后一个元素， -2 表示列表的倒数第二个元素，以此类推。
     * 
     * @param  integer $from   [下标(index)参数 开始值 默认从头开始]
     * @param  integer $to     [下标(index)参数 结束值 默认查至末位]
     * @return array   $idList [一个列表，包含指定区间内的元素]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月20日 09:12:04
     */
    public function getList( $from = 0, $to = -1, $idList = [] ) {
        /* 取数据id */
        $idList = $idList ?: Redisx::LRANGE($this->listKey, $from, $to);
        /* 根据数据id 读取redis对应的信息 */
        if ( !empty($idList) ) {
            foreach ($idList as $value) {
                /* 判断键是否存在 */
                if ( $this->exists($value) ) {
                    $lists[] = Redisx::HGETALL($this->hashKey . $value);
                } else {
                    $inexistenceIds[] = $value;
                }
            }
        }

        /* 数据处理 */
        $this->list['data'] = $lists ?? [];
        $return[] = $this->list;
        $return[] = $this->pageHtml;
        $return[] = $inexistenceIds ?? [];

        return $return;
    }

    /**
     * 获取单条数据，可指定字段
     * 
     * @param  string $field [指定获取字段名，多个用逗号分隔  暂无此功能]
     * @return array         [查询结果]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月22日 13:56:37
     */
    public function getInfo($value) {
        return Redisx::HGETALL($this->hashKey . $value);
    }

    /**
     * 判断键名在redis中是否存在
     * 
     * @param  string $value [主键的值]
     * @return integer       [是否存在该键，0不存在，1存在]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年2月21日 11:40:17
     */
    public function exists($value) {
        return Redisx::EXISTS($this->hashKey . $value);
    }

    /**
     * 将数据存入redis缓存，将数据和id同时存入redis
     * 
     * @param  array|\Closure  $datas    [要保存redis缓存数据数组 - 每条必须包含id | 一个闭包 ]
     * 数组示例：保存商品1，商品2，商品3的数据
     * $datas = [
     *     [ 'id' => '1', 'title' => '商品1', 所有字段要写全... ],
     *     [ 'id' => '2', 'title' => '商品2', 所有字段要写全... ],
     *     [ 'id' => '3', 'title' => '商品3', 所有字段要写全... ]
     * ];
     * 
     * 闭包示例：保存订单数据，将订单商品详情转为json存入redis
     * $datas = function () use (&$inexistenceList) {
     *     foreach ($inexistenceList as $key => $value) {
     *         $inexistenceList[$key]['order_detail'] = json_encode($value['order_detail']);
     *     }
     *     return $inexistenceList;
     * };
     * @return array                     [处理后的数据数组]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月19日 13:58:14
     */
    public function save( $datas ) {
        /* 闭包处理 */
        if ( $datas instanceof \Closure ) {
            $datas = call_user_func($datas);
        }

        /* 将数据写入redis */
        if ( is_array($datas) || !empty($datas) ) {
            /* redis管道 */
            Redisx::pipeline(function ($pipe) use ($datas) {
                foreach ($datas as $value) {
                    /* 存储单条数据 */
                    $pipe->HMSET($this->hashKey . $value[$this->idKey], $value);
                    /* id数组 用于分页 */
                    $pipe->LPUSH($this->listKey, $value[$this->idKey]);
                }
            });
        }

        return $datas;
    }

    /**
     * 将列表id数组数据存入redis缓存
     * 
     * @param  array  $datas  [要存入redis的数据数组]
     * 示例：将订单列表id数组存入redis
     * $datas = [
     *     'id' => 1,
     *     'id' => 2,
     *     'id' => 3,
     *     ...
     * ];
     * @return $this
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月21日 10:53:58
     */
    public function push( $datas ) {
        /* 将数据写入redis */
        if ( is_array($datas) || !empty($datas) ) {
            /* redis管道 */
            Redisx::pipeline(function ($pipe) use ($datas) {
                foreach ($datas as $key => $value) {
                    /* id数组 用于分页 */
                    $pipe->RPUSH($this->listKey, $value[$this->idKey]);
                }
            });
        }

        return $this;
    }

    /**
     * 更新redis缓存数据
     * 支持多条更新和单条更新
     * 多条记录更新需要把所有字段传入
     * 
     * @param  array|\Closure|string  $datas    [要更新redis缓存数据数组 - 每条必须包含主键 | 一个闭包 | 字符串 - 更新数据的id ]
     * 数组示例：更新商品1，商品2，商品3的数据
     * $datas = [
     *     [ 'id' => '1', 'title' => '商品1', 所有字段要写全... ],
     *     [ 'id' => '2', 'title' => '商品2', 所有字段要写全... ],
     *     [ 'id' => '3', 'title' => '商品3', 所有字段要写全... ]
     * ];
     * 
     * 闭包示例：更新订单数据，将订单商品详情转为json存入redis
     * $datas = function () use (&$inexistenceList) {
     *     foreach ($inexistenceList as $key => $value) {
     *         $inexistenceList[$key]['order_detail'] = json_encode($value['order_detail']);
     *     }
     *     return $inexistenceList;
     * };
     * 
     * 字符串示例： 要将商品id为1的商品的标题更新为商品2，库存更新为5000
     * $datas = '1';
     * $field = [
     *     'title' => '商品2',
     *     'stock' => 5000
     * ];
     * @return array [处理后的数据]
     * 
     * @author 黄东 406764368@qq.com
     * @version 2017年1月20日 10:03:04
     */
    public function update( $datas, $field = [] ) {
        /* 闭包处理 */
        if ( $datas instanceof \Closure ) {
            $datas = call_user_func($datas);
        }

        /* 将数据写入redis */
        if ( is_array($datas) && !empty($datas) ) {
            /* redis管道 */
            Redisx::pipeline(function ($pipe) use ($datas) {
                foreach ($datas as $key => $value) {
                    if (Redisx::EXISTS($this->hashKey . $value[$this->idKey])) {
                        $pipe->HMSET($this->hashKey . $value[$this->idKey], $value);
                    }

                }
            });
        } elseif ( ( is_string($datas) || is_int($datas) ) && !empty($datas) ) {
            $datas = strval($datas);
            /* redis管道 */
            Redisx::pipeline(function ($pipe) use ($field,$datas) {
                foreach ($field as $key => $value) {
                    if (Redisx::EXISTS($this->hashKey . $datas)){
                        $pipe->HSET($this->hashKey . $datas, $key, $value);
                    }
                }
            });
        }

        return $datas;
    }

    /**
     * 删除redis数据
     * 
     * @param  [integer|string|array] $datas [要删除数据的主键值或主键值数组]
     * 数组只能传一维数组，例如要删除id为1，2，3的店铺则传：[1,2,3]
     * 
     * @return [boolen]        [操作成功返回true，操作失败返回false]
     */
    public function delete( $datas ) {
        if ( is_numeric($datas) && !empty($datas) ) {
            $del = Redisx::DEL($this->hashKey . $datas);
            $lrem = Redisx::LREM($this->listKey, 0, $datas);
            if ( $del && $lrem ) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ( is_array($datas) || !empty($datas) ) {
            $result = Redisx::pipeline(function ($pipe) use ($datas) {
                foreach ($datas as $value) {
                    $pipe->DEL($this->hashKey . $value);
                    $pipe->LREM($this->listKey, 0, $value);
                }
            });
        } else {
            return false;
        }

        return boolval($result);
    }

    /**
     * redis自增
     * 
     * 自减请传负数
     * 
     * @param  integer $id       [主键值]
     * @param  string  $field    [自增字段名]
     * @param  boolean $ajaxFlag [ajax标识，默认调用ajax返回数据函数]
     * @param  mixed   $num      [增量，支持整型和浮点型，支持负数]
     * @return mixed             [自增后该域的值]
     */
    public function increment( $id, $field, $num = 1 ) {
        if ( Redisx::HEXISTS($this->hashKey . $id, $field) ) {
            $value = Redisx::HGET($this->hashKey . $id, $field);
            $value = get_numeric($value);
            if ( $value > 0 ) {
                if ( is_int($value) ) {
                    return Redisx::HINCRBY($this->hashKey . $id, $field, $num);
                } elseif ( is_float($value) ) {
                    return Redisx::HINCRBYFLOAT($this->hashKey . $id, $field, $num);
                }
            }
        }
        return true;
    }

    /**
     * 返回当前对象
     * 
     * @return $this
     */
    public function this() {
        return $this;
    }

    /**
     * 执行一个redis函数
     *
     * @param  string  $method     [函数名称]
     * @param  array   $parameters [参数]
     * @return mixed
     */
    public function __call($method, $parameters) {
        return Redisx::command($method, $parameters);
    }
}
