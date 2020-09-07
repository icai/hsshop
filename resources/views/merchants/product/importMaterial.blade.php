@extends('merchants.default._layouts')
@section('head_css')
<!-- 公共css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_kwvhib03.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_8bhfjewu.css" />
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <!--<li>
                <a href="{{ URL('/merchants/product/importGoods') }}">外部商品导入</a>
            </li>-->
            <li class="hover">
                <a href="{{ URL('/merchants/product/importMaterial') }}">导入商品素材</a>
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
    <div class="js-list-filter-region clearfix ui-box">
        <div class="widget-list-filter">
            <div>
                <!-- <a href="javascript:void(0);" class="zent-btn zent-btn-success js-add-template pull-left action">导入CSV或EXCEL文件</a> -->
                <a href="javascript:void(0);" class="zent-btn zent-btn-success js-add-template pull-left import_afanti">导入阿凡提XLS</a>
                <a href="javascript:void(0);" class="zent-btn zent-btn-success js-add-template pull-left import_xcx" style="margin-left:10px">导入小程序XLS</a>
                <a href="javascript:void(0);" class="zent-btn zent-btn-success js-add-template pull-left import_aiCard" style="margin-left:10px">导入AI智能获客商品XLS</a>
                <a href="javascript:void(0);" class="zent-btn zent-btn-success js-add-template pull-left import_taobao" style="margin-left:10px">导入淘宝ZIP</a>
                <a href="javascript:void(0);" class="zent-btn zent-btn-success js-add-template pull-left import_ali" style="margin-left:10px">导入阿里巴巴ZIP</a>
                <div class="common-helps-entry pull-left">
                    <a href="/home/index/detail/33" target="_blank">快速导入淘宝商品信息教程</a>
                </div>
                <div class="js-list-search form--search">
                    <form >
                        <input class="txt" name="title" value="{{isset($_GET['title'])?$_GET['title']:''}}" type="search" placeholder="搜索"/>
                        @foreach($_GET as $key => $get)
                          @if($key != 'title' )
                            <input type="hidden" name="{{$key}}" value="{{$get}}"/>
                          @endif
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
        <form role="form" name="shop_form">
              <table class="table table-striped">
                  <thead>
                      <tr>
                          <th class="text-left col-xs-3" colspan="3">
                              <input type="checkbox" id="all_material" name="">
                              <span>已导入商品素材</span>
                          </th>
                          <th class="col-xs-2">
                              导入时间
                          </th>
                          <th class="text-right col-xs-3">操作</th>
                      </tr>
                  </thead>
                  <tbody>
                       @if (!empty($list))
                         <!--  商品 列表 开始 -->
                         @foreach ($list as $v)
                      <tr>
                          <td>
                              <input type="checkbox" class="shop" name="ids[]" value="{{$v['id']}}">
                          </td>
                          <td>
                              <div class="shop_avatar">
                                  <img src="{{ imgUrl($v['img']) }}" style="width: 8rem;">
                              </div>
                          </td>
                          <td class="text-left shop_t">
                              <div class="shop_title">
                                  <p>{{$v['title']}}</p>
                                  <div>
                                      <span>￥{{$v['price']}}</span>
                                  </div>
                              </div>
                          </td>
                          <td>{{$v['created_at']}}</td>
                          <td class="text-right">
                          	  <div class="">
                          		  <a class="btn btn-default" href="{{URL('/merchants/product/editproduct/'.$v['id'])}}">编辑并上架</a>
                          	  </div>
                              <div style="padding-right: 10px;">
                              	  <a href="javascript:void(0)" class="out_delete" data-id="{{$v['id']}}">删除</a>
                              	  <span>-</span>
                              	  <a href="javascript:void(0)" class="product_detail_url" data-url="{{$v['detail_url']}}">链接</a>
                              </div>
                          </td>
                      </tr>
                       @endforeach
                      <!--  商品 列表 结束 -->
                      @else
                      <tr><td colspan="9">还没有相关数据</td></tr>
                      @endif
                  </tbody>
              </table>
              {{ csrf_field() }}
              @if (!empty($list))
              <div class="page">
                  <a herf="javascript:void(0);" class="btn btn-default delete matop10" style="float:left;">批量删除</a>
                  <div class="ui-popover ui-popover--confirm right-center" id="delete_prover">
                      <div class="ui-popover-inner clearfix ">
                          <div class="inner__header clearfix">
                              <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确定要删除吗？</div>
                              <div class="pull-right">
                                  <a href="javascript:void(0);" class="zent-btn zent-btn-primary zent-btn-small delete_sure">确定</a>
                                  <a href="javascript:void(0);" class="zent-btn zent-btn-small delete_cancel">取消</a></div>
                          </div>
                      </div>
                      <div class="arrow"></div>
                  </div>
                  <a herf="javascript:void(0);" class="btn btn-default on_sell matop10" style="float:left;margin-left:5px">批量上架</a>
                  <div class="ui-popover ui-popover--confirm right-center" id="on_sell_prover" style="margin-left:90px">
                      <input type="hidden" name="status" value="1">
                      <div class="ui-popover-inner clearfix ">
                          <div class="inner__header clearfix">
                              <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确定要上架吗？</div>
                              <div class="pull-right">
                                  <a href="javascript:void(0);" class="zent-btn zent-btn-primary zent-btn-small on_sell_sure">确定</a>
                                  <a href="javascript:void(0);" class="zent-btn zent-btn-small on_sell_cancel">取消</a></div>
                          </div>
                      </div>
                      <div class="arrow"></div>
                  </div>
                  <span class="page_detail">
                      <div class="js-has-company">
                          <div class="pagenavi js-pagenavi">
                             {{ $pageLinks }}
                          </div>
                      </div>
                  </span>
              </div>
              @endif
        </form>
    </div>
    <!--<div class="no-result">还没有相关数据</div>
    <div class="page">
        <span class="page_detail">共 1 条，每页 50 条</span>
    </div>-->
