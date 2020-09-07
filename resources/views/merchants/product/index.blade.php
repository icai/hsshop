@extends('merchants.default._layouts')
@section('head_css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_public.css" />
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_b1r28wcf.css" />
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<style type="text/css">
  /*==========以下部分是Validform必须的===========*/
    .Validform_checktip{
      margin-left:8px;
      line-height:20px;
      height:20px;
      overflow:hidden;
      color:#999;
      font-size:12px;
    }
    .Validform_error{
      background-color:#ffe7e7;
    }
    #Validform_msg{color:#7d8289; font: 12px/1.5 tahoma, arial, \5b8b\4f53, sans-serif; width:280px; -webkit-box-shadow:2px 2px 3px #aaa; -moz-box-shadow:2px 2px 3px #aaa; background:#fff; position:absolute; top:0px; right:50px; z-index:99999; display:none;filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#999999'); box-shadow: 2px 2px 0 rgba(0, 0, 0, 0.1);}
    #Validform_msg .iframe{position:absolute; left:0px; top:-1px; z-index:-1;}
    #Validform_msg .Validform_title{line-height:25px; height:25px; text-align:left; font-weight:bold; padding:0 8px; color:#fff; position:relative; background-color:#999;
    background: -moz-linear-gradient(top, #999, #666 100%); background: -webkit-gradient(linear, 0 0, 0 100%, from(#999), to(#666)); filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999', endColorstr='#666666');}
    #Validform_msg a.Validform_close:link,#Validform_msg a.Validform_close:visited{line-height:22px; position:absolute; right:8px; top:0px; color:#fff; text-decoration:none;}
    #Validform_msg a.Validform_close:hover{color:#ccc;}
    #Validform_msg .Validform_info{padding:8px;border:1px solid #bbb; border-top:none; text-align:left;}
</style>
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            @if ( $status == 1 )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/product/index/1') }}?tag={{request('tag')}}">出售中</a>
            </li>
            @if ( $status == -2 )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/product/index/-2') }}?tag={{request('tag')}}">已售罄</a>
            </li>
            @if ( $status == 0 )
            <li class="hover">
            @else
            <li>
            @endif
                <a href="{{ URL('/merchants/product/index/0') }}?tag={{request('tag')}}">仓库中</a>
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
    <div class="js-list-filter-region clearfix ui-box" style="position: relative;">
        <div class="widget-list-filter">
            <div class="pull-left">
                <a target="_blank" class="zent-btn zent-btn-success release-product" href="{{ URL('/merchants/product/create') }}">发布商品</a>
            </div>
            <div class="pull-left" style="margin: 0 10px;">
                <a class="zent-btn zent-btn-success  batchPrint" href="javascript:void(0);">导出所选商品</a>
            </div>
            <div class="pull-left" style="position: relative;">
                <a class="zent-btn zent-btn-success  allPrint" href="javascript:void(0);">导出所有商品</a>
                <div style="position: absolute;left:108px ;top: -10px; background: #fff;padding: 8px 10px;width: 260px;border: 1px solid #ccc;border-radius: 5px;display: none;" class="printall">
                    <div style="display: flex;justify-content: space-between;align-items: center;">
                      <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确认导出所有商品？</div>
                        <div class="pull-right">
                            <a href="javascript:void(0);" class="zent-btn zent-btn-primary zent-btn-small print_sure">确定</a>
                            <a href="javascript:void(0);" class="zent-btn zent-btn-small print_cancel">取消</a>
                        </div>
                    </div>
                    <div style="width: 10px;height: 10px;border: 1px solid #ccc;position: absolute;left: -6px;top: 17px;transform: rotate(45deg);-ms-transform: rotate(45deg);-moz-transform: rotate(45deg);-webkit-transform: rotate(45deg);-o-transform: rotate(45deg);background: #fff;border-right: 0;border-top: 0;"></div>
                </div>
            </div>
            <div style="position: relative;">
                <div class="js-list-tag-filter ui-chosen" style="width: 200px;">
                    <select class="shop_grounp" data-placeholder="" id="product_group">
                        <option value="0" @if(isset($_GET['group_id'])&&$_GET['group_id'] == 0) selected @endif>所有分组</option>
                        <option value="" @if(isset($_GET['group_id']) && request('group_id') == '') selected @endif>未分组</option>
                         @if(!empty($groups))
                          @foreach($groups as $group)
                            @if($group['title'] <> '卡密商品')
                           <option value="{{$group['id']}}"  @if(isset($_GET['group_id'])&&$_GET['group_id'] == $group['id']) selected @endif >{{$group['title']}}</option>
                           @endif
                          @endforeach
                        @endif
                    </select>
                </div>
                <div class="js-list-search ui-search-box">
                    <form>
                        <input class="txt" name="title" value="{{isset($_GET['title'])?$_GET['title']:''}}" type="search" placeholder="搜索">
                        @foreach($_GET as $key => $get)
                          @if($key != 'title' )
                            <input type="hidden" name="{{$key}}" value="{{$key == 'page' ? 1 : $get}}"/>
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
                  <th class="text-left col-xs-3 padlef" colspan="3">
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
                  <th class="col-xs-1">收藏量</th>
                  <th class="col-xs-1">商品编码</th>
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
                  <th class="col-xs-1">
                     <a href="javascript:void(0);">商品分组</a>
                  </th>
                  <th class="text-right col-xs-3">操作</th>
              </tr>
          </thead>
          <tbody>
                 <!--  商品 列表 开始 -->
                 @foreach ($list as $k => $v)
              <tr>
                  <td>
                      <input type="checkbox" class="shop" name="ids[]" value="{{$v['id']}}" data-id="{{$v['id']}}">
                  </td>
                  <td>
                      <div class="shop_avatar">
                          <img class="lazy" data-original="{{ imgUrl($v['img']) }}">
                      </div>
                  </td>
                  <td class="text-left shop_t">
                      <div class="shop_title">
                         <a href="/shop/preview/{{$v['wid']}}/{{$v['id']}}" target="_blank">{{$v['title']}}</a></li>
                          <div>
                              <span>￥{{!empty($v['is_price_negotiable']) ? '面议' : $v['price']}}</span>
                          </div>
                      </div>
                  </td>
                  <!--<td>UV:{{$v['uv_num']}}<br/>PV:{{$v['pv_num']}}</td>-->
                  <td>UV:{{ $biData[$k]['viewuv'] ?? 0 }}<br/>PV:{{ $biData[$k]['viewpv'] ?? 0 }}</td>
                  <!--收藏量-->
                  <td>{{$v['favoriteCount']}}</td>
                  <td class="pro_code">
                      @if(empty($v['goods_no']))
                        <div>-</div>
                      @else
                          <div class="first_code">{{$v['goods_no'][0]}}</div>
                      @endif
                      <div class="more-code">
                          <div>
                              @foreach($v['goods_no'] as $key => $no)
                                  <span>{{$no}}</span>
                              @endforeach
                          </div>
                      </div>
                  </td>
                  <td>{{$v['stock']}}</td>
                  <td>{{$v['sold_num']}}</td>
                  <td>{{$v['created_at']}}</td>
                  <td>
                      <input type="number" class="no int-sort form-control" id="int-sort" data-id="{{$v['id']}}" value="{{$v['sort']}}" />
                      <span class="serialNumber">{{$v['sort']}}</span>
                  </td>
                  <td style="max-width:100px;overflow:hidden;text-overflow: ellipsis;white-space: normal;">
                      @if(empty($v['group_name_array']))
                          未分组
                      @else
                          {{implode(',',$v['group_name_array'])}}
                      @endif
                  </td>
                  <td class="text-right action">
                      <a target="_blank" href="{{URL('/merchants/product/editproduct/'.$v['id'])}}">编辑</a>
                      @if($memberCardNum)
                          <span>-</span>
                          <a class="member_price" data-id="{{$v['id']}}" data-title="{{$v['title']}}">会员价格</a>
                      @endif
                      <!--<span>-</span>
                      <a href="javascript:void(0)" class="out_delete" data-id="{{$v['id']}}">删除</a>-->
                      <span>-</span>
                      <!--<a href="javascript:void(0)" class="product_detail_url" data-url="{{$v['detail_url']}}">链接</a>-->
                      <a href="javascript:void(0)" class="ads" data-id="{{$v['id']}}" data-url="{{$v['detail_url']}}" data-price={{$v['price']}}>推广商品</a>
                      @if($isCreate)
                          <div class="t-none">
                            <a href="javascript:void(0)" data-id="{{$v['id']}}" class="product-copy" >复制</a>
                        </div>
                     @endif
                  </td>
              </tr>
               @endforeach
          </tbody>
          <input type="hidden" name="tpl_id" value="">
      </table>
      <div class="" style="text-align: right;overflow: visible;">
  		@if (!empty($list))
      <div class="page">
          <ul class="nav" style="overflow:visible;">
              <!-- <li class="change_model">
                  <a href="javascript:void(0);">修改模板</a>
              </li> -->
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
                  <div class="ui-popover ui-popover--confirm right-center" id="delete_prover" style="width:270px;">
                      <div class="ui-popover-inner clearfix ">
                          <div class="inner__header clearfix">
                              <div class="pull-left text-center" style="line-height: 28px;font-size: 14px;">确定删除吗？</div>
                              <div class="pull-right">
                                  <a href="javascript:;" class="zent-btn zent-btn-primary zent-btn-small delete_sure">确定</a>
                                  <a href="javascript:;" class="zent-btn zent-btn-small delete_cancel">取消</a>
                              </div>
                          </div>
                      </div>
                      <div class="arrow"></div>
                  </div>
              </li>
              <li class="dropdown">
              	<a id="dLabel" data-target="#" href="http://example.com" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              		更多
              		<span class="caret"></span>
              	</a>              
              	<ul class="dropdown-menu" aria-labelledby="dLabel">  
              		<div class="san-relative">
              			<div class="triangle_border_down">
										    <span></span>
										</div>
              		</div>									 		
              		<li class="discount discount-fare" data-stopPropagation="true">
              			<a href="javascript:void(0);">运费模板</a>
              			<div class="popover top">
              				<div class="arrow"></div>
              				<div class="popover-inner popover-change">
	              				<div class="controls">
	              					<label class="radio inline ems">
	              				    <input type="radio" name="freight_type" value="1" checked>统一运费
	              				  </label>
	              					<div class="form-group ems_price">
	              						<div class="input-group">
	              							<div class="input-group-addon">￥</div>
	              							<input class="form-control int-fare" type="text" name="freight_price" value="0.00" >
	              						</div>
	              					</div>
	              				</div>
	              				<div class="control-group">
	              					<div class="controls ems_other">
	              						<label class="radio inline ems">
	              				      <input type="radio" name="freight_type" value="2">运费模板
	              				    </label>
	              						<div class="form-group ems_price">
	              							<select class="form-control fare-sel freight" name="freight_id">
	              								
	              							</select>
	              						</div>
	              						<p class="help-inline set_model">
	              							<a class="js-refresh-tag" href="javascript:;">刷新</a>
	              				      	<span>|</span>
	              							<a class="new-window" target="_blank" href="/merchants/currency/expressSet">新建</a>
	              						</p>
	              					</div>
	              				</div>
	              				<div class="button-fare">
	              					<button type="button" class="zent-btn zent-btn-primary zent-btn-small sure-fare">确定</button>
              					  <button type="reset" class="zent-btn zent-btn-small cancel-fare">取消</button>
	              				</div>	
	              				<hr>
              					<p class="ship-tips">注意：批量修改运费模版仅对“实物商品”有效</p>              				
              				</div>
              			</div>
              		</li>
              		<li class="discount associator" data-stopPropagation="true">
              			<a href="javascript:void(0);">会员折扣</a>
              			<div class="popover top allowance">
              				<div class="arrow"></div>
              				<div class="popover-inner popover-change" style="width: 280px;">
              					<div class="popover-content text-center">
              						<div class="tip_lef">
              							<label class="radio tip_lab">
                              <input type="radio" name="is_discount" value="1" checked="">
                              <span>参与</span>
                              </label>
              							<label class="radio tip_lab">
                              <input type="radio" name="is_discount" value="0">
                              <span>不参与</span>
                          	</label>              							
              						</div>
              							<button type="button" class="zent-btn zent-btn-primary zent-btn-small discount_confirm" style="margin-left: 20px;">确定</button>
              							<button type="reset" class="zent-btn zent-btn-small discount_cancel">取消</button>
              					</div>              					
              				</div>
              			</div>
              		</li>
                    <li class="discount is_point" data-stopPropagation="true">
                        <a href="javascript:void(0);">开启积分</a>
                        <div class="popover top allowance">
                            <div class="arrow"></div>
                            <div class="popover-inner popover-change" style="width: 280px;">
                                <div class="popover-content text-center">
                                    <div class="tip_lef">
                                        <label class="radio tip_lab">
                                            <input type="radio" name="is_point" value="1" checked="">
                                            <span>开启</span>
                                        </label>
                                        <label class="radio tip_lab">
                                            <input type="radio" name="is_point" value="0">
                                            <span>不开启</span>
                                        </label>
                                    </div>
                                    <button type="button" class="zent-btn zent-btn-primary zent-btn-small is_point_confirm" style="margin-left: 20px;">确定</button>
                                    <button type="reset" class="zent-btn zent-btn-small is_point_cancel">取消</button>
                                </div>
                            </div>
                        </div>
                    </li>
              	</ul>
              </li>	              
          </ul>
      </div>
      @endif
      	{{$pageHtml}}      	
      </div>
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
      
    </form>
</div>
@endsection
@section('other')
  <!--弹层-->
      <!-- tip -->
      <div class="tip">请选择商品</div>
        <!-- modal2 -->
        <div class="modal export-modal" id="myModal1">
            <div class="modal-dialog" id="modal-dialog1">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">
                          <span aria-hidden="true">&times;</span>
                          <span class="sr-only">Close</span>
                      </button>
                      <h4 class="modal-title" id="myModalLabel">设置商品模版</h4>
                  </div>
                  <div class="modal-body">
                      <table class="table">
                        <thead>
                            <tr>
                                <th>标题</th>
                                <th>创建时间</th>
                                <th class="text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>sdsdds</td>
                                <td>2017-07-31 13:34:49</td>
                                <td>
                                    <span>编辑</span>
                                    -
                                    <span>使用</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
                  <!-- <div class="modal-footer">
                  <a href="javascript:void(0)" class="btn btn-primary submit">提交</a></div> -->
              </div>
            </div>
        </div>
      <!-- 删除弹窗 -->
      <div class="popover del_popover left" role="tooltip">
        <div class="arrow"></div>
        <div class="popover-content">
            <span>你确定要删除吗？</span>
            <button class="btn btn-primary sure_btn">确定</button>
            <button class="btn btn-default cancel_btn">取消</button>
        </div>
      </div>
      <!-- 推广弹窗 -->
		<!--updata by 黄新琴 2018-8-27-->
		<div class="widget-promotion widget-promotion1" style="display: none;">
		    <ul class="widget-promotion-tab widget-promotion-tab1 clearfix">
		        <li class="wsc_code active">微商城</li>
		        <li class="xcx_code">小程序</li>
		    </ul>
		    <div class="widget-promotion-content js-tabs-content">
		    	<!--微商城-->
		        <div class="js-tab-content-wsc" style="display: block;">
		            <div>
		                <div class="widget-promotion-main">
		                    <div class="js-qrcode-content">
		                        <div class="widget-promotion-content">
		                            <label>商品页链接</label>
		                            <div class="input-append">
		                                <input type="text" class="form-control link_copy iblock link_url_wsc" style="vertical-align: middle;" readonly="" value="" />
		                                <a class="btn js-btn-copy-wsc code-copy-a" data-clipboard-text="">复制</a>
		                            </div>
		                        </div>
		                        <div class="widget-promotion-content">
		                            <label class="label-b">商品页二维码</label>
		                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
		                                <div class="qrcode">
		                                    <div class="qr_img"></div>
		                                    <div class="clearfix qrcode-links">
		                                        <a class="down_qrcode down_qrcode_wsc" href="javascript:void(0);">下载二维码</a>
		                                    </div>
		                                </div>
		                           	</div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		        <!--小程序-->
		        <div class="js-tab-content-xcx" style="display: none;">
		            <div>
		                <div class="widget-promotion-main">
		                    <div class="js-qrcode-content">
		                        <div class="widget-promotion-content">
		                            <label>小程序链接</label>
		                            <div class="input-append">
		                                <input type="text" class="form-control link_copy iblock link_url_xcx" style="vertical-align: middle;" readonly="" value="" />
		                                <a class="btn js-btn-copy-xcx code-copy-a" data-clipboard-text="">复制</a>
		                            </div>
		                        </div>
		                        <div class="widget-promotion-content">
		                            <label class="label-b">小程序二维码</label>
		                            <div class="qrcode-right-sidebar js-qrcode-right-sidebar">
		                                <div class="qrcode">
		                                    <div class="qr_img_xcx"></div>
		                                    <div class="clearfix qrcode-links">
		                                        <a class="down_qrcode down_qrcode_xcx" href="javascript:void(0);">下载小程序码</a>
		                                    </div>
		                                </div>
		                           </div>
		                        </div>           	
		                    </div> 
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<!--end-->
     
      <!-- 会员价格弹窗 -->
      <!-- 广告图片model -->
      <div class="modal export-modal myModal-adv" id="member_price">
          <div class="modal-dialog" id="member_price-dialog">
              <form class="form-horizontal" id="member_price_form">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="myModal-adv">
                              <span>&times;</span>
                              <span class="sr-only">Close</span>
                          </button>
                          <ul class="module-nav modal-tab">
                              <li class="active">
                                  <span>自定义会员价格</span>
                                  <a href="https://www.huisou.cn/home/index/helpDetail/847" target="_blank" data-type="goods" class="js-modal-tab">查看使用教程</a>
                              </li>
                          </ul>
                      </div>
                      <div class="modal-body">
                          <div class="setting_list">
                              <input type="hidden" id="productId"/>
                              <input type="hidden" id="wid" value="{{$wid}}"/>
                              <span>商品名称：</span><a href="#" target="_blank" id="memberPriceTitle"></a>
                          </div>
                          <div class="setting_list">
                              <span>优惠方式：</span>
                              <input type="radio" name="count_method" value="1" checked>减价
                              <input type="radio" name="count_method" value="2">
                              指定价格
                          </div>
                          <div class="setting_list show-vip-price">
                              <span>非会员显示会员价：</span>
                              <div class="top-tootip">
                                <i class="glyphicon glyphicon-question-sign f14 note_tip"></i>
                                <p class="table_item_tips"><em class="tip_mark"></em>勾选非会员显示会员价后，移动端非会员用户界面展示折扣力度最大的会员价</p>
                              </div>
                              <input type="radio" name="is_show_vip_price" value="1">是
                              <input type="radio" name="is_show_vip_price" value="0">否
                          </div>
                          <div class="setting_list">
                              <table class="reduce">
                              </table>
                              <table class="appointPrice">

                              </table>
                          </div>
                      </div>
                      <div class="modal-footer clearfix">
                          <div class="selected-count-region hide">
                              已选择<span class="js-selected-count">2</span>张图片
                          </div>
                          <div class="text-left js-batch-box" style="display:none">
                            <div class="btn-box">
                                    <a class="ui-btn js-empty">清空</a>
                                    <a class="ui-btn js-batch">批量</a>
                            </div>
                            <div class="batch-box" style="display:none">
                                    <div>批量设置：</div>
                                    <div class="select-vip">
                                        <div class="discount-desc" style="display:none">
                                            <div class="search-vip">
                                                <input type="text">
                                            </div>
                                            <div class="search-list"></div>
                                        </div>
                                        <div class="select-result">请选择</div>
                                    </div> 
                                    <div class="discount-num">
                                        <span class="discount-type">减</span>
                                        <input type="text">
                                        <span>元</span>
                                    </div>
                                    <div>
                                    <a class="btn-batch-confirm">确认</a>
                                    <a class="btn-batch-cancel">取消</a>
                                    </div>
                            </div>  
                          </div>
                          <div class="text-right">
                              <a class="ui-btn js-confirm ui-btn-primary">保存</a>
                              <a class="ui-btn js-cancel">取消</a>
                          </div>
                      </div>
                  </div>
              </form>
          </div>
      </div>
      <!--backdrop-->
      <div class="modal-backdrop"></div>
@endsection
@section('page_js')
<!-- 搜索插件 -->
<!-- layer -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>

<script src="{{config('app.source_url')}}static/js/jquery.lazyload.js"></script>
<script src="{{ config('app.source_url') }}static/js/require.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/main.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/product_b1r28wcf.js"></script>
<script>
    var isCreate = "{{ $isCreate }}";
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
   var SORT = [0,0,0,0];
   var ORDER_BY = ['price','stock','sold_num','created_at','sort'];
   var ORDER = ['asc','desc'];
   var FLAG = ['↑','↓'];
   var NAME = ['价格','库存','总销量','创建时间','序号'];
   function sort_desc(index,sort){
       var params = getallparam({order:ORDER[sort],orderby:ORDER_BY[index]});
       window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
   }

   $(function(){
       $('#product_group').change(function(){
           var group_id = $(this).children('option:selected').val();//这就是selected的值
           window.location.href = 'http://'+ location.host + location.pathname + '?'+getallparam({group_id:group_id});
       })
   });
</script>
<!--所有商品订单打印状态-->
<script type="text/javascript">
     var printStatus ={{$status}};
</script>
@endsection
