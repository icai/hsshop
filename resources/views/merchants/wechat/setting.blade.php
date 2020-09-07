@extends('merchants.default._layouts') @section('head_css')
<!-- 上传插件样式 -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
<!-- 微信公众号公共样式 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/wechat_1ynk47v5.css"> 
@endsection @section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="javascript:void(0);">公众号</a>
            </li>
            <li>
                <a href="javascript:void(0);">{{ $title }}</a>
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
@endsection @section('content')
<!-- 中间 开始 -->
<div class="content" style="display: -webkit-box;">
    <!--主体左侧列表开始-->
    @include('merchants.wechat.slidebar')
    <!--主体左侧列表结束-->
    <!--主体右侧内容开始-->
    <div class="right_container">
        <!-- <form class="form-horizontal" method="post" action="">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $detail['id'] or '' }}" />
            <input type="hidden" name="payment_id" value="{{ $detail['payment_id'] or '' }}" />
            名称：<input name="name" value="{{ $detail['name'] or '' }}" /><br />
            原始id：<input name="original_id" value="{{ $detail['original_id'] or '' }}" /><br />
            微信号：<input name="wechat_id" value="{{ $detail['wechat_id'] or '' }}" /><br />
            头像：<input type="file" name="img" value="{{ $detail['img'] or '' }}" /><br />
            AppID(应用ID)：<input name="app_id" value="{{ $detail['app_id'] or '' }}" /><br />
            AppSecret(应用密钥)：<input name="app_secret" value="{{ $detail['app_secret'] or '' }}" /><br />
            <input type="submit" value="保存" />
        </form> -->
        <form class="form-horizontal form" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $detail['id'] or '' }}" />
            <input type="hidden" name="payment_id" value="{{ $detail['payment_id'] or '' }}" />
            <div class="form-group">
                <label class="col-sm-4 control-label">名称：</label>
                <div class="col-sm-5">
                    <input name="name" class="form-control" value="{{ $detail['name'] or '' }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">原始id：</label>
                <div class="col-sm-5">
                    <input name="original_id" class="form-control" value="{{ $detail['original_id'] or '' }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">微信号：</label>
                <div class="col-sm-5">
                    <input name="wechat_id" class="form-control" value="{{ $detail['wechat_id'] or '' }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-4 control-label" accept="image/*">头像：</label>
                <div class="col-sm-5">
                   <input class="img" type="hidden" name="img" value="{{ $detail['img'] or '' }}" />
                    <div class="js_inline">
                        <img class="img_small no" src="{{ $detail['img'] or '' }}"/>
                        <a class="co_38f js_img" href="javascript:void(0);">添加图片</a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">AppID(应用ID)：</label>
                <div class="col-sm-5">
                   <input name="app_id" class="form-control" value="{{ $detail['app_id'] or '' }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-4 control-label">AppSecret(应用密钥)：</label>
                <div class="col-sm-5">
                   <input name="app_secret" class="form-control" value="{{ $detail['app_secret'] or '' }}" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4"></div>
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-default">保存</button>
                </div>
            </div>
        </form>
    </div>
    <input id="error_msg" type="hidden" value="{{ session('errorMsg') }}" />
    <!--主体右侧内容结束-->
</div>
<!-- 中间 结束 -->
<!--图片弹框开始-->
<div class="modal in export-modal myModal-adv" id="myModal-adv" onselectstart="return false;" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" id="modal-dialog-adv">
        <form class="form-horizontal">
            <div class="modal-content content_first">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                            <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
                        </li>
                    </ul>
                    <div class="search-region">
                        <div class="ui-search-box">
                            <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="category-list-region">
                        <ul class="category-list">
                          
                        </ul>

                    </div>
                    <div class="attachment-list-region">
                        <div class="imgData">
                            <ul class="image-list">
                               
                            </ul>
                            <div class="attachment-pagination">
                                <div class= "picturePage"></div><!-- 分页 -->
                            </div>
                            <a href="##" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;">上传图片</a>
                        </div>
                        <!--列表中的图片个数为0的时候显示这个模态框  no隐藏数据-->
                        <div id="layerContent_right" class="no">
                            <a class="js_addImg" href="#uploadImg">+</a>
                            <p>暂无数据，点击添加</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="text-center">
                        <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                        <a class="ui-btn ui-btn-primary no">确认</a>
                    </div>
                </div>
            </div>
            <div class="modal-content content_second">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <div class="cap_head clearfix">
                        <a class="co_38f js_prev" href="javascript:void(0);"><选择图片 </a>
                        <span>上传图片</span>
                    </div>

                </div>
                <div class="modal-body">
                    <div id="uploadLayerContent_botm">
                        <div id="wrapper">
                            <div id="container">
                                <!--头部，相册选择和格式选择-->
                                <div id="uploader">
                                    <div class="queueList">
                                        <div id="dndArea" class="placeholder">
                                            <label id="filePicker"></label>
                                            <!-- <p>或将照片拖到这里，单次最多可选300张</p> -->
                                        </div>
                                    </div>
                                    <div class="statusBar" style="display:none;">
                                        <div class="progress">
                                            <span class="text">0%</span>
                                            <span class="percentage"></span>
                                        </div>
                                        <div class="info"></div>
                                        <div class="btns">
                                            <div id="filePicker2"></div>
                                            <div class="uploadBtn">开始上传</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="text-center">
                        <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                        <a class="ui-btn ui-btn-primary no">确认</a>
                    </div>
                </div>
            </div>
            <div class="modal-content content_third">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                            <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
                        </li>
                    </ul>
                    <div class="search-region">
                        <div class="ui-search-box">
                            <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <ul id="iconStyleSelect">
                        <li id="style">风格:
                            <a href="##" class="selected">全部</a>
                            <a href="##">普通</a>
                            <a href="##">简约</a>
                        </li>
                        <li id="color">颜色:
                            <a href="##" class="selected">全部</a>
                            <a href="##">白色</a>
                            <a href="##">灰色</a>
                        </li>
                        <li id="type">类型:
                            <a href="##" class="selected">全部</a>
                            <a href="##">常规</a>
                            <a href="##">购物</a>
                            <a href="##">交通</a>
                            <a href="##">食物</a>
                            <a href="##">商务</a>
                            <a href="##">娱乐</a>
                            <a href="##">美妆</a>
                        </li>
                    </ul>
                    <div id="iconImgShow">
                        <ul id="iconImgSelect">
                            <li>
                                <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bb3503203766425965b7517336df979d.png?imageView2/2/w/160/h/160/q/75/format/png" />
                                <div class="attachment-selected no">
                                    <i class="icon-ok icon-white"></i>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div id="pageNum">
                        共<span>270</span>条，每页27条&nbsp;&nbsp;
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="selected-count-region hide">
                        已选择<span class="js-selected-count">2</span>张图片
                    </div>
                    <div class="text-center">
                        <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                        <a class="ui-btn ui-btn-primary no">确认</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!--图片弹框结束-->
@endsection @section('page_js') @parent
<!-- webuploader上传插件引入 -->
<script src="{{config('app.source_url')}}static/js/webuploader.js"></script>
<script src="{{config('app.source_url')}}mctsource/js/wechat_upload.js"></script>
<!-- 分页插件 -->
<script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
<!-- 微信模块公共样式 -->
<script src="{{ config('app.source_url') }}mctsource/js/wechat_base.js"></script>
<!-- 当前页面js -->
<script src="{{config('app.source_url')}}mctsource/js/wechat_1ynk47v5.js"></script>
@endsection
