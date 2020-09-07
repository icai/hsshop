@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_public.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_b1r28wcf.css" />
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
            <li class="hover">
            <li>
                <a href="{{ URL('/merchants/product/distributionGoods/1') }}">出售中</a>
            </li>
            <li class="hover">
            <li>
                <a href="{{ URL('/merchants/product/distributionGoods/-2') }}">已售罄</a>
            </li>
            <li class="hover">
            <li>
                <a href="{{ URL('/merchants/product/distributionGoods/0') }}">仓库中</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
    </div>   
    <!-- 三级导航 结束 -->
    
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!--程序猿和攻城狮正在积极努力搬砖中...-->
   	<div class="js-list-filter-region clearfix ui-box" style="position: relative;">
   		<div class="widget-list-filter">
   			<div class="pull-left">
                <a class="zent-btn zent-btn-success " href="{{ URL('/merchants/product/create') }}">发布商品</a>
            </div>
            <div style="position: relative;">
            	<div class="js-list-tag-filter ui-chosen" style="width: 200px;">
                    <select class="shop_grounp" data-placeholder="" id="product_group">
                        <option value="0" @if(isset($_GET['group_id'])&&$_GET['group_id'] == 1) selected @endif>所有分组</option>
                         @if(!empty($groups))
                          @foreach($groups as $group)
                           <option value="{{$group['id']}}"  @if(isset($_GET['group_id'])&&$_GET['group_id'] == $group['id']) selected @endif >{{$group['title']}}</option>
                          @endforeach
                        @endif
                    </select>
                </div>
                <div class="js-list-search ui-search-box">
                    <form >
                        <input class="txt" name="title" value="{{isset($_GET['title'])?$_GET['title']:''}}" type="search" placeholder="搜索" value="">
                        @foreach($_GET as $key => $get)
                          @if($key != 'title' )
                            <input type="hidden" name="{{$key}}" value="{{$get}}"/>
                          @endif
                        @endforeach
                    </form>
                </div>
            </div>
   		</div>
   	</div>
   	<form role="form" name="shop_form">
      @if (!empty($list))
      <table class="table table-striped">
          <thead>
              <tr>
                  <th class="text-left col-xs-3" colspan="3">
                      <input type="checkbox" id="all_shop" name="">
                      <span>商品</span>
                      @if(isset($_GET['orderby']) && $_GET['orderby'] =='price' && $_GET['order'] == 'desc')
                      <a href="javascript:void(0);" onclick="sort_desc(0,0)">价格 ↓</a>
                      @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='price' && $_GET['order'] == 'asc')
                      <a href="javascript:void(0);" onclick="sort_desc(0,1)">价格 ↑</a>
                      @else
                      <a href="javascript:void(0);" onclick="sort_desc(0,0)">价格</a>
                      @endif
                  </th>
                  <th class="col-xs-1">访问量</th>
                  <th class="col-xs-1">
                       @if(isset($_GET['orderby']) && $_GET['orderby'] =='stock' && $_GET['order'] == 'desc')
                       <a href="javascript:void(0);" onclick="sort_desc(1,0)">库存 ↓</a>
                       @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='stock' && $_GET['order'] == 'asc')
                       <a href="javascript:void(0);" onclick="sort_desc(1,1)">库存 ↑</a>
                       @else
                       <a href="javascript:void(0);" onclick="sort_desc(1,0)">库存</a>
                       @endif
                   </th>
                  <th class="col-xs-1">
                     @if(isset($_GET['orderby']) && $_GET['orderby'] =='sold_num' && $_GET['order'] == 'desc')
                    <a href="javascript:void(0);" onclick="sort_desc(2,0)">总销量 ↓</a>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='sold_num' && $_GET['order'] == 'asc')
                    <a href="javascript:void(0);" onclick="sort_desc(2,1)">总销量 ↑</a>
                    @else
                    <a href="javascript:void(0);" onclick="sort_desc(2,0)">总销量</a>
                    @endif
                  </th>
                  <th class="col-xs-2">
                     @if(isset($_GET['orderby']) && $_GET['orderby'] =='created_at' && $_GET['order'] == 'desc')
                    <a href="javascript:void(0);" onclick="sort_desc(3,0)">创建时间 ↓</a>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='created_at' && $_GET['order'] == 'asc')
                    <a href="javascript:void(0);" onclick="sort_desc(3,1)">创建时间 ↑</a>
                    @else
                    <a href="javascript:void(0);" onclick="sort_desc(3,0)">创建时间</a>
                    @endif
                  </th>
                  <th class="col-xs-1">
                     @if(isset($_GET['orderby']) && $_GET['orderby'] =='sort' && $_GET['order'] == 'desc')
                    <a href="javascript:void(0);" onclick="sort_desc(4,0)">序号 ↓</a>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='sort' && $_GET['order'] == 'asc')
                    <a href="javascript:void(0);" onclick="sort_desc(4,1)">序号 ↑</a>
                    @else
                    <a href="javascript:void(0);" onclick="sort_desc(4,0)">序号</a>
                    @endif
                  </th>
                  <th class="text-right col-xs-3">操作</th>
              </tr>
          </thead>
          <tbody>
                 <!--  商品 列表 开始 -->
                 @foreach ($list as $v)
              <tr>
                  <td>
                      <input type="checkbox" class="shop" name="ids[]" value="{{$v['id']}}">
                  </td>
                  <td>
                      <div class="shop_avatar">
                          <img src="{{ imgUrl($v['img']) }}">
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
                  <td>UV:{{$v['uv_num']}}<br/>PV:{{$v['pv_num']}}</td>
                  <td>{{$v['stock']}}</td>
                  <td>{{$v['sold_num']}}</td>
                  <td>{{$v['created_at']}}</td>
                  <td>{{$v['sort']}}</td>
                  <td class="text-right action">
                      <a href="{{URL('/merchants/product/editproduct/'.$v['id'])}}">编辑</a>
                      @if($memberCardNum)
                          <span>-</span>
                          <a class="member_price" data-id="{{$v['id']}}" data-title="{{$v['title']}}">会员价格</a>
                      @endif
                      <span>-</span>
                      <a href="javascript:void(0)" class="product_detail_url" data-url="{{$v['detail_url']}}">链接</a>
                    <div class="t-none">
                        <a href="javascript:void(0)" data-id="{{$v['id']}}" class="product-copy" >复制</a> 
                    </div>  
                  </td>
              </tr>
               @endforeach
          </tbody>
          <input type="hidden" name="tpl_id" value="">
      </table>
        {{$pageHtml}}
      <!--  商品 列表 结束 -->
      <!-- 修改分组popover1 -->
      <div class="popover fade top in" id="popover1" style="top:9999px">
          <div class="arrow" style="left: 50%;"></div>
          <div class="popover-inner popover-category2">
              <div class="popover-header clearfix">修改分组
                  <a href="{{URL('/merchants/product/productGroup')}}" target="_blank" class="pull-right">管理</a>
              </div>
              <div class="popover-content">
                  <ul class="popover-content-categories js-popover-content-categories">
                     @if(!empty($groups))
                         @foreach($groups as $group)
                             <li data-id="{{$group['id']}}" class="clearfix">
                                 <input type="checkbox" name="group_ids[]" value="{{$group['id']}}">
                                 <span class="category-title">{{$group['title']}}</span>
                             </li>
                          @endforeach
                     @endif
                  </ul>
              </div>
              <div class="popover-footer">
                  <a href="javascript:void(0);" class="zent-btn zent-btn-small zent-btn-primary js-btn-confirm">保存</a>
                  <a href="javascript:void(0);" class="zent-btn zent-btn-small js-btn-cancel">取消</a>
              </div>
          </div>
      </div>
      @else
      <div class="no_result">暂无相关数据</div>
      @endif
      {{ csrf_field() }}
      @if (!empty($list))
      <div class="page">
          <ul class="nav">
              <!--<li class="change_model">
                  <a href="javascript:void(0);">修改模板</a>
              </li>-->
              <li id="change_grounp">
                  <a href="javascript:void(0)">修改组</a>
                  <!-- prover -->
              </li>
              @if($status == 0)
              <li class="down_shop">
                  <a href="javascript:void(0);">上架</a>
                  <div class="ui-popover ui-popover--confirm right-center">
                      <div class="ui-popover-inner clearfix ">
                          <div class="inner__header clearfix">
                              <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确认上架？</div>
                              <div class="pull-right">
                                  <a href="javascript:void(0);" class="zent-btn zent-btn-primary zent-btn-small js-up-save">确定</a>
                                  <a href="javascript:void(0);" class="zent-btn zent-btn-small js-cancel">取消</a>
                              </div>
                          </div>
                      </div>
                      <div class="arrow"></div>
                  </div>
                  <input type="hidden" name="status" value="1">
              </li>
              @else
                  <li class="down_shop">
                      <a href="javascript:void(0);">下架</a>
                      <div class="ui-popover ui-popover--confirm right-center">
                          <div class="ui-popover-inner clearfix ">
                              <div class="inner__header clearfix">
                                  <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确认下架？</div>
                                  <div class="pull-right">
                                      <a href="javascript:void(0);" class="zent-btn zent-btn-primary zent-btn-small js-save">确定</a>
                                      <a href="javascript:void(0);" class="zent-btn zent-btn-small js-cancel">取消</a></div>
                              </div>
                          </div>
                          <div class="arrow"></div>
                      </div>
                      <input type="hidden" name="status" value="0">
                  </li>
              @endif
              <li class="delete">
                  <a href="javascript:void(0);">删除</a>
                  <div class="ui-popover ui-popover--confirm right-center" id="delete_prover">
                      <div class="ui-popover-inner clearfix ">
                          <div class="inner__header clearfix">
                              <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确定要删除吗？</div>
                              <div class="pull-right">
                                  <a href="javascript:;" class="zent-btn zent-btn-primary zent-btn-small delete_sure">确定</a>
                                  <a href="javascript:;" class="zent-btn zent-btn-small delete_cancel">取消</a>
                              </div>
                          </div>
                      </div>
                      <div class="arrow"></div>
                  </div>
              </li>
              <li class="discount">
                  <a href="javascript:void(0);">会员折扣</a>
                  <div class="popover top">
                      <div class="arrow"></div>
                      <div class="popover-inner popover-change" style="width: 280px;">
                          <div class="popover-content text-center">
                              <form class="form-inline">
                                  <label class="radio">
                                      <input type="radio" name="is_discount" value="1" checked="">参与</label>
                                  <label class="radio" style="margin-left: 12px;">
                                      <input type="radio" name="is_discount" value="0">不参与</label>
                                  <button type="button" class="zent-btn zent-btn-primary zent-btn-small discount_confirm" style="margin-left: 20px;">确定</button>
                                  <button type="reset" class="zent-btn zent-btn-small discount_cancel">取消</button></form>
                          </div>
                      </div>
                  </div>
              </li>
          </ul>
      </div>
      @endif
    </form>
</div>
@endsection
@section('page_js')
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_b1r28wcf.js"></script>
@endsection