@extends('merchants.default._layouts')
@section('head_css')
<!--bootstrapValidator文件引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}/static/css/bootstrapValidator.min.css"/>
<!--时间插件css引入-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}/static/css/bootstrap-datetimepicker.min.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_discount_new.css" />
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
                <a href="{{ URL('/merchants/marketing/discount') }}">限时折扣</a>
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
    <!--顶部导航内容-->
    <div class="content_top">
        <ul>
            <li onclick="javascript:location.href='{{ URL('/merchants/marketing/discount') }}'">所有促销</li>
            <li>未开始</li>
            <li>进行中</li>
            <li>已结束</li>
        </ul>
        <a href="##" class="tutorial"><span id="icon">?</span> 查看【限时折扣】使用帮助</a>
    </div>
    <!--中部主要内容-->
    <div class="content_center">
        <span class="setDiscount">设置限时折扣</span>
        <form id="defaultForm" class="form-horizontal" role="form">
            <b>活动信息</b>
            <div class="form-group">
                <label for="activeName" class="col-sm-2 control-label"><i>*</i>活动名称：</label>
                <div class="col-sm-3">
                    <input type="text" name="activeName" class="form-control" id="activeName" placeholder="请填写活动名称">
                </div>
            </div>
            <div class="form-group">
                <label for="bgTime" class="col-sm-2 control-label"><i>*</i>生效时间：</label>
                <div class="BCtime Gp">
                    <div class='input-group col-sm-4 date' id='datetimepicker1'>
                        <input type='text' name="Btime" class="form-control" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <font>至</font>
                    <div class='input-group col-sm-4 date' id='datetimepicker2'>
                        <input type='text' name="Ctime" class="form-control" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label"></label>
                <div class="col-sm-10">
                    <label><input type="checkbox" name="" id="cycleR" value="" />按周期重复</label>
                    <div class="cycleRepet hide">
                        <div class="day Gp">
                            <label><input type="radio" name="repetTime" id="repetTime_1" value="" checked="checked"/>每天</label>
                            <div class='input-group col-sm-2 date' id='datetimepicker3'>
                                <input type='text' class="form-control" value="00:00"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <font>至</font>
                            <div class='input-group col-sm-2 date' id='datetimepicker4'>
                                <input type='text' class="form-control" value="23:59"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                          </div>
                        </div>
                        <div class="month Gp">
                            <label><input type="radio" name="repetTime" id="repetTime_2" value="" />每月</label>
                            <select name="perMonthDay">
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>
                            <font>日</font>
                            <div class='input-group col-sm-2 date' id='datetimepicker5'>
                                <input type='text' class="form-control" value="00:00"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <font>至</font>
                            <div class='input-group col-sm-2 date' id='datetimepicker6'>
                                <input type='text' class="form-control" value="23:59"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="week Gp">
                            <label><input type="radio" name="repetTime" id="repetTime_3" value="" />每周</label>
                            <div class="weekDiv">
                                <div class="week Mon" style="margin-left: 16px;">周一 <div class="weekBoard"></div></div>
                                <div class="week Tues">周二 <div class="weekBoard"></div></div>
                                <div class="week Wen">周三 <div class="weekBoard"></div></div>
                                <div class="week Thurs">周四 <div class="weekBoard"></div></div>
                                <div class="week Fri">周五 <div class="weekBoard"></div></div>
                                <div class="week Sat">周六 <div class="weekBoard"></div></div>
                                <div class="week Sun">周日<div class="weekBoard"></div></div>
                            </div>
                            <div class='input-group col-sm-2 date' id='datetimepicker7'>
                                <input type='text' class="form-control" value="00:00"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <font>至</font>
                            <div class='input-group col-sm-2 date' id='datetimepicker8'>
                                <input type='text' class="form-control" value="23:59" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="activeTip" class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    <p class="hint">活动开始前，商品详情页价格下方将会预告活动开始时间和折扣。 <a class="hoverShowImg rtv" href="##">查看示例<img class="hoverImg abt hide" src="{{ config('app.source_url') }}/mctsource/images/activeTip.png" /></a></p>
                </div>
            </div>
            <div class="form-group">
                <label for="activeTip" class="col-sm-2 control-label">活动标签：</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="activeTip" placeholder="限时折扣">
                </div>
            </div>
            <div class="form-group">
                <label for="activeTip" class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    <p class="hint">活动期间展示于商品详情的价格旁边，2至5个字符。<a class="hoverShowImg rtv" href="##">查看示例<img class="hoverImg abt hide" src="{{ config('app.source_url') }}/mctsource/images/bgtime.png" /></a></p>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">限购设置：</label>
                <div class="col-sm-10 discountSelect">
                    <label><input type="radio" name="showset" id="showset_1" value="" checked="checked" />不限购</label>
                    <label><input type="radio" name="showset" id="showset_2" value="" />每人限购<input type="text" class="form-control"/>件</label>
                    <label><input type="radio" name="showset" id="showset_3" value="" />每人前<input type="text" class="form-control"/>件享受折扣</label>
                </div>
            </div>
            
            <!--选择活动商品主要部分-->
            <b>选择活动商品</b>
            <div class="selectGoods">
                <div class="selectGoods_top">
                    <ul>
                        <li class="li_active">第一步：选择商品</li>
                        <li>第二步：设置折扣(<n>0</n>)</li>
                    </ul>
                </div>
                <!--第一部中的内容-->
                <div class="change_show change_show_1">
                    <div class="goodsGroup">
                        <select name="">
                            <option value="goodsAll">所有分组</option>
                            <option value="goodsHide">列表中隐藏</option>
                        </select>
                        <select name="">
                            <option value="goodsTitle">商品标题</option>
                            <option value="goodsCode">商品编码</option>
                        </select>
                        <input type="text" name="" id="" class="form-control" placeholder="请输入商品名称" />
                        <button type="button" class="btn btn-primary">搜索</button>
                    </div>
                    <div class="goodsMsg">
                        <ul class="msgTitle flex_star">
                            <li>商品信息</li>
                            <li>库存</li>
                            <li>操作</li>
                        </ul>
                        <div class="msgContent">
                            <ul class="ulMsgContent flex_star" data="1">
                                <li class="flex_star">
                                    <input type="checkbox" name="" id="" class="choose" value=""/>
                                    <div class="goodsImg">
                                        <img src="{{ config('app.source_url') }}mctsource/images/20.png"/>
                                    </div>
                                    <span class="">
                                        <a href="##">电子卡券（购买时无需填写收货地址，测试商品，不发货，不退款）</a>
                                        <p>￥<pr>1.00</pr></p>
                                    </span>
                                </li>
                                <li class="inventory">9999</li>
                                <li><button type="button" class="btn btn-primary">参加折扣</button></li>
                            </ul>
                            <ul class="ulMsgContent flex_star" data="2">
                                <li class="flex_star">
                                    <input type="checkbox" name="" id="" class="choose" value=""/>
                                    <div class="goodsImg">
                                        <img src="{{ config('app.source_url') }}mctsource/images/20.png"/>
                                    </div>
                                    <span class="">
                                        <a href="##">电子111卡券（购买时无需填写收货地址，测试商品，不发货，不退款）</a>
                                        <p>￥<pr>5.00</pr></p>
                                    </span>
                                </li>
                                <li class="inventory">9999</li>
                                <li><button type="button" class="btn btn-primary">参加折扣</button></li>
                            </ul>
                            <ul class="ulMsgContent flex_star" data="3">
                                <li class="flex_star">
                                    <input type="checkbox" name="" id="" class="choose" value=""/>
                                    <div class="goodsImg">
                                        <img src="{{ config('app.source_url') }}mctsource/images/20.png"/>
                                    </div>
                                    <span class="">
                                        <a href="##">电子111222卡券（购买时无需填写收货地址，测试商品，不发货，不退款）</a>
                                        <p>￥<pr>10.00</pr></p>
                                    </span>
                                </li>
                                <li class="inventory">9999</li>
                                <li><button type="button" class="btn btn-primary">参加折扣</button></li>
                            </ul>
                        </div>
                    </div>
                    <div class="selectGoods_bottom flex_center">
                        <div class="selectGoods_bottom_left">
                            <label><input type="checkbox" name="" id="allChose" value="" />全选</label>
                            <button type="button" id="allJoin" class="btn btn-default">批量参加</button>
                            <!--<button type="button" class="btn btn-primary">第一页全部参加</button>-->                               
                        </div>
                        <div class="selectGoods_bottom_right">
                            <p>共<tn>1</tn>条，每页<pn>10</pn>条</p>
                        </div>
                    </div>
                </div>
                <!--第一部中内容结束-->
                <!--第二部中内容-->
                <div class="change_show change_show_2 hide">
                    <p id="emptyHint" class="hide">还没有选择活动商品</p>
                    <div class="bulk_title flex_center">
                        <div class="title_left flex_center cleac_float rtv">
                            <div class="bulk_discount rtv float_left">
                                <button type="button" class="btn btn-default bulk_D">批量打折</button>
                                <div class="bulk_D_inpGroup abt flex_star hide">
                                    <input type="text" name="bulk_D_inp" id="bulk_D_inp" class="form-control" value="0.0" />
                                    <a href="##" class="bulk_D_sureBtn">确定</a> |
                                    <a href="##" class="bulk_D_cancleBtn">取消</a>                                                
                                </div>
                            </div>
                            <div class="bulk_sale rtv float_right">
                                <button type="button" class="btn btn-default bulk_S">批量减价</button>
                                <div class="explain explain_1" title="" data-container="body" data-toggle="popover" data-placement="bottom" data-content="使用『批量打折』或『批量减价』批量设置后，您还可以单独调整每个商品的折扣和减价，也可以直接填写期望的价格。">? </div>
                                <!--<a tabindex="0" class="explain explain_1" role="button" data-toggle="popover" data-placement="内容">?</a>-->
                                <div class="bulk_S_inpGroup abt flex_star hide">
                                    <input type="text" name="bulk_S_inp" id="bulk_S_inp" class="form-control" value="0.0" />
                                    <a href="##" class="bulk_S_sureBtn">确定</a> |
                                    <a href="##" class="bulk_S_cancleBtn">取消</a>                                                
                                </div>
                            </div>
                        </div>
                        <div class="title_right">
                            <label><input type="radio" name="discountStyle" id="" value="" />抹去角和分</label>
                            <label><input type="radio" name="discountStyle" id="" value="" />抹去分</label>
                            <div class="explain explain_2" title="" data-container="body" data-toggle="popover" data-placement="bottom"  data-content="抹零是指付钱时不计整数以外的尾数；抹零对折后价低于1.00元的商品无效。">? </div>
                        	<!--<a tabindex="0" class="explain explain_2" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-content="内容?">?</a>-->
                        </div>
                    </div>
                    
                    <!--动态添加的内容-->
                    <div class="bulk_content"></div>
                    
                    <div class="bulk_bottom flex_center">
                        <div class="bulk_bottom_left">
                            <label><input type="checkbox" name="" id="allCancle" value="" />全选</label>
                            <button type="button" id="allRemove" class="btn btn-default">批量取消</button>                              
                        </div>
                        <div class="bulk_bottom_right">
                            <p>共<tn>1</tn>条，每页<pn>10</pn>条</p>
                        </div>
                    </div>
                </div>
                <!--第二部中内容结束-->
            </div>
            <div class="content_bottom">
                <button type="reset" class="btn btn-default">取消</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('page_js')
<!--时间插件引入的JS文件-->
<script src="{{ config('app.source_url') }}/static/js/moment/moment.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}/static/js/moment/locales.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}/static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
<!--layer.js文件引入-->
<script src="{{ config('app.source_url') }}/static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>  
<!--bootstrap表单验证js文件引入-->
<script src="{{ config('app.source_url') }}/static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ config('app.source_url') }}/static/js/zh_CN.js" type="text/javascript" charset="utf-8"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/marketing_discount_new.js"></script>
@endsection
