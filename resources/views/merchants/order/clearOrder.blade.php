<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <!-- <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"> -->
    <link href="{{ config('app.source_url') }}static/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <!-- 搜索美化插件 -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css">
    <!-- 核心bootstrap-rewrite.css文件（覆盖bootstrap样式，每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-rewrite.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('static/css/base.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <!-- 上传插件样式 -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
    <!-- 当前页面样式 -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}mctsource/css/img_common.css">
    <style type="text/css"> 
        /* 解决图片分组列表为0时 外边距溢出问题 2018-10-22 */
        #layerContent_right:before{
            content:"";
            display:table;
        }
    </style>

</head>
<body style='background-color: #ffffff'>
    <!--图片弹框开始-->
    <div class="modal export-modal myModal-adv" id="myModal-adv" style="display: inline-block;" onselectstart="return false;" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" id="modal-dialog-adv">
            <form class="form-horizontal">
                <div class="modal-content content_first content_box">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body img_title_box">
                        <div class="category-list-region title_list">
                            <ul class="category-list">
                                
                            </ul>
                            <div class='add_group'>
                                <div class="add_group_list" data-id='1'>+添加分组</div>
                                <div class="add_group_box hide">
                                    <div class='add_group_title'>添加分组</div>
                                    <input class='add_group_input' placeholder='不超过6个字' type="text" maxlength='6'  style="font-size:14px">
                                    <div class='clearfix add_group_btn'>
                                        <div class="btn_left">确定</div>
                                        <div class="btn_right">取消</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="attachment-list-region img_list_region">
                            <div class="search-region" style="display:none">
                                <div class="ui-search-box">
                                    <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                                </div>
                            </div>
                            <div class="imgData" style="padding-top:20px">
                                <ul class="image-list">
                                    
                                </ul>
                                <div class="attachment-pagination">
                                    <div class= "picturePage"></div>
                                    <!-- 分页 -->
                                </div>
                                <a href="javascript:void(0);" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 200px; bottom: 16px;">上传图片</a>
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
                <div class="modal-content content_second upload_img">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <div class="cap_head clearfix">
                            <a class="co_38f js_prev" href="javascrript:void(0);"> 选择图片 > </a>
                            <span>上传图片</span>
                        </div>
                    </div>
                    <div class="modal-body" style='height: 593px;'>
                        <div class='upload_group'>
                            {{--<span>上传至分组</span>--}}
                            {{--<select name="" id="">--}}
                                {{--<option value="">111111</option>--}}
                            {{--</select>--}}
                        </div>
                        <div id="uploadLayerContent_botm">
                            <div id="wrapper">
                                <div id="container">
                                    <!--头部，相册选择和格式选择-->
                                    <div id="uploader">
                                        <div class="queueList">
                                            <div id="dndArea" class="placeholder">
                                                <label id="filePicker"></label>
                                            </div>
                                        </div>
                                        <div class="statusBar">
                                            <div class="progress">
                                                <span class="text">0%</span>
                                                <span class="percentage"></span>
                                            </div>
                                            <div class="info" style='color: #999999; padding-top: 15px'>图片格式仅支持 gif、jpg、png, 大小不超过3.0 MB</div>
                                            <div class="btns">
                                                <div id='filePicker2'></div>
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
                            <input type="hidden" id="select_num" value="{{$id}}">
                            <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                            <a class="ui-btn ui-btn-primary no">确认</a>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
    <!--图片弹框结束-->

    <!-- jQuery.js -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script> 
    <!-- layer -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <!-- webuploader上传插件引入 -->
    <script src="{{config('app.source_url')}}static/js/webuploader.js"></script> 
    <!-- 分页插件 -->
    <script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
    <!-- 公共js文件 -->
    <script src="{{config('app.source_url')}}mctsource/static/js/base.js"></script>
    <!-- 批量上传或选择图片 -->
    <script src="{{config('app.source_url')}}mctsource/js/img_common.js"></script>
</body>
</html>
