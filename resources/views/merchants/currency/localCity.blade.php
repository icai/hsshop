@extends('merchants.default._layouts')
@section('head_css')
<!--bootstrape验证插件css-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}/static/css/bootstrapValidator.min.css"/>
<!--特殊按钮样式的css文件-->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/specialBtn.css"/>
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_3tf44wz3.css" />
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
            <li>
                <a href="{{ URL('/merchants/currency/orderSet') }}">上门自提</a>
            </li>
            <li class="hover">
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
            <h4>同城配送功能</h4>
            <p>启用后，买家下单可以选择同城配送，由你提供上门配送服务。<br />
                <a href="##" style="color: #2277FF;">查看【同城配送】功能使用教程</a></p>
            </div>
            <div class="top_right">
                <!-- 按钮 开始 -->
                <div class="switch_items">
                    <input type="checkbox" name="" value="" />
                    <label></label>
                </div>
                <!-- 按钮 结束 -->
            </div>
        </div>
        <div class="content_body">
            <form id="defaultForm" class="form-horizontal" role="form" style="position: relative;">
                <div class="form-group">
                    <label for="textarea" class="col-sm-2 control-label">配送范围介绍：</label>
                    <div class="col-sm-5">
                        <textarea name="intro" rows="6" cols="60" id="textarea" class="form-control" style="vertical-align: top;resize: both; padding: 5px; border-radius: 5px;"></textarea>
                        <i>配送区域图文信息将会在买家下单时显示。<a href="##" id="see">查看示例</a></i>
                    </div>
                </div>
                <!--查看示例开始-->
                <div class="board hide"></div>
                <div class="layer hide">
                    <div class="layer_top">
                        <div class="top_left">
                            配送区域图文信息将会在买家下单时显示
                        </div>
                        <div class="top_right"></div>
                    </div>
                    <div class="layer_content">
                        <img src="{{ config('app.source_url') }}mctsource/images/19.png"/>
                    </div>
                    <div class="layer_bottom">
                        <button type="button" class="btn btn-primary">我知道了</button>
                    </div>
                </div>
                <!--查看示例结束-->
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">配送范围图片：</label>
                    <div class="col-sm-5 imgbox">
                        <div class="fileInpDiv imgnum">
                            <a href="##">上传<br />图片</a>
                            <span class="close">X</span>
                            <input type="file" name="fileInp" class="filepath" />
                            <img src="" class="img2" />
                        </div>
                        <input id="store_img" type="hidden" name="store_img" value="" />
                        <i>建议尺寸：640 x 640 像素</i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="M1" class="col-sm-2 control-label">起送金额：</label>
                    <div class="col-sm-7">
                        <input type="text" name="sending_fee" id="M1" class="form-control" value="0" />元
                        <i>起送金额为商品促销后的实际售价，在优惠券抵扣、订单满减等订单优惠之前，不包括运费。</i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="M2" class="col-sm-2 control-label">配送费：</label>
                    <div class="col-sm-5">
                        <input type="text" name="shipping_fee" id="M2"  class="form-control" value="0" />元
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">定时送达：</label>
                    <div class="col-sm-5">
                        <label><input type="checkbox" name="" id="Sen"/>开启定时送达功能</label>
                        <i>开启后，买家下单选择同城送时，需要选择送达时间，商家按约定时间送达。</i>
                    </div>
                </div>
                <div class="form-group DsSend hide">
                    <label for="" class="col-sm-2 control-label">配送时间段：</label>
                    <div class="col-sm-8">
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
                        <i class="errMsg hide visitTimeErr">请至少选择一天接待时间</i>
                    </div>
                </div>
                <div class="form-group DsSend hide">
                    <label for="" class="col-sm-2 control-label">时段细分：</label>
                    <div class="col-sm-5">
                        <label><input type="radio" name="TimeSegment" id="" value="day" />天</label>
                        <label><input type="radio" name="TimeSegment" id="" value="MAN" />上午下午晚上（12:00和18:00为分界点）</label>
                        <label><input type="radio" name="TimeSegment" id="" value="hour" checked="checked"/>小时</label>
                        <label><input type="radio" name="TimeSegment" id="" value="halfHour" />半小时</label>
                        <i>买家可选的送达时间会根据时段进行细分。<a href="##">查看示例</a></i>
                    </div>
                </div>
                <div class="form-group DsSend hide">
                    <label for="" class="col-sm-2 control-label">预约下单：</label>
                    <div class="col-sm-6">
                        <label><input type="radio" name="BookingOrder" id="" checked="checked" value="no" />无需提前</label><br/>
                        <label>
                            <input type="radio" name="BookingOrder" id="" value="day" />提前
                            <select name="day">
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
                            </select> 天
                        </label><br/>
                        <i>为自然天，如：提前1天，则不管是凌晨1点还是晚上23点，都只能下明天以后的订单</i>
                        <label>
                            <input type="radio" name="BookingOrder" id="" value="hour" />提前
                            <select name="hour">
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
                            </select> 小时
                        </label><br/>
                        <label>
                            <input type="radio" name="BookingOrder" id="" value="minute" />提前
                            <select name="minute">
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
                            </select> 分钟
                        </label><br/>
                    </div>
                </div>
                <div class="form-group DsSend hide">
                    <label for="" class="col-sm-2 control-label">最长预约：</label>
                    <div class="col-sm-6">
                        <label><input type="radio" name="maxBooking" id="" checked="checked" value="no" />只能下当天单</label><br/>
                        <label>
                            <input type="radio" name="maxBooking" id="" value="day" />可预约
                            <select name="Yday">
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07" selected="selected">07</option>
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
                            </select> 天内订单
                        </label><br/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label"></label>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endsection
    @section('page_js')
    <!--bootstrap表单验证插件js-->
    <script src="{{ config('app.source_url') }}/static/js/bootstrapValidator.min.js" type="text/javascript" charset="utf-8"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/currency_3tf44wz3.js"></script>
    <!--特殊按钮js文件-->
    <script src="{{ config('app.source_url') }}mctsource/js/specialBtn.js" type="text/javascript" charset="utf-8"></script>
    @endsection