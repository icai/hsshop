@extends('merchants.default._layouts')
@section('head_css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/chosen.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/laydate.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css">
<!-- 上传 -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/webUp_style.css" />
<!-- <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_13gmx7ln.css"> -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_kwvhib03.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/publish_store.css">
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/product_evxshgq4.css" />
@endsection
@section('slidebar')
@include('merchants.product.slidebar')
@endsection
@section('middle_header')
<div class="model_box">
    <div class="model_box_div">
        <p>活动进行中，修改活动商品，会对活动造成未知的影响，请慎重修改，点击“确认”此次修改即生效</p>
        <div class="model_box_btn">
            <button class="btn btn_queren">确认</button>
            <button class="btn btn_close">取消</button>
        </div>
    </div>
</div>
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/product/index/1') }}">商品库</a>
            </li>   
            <li>
                <a href="{{ URL('/merchants/product/create') }}">发布商品</a>
            </li>
            @if(!empty($productId))
            <li>
                小程序对应商品路径：
                <input type="text" value="pages/main/pages/product/product_detail/product_detail?id={{$productId}}" class="copyContent" disabled />
                <input type="button" value="复制路径" class="copyPathBtn"/>
            </li>
            @endif

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
<input type="hidden" id="backUrl" value="{{$backUri}}"/>
<input type="hidden" id="wid" value="{{$wid}}" />
@verbatim
<!-- <div class="content" ng-click="hideAllModel()"> -->
<div class="content" ng-click="hideAllModel($event)">
    <input type="hidden" id="productId" value="{{$id || 0}}"/>
    <div class="goods-edit-area">
        <ul class="ui-nav-tab">
            <li class="js-step-1 {{kind['isactive']}}" ng-click="goStep($index,kind,baseform.$valid)" ng-repeat="kind in chooseStep">
                <a href="javascript:void(0);" ng-bind="kind['title']"></a>
            </li>
        </ul>
        <form name="baseform" class="ng-hide" novalidate ng-show="step==1" ng-cloak>
            <div class="form-horizontal fm-goods-info">
                <div id="base-info-region" class="goods-info-group">
                    <div class="goods-info-group-inner">
                        <div class="info-group-title vbox">
                            <div class="group-inner">基本信息</div>
                        </div>
                        <div class="info-group-cont vbox">
                            <div class="group-inner">
                                <div class="control-group">
                                    <label class="control-label" style="width: 103px;">虚拟卡密：
                                    </label>
                                    <div class="controls">
                                        <label class="radio inline">
                                            <input type="radio" name="is_card" ng-value="1" ng-model="baseinfo['is_card']" ng-checked="baseinfo['is_card'] == 1"/>是
                                        </label>
                                        <label class="radio inline">
                                            <input type="radio" name="is_card" ng-value="0" ng-model="baseinfo['is_card']" ng-checked="baseinfo['is_card'] == 0"/>否
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">商品分组：</label>
                                    <div class="controls">
                                        <input type="text" class="choos_product_group" placeholder="请选择商品分组"/>
                                        <select data-placeholder="请选择商品分组" class="chosen_select" style="width:100px;" multiple ng-model="baseinfo.group_id">
                                            <option value=""></option>
                                            <option value="{{item.group_id}}" ng-repeat="item in baseinfo.shopGrounp" ng-bind="item.title"></option>
                                        </select>
                                        <p class="help-inline">
                                        	<a class="new-window refresh" target="_blank" href="javascript:void(0);" ng-click="refresh()">刷新</a>
                                            <span>|</span>
                                            <a class="new-window" target="_blank" href="/merchants/product/createGroup">新建分组</a>
                                            <span>|</span>
                                            <a class="new-window" target="_blank" href="/home/index/detail/36">帮助</a>
                                        </p>
                                        <p class="help-desc js-tag-desc hide">使用“列表中隐藏”分组，商品将不出现在商品列表中</p>
                                    </div>
                                </div>
                                <div class="control-group" ng-show="baseinfo['is_card'] == 0">
                                    <label class="control-label" style="width: 103px;">自提开启：
                                    </label>
                                    <div class="controls">
                                        <label class="radio inline">
                                            <input type="radio" name="is_hexiao" value="1" ng-click="judgeFenxiao()" ng-model="baseinfo.is_hexiao" ng-checked="baseinfo.is_hexiao==1">是
                                        </label>
                                        <label class="radio inline">
                                            <input type="radio" name="is_hexiao" value="0" ng-click="wuliuhexiao()" ng-model="baseinfo.is_hexiao" ng-checked="baseinfo.is_hexiao==0">否
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" style="width: 103px;">批发开启：
                                    </label>
                                    <div class="controls">
                                        <label class="radio inline">
                                            <input type="radio" name="is_wholesale" ng-value="1" ng-model="baseinfo['is_wholesale']" ng-checked="baseinfo['is_wholesale'] == 1"/>是
                                        </label>
                                        <label class="radio inline">
                                            <input type="radio" name="is_wholesale" ng-value="0" ng-model="baseinfo['is_wholesale']" ng-checked="baseinfo['is_wholesale'] == 0"/>否
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group" ng-class="ngHide">
                                    <label class="control-label" style="width: 103px;">无需物流：
                                    </label>
                                    <div class="controls">
                                        <label class="radio inline">
                                            <input type="radio" name="no_logistics" value="1"  ng-model="baseinfo.no_logistics" ng-checked="baseinfo.no_logistics==1" ng-true-value="1">是
                                        </label>
                                        <label class="radio inline">
                                            <input type="radio" name="no_logistics" value="0"  ng-model="baseinfo.no_logistics" ng-checked="baseinfo.no_logistics==0" ng-true-value="0">否
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group" ng-class="ngHide">
                                    <label class="control-label" style="width: 103px;">时间设置：
                                    </label>
                                    <div class="controls">
                                        <label class="radio inline padding_left_0">
                                            <input class="form-control" type="text" ng-model="baseinfo.hexiao_start" name="hexiao_start" id="hexiao_start">
                                        </label>
                                        <span>到</span>
                                        <label class="radio inline padding_left_0">
                                            <input class="form-control" type="text" ng-model="baseinfo.hexiao_end" name="hexiao_end" id="hexiao_end">
                                        </label>
                                        <span style="margin-left: 10px; color: #999999"><i style="color: red; display: inline; margin-left: 5px;">*</i>请在规定的时间内到商家取货</span>
                                    </div>
                                </div>
                                <div class="js-electric-card" style="display: none;">
                                    <div class="control-group">
                                        <label class="control-label">
                                            商品有效期：<br />
                                            <span class="gray">(发布后不能修改)</span>
                                        </label>
                                        <div class="controls">
                                           <label class="radio inline has-input">
                                                <input type="radio" name="valid_period" value="0" checked="">长期有效
                                            </label>
                                            <label class="radio inline has-input">
                                                <input type="radio" name="valid_period" value="1">自定义有效期
                                            </label>
                                            <div class="js-valid-period valid-period" style="display: none;">
                                                <div class="input-append">
                                                    <input type="text" class="input-small hasDatepicker" id="item_validity_start" name="item_validity_start" value="" readonly="">
                                                    <label for="item_validity_start" class="add-on">
                                                        <i class="icon-calendar"></i>
                                                    </label>
                                                </div>
                                                <span>至</span>
                                                <div class="input-append">
                                                    <input type="text" class="input-small hasDatepicker" id="item_validity_end" name="item_validity_end" value="" readonly="">
                                                    <label for="item_validity_end" class="add-on">
                                                        <i class="icon-calendar"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="before_sell" style="display: none;">
                                    <div class="control-group">
                                        <label class="control-label">预售设置：</label>
                                        <div class="controls">
                                            <label class="checkbox inline ">
                                                <input type="checkbox" name="pre_sale" value="1" ng-model="baseinfo.presell_flag" ng-checked="baseinfo.presell_flag==1">预售商品
                                            </label>
                                        </div>
                                    </div>
                                    <div class="control-group ems_time" ng-show="baseinfo.presell_flag">
                                        <label class="control-label">
                                            <em class="required">*</em>发货时间：
                                        </label>
                                        <div class="controls">
                                            <label class="radio inline">
                                                <input type="radio" name="presell_delivery_type"  value="1" ng-model="baseinfo.presell_delivery_type"
                                                ng-checked="baseinfo.presell_delivery_type==1">
                                                <input type="text" class="form-control width_150"  id="datetime" ng-model="baseinfo.presell_delivery_time">
                                                <span class="begin_ems">开始发货</span>
                                            </label>
                                            <label class="radio inline etd-type">
                                                <input type="radio" name="presell_delivery_type" value="2" ng-model="baseinfo.presell_delivery_type" ng-checked="baseinfo.presell_delivery_type==2">付款成功
                                                <input name="etd_days" class="form-control width_40" type="number" min="1" max="90" value=""
                                                ng-model="baseinfo.presell_delivery_payafter">天后发货
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="goods-info-group">
                    <div class="goods-info-group-inner">
                        <div class="info-group-title vbox">
                            <div class="group-inner">库存/规格</div>
                        </div>
                        <div class="info-group-cont vbox">
                            <div class="group-inner">
                                <div class="control-group cardPass" ng-show="baseinfo['is_card']==1">
                                    <label class="control-label">
                                        选择卡密：
                                    </label>
                                    <div class="controls card_mi">
                                        <span class="InitCrdPr" ng-click="showCardIdModel()" ng-show="baseinfo['cam_id'] == 0">+添加卡密</span>
                                        <input type="text" class="form-control" name="card_id" value="" ng-click="showCardIdModel()" ng-show="baseinfo['cam_id'] != 0" style="width:248px"/>
                                    </div>
                                </div> 
                            	<div class="control-group" ng-show="baseinfo['is_card']==0">
                                    <label class="control-label">
                                    商品规格：</label>
                                    <div class="controls">
                                    	<div class="guige_group">
                                    		<div ng-repeat="list in Guival" ng-init="outerIndex = $index">
                                    			<div style="display: none;" ng-bind="guiGroupindex = $index"></div>
                                    			<h3 class="sku-group-title" ng-mouseenter="remove_flag = true" ng-mouseleave="remove_flag = false" ng-init="remove_flag = true">
                                    				<div class="select2-container js-sku-name" ng-class="{'select2-dropdown-open select2-container-active':list.selGuige}" ng-click="showGuiDialog(list,$event,outerIndex)" style="width: 100px;">
                                    					<a class="select2-choice" href="javascript:void(0)">
                                    						<span class="select2-chosen">{{guiSelS[$index].prop.title}}</span>
                                    						<span class="select2-arrow">
                                    							<b></b>
                                    						</span>
                                    					</a>
                                    				</div>
                                    				<div ng-show="list.selGuige" class="select2-drop select2-with-searchbox select2-drop-active">
                                                        <div class="t-search-warp">
                                                            <input type='text' class="form-control t-search" maxlength="4" name="sku_input" ng-keydown="searchKeydown($event,outerIndex)" ng-keyup="searchKeyup($event,outerIndex)" />
                                                        </div>
                                    					<ul class="select2-results"> 
                                    						<li class="select2-results-dept-0 select2-result select2-result-selectable" ng-repeat="ListSel in guiSel" ng-show="!ListSel.isHide" ng-click="changOpt(ListSel.title,ListSel.id,guiGroupindex)" ng-class="{'select2-highlighted':ListSel.isActive}" ng-mouseover="setIsActive($index)">
                                    							<div class="select2-result-label">
                                    								<span class="select2-match"></span>
                                    								{{ListSel.title}}
                                    							</div>
                                    						</li>
                                    					</ul>
                                    				</div>
                                    				<label class="checkbox inline close-check" ng-show="$index==0">
			                                            <input type="checkbox" name="gui_image" ng-model="guiCheckImg" ng-checked="guiCheckImg" ng-click="showGuiImage()"/>
			                                            <span>添加规格图片</span>
			                                        </label>
			                                        <a ng-show="remove_flag" class="js-remove-sku-group remove-guige-group close-row" href="javascript:void(0)" ng-click="removeRows($index)">x</a>
                                    			</h3>
                                    			<div class="guige-group-cont">
                                    				<div ng-show="guiSelS[$index].prop.id">
                                    					<div class="guige-atom-list">
                                    						<div ng-class="{true:'guige-atom active',false:'guige-atom'}[guiCheckImg && outerIndex == 0]" ng-mouseenter="close_flag = true" ng-mouseleave="close_flag = false" ng-init="close_flag = false" ng-repeat="guival in list">
                                    							<span class='guival_span' ng-click="editCont($event,$index,guival,outerIndex)">{{guival.title}}</span>
                                    							<div ng-show="close_flag"  ng-class="{black:color.active}" ng-click="removeAtom($index,guiGroupindex)" class="close-div atom-close close-modal small">x</div>
                                    							<div class="upload-img-wrap" ng-if="guiCheckImg && outerIndex == 0">
                                    								<div class="arrow" style='display: none'></div>
                                    								<div class="up-img">
                                    									<div class="add-image js-btn-add" style="background: url({{guival['img']}}) no-repeat 0 0 / 100% 100%;" ng-click="imageCK($index)" ng-bind="guival['img']? '' : '＋' "></div>
                                    								</div>
                                    							</div>
                                    						</div>
                                    					</div>
                                    					<a class="add-guige add-close" href="javascript:void(0)" ng-click="addCont($event,$index)">+添加</a>
                                    				</div>
                                    			</div>
                                    			<div class="guige-group-cont guige-group-tip" ng-if="guiCheckImg && outerIndex == 0">
                                    				<p class="help-desc">目前只支持为第一个规格设置不同的规格图片</p>
                                    				<p class="help-desc">设置后，用户选择不同规格会显示不同图片</p>
                                    				<p class="help-desc">建议尺寸：600 x 720像素</p>
                                    			</div>
                                    		</div>
                                    		<div>
                                    			<h3 class="sku-group-title">
                                    				<button type="button" class="btn_addRows btn btn_disabled" ng-show="Guival.length != 3" ng-click="addRows1($event)">添加规格项目</button>
                                    			</h3>
                                    		</div>
                                    	</div>
                                    </div>                                   
                                </div>
                                
                                <div class="control-group" ng-show="spkucun">
                                    <label class="control-label">
                                    商品库存：</label>
                                    <div class="controls spkucun">
                                        <table class="table-sku-stock" id='sku_table'>
                                        	<thead>
                                        		<tr>
                                        			<th class="text-center" ng-repeat="thList in Guival" ng-show="thList.length != 0 && thList[0].parent.title">{{thList[0].parent.title}}</th>
                                        			<!--<th class="text-center">尺寸</th>-->
                                        			<th class="th-price">价格（元）</th>
                                        			<th class="th-stock">库存</th>
                                                    <th class="th-code">商家编码</th>
                                                    <!--<th class="th-buyAmount">最小购买量</th>-->
                                        			<th class="th-code" ng-if="setEms.isWeightTel">重量（KG）</th>
                                        			<th class="text-right th-code" style="text-align: center;">销量</th>
                                        		</tr>
                                        	</thead>
											<tbody>
												<tr ng-repeat="item in specs">
													<td rowspan="{{item.rowspan0}}" ng-show="item.is_show== 1 ">{{item.v1}}</td>
													<td ng-show="item.v2" ng-if="item.is_show1 == 1" rowspan="{{item.rowspan1}}">{{item.v2}}</td>
													<td ng-show="item.v3">{{item.v3}}</td>
													<td>
                                        				<input type="text" ng-keyup="jiageC(item.price)" ng-model="item.price" value=""  class="form-control js-price input-mini pNegotiable" name="price_{{$index}}" ng-if="goodsinfo.is_price_negotiable == 0" required ensure-integer/>
                                                        <!-- 当为面议的时候不验证 -->
                                                        <input type="text" ng-keyup="jiageC(item.price)" ng-model="item.price" value=""  class="form-control js-price input-mini pNegotiable" name="price_{{$index}}" ng-if="goodsinfo.is_price_negotiable == 1"/>
                                                        <p class="help-block error-message  ng-hide" ng-show="baseform.price_{{$index}}.$dirty && baseform.price_{{$index}}.$error.required || baseform.price_{{$index}}.$pristine && submitted && baseform.price_{{$index}}.$error.required ">此项不能为空</p>
                                                        <p class="help-block error-message  ng-hide" ng-show="baseform.price_{{$index}}.$dirty && baseform.price_{{$index}}.$error.integer || baseform.price_{{$index}}.$pristine && submitted && baseform.price_{{$index}}.$error.integer ">此项必须为数字</p>
                                                        <!-- @{{baseform.price_{{$index}}.$error.required}} -->
                                        			</td>
                                        			<td>
                                        				<input type="text" value="" ng-keyup="tatolInvebtory(item.stock_num)" ng-model="item.stock_num" class="form-control js-price input-mini" name="stock_num_{{$index}}" ensure-integer/>
                                                        <p class="help-block error-message  ng-hide" ng-show="baseform.stock_num_{{$index}}.$dirty && baseform.stock_num_{{$index}}.$error.integer || baseform.stock_num_{{$index}}.$pristine && submitted && baseform.stock_num_{{$index}}.$error.integer">此项必须为数字</p>
                                        			</td>
                                        			<td>
                                        				<input type="text" value=""  ng-model="item.code" class="form-control js-price input-mini" name="code_{{$index}}"/>
                                        			</td>
                                        			<!--最小购买量-->
                                                    <!-- 重量 -->
                                                    <td ng-if="setEms.isWeightTel">
                                                        <input type="text" value="" ng-model="item.weight" class="form-control js-price input-mini" name="weight_{{$index}}" required ensure-integer ensure-integer1/>
                                                        <p class="help-block error-message pd_120" style="padding-left: 0" ng-show="baseform.weight_{{$index}}.$dirty && baseform.weight_{{$index}}.$error.integer1 || baseform.weight_{{$index}}.$pristine && submitted && baseform.weight_{{$index}}.$error.integer1">此项必须大于0</p>
                                                        <p class="help-block error-message  ng-hide" ng-show="baseform.weight_{{$index}}.$dirty && baseform.weight_{{$index}}.$error.required || baseform.weight_{{$index}}.$pristine && submitted && baseform.weight_{{$index}}.$error.required ">此项不能为空</p>
                                                        <p class="help-block error-message  ng-hide" ng-show="baseform.weight_{{$index}}.$dirty && baseform.weight_{{$index}}.$error.integer || baseform.weight_{{$index}}.$pristine && submitted && baseform.weight_{{$index}}.$error.integer">此项必须为数字</p>
                                                    </td>
                                        			<td class="text-right">
                                                        <input ng-keyup="tatolSellCount(item.sold_num)" type="text" value=""  ng-model="item.sold_num" class="form-control js-price input-mini" name="sold_num_{{$index}}"/>
                                                    </td> 
												</tr>
											</tbody>
                                        	<tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="batch-opts">
                                                            批量设置：
                                                            <span class="js-batch-type" style="display: inline;">
                                                                <a class="js-batch-price" href="javascript:void(0);" ng-click="changeShopPrice()">价格</a>
                                                                &nbsp;&nbsp;
                                                                <a class="js-batch-stock" href="javascript:void(0);" ng-click="changeStock()">库存</a>
                                                                &nbsp;&nbsp;
                                                                <a class="js-batch-stock" href="javascript:void(0);" ng-click="changeSales()">销量</a>
                                                            </span>
                                                            <span class="js-batch-form" style="display: none;">
                                                                <input type="text" class="form-control input-mini" placeholder="请输入价格" maxlength="10">
                                                                <a class="js-batch-save" href="javascript:;" ng-click="savePriceStock()">确定</a>
                                                                <a class="js-batch-cancel" href="javascript:;" ng-click="cancelPriceStock()">取消</a>
                                                                <p class="help-desc"></p>
                                                            </span>
                                                        </div>
                                                        <div class="batch-opts stock_tip">   
                                                            建议在商品规格添加完成后在进行批量设置
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="control-group">
                                    <label class="control-label">
                                        <!--<em class="required">*</em>-->
                                        总库存：
                                    </label>
                                    <div class="controls kucui">
                                        <input id="stockNum" type="text" class="form-control" name="stock" value="" required ng-model="goodsinfo['stock']" ng-disabled="specs.length">
                                        <label class="checkbox inline">
                                            <input type="checkbox" name="stock_show" ng-click='stock_check()' ng-checked="postData.stock_show ==1">
                                            <span>页面不显示商品库存</span>
                                        </label>
                                        <p class="help-desc help-desc-0">总库存为 0 时，会上架到『已售罄的商品』列表里</p>
                                        <p class="help-desc help-desc-1">发布后商品同步更新，以库存数字为准</p>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        总销量：
                                    </label>
                                    <div class="controls kucui">
                                        <input type="text" class="form-control" name="stock" value="" ng-model="goodsinfo['sold_num']" ng-disabled="specs.length">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        商家编码：
                                    </label>
                                    <div class="controls">
                                        <div class="form-group shop_price">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="goods_no" ng-model="postData.goods_no">
                                            </div>
                                        </div>    
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="goods-info-group">
                    <div class="goods-info-group-inner">
                        <div class="info-group-title vbox">
                            <div class="group-inner">商品信息</div>
                        </div>
                        <div class="info-group-cont vbox">
                            <div class="group-inner">
                                <div class="control-group" ng-if="baseinfo['is_wholesale'] == 1">
                                    <!-- 批发价设置 -->
                                    <table class="table table-condensed wholesale-table">
                                        <thead>
                                        <tr>
                                            <th>批发价设置：</td>
                                            <th>批发件数</td>
                                            <th>批发单价（元）</td>
                                            <th><input type="button" class="btn add-sale" value="添加批发价" ng-click="addWholesale($event)"/></td>
                                        </tr>
                                        </thead>
                                        <tr ng-repeat="note in goodsinfo['wholesale_array']">
                                            <td></td>
                                            <td><input type="number" ng-model="note['min']" ensure-int>至<input type="number" ng-model="note['max']" ensure-int></td>
                                            <td><input type="number" ng-model="note['price']"></td>
                                            <td>
                                            <a href="javascript:void(0);" class="delete_wholesale" ng-show="$index>=1" ng-click="deleteWholesale($index)"><img src="{{_host}}" alt=""></a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        <em class="required">*</em>
                                        商品名：
                                    </label>
                                    <div class="controls good_name">
                                        <input type="text" class="form-control" name="shop_title" value="" ng-model="goodsinfo['title']" required>
                                            <!-- <a class="js-refresh-tag" href="javascript:;">快速导入淘宝商品信息</a> -->
                                        </p>
                                    </div>
                                    <p class="help-block error-message pd_120 ng-hide" ng-show="baseform.shop_title.$dirty && baseform.shop_title.$error.required || baseform.shop_title.$pristine && submitted && baseform.shop_title.$error.required">商品名不能为空</p>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        <em class="required">*</em>
                                        价格：
                                    </label>
                                    <div class="controls">
                                        <div class="form-group shop_price">
                                            <div class="input-group">
                                                <div class="input-group-addon">￥</div>
                                                <input class="form-control s_price" type="text" value="" ng-disabled="spkucun" ng-model="goodsinfo['price']" name="price" ng-keyup="changePrice()" required>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control shop_input" name="" placeholder="" ng-model="goodsinfo['oprice']">
                                        <input type="text" class="form-control shop_input" name="" placeholder="成本价：￥0.00" ng-model="goodsinfo['cost_price']">
                                        <div class='clearfix' style='width:610px'>
                                        	<i style="display:inline-block; margin-left: 40px; color: #999; float: left;"><i style="display: inline-block; font-weight: bold; font-size: 14px; vertical-align: middle; color: red;">*</i>出售价</i>
                                        	<i style="display:inline-block; margin-left: 128px; color: #999; float: left;"><i style="display: inline-block; font-weight: bold; font-size: 14px; vertical-align: middle; color: red;">*</i>市场价</i>
                                        	<i style="display:inline-block; margin-left: 151px; color: #999; float: left;"><i style="display: inline-block; font-weight: bold; font-size: 14px; vertical-align: middle; color: red;">*</i>不会显示在移动端，仅用于成本分析</i>
                                        </div>
                                        <p class="help-block error-message price_error ng-hide" ng-show="baseform.price.$dirty && baseform.price.$error.required || baseform.price.$pristine && (submitted && baseform.price.$dirty && baseform.price.$invalid)">价格不能为空</p>    
                                    </div>
                                </div>
                                <!--add by 邓钊 2018-7-13 价格面议-->
                                <div class="control-group">
                                    <label class="control-label">
                                        <em class="required">*</em>
                                        面议开关：
                                    </label>
                                    <div class="controls price_discuss">
                                        <div>
                                            <input name='discuss' class="priceNegotiable mar2 btn_disabled" type="radio" ng-click='priceNegotiable()' ng-checked="goodsinfo.is_price_negotiable == 1"/>
                                            开启
                                            <input name='discuss' class="priceNegotiable mar2 btn_disabled discuss_input" type="radio" ng-click='priceNegotiableClose()' ng-checked="goodsinfo.is_price_negotiable == 0"/>
                                            关闭
                                            <span class='discuss_chose'>默认关闭</span>
                                        </div>
                                        <div class='discuss_cont' ng-if="goodsinfo.is_price_negotiable == 1">
                                            <div class="div_flex discuss_div">
                                                <input name='discuss_price' class="priceNegotiable mar2 btn_disabled" type="radio" ng-click='negotiableType(0)' ng-checked="goodsinfo.negotiable_type == 0"/>
                                                <span>&nbsp;价格面议</span>
                                            </div>
                                            <div class="div_flex discuss_div">
                                                <input name='discuss_price' class="priceNegotiable mar2 btn_disabled" type="radio" ng-click='negotiableType(1)' ng-checked="goodsinfo.negotiable_type == 1"/>
                                                <span>&nbsp;咨询电话</span>
                                                <input class='discuss_div_input' type="text" ng-model='negotiable[1].value' ng-keyUp='negotiableValue(negotiable[1].value)'>
                                            </div>
                                            <div class="div_flex discuss_div">
                                                <input name='discuss_price' class="priceNegotiable mar2 btn_disabled" type="radio" ng-click='negotiableType(2)' ng-checked="goodsinfo.negotiable_type == 2"/>
                                                <span>&nbsp;咨询微信</span>
                                                <input class='discuss_div_input' type="text" ng-model='negotiable[2].value' ng-keyUp='negotiableValue(negotiable[2].value)'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end-->
                                <div class="js-pre-sale-wrap" style="display: block;">
                                    <div class="control-group">
                                        <label class="control-label">
                                            <em class="required">*</em>
                                            商品图：
                                        </label>
                                        <div class="controls">
                                            <div class="picture-list ui-sortable">
                                                <ul class="js-picture-list app-image-list clearfix">
                                                    <li class="sort" ng-repeat="image in goodsinfo.img track by $index" ng-mouseover="showDelete(image,$index)" ng-mouseout="hideDelete(image)"  ng-drop="true" ng-drop-success="onDropShopImageComplete($index, $data,$event)" ng-drag="true" ng-drag-data="image">
                                                        <img ng-src="{{image['FileInfo']['path']}}" class="js-img-preview">
                                                        <a class="js-delete-picture close-modal small ng-hide" ng-show="image['close']" ng-click="removeImage($index)">×</a>
                                                    </li>
                                                    <li class="js-picture-add-li" ng-click="addImages()">
                                                        <a href="javascript:;" class="add-goods js-add-picture">+添加图片</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <p class="help-desc">
                                                750 x 750 像素，大小不超过3M。
                                            </p>
                                            <p class="help-block error-message ng-hide" ng-show="is_post && goodsinfo.img.length==0">
                                                商品图片最少为一张
                                            </p>
                                        </div>
                                        <div class="js-buy-url-group control-group ng-hide" ng-show="baseinfo['buy_way']==2">
                                            <label class="control-label">
                                                <em class="required">*</em>外部购买地址：
                                            </label>
                                            <div class="controls link_wai">
                                                <input type="text" name="out_buy_link" value="" class="form-control" placeholder="http://" ng-model="goodsinfo['out_buy_link']">
                                                <a style="display: none;" href="javascript:void(0);" class="js-help-notes circle-help">?</a>
                                                <p class="help-block error-message ng-hide" ng-show="waiLink_show">外链不能为空</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group js-pre-sale-item" style="margin-left: 86px; display: none;">
                                        <label class="control-label">预售结束时间</label>
                                        <div class="controls">
                                            <div class="input-append">
                                                <input type="text" id="pre_sale_end" name="pre_sale_end" value="" style="width: 130px;" class="hasDatepicker">
                                                <label for="pre_sale_end" class="add-on">
                                                    <i class="icon-calendar"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group js-pre-sale-item" style="margin-left: 86px; display: none;">
                                        <label class="control-label">预计发货时间</label>
                                        <div class="controls">
                                            <div class="input-append">
                                                <input type="text" class="input-small hasDatepicker" id="etd_start" name="etd_start" value="">
                                                <label for="etd_start" class="add-on">
                                                    <i class="icon-calendar"></i>
                                                </label>
                                            </div>至
                                            <div class="input-append">
                                                <input type="text" class="input-small hasDatepicker" id="etd_end" name="etd_end" value="">
                                                <label for="etd_end" class="add-on">
                                                    <i class="icon-calendar"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="c-gray ui-box js-pre-sale-item" style="margin-left: 114px; display: none;">注意：目前预售设置仅仅用于商品详情展示，不会关联到订单的处理流程，预售结束，商品也不会自动下架， 请务必按照约定时间发货以免引起客户投诉。
                                        <a href="javascript:void(0);" target="_blank" class="new-window">帮助</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="goods-info-group" ng-show="fxVisible['fx_flag']">
                	<div class="goods-info-group-inner">
                		<div class="info-group-title vbox">
                            <div class="group-inner">分销设置</div>
                        </div>
                        <div class="info-group-cont vbox">
                        	<div class="group-inner">
                        		<div class="control-group">
                        			<label class="control-label">
                                        是否开启分销：
                                    </label>
                                    <div class="controls overhidden">
                                    	<div class="show_dis overhidden patop2">
                                    		<label class="div_flex">
	                                    		<input name="setFenxiao" type="radio" ng-value="1" ng-click="judgeHexiao()" ng-model="setFenxiao['fenxiao_flag']" />
	                                    		<span>是</span>
	                                    	</label>
	                                    	<label class="div_flex">
	                                    		<input name="setFenxiao" type="radio" ng-value="0" ng-model="setFenxiao['fenxiao_flag']" />
	                                    		<span>否</span>
	                                    	</label>
                                    	</div>
                                    </div>
                        		</div>
                        		<div class="control-group" ng-show="setFenxiao['fenxiao_flag']">
                        			<label class="control-label">
                                        分销模板：
                                    </label>
                                    <div class="controls">
                                    	<div class="level changef patop7">
                                    		<span class="level f_level">{{fxTitle}}</span>
                                    		<a ng-click="fenxiao()" href="javascript:;">【更换】</a>
                                    	</div>
                                    </div>
                        		</div>
                        		<div class="control-group" ng-show="setFenxiao['fenxiao_flag']">
                        			<label class="control-label">
                                        佣金设置：
                                    </label>
                                    <div class="controls">
                                    	<ul class="comset">
                                    		<li>
                                    			<p>商品价格</p>
                                    			<div>{{goodsinfo['price']}}</div>
                                    		</li>
                                    		<li>
                                    			<p>商品成本</p>
                                    			<div>{{goodsinfo['price']*(goodsinfo['cost']/100)|number:2}}</div>
                                    		</li>
                                            <!-- <li>
                                                <p>本级佣金</p>
                                                <div>{{goodsinfo['price']*(goodsinfo['zero']/100)|number:2}}</div>
                                            </li> -->
                                    		<li>
                                    			<p>一级佣金</p>
                                    			<div>{{goodsinfo['price']*(goodsinfo['one']/100)|number:2}}</div>
                                    		</li>
                                    		<li>
                                    			<p>二级佣金</p>
                                    			<div>{{goodsinfo['price']*(goodsinfo['sec']/100)|number:2}}</div>
                                    		</li>
                                    	</ul>
                                    </div>
                                    <p class="comsettip">如果商品有多个sku的售价不一致，则按相应比例换算成佣金金额</p>
                        		</div>
                        	</div>
                        </div>
                	</div>
                </div>

                <!--物流模块-->
                <div class="goods-info-group ng-hide" ng-show="baseinfo['buy_way']==1">
                    <div class="goods-info-group-inner">
                        <div class="info-group-title vbox">
                            <div class="group-inner">物流/其它</div>
                        </div>
                        <div class="info-group-cont vbox">
                            <div class="group-inner">
                                <div class="control-group margin_0" ng-show="baseinfo['is_card']==0">
                                    <label class="control-label">
                                        <em class="required">*</em>
                                        物流设置：
                                    </label>
                                    <form method="post">
                                    	<!--add by 韩瑜 date 2018-7-18 添加纯自提商品-->
	                                    <label class="radio inline" style="margin-left: 16px;">
	                                            <input type="radio" class="radio_top7" name="is_logistics" ng-model="baseinfo.is_logistics" ng-click="clickwuliu()" id="is_group" ng-value="1" >
	                                            <span>物流</span>
	                                    </label>
	                                    <label class="radio inline">
	                                            <input type="radio" class="radio_top7" name="is_logistics" ng-model="baseinfo.is_logistics" ng-click="wuxuwuliu()" id="no_group" ng-value="0">
	                                            <span>无需物流</span>
	                                            <span class="ziti_tip">请开启自提总开关，并添加自提点，否则会下单会报错</span>
	                                    </label>
	                                    <!--end-->
                                    </form>
                                    <div class="controls martop_20" ng-class="zitihide" >
                                        <label class="radio inline ems div_flex">
                                            <input type="radio" class="radio_top7" name="shop_method" value="1" ng-model="setEms.freight_type" ng-checked="setEms.freight_type==1">
                                            	<span>统一运费</span>
                                        </label>
                                        <div class="form-group ems_price">
                                            <div class="input-group">
                                              <div class="input-group-addon">￥</div>
                                              <input class="form-control" class="radio_top7" type="text" value="0.00" ng-model="setEms.freight_price">
                                            </div>
                                        </div>     
                                    </div>
                                </div>
                                <div class="control-group" ng-show="baseinfo['is_card']==0">
                                    <div class="controls ems_other" ng-class="zitihide" >
                                        <label class="radio inline ems">
                                            <input type="radio" name="shop_method" value="2" ng-model="setEms.freight_type" ng-checked="setEms.freight_type==2">运费模板
                                        </label>
                                        <div class="form-group ems_price">
                                            <select class="form-control" ng-model="postData.freight_id">
                                                <option value="" ng-selected="postData.freight_id==0">请选择</option>
                                                <option value="{{item.id}}" ng-repeat="item in setEms.freight_id" ng-bind="item.title" ng-selected="postData.freight_id==item.id">
                                                </option>
                                            </select>
                                        </div>
                                        <p class="help-inline set_model">
                                            <!--<a class="js-refresh-tag" href="javascript:;">刷新</a>
                                            <span>|</span>-->
                                            <a class="new-window" target="_blank" href="/merchants/currency/expressSet">新建</a>
                                            <span>|</span>
                                            <a class="new-window" target="_blank" href="https://www.huisou.cn/home/index/helpDetail/720">如何设置合适的运费模板</a>
                                        </p>
                                    </div>
                                </div>
                                <!-- 无规格商品重量选择开始 --> 
                                <div class="controls control-group" ng-if="setEms.isWeightTel && specs.length==0">
                                    <label class="control-label" style="text-align: left;width: 80px;"><em class="required">*</em>商品重量：</label>
                                    <div >
                                        <input type="text" class="form-control singleWeight" name="weight" ng-model="setEms.weight" required ensure-integer ensure-integer1>KG
                                    </div>

                                    <p class="help-block error-message pd_120" style="padding-left: 0" ng-show="baseform.weight.$dirty && baseform.weight.$error.integer1 || baseform.weight.$pristine && submitted && baseform.weight.$error.integer1">重量必须大于0</p>
                                    <p class="help-block error-message pd_120" ng-show="baseform.weight.$dirty && baseform.weight.$error.required || baseform.weight.$pristine && submitted" style="padding-left: 0">商品重量不能为空</p>
                                    <p class="help-block error-message  ng-hide" ng-show="baseform.weight.$dirty && baseform.weight.$error.integer || baseform.weight.$pristine && submitted && baseform.weight.$error.integer ">此项必须为数字</p>
                                </div>
                                <!-- 无规格商品重量选择结束 -->
                                <div class="control-group">
                                    <label class="control-label">每人限购：</label>
                                    <div class="controls">
                                        <input type="text" class="form-control forbidden_buy js_control-input js-control-num" placeholder="0代表不限购" ng-model="setEms['quota']">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">最小购买量：</label>
                                    <div class="controls">
                                        <input type="text" class="form-control forbidden_buy js_control-input js-buy-min" ng-model="setEms['buy_min']">
                                    </div>
                                </div>
                                <div class="control-group" style="display: none;">
                                   <label class="control-label">
                                        购买权限：
                                    </label>
                                    <div class="controls">
                                        <label class="radio inline buy_control">
                                            <input type="checkbox" name="shop_method" value="1" checked="" ng-model="setEms['buy_permissions_flag']" ng-checked="setEms['buy_permissions_flag']==1">设置购买权限
                                        </label>
                                        <div class="js-purchase-right-setting ng-hide" ng-show="setEms['buy_permissions_flag']">
                                            <label class="checkbox mt10">
                                                <span>请选择可购买该商品的会员身份</span>
                                                <div class="js-member-level">
                                                    <div class="ui-tag">
                                                        <select data-placeholder="请选择会员身份" class="form-control" id="member_level" multiple ng-model="setEms.buy_permissions_level_id">
                                                            <option> </option>
                                                            <option ng-repeat="item in setEms.buy_permissions_level" value="{{item['id']}}" ng-bind="item.title"></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </label>
                                            <label class="nochoose">
                                                <span>以上均不选择，则默认为所有用户</span>
                                            </label>
                                        </div>   
                                    </div>
                                </div>
                                <div class="control-group" style="display: block;">
                                    <label class="control-label">
                                        要求留言： 
                                    </label>
                                    <div class="controls">
                                        <div class="notes" ng-repeat="note in setEms['noteList']">
                                            <input type="text" class="form-control" name="note_{{$index}}" ng-model="note['title']" required ensure-unique>
                                            <select class="form-control log-select" ng-model="note['type']" ng-change="changeInputValue()" ng-init = "note['type'] = note['type'] ? note['type']:'0' ">
                                                <option value="0" ng-selected="note['type']==0">文本格式</option>
                                                <option value="1" ng-selected="note['type']==1">数字格式</option>
                                                <option value="2" ng-selected="note['type']==2">邮箱格式</option>
                                                <option value="5" ng-selected="note['type']==5">身份证号码</option>
                                                <option value="6" ng-selected="note['type']==6">图片</option>
                                                <option value="7" ng-selected="note['type']==7">手机格式</option>
                                            </select>
                                            <input type="checkbox" name="bt-check" ng-checked="note.required==1" ng-model="note.required" value="1">必填
                                            <a href="javascript:void(0);" ng-click="removeNote($index)">删除</a>
                                            <p class="help-block error-message ng-hide" ng-show="baseform.note_{{$index}}.$dirty && baseform.note_{{$index}}.$error.required">字段不能为空</p>
                                            <p class="help-block error-message ng-hide" ng-show="baseform.note_{{$index}}.$dirty && baseform.note_{{$index}}.$error.unique">
                                                此字段不能和其他重复
                                            </p>
                                        </div>
                                        <a href="javascript:;" class="js-add-message control-action" ng-click="addNote()">+ 添加字段</a>
                                        <p class="help-desc">
                                            单件商品最多可设置10条留言
                                            <font style="display: block;color:red;margin-top: 5px;">(注:留言只针对拼团商品有效)</font>
                                        </p>
                                    </div> 
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        开售时间： 
                                    </label>
                                    <div class="controls">
                                        <div class="sell_time">
                                            <label class="div_flex patop2">
                                                <input type="radio" value="1" name="sell_time_choose" ng-model="setEms['sale_time_flag']" ng-checked="setEms['sale_time_flag']==1" ng-change="sellStyle()">
                                                	<span>立即开售</span>
                                            </label>
                                            <div class="setime patop7">
                                                <label class="div_flex">
                                                    <input type="radio" name="sell_time_choose" value="2" ng-model="setEms['sale_time_flag']" ng-checked="setEms['sale_time_flag']==2"
                                                    ng-change="sellStyle()">
                                                    <span>定时开售</span>
                                                </label>
                                                <div class="input-group date" >
                                                    <input type="text" class="form-control" name="" id="datetimepicker" ng-model="setEms['sale_time']" style="border-radius: 5px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group" ng-show="discount_show != 0">
                                    <label class="control-label">
                                        会员折扣： 
                                    </label>
                                    <div class="controls">
                                        <div class="discount">
                                            <label class="div_flex">
                                                <input type="checkbox" name="" ng-checked="setEms['is_discount']==1">
                                                <span>参加会员折扣</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        是否开启积分：
                                    </label>
                                    <div class="controls">
                                        <div class="show_dis patop5">
                                            <label>
                                                <input name="is_point" type="radio" ng-value="1" ng-model="setEms['is_point']" ng-checked="setEms['is_point'] == 1"/>
                                                <span>是</span>
                                            </label>
                                            <label>
                                                <input name="is_point" type="radio" ng-value="0" ng-model="setEms['is_point']" ng-checked="setEms['is_point'] == 0"/>
                                                <span>否</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="goods-info-group">
                    <div class="goods-info-group-inner">
                        <div class="info-group-title vbox">
                            <div class="group-inner">商品分享设置</div>
                        </div>
                        <div class="info-group-cont vbox">
                            <div class="group-inner">
                                <div class="control-group">
                                    <label class="control-label">
                                        分享标题设置：
                                    </label>
                                    <div class="controls">
                                        <div class="show_dis">
                                            <label>
                                                <input name="share_title" type="text" size="30" style="border-radius: 4px;" class="form-control"  ng-model="postData.share_title"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        分享内容设置：
                                    </label>
                                    <div class="controls">
                                        <div class="level changef">
                                            <textarea cols="50" rows="10" style="border-radius: 4px;" name="share_desc" class="form-control" ng-model="postData.share_desc"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">
                                        分享页图片：
                                    </label>
                                    <div class="controls" style="position: relative; top:7px">
                                        <div class="picture-list ui-sortable">
                                            <ul class="js-picture-list app-image-list clearfix">
                                                <li class="sort" ng-show="postData.share_img">
                                                    <img ng-src="{{postData.share_img}}" class="js-img-preview">
                                                    <a class="js-delete-picture close-modal small"  ng-click="removeShareImage()">×</a>
                                                    <input name="share_img" style="display: none" type="text" ng-model='postData.share_img'>
                                                </li>
                                                <a href="javascript:;" class="js-add-picture" ng-class="{true:'add-goods_true',false:'add-goods_false'}[classShow]" ng-click="addShareImages()">{{addPic}}</a>
                                                
                                            </ul>
                                            <p class="share-desc">
                                                建议尺寸：750 x 750 像素，大小不超过3M。
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-actions">
                <div class="form-actions text-center" >
                    <a class="zent-btn zent-btn-primary js-switch-step" ng-click="goNext(baseform.$valid)">下一步</a>
                </div>
            </div>
        </form>
        <form class="form-horizontal fm-goods-info card ng-hide" ng-show="step==2" name="editorForm">
            <div class="card_left">
                <div class="left_content">
                    <h1>
                        <span ng-bind="editors[0]['title']"></span>
                    </h1>
                </div>
                <div class="app-entry">
                    <div class="js-fields-region">
                        <div class="app-fields ui-sortable">
                            <div class="goods-details-block">
                                <h4>基本信息区</h4>
                                <p>固定样式，显示商品主图、价格等信息</p>
                            </div>
                            <div class="app-field clearfix {{editor['editing']}}" data-type="{{editor['type']}}" ng-repeat = 'editor in editors ' style="background:{{editor['bgcolor']}}" ng-click="tool($event,editor)" ng-mouseover="addboder($event)" ng-mouseout="removeboder($event,editor)" ng-drop="true" ng-drop-success="onDropPageComplete($index, $data,$event)">
                                <div class="js-config-region" ng-if="editor['type'] == 'shop_detail'">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <div class="ueContent" style="background: #fff;" ng-if="editor.content" ng-bind-html="editor.content | to_trusted">
                                            
                                        </div>
                                        <div class="ueContent" style="background: #fff;" ng-else>
                                            <h4 ng-show="!editor.content" style="text-align: center;margin:20px 0 8px;">商品详情区</h4>
                                            <p ng-show="!editor.content" style="text-align: center;margin-bottom: 10px;">点击进行编辑</p>
                                        </div>
                                    </div>
                                    <div class="actions">
                                        <div class="actions-wrap">
                                            <span class="action edit">编辑</span>
                                        </div>
                                    </div>
                                </div>
                                <div ng-drag="true" ng-drag-data="editor">
                                    <!-- 优化券 -->
                                    <coupon ng-if="editor['type'] == 'coupon'"></coupon>
                                    <!-- 会员中心默认 -->
                                    <member ng-if="editor['type'] == 'member'"></member>
                                    <!-- 编辑器框内容 -->
                                    <editor-text ng-show = "editor['type'] == 'rich_text'" class="custom-richtext"></editor-text>
                                    <!-- 商品添加 -->
                                    <goods ng-if = "editor['type'] == 'goods'"></goods>
                                    <!-- 广告添加 -->
                                    <advs ng-if = "editor['type'] == 'image_ad'"></advs>
                                    <!-- 标题添加 -->
                                    <add-title ng-if = "editor['type'] == 'title'"></add-title>
                                    <!-- 店铺导航 -->
                                    <shop ng-if = "editor['type'] == 'store'"></shop>
                                    <!-- 公告 -->
                                    <notice ng-if="editor['type'] == 'notice'"></notice>
                                    <!-- 商品搜索 -->
                                    <search ng-if="editor['type'] == 'search'"></search>
                                    <!-- 商品列表 -->
                                    <goodslist ng-if = "editor['type'] == 'goodslist'"></goodslist>
                                    <!-- 自定义model -->
                                    <model ng-if = "editor['type'] == 'model'"></model>
                                    <!-- 商品分组 -->
                                    <goodgroup ng-if="editor['type'] == 'good_group'"></goodgroup>
                                    <!-- 图片导航 -->
                                    <imagelink ng-if="editor['type'] == 'image_link'"></imagelink>
                                    <!-- 文本导航 -->
                                    <textlink ng-if="editor['type'] == 'textlink'"></textlink>
                                    <!-- 视频 -->
                                    <cvideo ng-if="editor['type'] == 'video'"></cvideo>
                                    <div class="actions" ng-hide="$index == 0">
                                        <div class="actions-wrap">
                                            <span class="action edit">编辑</span>
                                            <span class="action edit" ng-click="addContent($event,$index,editor,25)">加内容</span>
                                            <span ng-click="deleteAll($index)" class="action delete">删除</span>
                                        </div>
                                    </div>
                                </div>
                            </div>                               
                        </div>
                    </div>
                </div>
                <!-- 底部自定义导航 -->
                <div class="js-add-region">
                    <div>
                        <div class="app-add-field">
                            <h4>添加内容</h4>
                            <p style='font-size: 12px;color: #ff954d;margin-bottom: 10px;'>注：以下组件除"图片广告"、"视频"外，其余组件在小程序商品详情中暂不显示</p>
                            <ul>
                                <li ng-click="addeditor(1)">
                                    <a class="js-new-field" data-field-type="rich_text">富文本</a>
                                </li>
                                <li ng-click="addgoods(1)">
                                    <a class="js-new-field" data-field-type="goods">商品</a>
                                </li>
                                <li ng-click="addAdvImages(1)">
                                    <a class="js-new-field" data-field-type="image_ad">图片
                                        <br>广告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addTitle(1)">标题</a>
                                </li>
                                <li ng-click="addShop(1)">
                                    <a class="js-new-field" data-field-type="store">
                                        进入<br>店铺
                                    </a>
                                </li>
                               <!--  <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addCoupon()">优惠券</a>
                                </li> -->
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addNotice(1)">公告</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="title" ng-click="addSearch(1)">商品<br />搜索</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="goddslist" ng-click="addGoodsList(1)">商品<br />列表</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="model" ng-click="addModel(1)">自定义<br />模块</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(1)">商品<br />分组</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="image_link" ng-click="addLinkImages(1)">图片<br />导航</a>
                                </li>
                                <li>
                                    <a class="js-new-field" data-field-type="image_link" ng-click="addtextLink(1)">文本<br />导航</a>
                                </li>
                                <li>
                                    <a class="js-new-field" ng-click="addVideo(1)">视频</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card_right card_right_list" ng-show="editors[index]['showRight']">
                <div class="arrow"></div>
                <div ng-show="!editors[index]['is_add_content']">
                    <div class="shop_detail" ng-show="editors[index]['cardRight'] == 17">
                        <div class="app-sidebar-inner goods-sidebar-goods-template js-goods-sidebar-sub-title">
                        </div>
                        <div class="app-sidebar-inner goods-sidebar-sub-title js-goods-sidebar-sub-title">
                            <p class="">商品简介(选填，微信分享给好友时会显示这里的文案)</p>
                            <textarea rows="2" ng-model="productIntro" class="js-sub-title input-sub-title form-control"></textarea>
                        </div>
                        <textarea id="category_editor" style="width:100%;height:400px"></textarea>
                    </div>
                    <cr-member ng-if="editors[index]['cardRight'] == 1"></cr-member>
                    <!-- 富文本编辑器右侧 -->
                    <cr-richtext ng-show="editors[index]['cardRight'] == 3"></cr-richtext>
                    <!-- 商品右侧 -->
                    <cr-goods class="goods" ng-if="editors[index]['cardRight'] == 4"></cr-goods>
                    <!-- 广告右侧 -->
                    <cradvs class="advs" ng-if="editors[index]['cardRight'] == 5"></cradvs>
                    <!-- 标题右侧 -->
                    <crtitle class="crtitle" ng-if="editors[index]['cardRight'] == 6"></crtitle>
                    <!-- 店铺导航右侧 -->
                    <crshop ng-if="editors[index]['cardRight'] == 7"></crshop> 
                    <!-- 优惠券右侧 -->
                    <crcoupon ng-if="editors[index]['cardRight'] == 8"></crcoupon>
                    <!-- 公告右侧 -->
                    <crnotice class="crnotice" ng-if="editors[index]['cardRight'] == 9"></crnotice>
                    <!-- 商品搜索右侧 -->
                    <crsearch class="crsearch" ng-if="editors[index]['cardRight'] == 11"></crsearch>
                    <!-- 商品列表右侧 -->
                    <crgoodslist class="crgoodslist" ng-if="editors[index]['cardRight'] == 12"></crgoodslist>
                    <!-- 自定义模块右侧 -->
                    <crmodel class="crmodel" ng-if="editors[index]['cardRight'] == 13"></crmodel>
                    <!-- 商品分组 -->
                    <crgoodgroup class="crgoodgroup" ng-if="editors[index]['cardRight'] == 14"></crgoodgroup>
                    <!-- 图片链接右侧 -->
                    <crimagelink class="crimagelink" ng-if="editors[index]['cardRight'] == 15"></crimagelink>
                     <!-- 文本导航右侧 -->
                    <crtextlink class="crtextlink" ng-if="editors[index]['cardRight'] == 16"></crtextlink>
                    <!-- 视频右侧 -->
                    <crvideo ng-show="editors[index]['cardRight'] == 24"></crvideo>
                </div>
                <div class="app-add-field app-add-field1" ng-show="editors[index]['is_add_content']">
                    <h4>添加内容</h4>
                    <ul>
                        <li ng-click="addeditor(2)">
                            <a class="js-new-field" data-field-type="rich_text">富文本</a>
                        </li>
                        <li ng-click="addgoods(2)">
                            <a class="js-new-field" data-field-type="goods">商品</a>
                        </li>
                        <li ng-click="addAdvImages(2)">
                            <a class="js-new-field" data-field-type="image_ad">图片
                                <br>广告</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="title" ng-click="addTitle(2)">标题</a>
                        </li>
                        <li ng-click="addShop(2)">
                            <a class="js-new-field" data-field-type="store">
                                进入<br>店铺
                            </a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="coupon" ng-click="addCoupon(2)">优惠券</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="notice" ng-click="addNotice(2)">公告</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="goods" ng-click="addSearch(2)">商品<br />搜索</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="goddslist" ng-click="addGoodsList(2)">商品<br />列表</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="model" ng-click="addModel(2)">自定义<br />模块</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="good_group" ng-click="addGoodGroup(2)">商品<br />分组</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addLinkImages(2)">图片<br />导航</a>
                        </li>
                        <li>
                            <a class="js-new-field" data-field-type="image_link" ng-click="addtextLink(2)">文本<br />导航</a>
                        </li>
                        <li>
                            <a class="js-new-field" ng-click="addVideo(2)">视频</a>
                        </li>
                    </ul>
                </div>              
            </div>
            <div class="clear"></div>
            <div class="app-actions">
                <div class="form-actions text-center">
                    <button class="zent-btn" ng-click="prev()">上一步</button>
                    <button class="zent-btn zent-btn-primary js-switch-step" ng-click="getForm(baseform.$valid,1)">上架</button>
                    <button class="zent-btn js-switch-step" ng-click="getForm(baseform.$valid,0)">下架</button>
                    <!-- <button class="zent-btn" ng-click="preview()">预览</button> -->
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal export-modal" id="myModal">
    <div class="modal-dialog" id="modal-dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#" class="js-modal-tab">已上架商品</a>
                            <span>|</span>
                        </li>
                        <li style="display: none;">
                            <a href="#" class="js-modal-tab">商品分组</a>
                            <span>|</span>
                        </li>
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/merchants/product/create" target="_blank" class="new_window">新建商品</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                            <tr>
                                <th>
                                    <div class="td-cont">
                                        <span>标题 </span>
                                    </div>
                                </th>
                                <th class="information"></th>
                                <th>
                                    <div class="td-cont">
                                        <span>创建时间</span>
                                    </div>
                                </th>
                                <th class="opts">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle">
                                                <a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchGoods()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat = "list in goodList" id="list_{{list.id}}" data-price="{{list['price']}}">
                                <td class="image">
                                    <div class="td-cont">
                                        <img src="{{list['thumbnail']}}">
                                    </div>
                                </td>
                                <td class="title">
                                    <div class="td-cont">
                                        <a target="_blank" class="new_window" href="javascript:void(0);">{{list['name']}}</a>

                                    </div>
                                </td>
                                <td>
                                    <span>
                                        {{list['timeDay']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_{{list.id}}" href="javascript:void(0);" ng-click="choose($index,list)">选取</button> 
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseSure()">
                        <input type="button" class="btn btn-primary" value="确定使用">
                    </div>
                    <div class="good_pagenavi"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!--弹框-->
<div class="ui-popover top-center" id="changeTitleProver" ng-style="tcStyle">
    <div class="ui-popover-inner">
        <span></span>
        <ul id="guigeSearch" class="guige-search-choice1">
        	<li class="guige-search-choice2 guige-search-choice2f" ng-repeat="guigeval in Guigeval">
	        	<p>{{guigeval.val}}</p>
	        	<a ng-click="guigecancle($index)">x</a>
	        </li>
	        <li class="guige-search-choice2f" style="width: 100%;">
	        	<input class="form-control" ng-model="inpGuigeval" ng-click="addContInp($event)" ng-keyup="guigeEnter($event)" type="text" style="margin-bottom: 0;border: none;" id="spec_input">
	        </li>
	        
        </ul>
        <a href="javascript:void(0);" class="zent-btn zent-btn-primary js-save guibtn" ng-click="tc_sure()">确定</a>
        <a href="javascript:void(0);" class="zent-btn js-cancel guibtn" ng-click="tc_cancle()">取消</a>
    </div>
    <div class="arrow"></div>
</div>
<div class="select2-drop select2-drop-multi select2-display-none select2-drop-active" ng-style="tcSelStyle">
	<ul class="select2-results">
		<li class="select2-results-dept-0 select2-result select2-result-selectable" ng-click="addContList(ListSel,ListSel.title)" ng-repeat="ListSel in tcSelS" ng-class="{'select2-highlighted':selVal2}" ng-mouseenter="selVal2 = true" ng-mouseleave="selVal2 = false">
			<div class="select2-result-label">
				<span class="select2-match"></span>
				{{ListSel.title}}
			</div>
		</li>
	</ul>
</div>

@endverbatim
@endsection
@section('other')
<!-- 广告图片model -->
<div class="modal export-modal myModal-adv" id="myModal-adv">
    <div class="modal-dialog" id="modal-dialog-adv">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul class="module-nav modal-tab">
                        <li ng-show="uploadShow">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab"
                               ng-show="uploadShow" ng-click="showImage()">< 选择图片 |</a>
                        </li>
                        <li class="modal-tab-a" ng-show="!uploadShow">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">我的图片</a>
                        </li>
                        <li class="active" ng-show="uploadShow">
                            <a href="#js-module-tag" data-type="tag" class="js-modal-tab">上传图片</a>
                        </li>
                    </ul>
                    
                </div>
                <div class="modal-body" ng-show="!uploadShow">
                    <div class="category-list-region js-category-list-region" >
                        <ul class="category-list">
                            <li class="js-category-item" ng-class="{true :'active', false :''}[grounp.isactive]"  ng-repeat="grounp in grounps" ng-click="chooseGroup(grounp)"> @{{grounp['name']}}
                                <span class="category-num" ng-bind="grounp['number']"></span>
                            </li>
                        </ul>
                        <!-- 添加分组 by 崔源 2018.10.17 -->
                        <div class='add_group'>
                                <div class="add_group_list" data-id='1'>+添加分组</div>
                                <div class="add_group_box hide">
                                    <div class='add_group_title'>添加分组</div>
                                    <input class='add_group_input' placeholder='不超过6个字' type="text" maxlength='6'>
                                    <div class='clearfix add_group_btn'>
                                        <div class="btn_left">确定</div>
                                        <div class="btn_right">取消</div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="attachment-list-region js-attachment-list-region">
                        <!-- 搜索框 by 崔源 2018.10.17  -->
                        <!-- <div class="search-region" style="display:none">
                            <div class="ui-search-box">
                                <input class="txt js-search-input search_input" type="text" placeholder="搜索" value="">
                            </div>
                        </div> -->
                    <div class="imgData" >
                        <ul class="image-list"  ng-show="picNumber">
                            <li class="image-item js-image-item" data-id="701007915" ng-repeat="image in uploadImages" ng-click="chooseImage(image,$index)">
                                <div class="image-box" style="background-image: url(@{{image['FileInfo']['path']}})"></div>
                                <div class="image-title">@{{image['FileInfo']['name']}}
                                </div>
                                <div class="attachment-selected" ng-show="image['isShow']">
                                    <i class="icon-ok icon-white"></i>
                                </div>
                            </li>
                        </ul>
                        <div class="attachment-pagination js-attachment-pagination">
                                <div class="ui-pagination">
                                       <span class="ui-pagination-total">共8条， 每页15条</span>
                                 </div>
                             </div>
                          <a href="javascript:;" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 200px; bottom: 16px;" ng-click="upload()" ng-show="picNumber">上传图片</a>
                    </div>
                     <!--列表中的图片个数为0的时候显示这个模态框-->
                        <div id="layerContent_right" class="no" ng-show="!picNumber" >
                                 <a class="js_addImg" href="#uploadImg" ng-click="upload()">+</a>
                                 <p>暂无数据，点击添加</p>
                         </div>

                    </div>
                </div>
                <div class="modal-body" ng-show="uploadShow">
                    <div id="container">
                        <!--头部，相册选择和格式选择-->
                        <div id="uploader">
                            <div class="queueList">
                                <div id="dndArea" class="placeholder">
                                    <div id="filePicker"></div>
                                    <p>或将照片拖到这里，单次最多可选300张</p>
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
                <div class="modal-footer clearfix">
                    <div class="selected-count-region js-selected-count-region hide">
                        已选择<span class="js-selected-count">2</span>张图片
                    </div>
                    <div class="text-center">
                        <button class="ui-btn js-confirm ui-btn-disabled" disabled="disabled" ng-show="!chooseSureBtn && !uploadShow">确认</button>
                        <button class="ui-btn js-confirm ui-btn-primary" ng-show="chooseSureBtn  && !uploadShow" ng-click="chooseAdvSureBtn()">确认</button>
                        <button class="ui-btn js-confirm ui-btn-primary" ng-show="uploadShow" ng-click="uploadSureBtn()">确认</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 视频模态框 -->
<div class="modal export-modal myModal-adv" id="video_model">
    <div class="modal-dialog" id="video_model_dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideVideoModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul class="module-nav modal-tab">
                        <li>
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">我的视频</a>
                        </li>
                    </ul>
                    <!-- <div class="search-region">
                        <div class="ui-search-box">
                            <input class="txt js-search-input" type="text" placeholder="搜索" value="" ng-keypress="videoSearch($event)">
                        </div>
                    </div> -->
                </div>
                <div class="modal-body">
                    <div class="category-list-region js-category-list-region">
                        <ul class="category-list">
                            <li class="js-category-item" ng-class="{true:'active',false:''}[video.groupingIndex == $index]" ng-repeat="item in video.groupList" ng-click="switchVideoGroup(item,$index)">    
                                @{{item.name}}
                                <span class="category-num" ng-bind="item.number"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="attachment-list-region js-attachment-list-region">
                        <ul class="image-list">
                            <li class="video_item" ng-repeat = "item in video.modelVideoList" ng-click="checkedVideoItem(item,$index)">
                                <div class="video_item_detail">
                                    <img class="detail_cover" ng-src="{{ imgUrl() }}@{{item.file_cover}}">
                                    <div class="detail_info">
                                        <div class="detail_info_top">
                                            <span>@{{item.FileInfo.name}}</span>
                                            <!-- <span>00:05</span> -->
                                        </div>
                                        <div class="detail_info_sub">
                                            <span>@{{item.created_at}}</span>
                                            <!-- <span>167.5kb</span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="attachment-selected" ng-class="{true:'',false:'hide'}[video.checkedIndex == $index]">
                                    <i class="icon-ok icon-white"></i>
                                </div>
                            </li>

                        </ul>
                        <div class="attachment-pagination js-attachment-pagination">
                            <div class="video_model_page">
                                <span class="ui-pagination-total"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="text-center">
                        <button class="ui-btn js-confirm ui-btn-disabled" disabled="disabled" ng-class="{true:'',false:'hide'}[video.checkedIndex == -1]">确认</button>
                        <button class="ui-btn js-confirm ui-btn-primary" ng-class="{true:'hide',false:''}[video.checkedIndex == -1]" ng-click="sureUseVideo()">确认</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 视频上传 -->
<div class="zent-dialog-r-wrap upload_video">
    <div class="zent-dialog-r rc-video-upload__dialog" style="min-width: 600px; max-width: 85%;">
        <button type="button" class="zent-dialog-r-close" ng-click="closeUploadVideo()">×</button>
        <div class="zent-dialog-r-body">
            <div class="zent-tabs rc-video-upload__tabs rc-video-upload__tabs--onlyone">
                <div class="zent-tabs-nav zent-tabs-size-normal zent-tabs-type-slider zent-tabs-align-left zent-tabs-third-level">
                    <div class="zent-tabs-nav-content">
                        <div class="zent-tabs-scroll">
                            <div class="zent-tabs-tabwrap" role="tablist">
                                <span class="zent-tabs-nav-ink-bar" style="width: 90px; left: 0px;"></span>
                                <div>
                                    <div role="tab" aria-labelledby="zent-tabpanel-1-1" class="zent-tabs-tab zent-tabs-actived" aria-disabled="false" aria-selected="true">
                                        <div class="zent-tabs-tab-inner">
                                           上传视频
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="zent-tabs-panewrap">
                    <div role="tabpanel" id="zent-tabpanel-1-1" class="zent-tab-tabpanel ">
                        <form class="zent-form zent-form--horizontal rc-video-upload__form">
                            <div class="rc-video-upload__progress">
                                <div class="rc-video-upload__progress-item">
                                    <span class="rc-video-upload__progress-item-close" ng-click="reUploadVideo()">×</span>
                                    <div class="rc-video-upload__progress-item-progress"></div>
                                    <div class="rc-video-upload__progress-item-detail">
                                        <span class="rc-video-upload__progress-item-detail-name">QQ20171107-091836 (1).mp4</span>
                                        <span class="rc-video-upload__progress-item-detail-speed"></span>
                                        <span class="rc-video-upload__progress-item-detail-total"></span>
                                        <span class="rc-video-upload__progress-item-detail-percent">
                                            100%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="zent-form__control-group add_video">
                                <div class="zent-form__control-label">本地视频：</div>
                                <div class="zent-form__controls">
                                    <div class="rc-video-upload__choose">
                                        +
                                        <input id="upload_video" type="file" placeholder="添加 +" accept=".mp4">
                                    </div>
                                    <p class="zent-form__help-desc">
                                        <span>
                                            点击“+”选择视频，视频大小不超过30 MB，建议宽高比16:9
                                            <br>
                                            支持的视频文件类型包括：mp4
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <input type="hidden" name="video_url">
                            <input type="hidden" name="id">
                            <!-- has-error -->
                            <div class="zent-form__control-group  rc-video-upload__form-input video_name">
                                <label class="zent-form__control-label">
                                    <em class="zent-form__required">*</em>
                                    名称：
                                </label>
                                <div class="zent-form__controls">
                                    <div class="zent-input-wrapper">
                                        <input type="text" class="zent-input" name="video_name" value="" placeholder="最长不超过10个字">
                                    </div>
                                    <p class="zent-form__error-desc">请输入不超过10个字的视频名称</p>
                                </div>
                            </div>
                            <div class="zent-form__control-group rc-video-upload__form-input">
                                <label class="zent-form__control-label">
                                    分组：
                                </label>
                                <div class="zent-form__controls">
                                    <div class="zent-popover-wrapper zent-select  " style="display: inline-block;">
                                        <select style="position: relative;top: 4px;" name="grounp">
                                            <option value="@{{grounp.id}}" ng-repeat="grounp in grounps">@{{grounp.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="zent-form__control-group rc-video-upload__form-input">
                                <label class="zent-form__control-label">
                                    封面：
                                </label>
                                <div class="zent-form__controls">
                                    <div class="zent-popover-wrapper zent-select  " style="display: flex;align-items: center;position:relative;top:1px">
                                        <div class="image_views" style="height: 80px;overflow: hidden;display:none">
                                            <img src="" style="max-width:100%;max-height:100%">
                                        </div>
                                        <input type="hidden" name="image_url">
                                        <a style="position:relative;top:3px" href="javascript:void(0);">+加图<input type="file" id="upload_image" style="position:absolute;top:0;left:0;right:0;bottom:0;opacity:0;width: 100%;" accept="image/jpg, image/jpeg, image/png"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="rc-video-upload__publish">
                                <div>
                                    <label class="zent-checkbox-wrap zent-checkbox-checked">
                                        <span class="zent-checkbox">
                                            <input name="aggree_input" type="checkbox" value="">
                                        </span>
                                        <span>
                                            同意《
                                            <a href="https://www.huisou.cn/home/index/detail/654/news" target="_blank" rel="noopener noreferrer">视频上传服务协议</a>
                                            》
                                        </span>
                                    </label>
                                    <button type="submit" class="zent-btn-disabled zent-btn video_btn" disabled="">确定</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="zent-dialog-r-backdrop"></div>
<!-- 商品列表商品分组选择Model -->
<div class="modal export-modal" id="goodslist_model">
    <div class="modal-dialog" id="goodslist_model_dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">商品分组</a>
                            <span>|</span>
                        </li>
                        <li>
                            <a href="/merchants/product/productGroup" data-type="tag" class="js-modal-tab" target="_blank">分组管理</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                            <tr>
                                <th>
                                    <div class="td-cont">
                                        <span>标题 </span>
                                        <!-- <a href="#">刷新</a> -->
                                    </div>
                                </th>
                                <th class="information"></th>
                                <th>
                                    <div class="td-cont">
                                        <span>创建时间</span>
                                    </div>
                                </th>
                                <th class="opts">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchShopGroup()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat = "list in goodsGroupList">
                                <td class="title" colspan="2">
                                    <div class="td-cont">
                                        <a target="_blank" class="new_window" href="javascript:void(0);">@{{ list['name'] }}</a>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                        @{{list['created_at']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseShopGroupSure($index,list)" ng-class="list.isActive ? 'btn-primary': ''">选取</button> 
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseGroupSure()">
                        <input type="button" class="btn btn-primary" value="确定使用">
                    </div>
                    <div class="page_shopgroup"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 添加自定义Model -->
<div class="modal export-modal" id="component_model">
    <div class="modal-dialog" id="component_model_dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">自定义页面模块</a>
                            <span>|</span>
                        </li>
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/merchants/store/componentAdd" target="_blank" class="new_window">新建自定义页面模块</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                            <tr>
                                <th>
                                    <div class="td-cont">
                                        <span>标题 </span>
                                        <!-- <a href="#">刷新</a> -->
                                    </div>
                                </th>
                                <th class="information"></th>
                                <th>
                                    <div class="td-cont">
                                        <span>创建时间</span>
                                    </div>
                                </th>
                                <th class="opts">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchComponent()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat = "list in components">
                                <td class="title" colspan="2">
                                    <div class="td-cont">
                                        <a target="_blank" class="new_window" href="javascript:void(0);">@{{ list['name'] }}</a>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                        @{{list['created_at']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseComponent($index,list)">选取</button> 
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="choosePageSure()">
                        <input type="button" class="btn btn-primary" value="确定使用">
                    </div>
                    <div class="page_component"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 链接选择商品框 -->
<div class="modal export-modal" id="chooseShopModel">
    <div class="modal-dialog" id="chooseShopModel-dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li class = "js_switch" ng-class="{true: 'active', false: ''}[productModal.navIndex == $index]" ng-repeat="item in productModal.navList" ng-click="switchProductNav($index)">
                            <a href="javascript:void(0);" data-type="goods" class="js-modal-tab">@{{item}}</a>
                        </li>
                        <li ng-repeat="item in productModal.new" ng-if="productModal.navIndex == $index">
                            <a ng-href="@{{item.href}}" target="_blank" class="new_window">@{{item.title}}</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                        <tr>
                            <th style="width: 40%;text-align: left;">
                                <div class="td-cont">
                                    <span>标题</span>
                                   <!--  <a href="#" ng-click="refresh()">刷新</a> -->
                                </div>
                            </th>
                            <th  style="width: 25%;">
                                <div class="td-cont">
                                    <span>创建时间</span>
                                </div>
                            </th>
                            <th class="opts">
                                <div class="td-cont">
                                    <form class="form-search">
                                        <div class="input-append">
                                            <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchProductList()">搜</a>
                                        </div>
                                    </form>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat = "list in productModal.list" id="list_@{{$index}}" data-price="@{{list['price']}}">
                            <td class="image" style="text-align: left;">
                                <div class="td-cont" style="display: inline-block;" ng-if="list['thumbnail']">
                                    <img ng-src=" @{{host}}@{{list['thumbnail']}}">
                                </div>
                                <div class="td-cont" style="display: inline-block;vertical-align: top;">
                                    <a target="_blank" class="new_window" href="javascript:void(0);">@{{list['name']}}</a>
                                </div>
                            </td>
                            <td>
                                <span>
                                    @{{list['timeDay']}}
                                </span>
                            </td>
                            <td>
                                <div class="td-cont text-right">
                                    <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="chooseShopLink($index,list)">选取</button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseSure()">
                        <!-- <input type="button" class="btn btn-primary" value="确定使用"> -->
                    </div>
                    <div class="good_pagenavi">
                        <span class="total">共 2 条，每页 8 条</span></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 添加微页面Model -->
<div class="modal export-modal" id="page_model">
    <div class="modal-dialog" id="page-dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">微页面</a>
                            <span>|</span>
                        </li>
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/v2/showcase/goods/edit" target="_blank" class="new_window">新建微页面</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                            <tr>
                                <th>
                                    <div class="td-cont">
                                        <span>标题 </span>
                                        <!-- <a href="#">刷新</a> -->
                                    </div>
                                </th>
                                <th class="information"></th>
                                <th>
                                    <div class="td-cont">
                                        <span>创建时间</span>
                                    </div>
                                </th>
                                <th class="opts">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchPage()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat = "list in pageList">
                                <td class="title" colspan="2">
                                    <div class="td-cont">
                                        <a target="_blank" class="new_window" href="javascript:void(0);">@{{ list['name'] }}</a>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                        @{{list['created_at']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_@{{$index}}" href="javascript:void(0);" ng-click="choosePageLinkSure($index,list)">选取</button> 
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="choosePageSure()">
                        <input type="button" class="btn btn-primary" value="确定使用">
                    </div>
                    <div class="page_pagenavi"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 微预约选择弹窗 -->
<div class="modal export-modal" id="activity_appointment">
    <div class="modal-dialog" id="activity-dialog-appointment">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">微预约</a>
                            <span>|</span>
                        </li>
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/merchants/wechat/bookSave" target="_blank" class="new_window">新建预约</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                            <tr>
                                <th>
                                    <div class="td-cont">
                                        <span>标题 </span>
                                        <!-- <a href="#">刷新</a> -->
                                    </div>
                                </th>
                                <th class="information"></th>
                                <th>
                                    <div class="td-cont">
                                        <span>创建时间</span>
                                    </div>
                                </th>
                                <th class="opts">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle">
                                                <a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchAppoint()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat = "list in appointMent">
                                <td class="title" colspan="2">
                                    <div class="td-cont">
                                        <a target="_blank" class="new_window" href="javascript:void(0);">@{{ list['title'] }}</a>
                                    </div>
                                </td>
                                <td>
                                    <span>
                                        @{{list['created_at']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseAppointSure($index,list)">选取</button> 
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="choosePageSure()">
                        <input type="button" class="btn btn-primary" value="确定使用">
                    </div>
                    <div class="appoint_pagenavi"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 选择卡密弹窗 start -->
<div class="modal export-modal" id="my_card_model">
    <div class="modal-dialog" id="card_model-dialog">
        <form class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" ng-click="hideModel()">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <ul class="module-nav modal-tab">
                        <li class="active">
                            <a href="#js-module-goods" data-type="goods" class="js-modal-tab">发卡密</a>|</li>
                        <li class="link-group link-group-0" style="display: inline-block;">
                            <a href="/merchants/cam/create" target="_blank" class="new_window">新建发卡密</a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body">
                    <table class="table table-striped ui-table ui-table-list">
                        <thead>
                            <tr>
                                <th class="col-sm-4 col-xs-4">
                                    <div class="td-cont">
                                        <span>活动名称 </span>
                                        <!-- <a href="#">刷新</a> -->
                                    </div>
                                </th>
                                <!-- <th class="col-sm-2 col-xs-2">
                                    <div class="td-cont">
                                        <span>有效期</span>
                                    </div>
                                </th> -->
                                <!-- <th class="information"></th> -->
                                <th class="col-sm-2 col-xs-2">
                                    <div class="td-cont">
                                        <span>类型</span>
                                    </div>
                                </th>
                                <th class="col-sm-2 col-xs-2">
                                    <div class="td-cont">
                                        <span>剩余库存</span>
                                    </div>
                                </th>
                                <th class="opts" class="col-sm-4 col-xs-4">
                                    <div class="td-cont">
                                        <form class="form-search">
                                            <div class="input-append">
                                                <input class="input-small js-modal-search-input form-control" type="text" ng-model="searchTitle"><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" ng-click="searchCardId()">搜</a>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat = "list in cardIdList" id="list_@{{list.id}}" data-price="@{{list['price']}}">
                                <td class="image">
                                    <div class="td-cont">
                                        @{{list['name']}} 
                                    </div>
                                </td>
                                <!-- <td>
                                    <span>
                                        @{{list['begin_time']}}
                                        至
                                        @{{list['end_time']}}
                                    </span>
                                </td> -->
                                <td>
                                    <span ng-show="list['type']==2">
                                        <!-- 类型 -->
                                        通用码
                                    </span>
                                    <span ng-show="list['type']==1">
                                        <!-- 类型 -->
                                        一商品一码
                                    </span>
                                </td>
                                <td>
                                    <span>
                                        <!-- 剩余库存 -->
                                        @{{list['stock']}}
                                    </span>
                                </td>
                                <td>
                                    <div class="td-cont text-right">
                                        <button class="btn js-choose choose_btn_@{{list.id}}" href="javascript:void(0);" ng-click="chooseCardId($index,list)">选取</button> 
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer clearfix">
                    <div style="" class="js-confirm-choose pull-left" ng-show="tempSure" ng-click="chooseCardIdSure()">
                        <input type="button" class="btn btn-primary" value="确定使用">
                    </div>
                    <div class="cardId_pagenavi"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 选择卡密弹窗 end -->

<!-- tip -->
<div class="tip">请选择商品</div>
<!--backdrop-->
<div class="modal-backdrop"></div>
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/angular.min.js"></script>
<script type="text/javascript">// 高版本juqery兼容choose
    //update 华亢 at 2018/8/8
    var wid = "{{$wid}}";
    jQuery.browser = {}; 
    (function() {
        jQuery.browser.msie = false;
        jQuery.browser.version = 0;
        if (navigator.userAgent.match(/MSIE ([0-9]+)./)) {
            jQuery.browser.msie = true;
            jQuery.browser.version = RegExp.$1;
        }
    })();
    var _id = '{{$is_in_activity}}'
     //add by 倪凯嘉 2019-1-27
     @if(!empty($productId))
    var productId='{{$productId}}'
    @else
    var productId=undefined;
    @endif
    //end
    if(_id == 1){
        $(".close-div").remove()
        $(".add-close").remove()
        $(".close-row").remove()
        $(".close-check").remove()
        $(".btn_disabled").attr('disabled','disabled')
        $(".js-sku-name").css({
            'opacity':'0.65'
        })
        $(".js-sku-name>a").css({
            "cursor": "not-allowed"
        })
    }

    var _host = "{{ config('app.source_url') }}";
    var imgUrl = "{{ imgUrl() }}";
    var store={!! $store !!};
    store.member_url = '/shop/member/index/'+store.id;
    @if(isset($template))
    var template={!! $template !!}
    @else
    var template=''
    @endif

    @if(isset($product))
        var product = {!! $product !!}
    @else
        var product = []
    @endif
</script>
<script type="text/javascript">
    var host = "{{ config('app.url') }}";
</script>
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>

<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<!-- webuploader -->

<!-- layer弹窗 -->
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js?t=123"></script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
<!-- webuploader -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/webuploader.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
<!-- layer弹窗 -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- datePicker -->
<script type="text/javascript" src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<!-- ajax分页js -->
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script> 
<script src="{{ config('app.source_url') }}static/js/md5.js"></script>
<script type="text/javascript">
    var _host = "{{ config('app.source_url') }}";
    var discount_show = "{{ $card }}"; //折扣是否显示
    var videoUrl = "{{videoUrl()}}";
</script>
<script src="{{ config('app.source_url') }}static/js/ngDraggable.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/model.js"></script>
<script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/product_public.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/product_evxshgq4.js"></script>
@endsection