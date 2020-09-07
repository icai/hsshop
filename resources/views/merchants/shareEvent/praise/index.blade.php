@extends('merchants.default._layouts')
@section('head_css')
    <!-- layer  -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/share_zan.css" />
@endsection
@section('slidebar')
    @include('merchants.marketing.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="{{ URL('/merchants/marketing') }}">营销中心</a>
                </li>
                <li>
                    <a href="javascript:void(0)">集赞</a>
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
        <!-- 导航模块 开始 -->
        <div class="nav_module clearfix pr">
            <div class="pull-left">
                <!-- 导航 开始 -->
                <ul class="tab_nav">
                    <li @if(!isset($_GET['type']))class="hover" @endif>
                        <a href="/merchants/share/praise/index">所有活动</a>
                    </li>
                    <li @if(isset($_GET['type']) && $_GET['type'] == 0)class="hover" @endif>
                        <a href="/merchants/share/praise/index?type=0">进行中</a>
                    </li>
                    <li @if(isset($_GET['type']) && $_GET['type'] == 1)class="hover" @endif>
                        <a href="/merchants/share/praise/index?type=1">已结束</a>
                    </li>
                </ul>  
            </div>
            <a class="nav_module_blank" href="##" data-toggle="modal" data-target="#setXiang">集赞设置</a>
        </div> 
        <!-- 导航模块 结束 -->
        <!-- search 开始 -->
        <div class="mb-15 clearfix pr">
            <div class="widget-list-filter">
                <div class="pull-left">
                    <a class="btn btn-success " href="{{ URL('/merchants/share/praise/save
') }}">新建集赞活动</a>
                </div>
                <div class="pull-right">
                    <!-- <span style="font-weight: bold;font-size: 15px;">是否开启红包?</span>
                    <span class="switch fr switchEnvelope" data-toggle="modal" data-target="#envelopeRule"></span>
                    <div class="btn btn-success openEnvelopeModal" data-toggle="modal" data-target="#envelopeModal">设置红包</div> -->
                    <div class="js-list-search ui-search-box" style="display: inline-block;">
                        <div class="btn btn-primary sure_btn" data-toggle="modal" data-target="#myModal">一键翻新</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- search 结束 -->
        <!-- 列表 开始 -->
        <div class="main_content">
            <ul class="main_content_title">
                <li>活动名称</li>
                <li>活动状态</li> 
                <li class="">操作</li>
            </ul>
            @foreach($list as $v)
            <?php
                $typeName = ($v['type'] == 1 || $v['end_time'] < time()) ? '已失效或已过期' : '进行中';
            ?>
            <ul class="data_content">
                <li class="blue ovhid">{{$v['title']}}</li>
                <li class="gray1">
                    {{$typeName}}
                </li>
                <li class="pr">                    
                    <a class="see_create" href="/merchants/share/praise/save?id={{$v['id']}}">编辑</a>
                    @if($v['type']==1)
                    -<a class="delete" href="javascript:void(0);" data-id="{{$v['id']}}">删除</a>
                    @endif
                    @if($v['type']==0)
                    -<a class="invalid" href="javascript:void(0);" data-id="{{$v['id']}}">使失效<span style="color:#008000">[?]</span></a>
                    @endif
                </li>
            </ul>
            @endforeach
            <!-- <ul class="data_content">暂无数据</ul> --> 
        </div> 
        <!-- 列表 结束 -->
        <!-- 分页 -->
        <div class="text-right">
            {{$pageHtml}}
        </div>
        <!--一键翻新-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">集赞一键翻新</h4>
                    </div>
                    <div class="modal-body">
                        一键翻新后，店铺所有集赞的老用户将会变成新用户，可以再次帮助分享者减价。
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn_refresh">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <!--红包规则说明-->
        <div class="modal fade" id="envelopeRule" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">红包规则说明</h4>
                    </div>
                    <div class="modal-body">
                        <p>开启红包后,您可以设置红包的金额</p>
                        <p>用户每天都可以领一个红包，红包当天有效，过期失效。</p>
                        <p>用户分享了集赞活动商品后，该红包自动抵扣领取的金额。</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary rule_sure">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <!--集赞设置弹框-->
        <div class="modal fade" id="setXiang" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">集赞设置</h4>
                    </div>
                    <div class="modal-body">
                        <div class="control-group clear">
                            <label class="control-label required required_la">卡片图片设置：</label>
                            <div class="controls">
                                <label class="flex_tip">
                                    <input type="radio" name="is_open_card" value="0" checked>关闭
                                </label>
                                <label class="flex_tip">
                                    <input type="radio" name="is_open_card" value="1">开启
                                </label>
                            </div>
                        </div>
                        <h4 style="margin-top: 15px;">分享设置:</h4>
                        <div class="control-group clear" style="margin-bottom: 20px;">
                            <label class="control-label required">分享标题设置：</label>
                            <div class="controls">
                                <input class="form-control z-title" type="text" name="share_title" maxlength="23">
                                <p class="up_tip" style="margin-left:105px">不超过23个汉字</p>
                            </div>
                        </div>

                        <div class="control-group clear">
                            <label class="control-label required">分享图片：</label>
                            <div class="controls">
                                <label class="input-append">
                                    <div class="">
                                        <div class="sel-goods share_img">
                                            <img class="img-goods" src="">
                                            <!-- <span class="remove-img">×</span> -->
                                        </div>
                                        <div class="image-wrap image-warp-active">
                                            <div class="js-upload-image add_active_img share_img_add"  data-imgadd="2">+添加</div>
                                        </div>
                                    </div>
                                    <p class="up_tip">建议宽高尺寸：420*336</p>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary xiang_sure">确定</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 设置红包弹框 -->
        <div class="modal fade" id="envelopeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">红包设置</h4>
                    </div>
                    <div class="modal-body">
                        <div class="set_envelope">
                           <div class="envelope_label required">新人红包：</div>
                           <div class="envelope_config">
                               <div >
                                   <label>
                                       <input type="radio" name="type" value="0" checked>固定
                                   </label>
                                   <label>
                                       <input type="radio" name="type" value="1">随机
                                   </label>
                               </div>
                               <div class="config_item">
                                   每人减
                                   <input class="form-control wd_80 moneyLimit" type="number" name="fixed">
                                   元
                               </div>
                               <div class="config_item none">
                                   每人减
                                   <input class="form-control wd_80 moneyLimit" type="number" name="minimum">
                                   至
                                   <input class="form-control wd_80 moneyLimit" type="number" name="maximum">
                                   元
                               </div>

                           </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary setEnvelope">保存</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
<script type="text/javascript">
    var _host = "{{ imgUrl() }}";
    var wid = {{session('wid')}};
    var reduceData = {!! json_encode($reduceData) !!};//红包 集赞设置数据
    console.log(reduceData)
</script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/extendPagination.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/share_zan.js"></script> 
@endsection