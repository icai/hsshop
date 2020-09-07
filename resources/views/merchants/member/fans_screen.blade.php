@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url') }}mctsource/css/member_y3emxg6y.css">

@endsection

@section('slidebar')
    @include('merchants.member.slidebar')
@endsection

@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="common_nav">
                <li class="">
                    <a href="/merchants/member/fans">我的粉丝</a>
                </li>
                <li @if(!$status) class="hover" @endif>
                    <a href="{{URL('/merchants/member/fans/screen')}}">等级筛选</a>
                </li>
                <li @if($status) class="hover" @endif>
                    <a href="{{URL('/merchants/member/fans/screen/1')}}">购买力筛选</a>
                </li>
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>
        <!-- 三级导航 结束 -->
        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection


@section('content')
    <div class="content">
        <div class="search_header">
            @if($status)
                <form action="{{URL('/merchants/member/fans/screen/1')}}" method="get" role="form">
                    @php

                    $t_time = isset($input['t_time'])?$input['t_time']:null;
                    $tradeCount = isset($input['tradeCount'])?$input['tradeCount']:null;
                    $avg_price = isset($input['avg_price'])?$input['avg_price']:null;
                    @endphp

                    <input name="t_time" type="hidden" value="{{$t_time}}">
                    <input name="tradeCount" type="hidden" value="{{$tradeCount}}">
                    <input name="avg_price" type="hidden" value="{{$avg_price}}">
                    <ul>
                        <li class="border_top">
                            <div class="border_right">
                                <span>最近消费：</span>
                            </div>

                            @php
                            $t_span = '';
                            $t_timeArr = array('1w','2w','1m','2m','3m','6m','-6m');
                            if($s=strpos($t_time,'|')){
                                list($start_time,$end_time) = explode('|',$t_time);
                                $valueTime =  $start_time.'|'.$end_time;
                                $htmlTime = $start_time.' 到 '.$end_time;
                                $t_span = in_array($t_time,$t_timeArr)?'':"<span class=\"nav_list last_date border1 \" value=\"".$valueTime."\">".$htmlTime."</span>";
                            }
                            @endphp

                            <div class="search_nav " >
                                <div class="focus_time" name="t_time">
                                    <span class="nav_list @if(null==$t_time) border1 @endif">不限</span>
                                    <span class="nav_list @if($t_time=='1w') border1 @endif" value="1w" >1周内</span>
                                    <span class="nav_list @if($t_time=='2w') border1 @endif" value="2w" >2周内</span>
                                    <span class="nav_list @if($t_time=='1m') border1 @endif" value="1m" >1个月内</span>
                                    <span class="nav_list @if($t_time=='2m') border1 @endif" value="2m" >2个月内</span>
                                    <span class="nav_list @if($t_time=='3m') border1 @endif" value="3m" >3个月内</span>
                                    <span class="nav_list @if($t_time=='6m') border1 @endif" value="6m" >6个月内</span>
                                    <span class="nav_list @if($t_time=='-6m') border1 @endif" value="-6m" >6个月前</span>
                                    <?php
                                        echo $t_span;
                                    ?>
                                </div>
                                <a href="javascript:void(1);" class="choose_time">自定义..</a>
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>购买次数：</span>
                            </div>
                            <?php
                            $tradeCount_span = '';
                            if(strpos($tradeCount,'-')){
                               // list($startTradeCount,$endTradeCount)= explode('-',$array['tradeCount']);
                                $tradeCount_span = "<span class=\"nav_list credit_date border1 \" value=\"".$tradeCount."\">".$tradeCount."</span>";
                            }
                            ?>
                            <div class="search_nav" >
                                <div class="focus_time " name="tradeCount">
                                    <span class="nav_list @if(null==$tradeCount) border1 @endif">不限</span>
                                    <span class="nav_list @if($tradeCount=='1') border1 @endif" value="1" >1+</span>
                                    <span class="nav_list @if($tradeCount=='2') border1 @endif" value="2" >2+</span>
                                    <span class="nav_list @if($tradeCount=='3') border1 @endif" value="3" >3+</span>
                                    <span class="nav_list @if($tradeCount=='4') border1 @endif" value="4" >4+</span>
                                    <span class="nav_list @if($tradeCount=='5') border1 @endif" value="5" >5+</span>
                                    <span class="nav_list @if($tradeCount=='10') border1 @endif" value="10" >10+</span>
                                    <span class="nav_list @if($tradeCount=='15') border1 @endif" value="15" >15+</span>
                                    <span class="nav_list @if($tradeCount=='20') border1 @endif" value="20" >20+</span>
                                    <span class="nav_list @if($tradeCount=='30') border1 @endif" value="30" >30+</span>
                                    <span class="nav_list @if($tradeCount=='50') border1 @endif" value="50" >50+</span>
                                    <?php
                                      echo $tradeCount_span;
                                    ?>
                                </div>
                                <a href="javascript:void(1);" class="write_credit">自定义..</a>
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>商品均价：</span>
                            </div>
                            @php
                                $avg_span = '';
                                if(strpos($avg_price,'-')){
                                    $avg_priceArr = array('50-','50-80','80-150','150-200','200-300','300-500','500-1000');
                                    if(in_array($avg_price,$avg_priceArr)){
                                        $avg_span = '';
                                    }else{
                                        $avg_span = "<span class=\"nav_list credit_date border1 \" value=\"".$avg_price."\">".$avg_price."</span>";
                                    }
                                }
                            @endphp
                            <div class="search_nav" >
                                <div class="focus_time " name="avg_price">
                                    <span class="nav_list @if(null==$avg_price) border1 @endif">不限</span>
                                    <span class="nav_list @if($avg_price=='50') border1 @endif" value="50" >50-</span>
                                    <span class="nav_list @if($avg_price=='50-80') border1 @endif" value="50-80" >50-80</span>
                                    <span class="nav_list @if($avg_price=='80-150') border1 @endif" value="80-150" >80-150</span>
                                    <span class="nav_list @if($avg_price=='150-200') border1 @endif" value="150-200" >150-200</span>
                                    <span class="nav_list @if($avg_price=='200-300') border1 @endif" value="200-300" >200-300</span>
                                    <span class="nav_list @if($avg_price=='300-500') border1 @endif" value="300-500" >300-500</span>
                                    <span class="nav_list @if($avg_price=='500-1000') border1 @endif" value="500-1000" >500-1000</span>
                                    <span class="nav_list @if($avg_price=='1000') border1 @endif" value="1000" >1000+</span>
                                    @php
                                        echo $avg_span;
                                    @endphp
                                </div>
                                <a href="javascript:void(1);" class="write_credit">自定义..</a>
                            </div>
                        </li>
                    </ul>
                    <div class="btn_grounp">
                        <input type="submit" class="btn btn-primary" value="筛选">
                    </div>
                </form>
            @else
                <form action="{{URL('/merchants/member/fans/screen')}}" method="get" role="form">
                @php
                    $integral = isset($input['integral'])?$input['integral']:null;
                    $cid = isset($input['cid'])?$input['cid']:null;
                    $tagId = isset($input['tag_id'])?$input['tag_id']:null;
                    $tao_level = isset($input['tao_level'])?$input['tao_level']:null;
                    $tao_vip = isset($input['tao_vip'])?$input['tao_vip']:null;
                    $f_time = isset($input['f_time'])?$input['f_time']:null;
               @endphp
                    <input name="integral" type="hidden" value="{{$integral}}">
                    <input name="cid" type="hidden" value="{{$cid}}">
                    <input name="tag_id" type="hidden" value="{{$tagId}}">
                    <input name="tao_level" type="hidden" value="{{$tao_level}}">
                    <input name="tao_vip" type="hidden" value="{{$tao_vip}}">
                    <input name="f_time" type="hidden" value="{{$f_time}}">

                    <ul>
                        <li class="border_top">
                            <div class="border_right">
                                <span>本店积分：</span>
                            </div>
                            <div class="search_nav" >
                                <div class="credit_range focus_time" name="integral">
                                    @php

                                        $span = '';
                                        $integralArr = array('0-100','101-200','201-500','501-1000','1000+');
                                        if($integral){
                                            $span = in_array($integral,$integralArr)?'':"<span class=\"nav_list credit_date border1 \" value=\"".$integral."\">".$integral."</span>";
                                        }
                                    @endphp

                                    <span class="nav_list @if(null==$integral) border1 @endif">不限</span>
                                    <span class="nav_list @if($integral=='0-100') border1 @endif"  value="0-100">0-100</span>
                                    <span class="nav_list @if($integral=='101-200') border1 @endif" value="101-200">101-200</span>
                                    <span class="nav_list @if($integral=='201-500') border1 @endif" value="201-500">201-500</span>
                                    <span class="nav_list @if($integral=='501-1000') border1 @endif" value="501-1000">501-1000</span>
                                    <span class="nav_list @if($integral=='1000+') border1 @endif" value="1000+">1000+</span>
                                    @php

                                        echo $span;
                                    @endphp
                                </div>
                                <a href="javascript:void(1);" class="write_credit">自定义..</a>
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>会员卡等级：</span>
                            </div>
                            <div class="search_nav" name="cid">
                                <span class="nav_list @if(null==$cid) border1 @endif" >不限</span>
                                @foreach($card as $c)
                                    <span class="nav_list @if($cid==$c['id']) border1 @endif" value="{{$c['id']}}">{{$c['card_title']}}</span>
                                @endforeach
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>会员标签：</span>
                            </div>
                            <div class="search_nav" name="tag_id" >
                                <span class="nav_list @if(null==$tagId) border1 @endif"  >不限</span>
                                @foreach($label as $l)
                                    <span  class="nav_list @if($l['id']==$tagId) border1 @endif" value="{{$l['id']}}">{{$l['rule_name'] or ''}}</span>
                                @endforeach
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>淘宝等级：</span>
                            </div>
                            <div class="search_nav" name="tao_level">
                                <span class="nav_list @if(null==$tao_level) border1 @endif">不限</span>
                                <span class="nav_list @if($tao_level=='1x') border1 @endif" value="1x">一星</span>
                                <span class="nav_list @if($tao_level=='2x') border1 @endif" value="2x">二星</span>
                                <span class="nav_list @if($tao_level=='3x') border1 @endif" value="3x">三星</span>
                                <span class="nav_list @if($tao_level=='4x') border1 @endif" value="4x">四星</span>
                                <span class="nav_list @if($tao_level=='5x') border1 @endif" value="5x">五星</span>
                                <span class="nav_list @if($tao_level=='1z') border1 @endif" value="1z">一钻</span>
                                <span class="nav_list @if($tao_level=='2z') border1 @endif" value="2z">二钻</span>
                                <span class="nav_list @if($tao_level=='3z') border1 @endif" value="3z">三钻</span>
                                <span class="nav_list @if($tao_level=='4z') border1 @endif" value="4z">四钻</span>
                                <span class="nav_list @if($tao_level=='5z') border1 @endif" value="5z">五钻</span>
                                <span class="nav_list @if($tao_level=='1h') border1 @endif" value="1h">一皇冠</span>
                                <span class="nav_list @if($tao_level=='2h') border1 @endif" value="2h">二皇冠</span>
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>淘宝会员：</span>
                            </div>
                            <div class="search_nav" name="tao_vip">
                                <span class="nav_list @if(null==$tao_vip) border1 @endif">不限</span>
                                <span class="nav_list @if($tao_vip=='1') border1 @endif" value="1">VIP1</span>
                                <span class="nav_list @if($tao_vip=='2') border1 @endif" value="2">VIP2</span>
                                <span class="nav_list @if($tao_vip=='3') border1 @endif" value="3">VIP3</span>
                                <span class="nav_list @if($tao_vip=='4') border1 @endif" value="4">VIP4</span>
                                <span class="nav_list @if($tao_vip=='5') border1 @endif" value="5">VIP5</span>
                                <span class="nav_list @if($tao_vip=='6') border1 @endif" value="6">VIP6</span>
                            </div>
                        </li>
                        <li class="border_top">
                            <div class="border_right">
                                <span>关注时间：</span>
                            </div>

                            <?php
                            $f_span = '';
                            $f_timeArr = array('1w','2w','1m','2m','3m','6m','-6m');
                            if($s=strpos($f_time,'|')){
                                list($start_time,$end_time) = explode('|',$f_time);
                                $valueTime =  $start_time.'|'.$end_time;
                                $htmlTime = $start_time.' 到 '.$end_time;
                                $f_span = in_array($f_time,$f_timeArr)?'':"<span class=\"nav_list last_date border1 \" value=\"".$valueTime."\">".$htmlTime."</span>";
                            }
                            ?>

                            <div class="search_nav" >
                                <div class="focus_time" name="f_time">
                                    <span class="nav_list @if(null==$f_time) border1 @endif">不限</span>
                                    <span class="nav_list @if($f_time=='1w') border1 @endif" value="1w" >1周内</span>
                                    <span class="nav_list @if($f_time=='2w') border1 @endif" value="2w" >2周内</span>
                                    <span class="nav_list @if($f_time=='1m') border1 @endif" value="1m" >1个月内</span>
                                    <span class="nav_list @if($f_time=='2m') border1 @endif" value="2m" >2个月内</span>
                                    <span class="nav_list @if($f_time=='3m') border1 @endif" value="3m" >3个月内</span>
                                    <span class="nav_list @if($f_time=='6m') border1 @endif" value="6m" >6个月内</span>
                                    <span class="nav_list @if($f_time=='-6m') border1 @endif" value="-6m" >6个月前</span>
                                    <?php
                                        echo $f_span;
                                    ?>
                                </div>
                                <a href="javascript:void(1);" class="choose_time">自定义..</a>
                            </div>
                        </li>
                    </ul>
                    <div class="btn_grounp">
                        <input type="submit" class="btn btn-primary" value="筛选">
                    </div>
                </form>

            @endif



        </div>
        <div class="fans">
            <div class="fans_total">
                搜索结果(共 {{$total}} 人)
            </div>
            <div class="fans_list">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="fans_detail col-sm-2 text-left">
                            <input type="checkbox" id="all_fans" name="checkbox">粉丝</th>
                        <th class="col-sm-1 text-info">会员卡</th>
                        <th class="col-sm-1 text-info">积分</th>
                        <th class="col-sm-1 text-info">关注时间</th>
                        <th class="col-sm-1 text-info">最后对话</th>
                        <th class="col-sm-1 text-info">最后购买</th>
                        <th class="col-sm-1 text-info">购买</th>
                        <th class="col-sm-1 text-info">均价</th>
                        <th class="col-sm-2 text-right">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fans as $f)
                        <?php
                        $createTime = date('Y-m-d H:i:s',$f['created_at']);
                        $sayTime = date('Y-m-d H:i:s',$f['last_say_at']);
                        $lastBuy = empty($f['tradeCount'])?'无':'有';
                        // dd($f);
                        ?>
                        <tr>
                            <td class="avatar">
                                <div>
                                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/8f9c442de8666f82abaf7dd71574e997.png" alt="">
                                    <input type="checkbox" class="fans" name="checkbox" value="{{$f['id']}}" >
                                </div>
                                <p>{{$f['fans_name']}}</p>
                            </td>
                            <td>Mark</td>
                            <td>{{$f['integral']}}</td>
                            <td>{{$createTime}}</td>
                            <td>{{$sayTime}}</td>
                            <td>{{$lastBuy}}</td>
                            <td>{{$f['tradeCount']}}</td>
                            <td>{{$f['avg_price']}}</td>
                            <td class="action">
                                <a href="javascript:;" class="add_biao" onclick="waiAddHtml('{{$f['id']}}')">加标签</a>
                                <a href="#">查看对话记录</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="page">
                    <ul class="nav nav-pills" role="tablist">
                        <li role="presentation" class="dropdown">
                            <a id="drop4" href="#" class="dropdown-toggle text-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                设等级
                                <span class="caret"></span>
                            </a>
                            <ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
                                <li role="presentation" class="setting_level">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(1);">给选中的人设等级</a>
                                </li>
                                
                            </ul>
                            <form id="form_level">
                                <div class="level">
                                    <div class="triangle"></div>
                                    <div class="level_header">给选中的人设等级</div>
                                    <div class="level_content">
                                        <select name ='fans_level' class="form-control level_select">

                                            <option >1</option>
                                            <option >2</option>
                                            <option >3</option>
                                            <option >4</option>
                                            <option >5</option>

                                        </select>
                                        <span class="arrow">
                                            <span class="caret"></span>
                                        </span>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                        <button class="btn btn-primary" id="ls_btn" type="button" onclick="refer('level')">确定</button>
                                        <a class="btn btn-default" id="lc_btn">取消</a>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <li role="presentation" class="dropdown">
                            <a id="drop5" href="#" class="dropdown-toggle text-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                加标签
                                <span class="caret"></span>
                            </a>
                            <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="drop5">
                                <li role="presentation" class="setting_biao">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(1);">给选中的人加标签</a>
                                </li>
                                
                            </ul>
                            <form id ="form_tag">
                                <div class="biao">
                                    <div class="triangle"></div>
                                    <div class="biao_header">给选中的人加标签</div>
                                    <div class="biao_content">
                                        <select class="form-control" name="tag_id" >
                                            @foreach( $label as $l)
                                                <option value="{{$l['id']}}">{{$l['rule_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="biao_footer">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                        <button class="btn btn-primary" id="bs_btn" type="button" onclick="return refer('tag')">确定</button>
                                        <a class="btn btn-default" id="bc_btn">取消</a>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <li role="presentation" class="dropdown">
                            <a id="drop6" href="#" class="dropdown-toggle text-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                给积分
                                <span class="caret"></span>
                            </a>
                            <ul id="menu3" class="dropdown-menu" role="menu" aria-labelledby="drop6">
                                <li role="presentation" class="setting_credit">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(1);">给选中的人发积分</a>
                                </li>
                                <!--
                                <li role="presentation" class="setting_credit">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(1);">给筛选出来的433242人发积分</a>
                                </li>
                                -->
                                <li role="presentation" class="clear_credit">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(1);">对选中的人清积分</a>
                                </li>
                                <!--
                                <li role="presentation" class="clear_credit">
                                    <a role="menuitem" tabindex="-1" href="javascript:void(1);">对筛选出来的3434人清积分</a>
                                </li>
                                -->
                            </ul>
                            <form id="form_empty">
                                <div class="cl_credit">
                                    <div class="triangle"></div>
                                    <div class="credit_header">
                                        <span>对选中的人清积分</span>
                                        <input type="hidden" name="integral" value="0" >
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                        <button class="btn btn-primary" id="cs_btn" type="button" onclick="return refer('empty')">确定</button>
                                        <a class="btn btn-default" id="cc_btn">取消</a>
                                    </div>
                                </div>
                            </form>
                            <form id="form_integral">
                                <div class="se_credit">
                                    <div class="triangle"></div>
                                    <div class="se_credit_header">给选中的人加标签</div>
                                    <div class="se_credit_content">
                                        <div class="form-group">
                                            <div class="score">分数</div>
                                            <input type="number" name="integral" class="form-control" id="exampleInputEmail1" placeholder="积分数 ( 可以为负数 )">
                                        </div>
                                        <div class="notice">
                                            <input type="checkbox">发送通知 ( 仅48小时互动过的粉丝，才能收到通知 )
                                        </div>
                                    </div>
                                    <div class="se_credit_footer">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                        <input id="action_url" type="hidden" value="{{URL('/merchants/member/fans/edit')}}">
                                        <button class="btn btn-primary" type="button" id="ss_btn" onclick="return refer('integral');" >确定</button>
                                        <a class="btn btn-default" id="sc_btn"  >取消</a>
                                    </div>
                                </div>
                            </form>
                        </li>
                    </ul>
                    <span class="page_detail">
                        <?php
                            echo $pageList;
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- 底部logo 开始 -->
    <div id="app-footer" class="footer">
        <a href="javascript:void(0);" class="logo" target="_blank">HUISOU</a>
    </div>
    <!-- 底部logo 结束 -->
@endsection
@section('other')
    <!-- 外部标签 -->
    <form id="form_wai" >
        <div class="wai_biao">
            <div class="triangle"></div>
            <div class="wai_biao_content">
                <select name="tag_id"  class="form-control">
                    @foreach( $label as $l)
                        <option value="{{$l['id']}}">{{$l['rule_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="wai_biao_footer">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <button class="btn btn-primary" type="button" id="wbs_btn" onclick="refer('wai')">确定</button>
                <a class="btn btn-default" id="wbc_btn">取消</a>
            </div>
        </div>
    </form>
    <!-- 自定义积分区间 -->
    <div class="range_credit">
        <div class="triangle"></div>
        <div class="range_credit_content">
            <input type="number" name="min_credit" class="form-control" placeholder="">
            <span>-</span>
            <input type="number" name="max_credit" class="form-control" placeholder="">
        </div>
        <div class="range_credit_footer">
            <button class="btn btn-primary" id="rcs_btn">确定</button>
            <button class="btn btn-default" id="rcc_btn">取消</button>
        </div>
    </div>
    <!-- 自定义时间区间 -->
    <div class="range_price">
        <div class="triangle"></div>
        <div class="range_price_content">
            <input type="text" class="form-control" placeholder="" name="start_date" id="start_date">
            <span>-</span>
            <input type="text" class="form-control" placeholder="" name="end_date" id="end_date">
        </div>
        <div class="range_price_footer">
            <button class="btn btn-primary" id="rps_btn">确定</button>
            <button class="btn btn-default" id="rpc_btn">取消</button>
        </div>
    </div>
    <!-- 中间 结束 -->
    <!--弹层-->
    <div class="tip"></div>
@endsection

@section('page_js')
    @parent
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 图表插件 -->
    <script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>
    <!-- laydata -->
    <script src="{{config('app.source_url')}}static/js/layer/laydate.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>s
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member_4907semr.js"></script>
    <script>
        function refer(v){
            url = $('#action_url').val();
            if(v=='wai'){
                // str = "<input name='id[]' type='hidden' value='" + ids + "' >";
            }else {
                $(".avatar input[type='checkbox']:checked").each(function (i) {
                    if (0 == i) {
                        str = "<input name='id[]' type='hidden' value='" + $(this).val() + "' >";
                    } else {
                        str += (" " + "<input name='id[]' type='hidden' value='" + $(this).val() + "' >");
                    }
                });
                // $('#form_'+v).append(str);
                $('#form_'+ v +' .triangle').html(str);
            }

            $.post(url, $('#form_'+v).serialize(), function( data ) {
               
                if ( data.status == 1 ) {
                    layer.msg( data.info,{icon:6});
                    /* 后台验证通过 */
                    if ( data.url ) {
                        /* 后台返回跳转地址则跳转页面 */
                        window.location.href = window.location.href;
                    } else {
                        /* 后台没有返回跳转地址 */
                        // to do somethings
                    }
                } else {
                    layer.msg( data.info);
                    /* 后台验证不通过 */
                    $('input[type="submit"]').prop('disabled', false);
                    // to do somethings
                }
            }, 'json');
            return false;
            // window.location.href = url+'/';
        }

        function waiAddHtml(id) {
            $('#form_wai .triangle').html("<input name='id[]' type='hidden' value='" + id + "' >");
            // ids = id;
        }
    </script>
@endsection