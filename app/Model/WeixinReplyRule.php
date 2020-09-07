<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use LinkToService;
use RedisService;

/**
 * 回复规则
 * 
 * @author 黄东 406764368@qq.com
 * @version  2017年3月15日 17:39:43
 */
class WeixinReplyRule extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;
    
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_reply_rule';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 所有关联关系
     * 
     * @var array
     */
    public $withAll = ['weixinReplyKeyword', 'weixinReplyContent'];

    /**
     * 关联到回复关键词表
     * 
     * @return Builder
     */
    public function weixinReplyKeyword() {
        return $this->hasMany('App\Model\WeixinReplyKeyword', 'rule_id')->select(Schema::getColumnListing('weixin_reply_keyword'));
    }

    /**
     * 关联到回复内容表
     * 
     * @return Builder
     */
    public function weixinReplyContent() {
        return $this->hasMany('App\Model\WeixinReplyContent', 'rule_id')->select(Schema::getColumnListing('weixin_reply_content'));
    }

    /**
     * 数据处理
     * 
     * @param  array $list [需要被处理的数据]
     * @return array       [处理后的数据]
     */
    public function dealDatas($list)
    {
        $list = $list['data'];
        
        foreach ($list as $key => $value) {
            //取出的列表如果是json Herry
            if (!is_array($value['weixinReplyKeyword'])) {
                $value['weixinReplyKeyword'] = json_decode($value['weixinReplyKeyword'], true);
                $list[$key] = $value;
            }
            if (!is_array($value['weixinReplyContent'])) {
                $value['weixinReplyContent'] = json_decode($value['weixinReplyContent'], true);
                $list[$key] = $value;
            }

            foreach ($value['weixinReplyContent'] as $k => $v) {
                $v['config'] = json_decode($v['config'], true);
                $urlDatas['wid']  = $v['wid'];
                $urlDatas['id']   = $v['config']['id'] ?? 0;
                $urlDatas['type'] = 0;
                switch ( $v['type'] ) {
                    case '1':
                        // 文本
                        $v['config']['show_title'] = '文本';
                        $v['config']['show_sub']   = $v['config']['content'];
                        break;
                    case '2':
                        // 图片
                        $v['config']['show_title'] = '图片';
                        $v['config']['show_sub']   = $v['config']['url'];
                        break;
                    case '3':
                        // 图文
                        $v['config']['show_title'] = '图文';
                        $v['config']['show_sub']   = $v['config']['title'];

                        if ( $v['config']['type'] == 1 ) {
                            $urlDatas['type'] = 10;
                        } elseif ( $v['config']['type'] == 2 ) {
                            $urlDatas['type'] = 11;
                        }
                        break;
                    case '4':
                        // 语音
                        $v['config']['show_title'] = '语音';
                        $v['config']['show_sub']   = '语音';
                        break;
                    case '5':
                        // 音乐
                        $v['config']['show_title'] = '音乐';
                        $v['config']['show_sub']   = $v['config']['title'];
                        break;
                    case '6':
                        // 其他
                        switch ( $v['config']['type'] ) {
                            case '1':
                                // 商品
                                $v['config']['show_title'] = '商品';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = 1;
                                break;
                            case '2':
                                // 商品分组
                                $v['config']['show_title'] = '商品分组';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = 2;
                                break;
                            case '3':
                                // 微页面
                                $v['config']['show_title'] = '微页面';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = 3;
                                break;
                            case '4':
                                // 微页面分类
                                $v['config']['show_title'] = '微页面分类';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = 4;
                                break;
                            case '5':
                                // 店铺主页
                                $v['config']['show_title'] = '店铺主页';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = 5;
                                break;
                            case '6':
                                // 会员主页
                                $v['config']['show_title'] = '会员主页';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = 6;
                                break;
                            case '7':
                                // 营销活动
                                $v['config']['show_title'] = '营销活动';
                                $v['config']['show_sub']   = $v['config']['title'];
                                $urlDatas['type'] = $v['config']['type'];
                                $urlDatas['activeType'] = $v['config']['activeType'];
                                break;
                            default:
                                $v['config']['show_title'] = '';
                                $v['config']['show_sub']   = '';
                                break;
                        }
                        break;
                    default:
                        $v['config']['show_title'] = '';
                        $v['config']['show_sub']   = '';
                        break;
                }

                //营销活动的时候链接地址单独设置 update by wuxiaoping 2017.08.25
                if($urlDatas['type'] == 7){
                    if($urlDatas['activeType'] == 1)
                    {
                        $urlDatas['url'] = config('app.url').'shop/activity/egg/index/'.$list[0]['wid'].'/'.$urlDatas['id'];
                    }else{
                        $urlDatas['url'] = config('app.url').'shop/activity/wheel/'.$list[0]['wid'].'/'.$urlDatas['id'];
                    }
                    
                }else{
                    $urlDatas = LinkToService::parseUrl($urlDatas);
                }
                
                $list[$key]['weixinReplyContent'][$k]['url'] = $urlDatas['url'];
                $list[$key]['weixinReplyContent'][$k]['config'] = $v['config'];
                unset($urlDatas);
            }
        }

        return $list;
    }

    /**
     * 手动更新数据
     * 
     * @param  array  $datas   [需要手动更新的数据]
     * @param  array  $input   [最新数据]
     * @return array           [手动更新后的数据]
     */
    public function artificialDatas( $datas, $input ) {
        foreach ($datas as $key => $value) {
            if ( $value['id'] == $input['id'] ) {
                foreach ($value as $k => $v) {
                    if ( isset($input[$k]) ) {
                        $datas[$key][$k] = $input[$k];
                    }
                }
            }
        }

        return $datas;
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
}