@endsection
@section('other')
 <!-- 导入商品素材弹窗 -->
    <div class="modal export-modal" id="myModal">
        <div class="modal-dialog" id="modal-dialog">
            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">文件上传</h4>
                    </div>
                    <div class="modal-body">
                        <div class="preview-container js-preview-container">
                            <div>
                                <div class="control-group name_container">
                                    <label class="control-label">文件名：</label>
                                    <div class="controls">
                                        <div class="control-action file_name"></div>
                                    </div>
                                </div>
                                <div class="control-group size_container">
                                    <label class="control-label">文件大小：</label>
                                    <div class="controls">
                                        <div class="control-action file_size"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                {{--<div class="js-help help-section">
                                    <div>
                                        <span>导入到分类：</span>
                                        <select name="cid" class="form-control wauto iblock input-sm">
                                            <option value="">请选择</option>
                                            @foreach($categories as $v)
                                                <option value="{{$v['id']}}">{{$v['category_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>--}}
                                <div class="file-select-button">
                                    <a href="javascript:void(0);" data-toggle-text="重新选择..." class="choose control-action">选择文件...</a>
                                    <input id="upload_flie" class="js-fileupload-input fileupload" type="file" name="upload">
                                </div>
                                <div class="help-block">最大支持 1 MB CSV和EXCEL的文件。</div>
                                <div class="js-help help-section">
                                    <div>
                                        <p>请使用最新版的淘宝助手导出CSV文件。单次导入商品最大数量为40个。(或者使用EXCEL文件)
                                        <!--<a href="javascipt:void(0);" class="new-window" target="_blank">如何导入商品？</a>-->
                                        <a href="{{ config('app.url') }}hsshop/other/template/product_import.xls">下载模板</a>
                                        </p>
                                        {{--<p>导入后的商品将出现在“外部商品素材”。</p>--}}
                                        <p>导入商品后需要一段时间同步商品信息，之后才会出现，请耐心等待。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group js-notice">
                            <div class="alert-pane">请选择一个文件。</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--<a href="javascript:void(0)" class="btn btn-primary submit">提交</a>-->
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-primary submit" value="提交"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal export-modal" id="myModal-taobao">
        <div class="modal-dialog" id="modal-dialog-taobao">
            <form class="form-horizontal" action="{{URL('/merchants/product/importTaobao')}}" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" id="close_taobao" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">文件上传</h4>
                    </div>
                    <div class="modal-body">
                        <div class="preview-container js-preview-container">
                            <div>
                                <div class="control-group name_container">
                                    <label class="control-label">文件名：</label>
                                    <div class="controls">
                                        <div class="control-action file_name"></div>
                                    </div>
                                </div>
                                <div class="control-group size_container">
                                    <label class="control-label">文件大小：</label>
                                    <div class="controls">
                                        <div class="control-action file_size"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                {{--<div class="js-help help-section">
                                    <div>
                                        <span>导入到分类：</span>
                                        <select name="cid" class="form-control wauto iblock input-sm mt10">
                                            <option value="">请选择</option>
                                            @foreach($categories as $v)
                                                <option value="{{$v['id']}}">{{$v['category_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>--}}

                                <div class="js-help help-section">
                                    <div>
                                        <span>导入到分组：</span>
                                        <select name="gid" class="form-control wauto iblock input-sm mt10">
                                            <option value="">请选择</option>
                                            @foreach($groups[0]['data'] as $v)
                                                <option value="{{$v['id']}}">{{$v['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="file-select-button">
                                    <a href="javascript:void(0);" data-toggle-text="重新选择..." class="choose control-action">选择文件...</a>
                                    <input id="upload_taobao" class="js-fileupload-input fileupload" type="file" name="upload_taobao">
                                </div>
                                <div class="help-block">最大支持 50 MB 的ZIP文件。</div>
                                <div class="js-help help-section">
                                    <div>
                                        <p>压缩包内由多个tbi图片和1个csv文件组成不得包含其他文件</p>
                                        <p>请使用最新版的淘宝助手导出CSV文件。单次导入商品最大数量为20个。</p>
                                        {{--<p>导入后的商品将出现在“外部商品素材”。</p>--}}
                                        <p>导入商品后需要一段时间同步商品信息，之后才会出现，请耐心等待。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group js-notice">
                            <div class="alert-pane">请选择一个文件。</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--<a href="javascript:void(0)" class="btn btn-primary submit">提交</a>-->
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-primary submit" value="提交"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal export-modal" id="myModal-ali">
        <div class="modal-dialog" id="modal-dialog-ali">
            <form class="form-horizontal" action="{{URL('/merchants/product/importAli')}}" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" id="close_ali" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">文件上传</h4>
                    </div>
                    <div class="modal-body">
                        <div class="preview-container js-preview-container">
                            <div>
                                <div class="control-group name_container">
                                    <label class="control-label">文件名：</label>
                                    <div class="controls">
                                        <div class="control-action file_name"></div>
                                    </div>
                                </div>
                                <div class="control-group size_container">
                                    <label class="control-label">文件大小：</label>
                                    <div class="controls">
                                        <div class="control-action file_size"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                {{--<div class="js-help help-section">
                                    <div>
                                        <span>导入到分类：</span>
                                        <select name="cid" class="form-control wauto iblock input-sm mt10">
                                            <option value="">请选择</option>
                                            @foreach($categories as $v)
                                                <option value="{{$v['id']}}">{{$v['category_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="js-help help-section">
                                    <div>
                                        <span>导入到分组：</span>
                                        <select name="gid" class="form-control wauto iblock input-sm mt10">
                                            <option value="">请选择</option>
                                            @foreach($groups[0]['data'] as $v)
                                                <option value="{{$v['id']}}">{{$v['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="file-select-button">
                                    <a href="javascript:void(0);" data-toggle-text="重新选择..." class="choose control-action">选择文件...</a>
                                    <input id="upload_ali" class="js-fileupload-input fileupload" type="file" name="upload_ali">
                                </div>
                                <div class="help-block">最大支持 50 MB 的ZIP文件。</div>
                                <div class="js-help help-section">
                                    <div>
                                        <p>压缩包内由多个ALI文件和1个csv文件组成不得包含其他文件</p>
                                        <p>请使用最新版的阿里巴巴商机助理导出CSV文件。单次导入商品最大数量为20个。</p>
                                        {{--<p>导入后的商品将出现在“外部商品素材”。</p>--}}
                                        <p>导入商品后需要一段时间同步商品信息，之后才会出现，请耐心等待。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group js-notice">
                            <div class="alert-pane">请选择一个文件。</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--<a href="javascript:void(0)" class="btn btn-primary submit">提交</a>-->
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-primary submit" value="提交"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal export-modal" id="myModal-afanti">
        <div class="modal-dialog" id="modal-dialog-afanti">
            <form class="form-horizontal" action="{{URL('/merchants/product/importAfanti')}}" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" id="close_afanti" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">文件上传</h4>
                    </div>
                    <div class="modal-body">
                        <div class="preview-container js-preview-container">
                            <div>
                                <div class="control-group name_container">
                                    <label class="control-label">文件名：</label>
                                    <div class="controls">
                                        <div class="control-action file_name"></div>
                                    </div>
                                </div>
                                <div class="control-group size_container">
                                    <label class="control-label">文件大小：</label>
                                    <div class="controls">
                                        <div class="control-action file_size"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                {{--<div class="js-help help-section">
                                    <div>
                                        <span>导入到分类：</span>
                                        <select name="cid" class="form-control wauto iblock input-sm mt10">
                                            <option value="">请选择</option>
                                            @foreach($categories as $v)
                                                <option value="{{$v['id']}}">{{$v['category_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>--}}

                                <div class="file-select-button">
                                    <a href="javascript:void(0);" data-toggle-text="重新选择..." class="choose control-action">选择文件...</a>
                                    <input id="upload_afanti" class="js-fileupload-input fileupload" type="file" name="upload_afanti">
                                </div>
                                {{--<div class="help-block">最大支持 2 MB 的XLS文件。</div>--}}
                                <div class="js-help help-section">
                                    <div>
                                        {{--<p>单次导入商品最大数量为20个。</p>
                                        <p>导入后的商品将出现在“外部商品素材”。</p>--}}
                                        <p>导入商品后需要一段时间同步商品信息，之后才会出现，请耐心等待。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group js-notice">
                            <div class="alert-pane">请选择一个文件。</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--<a href="javascript:void(0)" class="btn btn-primary submit">提交</a>-->
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-primary submit" value="提交"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal export-modal" id="myModal-xcx">
        <div class="modal-dialog" id="modal-dialog-xcx">
            <form class="form-horizontal" action="{{URL('/merchants/product/importXCX')}}" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" id="close_xcx" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">文件上传</h4>
                    </div>
                    <div class="modal-body">
                        <div class="preview-container js-preview-container">
                            <div>
                                <div class="control-group name_container">
                                    <label class="control-label">文件名：</label>
                                    <div class="controls">
                                        <div class="control-action file_name"></div>
                                    </div>
                                </div>
                                <div class="control-group size_container">
                                    <label class="control-label">文件大小：</label>
                                    <div class="controls">
                                        <div class="control-action file_size"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <div class="file-select-button">
                                    <a href="javascript:void(0);" data-toggle-text="重新选择..." class="choose control-action">选择文件...</a>
                                    <input id="upload_xcx" class="js-fileupload-input fileupload" type="file" name="upload_xcx">
                                </div>
                                <div class="js-help help-section">
                                    <div>
                                        <p>导入商品后需要一段时间同步商品信息，之后才会出现，请耐心等待。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group js-notice">
                            <div class="alert-pane">请选择一个文件。</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-primary submit" value="提交"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
     {{--导入会搜云新零售系统的商品--}}
     <div class="modal export-modal" id="myModal-aiCard">
         <div class="modal-dialog" id="modal-dialog-aiCard">
             <form class="form-horizontal" action="{{URL('/merchants/product/import_card')}}" method="post" enctype="multipart/form-data">
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" id="close_aiCard" data-dismiss="modal">
                             <span aria-hidden="true">&times;</span>
                             <span class="sr-only">Close</span>
                         </button>
                         <h4 class="modal-title" id="myModalLabel">文件上传</h4>
                     </div>
                     <div class="modal-body">
                         <div class="preview-container js-preview-container">
                             <div>
                                 <div class="control-group name_container">
                                     <label class="control-label">文件名：</label>
                                     <div class="controls">
                                         <div class="control-action file_name"></div>
                                     </div>
                                 </div>
                                 <div class="control-group size_container">
                                     <label class="control-label">文件大小：</label>
                                     <div class="controls">
                                         <div class="control-action file_size"></div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="control-group">
                             <div class="controls">
                                 <div class="file-select-button">
                                     <a href="javascript:void(0);" data-toggle-text="重新选择..." class="choose control-action">选择文件...</a>
                                     <input id="upload_aiCard" class="js-fileupload-input fileupload" type="file" name="upload_aiCard">
                                 </div>
                                 <div class="js-help help-section">
                                     <div>
                                         <p>导入商品后需要一段时间同步商品信息，之后才会出现，请耐心等待。</p>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="control-group js-notice">
                             <div class="alert-pane">请选择一个文件。</div>
                         </div>
                     </div>
                     <div class="modal-footer">
                         {{ csrf_field() }}
                         <input type="submit" class="btn btn-primary submit" value="提交"/>
                     </div>
                 </div>
             </form>
         </div>
     </div>

    <!-- tip -->
    <div class="tip">请选择商品</div>
    <!--backdrop-->
    <div class="modal-backdrop"></div>
    <!-- 删除弹窗 -->
    <div class="popover del_popover left" role="tooltip">
      <div class="arrow"></div>
      <div class="popover-content">
          <span>你确定要删除吗？</span>
          <button class="btn btn-primary sure_btn">确定</button>
          <button class="btn btn-default cancel_btn">取消</button>
      </div>
    </div>

@endsection
@section('page_js')
<!-- layer弹窗 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_8bhfjewu.js"></script>
<script>
/**
    * 获取所有url上的参数
    * 修改 并返回 对应 url的参数值
    */
   function getallparam(obj){
       var sPageURL = window.location.search.substring(1);
       var sURLVariables = sPageURL.split('&');
       var flag = 0;
       for(var i = 0; i< sURLVariables.length; i++){
           var sParameterName = sURLVariables[i].split('=');
           if (undefined != obj[sParameterName[0]]){
               sParameterName[1] = obj[sParameterName[0]];
               flag++;
           }
           sURLVariables[i] = sParameterName.join('=');
       }
       var newquery = sURLVariables.join('&');
       for(var key in obj){
           if(-1 === newquery.indexOf(key)){
               newquery += '&'+key+'='+obj[key];
           }
       }
       return newquery;
   }

   //点击排序
   var ORDER_BY = ['created_at'];
   var ORDER = ['asc','desc'];
   function sort_desc(index,sort){
       var params = getallparam({order:ORDER[sort],orderby:ORDER_BY[index]});
       window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
   }

   $(function(){
       $('#tbproduct_created_at').change(function(){
           var group_id = $(this).children('option:selected').val();//这就是selected的值
           window.location.href = 'http://'+ location.host + location.pathname + '?'+getallparam({group_id:group_id});
       })
   })
</script>
@endsection
