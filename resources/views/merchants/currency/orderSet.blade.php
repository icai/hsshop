@extends('merchants.default._layouts')
@section('head_css')
<!--特殊按钮样式的css文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
<!--bootstrape验证插件css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}/static/css/bootstrapValidator.min.css"/>
<!--图片上传引入的css文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}/static/css/webuploader.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_q6hnv94c.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/currency/orderSet') }}">上门自提</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/localCity') }}">同城配送</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/express') }}">快递发货</a>
            </li>
            <li>
                <a href="{{ URL('/merchants/currency/tradingSet') }}">交易设置</a>
            </li>
        </ul>
        <!-- 普通导航 结束  -->
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
    <div class="content_top">
        <div class="top_left">
            <h4>买家上门自提功能</h4>
            <p>启用上门自提功能后，买家可以就近选择你预设的自提点，下单后你需要尽快将商品配送至指定自提点。<br />
                <a href="https://www.huisou.cn/home/index/detail/785/help" target="_blank" style="color: #2277FF;">查看【上门自提】功能使用教程</a></p>
            </div>
            <div class="top_right">
                <!-- 按钮 开始 -->
                <div class="switch_items">
                    <input type="checkbox" checked name="" value="" />
                    <label></label>
                </div>
                <!-- 按钮 结束 -->
            </div>
        </div>
        <button type="button" id="addNewAdress" class="btn btn-success">新增自提点</button>
        <!--动态添加显示块-->
        <div class="addContentShow">
            <ul class="title" style="background: #F8F8F8;">
                <li>自提点名称</li>
                <li>省份</li>
                <li>城市</li>
                <li>地区</li>
                <li>地址</li>
                <li>联系电话</li>
                <li>操作</li>
            </ul>
        </div>
        <span class="page_num" style="float: right;">共<an>0</an>条，每页20条</span>

        <!--蒙板-->
        <div class="board hide"></div>    
        <!--弹出层-->
        <div class="layer hide">
            <div class="layer_top">
                <div class="layer_top_left">添加自提点</div>
                <div class="layer_top_right"></div>
            </div>
            <div class="layer_center">
                <form id="defaultForm" class="form-horizontal" role="form">
                    <!--自提点名称-->
                    <div class="form-group">
                        <label for="adressName" class="col-sm-2 control-label"><i>*</i>自提点名称：</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="adressName" name="adressName" placeholder="请填写自提点地址便于买家理解和管理">
                        </div>
                    </div>
                    <!--自提点地址-->
                    <div class="form-group">
                        <label for="adress" class="col-sm-2 control-label"><i>*</i>自提点地址：</label>
                        <div class="col-sm-7">
                            <!--三级联动块-->
                            <div class="control-group" style="display: inline-block;">
                                <div class="controls">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <select name="location_p" id="location_p"  class="form-control"></select>
                                        </div>
                                        <div class="col-xs-4">
                                            <select name="location_c" id="location_c"  class="form-control"></select>
                                        </div>
                                        <div class="col-xs-4">
                                            <select name="location_a" id="location_a"  class="form-control"></select>
                                        </div>
                                    </div>
                                    <script src="{{ config('app.source_url') }}/static/js/region_select.js"></script>
                                    <script type="text/javascript">
                                        new PCAS('location_p', 'location_c', 'location_a', '北京市', '', '');
                                    </script>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <!--搜索地图-->
                    <div class="form-group">
                        <label for="addTxt" class="col-sm-2 control-label"><i>*</i>详细地址：</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control" id="addTxt" name="addTxt" placeholder="请填写自提点的具体地址，最短5个字符，最长120字">
                                <span class="input-group-btn">
                                    <button id="addBtn" class="btn btn-default" type="button">搜索地图</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!--地图定位-->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label"><i>*</i>地图定位：</label>
                        <div class="col-sm-6">
                            <div id="mapShow"  style="width: 160%; height: 500px; border: 1px solid #CCCCCC;">
                            </div>
                        </div>
                    </div>
                    <!--联系电话-->
                    <div class="form-group">
                        <label for="firstNum" class="col-sm-2 control-label"><i>*</i>联系电话：</label>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-xs-3">
                                    <input type="text" class="form-control" id="first_number" name="first_number" placeholder="区号">
                                </div>
                                <div class="col-xs-1">-</div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control" id="last_number" name="last_number" placeholder="请填写准确联系电话，便于买家联系（区号可空）">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--接待时间-->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label"><i>*</i>接待时间：</label>
                        <div class="col-sm-10 Time" >
                            <div class="timeDivShow"></div>
                            <div class="selectTimeDiv">
                                <select name="beginHour">
                                    <option value="00">00</option>
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
                                </select> 时
                                <select name="beginMinut">
                                    <option value="00">00</option>
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
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                </select> 分 ~
                                <select name="endHour">
                                    <option value="00">00</option>
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
                                </select> 时
                                <select name="endMinut">
                                    <option value="00">00</option>
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
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                </select> 分
                                <div class="weekDiv">
                                    <div class="week Mon">周一 <div class="weekBoard"></div></div>
                                    <div class="week Tues">周二 <div class="weekBoard"></div></div>
                                    <div class="week Wen">周三 <div class="weekBoard"></div></div>
                                    <div class="week Thurs">周四 <div class="weekBoard"></div></div>
                                    <div class="week Fri">周五 <div class="weekBoard"></div></div>
                                    <div class="week Sat">周六 <div class="weekBoard"></div></div>
                                    <div class="week Sun">周日<div class="weekBoard"></div></div>
                                </div>
                                <a href="##" id="beSure">确认 </a><a href="##" class="cancle">| 取消</a>
                            </div>
                            <a href="javascript:void(0)" class="addTimeDiv hide" style="color: #27f;">新增时间段</a>
                            <input type="hidden" name="store_time" id="store_time" value="" />
                        </div>
                    </div>
                    <!--自提点照片-->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label"><i>*</i>自提点照片：</label>
                        <div class="col-sm-10">
                            <div id="selImg" style="display: inline-block;">
                                <a href="##" id="addImg" data-toggle="modal" data-target="#myModal-adv">+加图</a>
                            </div>
                            <input id="store_img" type="hidden" name="store_img" value="" />
                        </div>
                    </div>
                    <!--商家推荐-->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">商家推荐：</label>
                        <div class="col-sm-7">
                            <textarea name="introduce" rows="4" cols="40" style="resize: both; padding: 5px;" placeholder="可描述自提点的活动或相关备注信息（最多200个字）"></textarea>
                        </div>
                    </div>
                    <!--同时作为线下门店接待-->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label"></label>
                        <div class="col-sm-7">
                            <label for="checkBox" style="font-size: 13px;"><input type="checkbox" name="" id="checkBox" value="" />同时作为线下门店接待</label>
                        </div>
                    </div>
                    <div class="layer_bottom">
                        <button type="submit" class="btn btn-primary">保存自提点</button>
                    </div>
                </form>
            </div>
            <!-- 广告图片model -->
            <div class="modal export-modal myModal-adv" id="myModal-adv">
                <div class="modal-dialog" id="modal-dialog-adv">
                    <form class="form-horizontal">
                        <div class="modal-content modal_content_1">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Close</span>
                                </button>
                                <ul class="module-nav modal-tab">
                                    <li class="active">
                                        <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                                        <a href="#uploadImgLayer" class="js-modal-tab"style="color: #27f;">图标库</a>
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
                                        <li class="js-category-item active">未分组  
                                            <span>8</span>
                                        </li>
                                        <li class="js-category-item">111111
                                            <span>0</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="attachment-list-region attachment_1">
                                    <ul class="image-list">
                                        <li class="image-item">
                                            <img class="image-box" src="{{ config('app.source_url') }}mctsource/images/logoExample.png">
                                            <div class="image-meta">1920*1200</div>
                                            <div class="image-title">01.png</div>
                                            <div class="attachment-selected hide">
                                                <i class="icon-ok icon-white"></i>
                                            </div>
                                        </li>
                                        <li class="image-item">
                                            <img class="image-box" src="{{ config('app.source_url') }}mctsource/images/06.png">
                                            <div class="image-meta">1920*1200</div>
                                            <div class="image-title">01.png</div>
                                            <div class="attachment-selected hide">
                                                <i class="icon-ok icon-white"></i>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="attachment-pagination">
                                        <div class="ui-pagination">
                                            <span class="ui-pagination-total">共8条， 每页15条</span>
                                        </div>
                                    </div>
                                    <a href="#uploadImg" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left: 180px; bottom: 16px;">上传图片</a>
                                </div>
                                <!--列表中的图片个数为0的时候显示这个模态框-->
                                <div class="attachment-list-region Img_add hide">
                                    <div id="layerContent_right">
                                        <a href="#uploadImg">+</a>
                                        <p>暂无数据，点击添加</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer clearfix">
                                <div class="selected-count-region" style="">
                                  已选择<span class="js-selected-count">0</span>张图片
                              </div>
                              <div class="text-center">
                                <button type="button" class="ui-btn js-confirm" href="##" disabled="disabled">确认</button>
                            </div>
                        </div>
                    </div>
                    <!--上传图片模态框-->
                    <div class="modal-content modal_content_2 hide">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <ul class="module-nav modal-tab">
                                <li class="active">
                                    <a href="#layer" class="js-modal-tab" style="color: #27f;"><选择图片</a>
                                    <a href="##" class="js-modal-tab">| 上传图片</a>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-body"  id="uploadLayerContent">
                                        
                                        <div id="uploadLayerContent_botm">
                                            <div id="wrapper">
                                                <div id="container">
                                                    <!--头部，相册选择和格式选择-->
                                                    <div id="uploader">
                                                        <div class="queueList">
                                                            <div id="dndArea" class="placeholder">
                                                                <label id="filePicker"></label>
                                                                <p>或将照片拖到这里，单次最多可选300张</p>
                                                            </div>
                                                        </div>
                                                        <div class="statusBar" style="display:none;">
                                                            <div class="progress">
                                                                <span class="text">0%</span>
                                                                <span class="percentage"></span>
                                                            </div><div class="info"></div>
                                                            <div class="btns">
                                                                <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer clearfix">
                                        <div class="text-center">
                                            <button class="ui-btn js-confirm ui-btn-primary">确认</button>
                                        </div>
                                    </div>
                                </div>
                                <!--图片库模态框-->
                                <div class="modal-content modal_content_3 hide">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                            <span class="sr-only">Close</span>
                                        </button>
                                        <ul class="module-nav modal-tab">
                                            <li class="active">
                                                <a href="#layer" class="js-modal-tab" style="color: #27f;">我的图片</a>
                                                <a href="##" class="js-modal-tab">图标库</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div  id="iconLibraryContent" class="modal-body">
                                        <ul id="iconStyleSelect">
                                            <li id="style">风格: <a href="##" class="selected">全部</a><a href="##">普通</a><a href="##">简约</a></li>
                                            <li id="color">颜色: <a href="##" class="selected">全部</a><a href="##">白色</a><a href="##">灰色</a></li>
                                            <li id="type">类型: <a href="##" class="selected">全部</a><a href="##">常规</a><a href="##">购物</a><a href="##">交通</a><a href="##">食物</a><a href="##">商务</a><a href="##">娱乐</a><a href="##">美妆</a></li>
                                        </ul>
                                        <div id="iconImgShow">
                                            <ul id="iconImgSelect" class="image-list">
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile2.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/present3.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/luckyPrize.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/present2.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                                <li class="image-item">
                                                    <img src="{{ config('app.source_url') }}mctsource/images/smile1.png"/>
                                                    <div class="attachment-selected hide">
                                                        <i class="icon-ok icon-white"></i>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div id="pageNum">
                                            共<span>270</span>条，每页27条&nbsp;&nbsp;
                                            <div id="show"></div>
                                            <div class="pagination"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer clearfix">
                                        <div class="selected-count-region">
                                          已选择<span class="js-selected-count">0</span>张图片
                                      </div>
                                      <div  id="iconLibraryLayerBtn" class="text-center">
                                        <button type="button" class="ui-btn js-confirm" href="##" disabled="disabled">确认</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--弹出层结束-->
        </div>
        @endsection
        @section('page_js')
        <!--地图接口-->
        <script src="http://api.map.baidu.com/api?v=2.0&ak=Gl9ARRgPlcASCW55a33dw5AE8URjrKRu"></script>
        <!--地图的方法-->
        <script src="{{ config('app.source_url') }}mctsource/js/self_public/map_public.js" type="text/javascript" charset="utf-8"></script>
        <!--分页器js引入-->
        <script src="{{ config('app.source_url') }}/static/js/jqPaginator.min.js" type="text/javascript" charset="utf-8"></script>
        <!-- 当前页面js -->
        <script src="{{ config('app.source_url') }}mctsource/js/currency_q6hnv94c.js"></script>
        <!--bootstrap表单验证插件js-->
        <script src="{{ config('app.source_url') }}/static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
        <!--图片上传引入的js文件-->
        <script src="{{ config('app.source_url') }}/static/js/webuploader.js" type="text/javascript" charset="utf-8"></script>
        <script src="{{ config('app.source_url') }}mctsource/js/webup_load.js" type="text/javascript" charset="utf-8"></script>
        <!--特殊按钮js文件-->
        <script src="{{ config('app.source_url') }}mctsource/js/specialBtn.js" type="text/javascript" charset="utf-8"></script>
        @endsection