@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 当前页面css -->
    <!-- href="{{ config('app.source_url') }}mctsource/css/capital_cvillmk5.css"-->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('mctsource/css/member.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_pe8avuyj.css">

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
            <li class="hover">
                <a href="#&status=1">我的粉丝</a>
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
        <?php
        $gander = isset($input['gander'])?$input['gander']:null;
        $regions_id = isset($input['regions_id'])?$input['regions_id']:null;
        $fs = isset($input['fs'])?$input['fs']:null;
        ?>
        <form action="/merchants/member/fans" role="form">
            <input name="gander" type="hidden" value="{{$gander}}">
            <input name="fs" type="hidden" value="{{$fs}}">
            <input name="regions_id" type="hidden" value="{{$regions_id}}">
            <ul>
                <li>
                    <div class="border_right">
                        <span>关键词：</span>
                    </div>
                    <div>
                        <input type="text" name="fans_name" value="{{isset($input['fans_name'])?$input['fans_name']:''}}" class="form-control" placeholder="微信昵称"  >
                    </div>
                </li>
                <li class="border_top">
                    <div class="border_right">
                        <span>账户：</span>
                    </div>
                    <div class="search_nav one_choose" name="fs">
                        <span class="nav_list @if(null==$fs) border1 @endif ">不限</span>
                        <span class="nav_list @if(1==$fs) border1 @endif" value="1">有旺旺</span>
                        <span class="nav_list @if('0'===$fs) border1 @endif" value="0" >无旺旺</span>
                        <span class="nav_list @if(2==$fs) border1 @endif" value="2">已跑路</span>
                        <span class="nav_list @if(3==$fs) border1 @endif" value="3" >已关注</span>
                    </div>
                </li>
                <li class="border_top">
                    <div class="border_right">
                        <span>性别：</span>
                    </div>
                    <div class="search_nav one_choose" name="gander">
                        <span class="nav_list @if(empty($gander)) border1 @endif" value="0">不限</span>
                        <span class="nav_list @if('1'==$gander) border1 @endif" value="1">男</span>
                        <span class="nav_list @if('2'==$gander) border1 @endif" value="2">女</span>
                        <span class="nav_list @if('3'==$gander) border1 @endif" value="3">未知</span>
                    </div>
                </li>
                <li class="border_top">
                    <div class="border_right">
                        <span>地域：</span>
                    </div>
                    <div class="search_nav multy_choose" name="regions_id" id="regions_id">
                        <span class="nav_list @if(empty($regions_id)) border1 @endif no_limit" data-value="0">不限</span>
                        <span class="regions_id area_show ">
                            <?php
                               // $regionsArr = explode(',',$regions_id);
                                $border1 = '';
                                if($regions_id){
                                    $border1 = 'border1';
                                }
                                foreach ($default as $k=> $defaultSpan){
                                    echo '<span class="nav_list '.$border1.'" data-value="'.$k.'">'.$defaultSpan.'</span>';
                                }
                                ?>

                        </span>
                        @foreach($regions as $r)
                            <?php
                                if($r->id==84){
                                    continue;
                                }
                            ?>
                        @endforeach
                        <a class="more" href="javascript:void(0);">更多..</a>
                    </div>
                </li>
            </ul>
            <div class="btn_grounp">
                <input type="submit" class="btn btn-primary" value="筛选">
            </div>
        </form>
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
                        <p>{{$f['fans_name'] or ''}}</p>
                    </td>
                    <td>Mark</td>
                    <td>{{$f['integral'] or 0}}</td>
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
                            设等级<span class="caret"></span>
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
                            加标签 <span class="caret"></span>
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
                        <a  id="drop6" href="#" class="dropdown-toggle text-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            给积分<span class="caret"></span>
                        </a>
                        <ul id="menu3" class="dropdown-menu" role="menu" aria-labelledby="drop6">
                            <li role="presentation" class="setting_credit">
                                <a role="menuitem" tabindex="-1" href="javascript:void(1);" >给选中的人发积分</a>
                            </li>
                            
                            <li role="presentation" class="clear_credit">
                                <a role="menuitem" tabindex="-1" href="javascript:void(1);">对选中的人清积分</a>
                            </li>

                           
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
<!-- 中间 结束 -->
<!--弹层-->
<div class="tip"></div>
<!-- 地区显示框 -->
<div class="ui-popover top-center" id="area_popover">
    <div class="ui-popover-inner clearfix popover-select-city">
        <div class="inner__header" style="width: 600px">
            <ul class="items-ul">
                <li class="search_nav regions_id area_show">
                    <span class="nav_list" data-value="-2" data-tag="珠三角">江浙沪</span>
                </li>
                <li class="search_nav regions_id area_show">
                    <span class="nav_list" data-value="-3" data-tag="港澳台">珠三角</span>
                </li>
                <li class="search_nav regions_id area_show">
                    <span class="nav_list" data-value="-4" data-tag="江浙沪">港澳台</span>
                </li>
                <li class="search_nav regions_id area_show">
                    <span class="nav_list" data-value="-5" data-tag="京津">京津</span>
                </li>
            </ul>
            <ul class="items-ul">
                @foreach($regions as $r)
                    <?php
                        if($r->id==84){
                            continue;
                        }
                    ?>
                    <li class="search_nav regions_id area_show">
                        <span class="nav_list @if($regions_id==$r->id) border1 @endif" data-value="{{$r->id}}" data-tag="{{$r->title}}">{{$r->title}}</span>
                    </li>
                @endforeach
            </ul>
            <div class="select-all">
                <label>
                    <input type="checkbox" class="js-select-all">全选
                </label>
            </div>
        </div>
        <div class="inner__content" style="width: 600px">
            <a href="javascript:;" class="zent-btn zent-btn-primary zent-btn-small js-save">确定</a>
            <a href="javascript:;" class="zent-btn zent-btn-small js-cancel">取消</a>
        </div>
    </div>
    <div class="arrow"></div>
</div>
@endsection

@section('page_js')
    @parent
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 图表插件 -->
    <script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
    <!-- 当前页面js -->
    <script src="{{config('app.source_url')}}mctsource/js/member_7hp846oy.js"></script>
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
                $('#form_'+ v +' .triangle').html(str);
            }

            $.post(url, $('#form_'+v).serialize(), function( data ) {
                if ( data.status == 1 ) {
                    console.log(data);
                    layer.msg( data.info,{icon:6});
                    /* 后台验证通过 */
                    if ( data.url ) {
                    } else {
                    }
                } else {
                    layer.msg( data.info);
                    $('input[type="submit"]').prop('disabled', false);
                }
            }, 'json');
            return false;
        }

        function waiAddHtml(id) {
            $('#form_wai .triangle').html("<input name='id[]' type='hidden' value='" + id + "' >");
        }
    </script>
@endsection