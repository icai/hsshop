@extends('merchants.default._layouts')
@section('head_css')
<!-- layer  -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" /> 
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_info.css" />
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
                <li>
                    <a href="javascript:void(0);">注册信息管理</a>
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
    <div class="widget-list-filter">
        <form class="form-horizontal ui-box list-filter-form" method="get" action="">
            <div class="clearfix">
                <div class="filter-groups">
                    <div class="control-group">
                        <label>手机号：</label>
                        <div class="controls">
                            <input type="text" name="mobile">
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="filter-groups">
                    <div class="control-group">
                        <label>姓名：</label>
                        <div class="controls">
                            <input type="text" name="name">
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="control-group timer">
                    <label class="control-label">提交时间：</label>
                    <div class="controls">
                        <input type="text" name="start_time" value="{{ request('start_time') }}" class="js-start-time hasDatepicker" id="startDate">
                        <span>至</span>
                        <input type="text" name="end_time" value="{{ request('end_time') }}" class="js-end-time hasDatepicker" id="endDate">
                        <span class="date-quick-pick" data-days="7">近7天</span>
                        <span class="date-quick-pick" data-days="30">近30天</span>
                    </div>
                </div>
            </div>
            <div class="control-group search">
                <div class="controls">
                    <input class="btn btn-primary js-filter" type="submit" value="筛选" />
                </div>
            </div>
        </form>
    </div>

    <table class="table table-striped">
          <thead>
              <tr>
                  <th class="text-left col-xs-1" colspan="3">
                      提交时间
                  </th>
                  <th class="col-xs-1">姓名</th>
                  <th class="col-xs-2">
                      手机号：
                   </th>
                  <th class="col-xs-2">
                    公司名称
                  </th>
                  <th class="col-xs-2">
                     职务
                  </th>
                  <th class="text-right col-xs-3">操作</th>
              </tr>
          </thead>
          <tbody>
                <!--  商品 列表 开始 -->
                <tr>
                  <td>
                      <input type="checkbox" class="shop" name="ids[]" value="">
                  </td>
                  <td>
                      <div class="shop_title">
                         <a href="" target="_blank">4324234</a></li>
                          <div>
                              <span>3434</span>
                          </div>
                      </div>
                  </td>
                  <td class="text-left shop_t">
                      
                  </td>
                 
                  <td>34</td>
                  <td>34535</td>
                  <td>43534</td>
                  <td>345345</td>
                  <td class="text-right action">
                      <a href="javascipt:void(0);" class="see_detail">查看</a>
                    <div class="t-none">
                        <a href="javascript:void(0)" class="product-copy" >删除</a> 
                    </div>  
                  </td>
              </tr>
          </tbody>
          <input type="hidden" name="tpl_id" value="">
    </table>
    <div class="action">
        <input type="checkbox" name="checkall"> 全选
        <div class="pagation">
            <!-- 分页 -->
            分页区域
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">用户信息</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group">
             <label class="col-sm-3 control-label">姓名：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    wdd
                </div>
            </div>
          </div>
          <div class="form-group">
             <label class="col-sm-3 control-label">手机号：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    wdd
                </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label">公司名称：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    wdd
                </div>
            </div>
          </div>
          <div class="form-group">
             <label class="col-sm-3 control-label">财务：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    wdd
                </div>
            </div>
          </div>
          <div class="form-group">
             <label class="col-sm-3 control-label">营业执照：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    <img src="http://g.hiphotos.baidu.com/image/pic/item/c8ea15ce36d3d539f09733493187e950342ab095.jpg">
                </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label">身份证正面：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    <img src="http://g.hiphotos.baidu.com/image/pic/item/c8ea15ce36d3d539f09733493187e950342ab095.jpg">
                </div>
            </div>
          </div>
          <div class="form-group">
             <label class="col-sm-3 control-label">身份证反面：</label>
            <div class="col-sm-9">
                <div class="info_detail">
                    <img src="http://g.hiphotos.baidu.com/image/pic/item/c8ea15ce36d3d539f09733493187e950342ab095.jpg">
                </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/member_info.js"></script>   
@endsection
