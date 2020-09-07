@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/marketing_lucky_draw.css" />
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
                    <a href="javascript:void(0);">幸运大抽奖</a>
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
                <li>列表活动</li>
            </ul>
            <a href="{{ config('app.url') }}home/index/detail/626/help" class="tutorial"><span id="icon">?</span>  查看如何玩转【幸运大抽奖】</a>
        </div>
        <!--中部主要内容-->
        <div class="content_center">
            <ul class="step">
                <li class="active">创建活动</li>
                <li>用户参与设置</li>
                <li>中奖设置</li>
                <li>完成</li>
            </ul>
            <div class="center_main cleac_float">
                <div class="center_left float_left">
                    <img src="public/hsadmin/images/lucky.jpg"/>
                    <img src="public/hsadmin/images/luckyPrize.png"/>
                    <div class="lucky_board">
                        <table border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <td id="prize_1"><img src="public/hsadmin/images/present1.png"/></td>
                                <td id="prize_2"><img src="public/hsadmin/images/present2.png"/></td>
                                <td><img src="public/hsadmin/images/smile1.png"/></td>
                                <td id="prize_4"><img src="public/hsadmin/images/present3.png"/></td>
                            </tr>
                            <tr>
                                <td><img src="public/hsadmin/images/smile2.png"/></td>
                                <td class="prize" colspan="2" rowspan="2"><img src="public/hsadmin/images/prize.png"/></td>
                                <td><img src="public/hsadmin/images/smile1.png"/></td>
                            </tr>
                            <tr>
                                <td id="prize_9"><img src="public/hsadmin/images/present2.png"/></td>
                                <td id="prize_12"><img src="public/hsadmin/images/present3.png"/></td>
                            </tr>
                            <tr>
                                <td><img src="public/hsadmin/images/smile1.png"/></td>
                                <td id="prize_14"><img src="public/hsadmin/images/present2.png"/></td>
                                <td><img src="public/hsadmin/images/smile2.png"/></td>
                                <td id="prize_16"><img src="public/hsadmin/images/present1.png"/></td>
                            </tr>
                        </table>
                        <a href="###">查看奖品</a>
                        <span class="activity valid_time">
	            					1.活动有效时间：<br />
	            					<span id="beginTime">未填写 </span>至
	            					<span id="closeTime">未填写</span>
	            				</span><br />
                        <span class="activity issuer">
	            					2.发行方：<br />
	            					<span id="issuer_name">拿去用121212121212</span>
	            				</span>
                    </div>
                </div>
                <div class="center_right float_right rtv">
                    <form id="defaultForm" method="post" class="form-horizontal" action="" >
                        <!--第一步-->
                        <div class="steps step_1">
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><i>*</i>活动名称:</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="activeName" placeholder="填写活动名称" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><i>*</i>开始时间:</label>
                                <div class="col-lg-8">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' class="form-control" name="Btime" />
                                        <span class="input-group-addon">
								                    <span class="glyphicon glyphicon-calendar"></span>
								                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><i>*</i>结束时间:</label>
                                <div class="col-lg-8">
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input type='text' class="form-control" name="Ctime" />
                                        <span class="input-group-addon">
								                    <span class="glyphicon glyphicon-calendar"></span>
								                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">活动说明:</label>
                                <div class="col-lg-8">
                                    <textarea name="" rows="3" cols="25" ></textarea>
                                </div>
                            </div>
                        </div>
                        <!--第二部-->
                        <div class="steps step_2 hide">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">消耗积分:</label>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" name="lose_integral" placeholder="为0时不消耗积分" />
                                    <p style="color: #999;font-size: 12px;">用户每次参与需要消耗积分</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">参与送积分:</label>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" name="send_integral" placeholder="请填写 积分" />
                                    <label><input type="checkbox" name="" id="" value="" />仅送给未中奖的用户</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><i>*</i>参与次数:</label>
                                <div class="col-lg-8">
                                    <label><input type="radio" name="participate_number" value="" checked="checked" />一人一次</label>
                                    <label><input type="radio" name="participate_number" value="" />一天一次</label>
                                    <label><input type="radio" name="participate_number" value="" />一人两次</label>
                                </div>
                            </div>
                        </div>
                        <!--第三部-->
                        <div class="steps step_3 hide">
                            <div class="line rtv">
                                <p class="abt">中奖概率</p>
                            </div>
                            <div class="win_rate flex_middle">
                                中奖率<input type="text" class="form-control" placeholder="0-100" />%
                                <a href="##" class="icon">?</a>
                            </div>
                            <div class="line rtv">
                                <p class="abt">设置奖品</p>
                            </div>
                            <p class="setPrize_hint">等级设置的奖品数量越多，则该等级中奖率越高。</p>
                            <p class="setPrize_hint">例如：设置一等奖 10个，二等奖20个，则中二等奖概率高于一等奖</p>
                            <div class="set_prizeDiv">
                                <ul class="prize_list cleac_float">
                                    <li class="selected">奖品一</li>
                                    <li>奖品二</li>
                                    <li>奖品三</li>
                                    <li>奖品四</li>
                                </ul>
                                <div class="prizeDiv prize_selectDiv_1">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">选择奖品:</label>
                                        <div class="col-lg-8">
                                            <label><input type="radio" name="preferential_1" value="" checked="checked" />赠送积分</label>
                                            <label><input type="radio" name="preferential_1" value="" />送优惠</label>
                                            <label><input type="radio" name="preferential_1" value="" />赠品</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">赠送积分:</label>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control" name="prize_send_integral_1" placeholder="请填写积分数" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">奖品数量:</label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                                <input class="form-control result" type="text" name="prize_number_1" value="0">
                                                <div class="input-group-addon">个</div>
                                            </div>
                                            <p class="hint">奖品数量为0时不设此奖项</p>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group">
                                        <label class="selImg col-lg-4 control-label"></label>
                                        <div class="col-lg-8">
                                            <a href="##" class="btn btn-success update" style="color: white;">上传奖品图片</a>
                                            <a href="##" class="btn btn-default clear" style="color: black;">清空</a>
                                            <p class="hint">仅支持 jpg、png、 尺寸48*48 不超过1M</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="prizeDiv prize_selectDiv_2 hide">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">选择奖品:</label>
                                        <div class="col-lg-8">
                                            <label><input type="radio" name="preferential_2" value="" checked="checked" />赠送积分</label>
                                            <label><input type="radio" name="preferential_2" value="" />送优惠</label>
                                            <label><input type="radio" name="preferential_2" value="" />赠品</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">赠送积分:</label>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control" name="prize_send_integral_2" placeholder="请填写积分数" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">奖品数量:</label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                                <input class="form-control result" type="text" name="prize_number_2" value="1">
                                                <div class="input-group-addon">个</div>
                                            </div>
                                            <p class="hint">奖品数量为0时不设此奖项</p>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group">
                                        <label class="selImg col-lg-4 control-label"></label>
                                        <div class="col-lg-8">
                                            <a href="##" class="btn btn-success update" style="color: white;">上传奖品图片</a>
                                            <a href="##" class="btn btn-default clear" style="color: black;">清空</a>
                                            <p class="hint">仅支持 jpg、png、 尺寸48*48 不超过1M</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="prizeDiv prize_selectDiv_3 hide">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">选择奖品:</label>
                                        <div class="col-lg-8">
                                            <label><input type="radio" name="preferential_3" value="" checked="checked" />赠送积分</label>
                                            <label><input type="radio" name="preferential_3" value="" />送优惠</label>
                                            <label><input type="radio" name="preferential_3" value="" />赠品</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">赠送积分:</label>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control" name="prize_send_integral_3" placeholder="请填写积分数" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">奖品数量:</label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                                <input class="form-control result" type="text" name="prize_number_3" value="2">
                                                <div class="input-group-addon">个</div>
                                            </div>
                                            <p class="hint">奖品数量为0时不设此奖项</p>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group">
                                        <label class="selImg col-lg-4 control-label"></label>
                                        <div class="col-lg-8">
                                            <a href="##" class="btn btn-success update" style="color: white;">上传奖品图片</a>
                                            <a href="##" class="btn btn-default clear" style="color: black;">清空</a>
                                            <p class="hint">仅支持 jpg、png、 尺寸48*48 不超过1M</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="prizeDiv prize_selectDiv_4 hide">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">选择奖品:</label>
                                        <div class="col-lg-8">
                                            <label><input type="radio" name="preferential_4" value="" checked="checked" />赠送积分</label>
                                            <label><input type="radio" name="preferential_4" value="" />送优惠</label>
                                            <label><input type="radio" name="preferential_4" value="" />赠品</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">赠送积分:</label>
                                        <div class="col-lg-7">
                                            <input type="number" class="form-control" name="prize_send_integral_4" placeholder="请填写积分数" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">奖品数量:</label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                                <input class="form-control result" type="text" name="prize_number_4" value="3">
                                                <div class="input-group-addon">个</div>
                                            </div>
                                            <p class="hint">奖品数量为0时不设此奖项</p>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="form-group">
                                        <label class="selImg col-lg-4 control-label"></label>
                                        <div class="col-lg-8">
                                            <a href="##" class="btn btn-success update" style="color: white;">上传奖品图片</a>
                                            <a href="##" class="btn btn-default clear" style="color: black;">清空</a>
                                            <p class="hint">仅支持 jpg、png、 尺寸48*48 不超过1M</p>
                                        </div>
                                    </div>
                                </div>
                                <!--点击之后的蒙板和弹框-->
                                <div id="maskDiv">
                                    <div id="layer">
                                        <div class="layerTop">
                                            <div class="layerTop_left">
                                                <a href="##">我的图片</a>
                                                <a href="#uploadImgLayer" style="color: #27f;">图标库</a>
                                            </div>
                                            <div class="layerTop_right">
                                                <div class="searchImg">
                                                    <img src="public/static/images/chosen-sprite.png"/>
                                                </div>
                                                <input type="text" name="search" id="search" class="form-control" placeholder="搜索" />
                                                <a href="##"  class="closeImg">
                                                    <img src="public/static/images/chosen-sprite.png"/>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="layerContent" class="cleac_float">
                                            <div id="layerContent_left">
                                                <a href="##"><p>未分组</p><n>0</n></a>
                                            </div>
                                            <div id="layerContent_right">
                                                <a href="#uploadImg">+</a>
                                                <p>暂无数据，点击添加</p>
                                            </div>
                                        </div>
                                        <div id="layerBottom">
                                            <button type="button">确认</button>
                                        </div>
                                    </div>
                                    <!--上传图片弹出层-->
                                    <div id="uploadImgLayer" style="display: none; height: 500px;">
                                        <div class="layerTop">
                                            <div class="layerTop_left">
                                                <a href="#layer" style="color: #27f;">>选择图片</a>
                                                <a href="##"> | 上传图片</a>
                                            </div>
                                            <div class="layerTop_right">
                                                <a href="##" class="closeImg" style="margin-top: -14px;">
                                                    <img src="public/static/images/chosen-sprite.png"/>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="uploadLayerContent">
                                            <div id="uploadLayerContent_top">
                                                网络图片：
                                                <input type="text" class="form-control" placeholder="请添加网络地址">
                                                <button class="btn btn-default" type="button" style="height: 35px;">提取</button>
                                            </div>
                                            <div id="uploadLayerContent_botm">
                                                本地图片：<i style="display: inline-block; margin-left: 70px; color: #C3C3C3;">仅支持jpg、gif、png三种格式, 大小不超过1 MB</i>
                                                <div id="img">
                                                    <div class="imgbox1">
                                                        <div class="imgnum ">
                                                            <input type="file" class="filepath1" />
                                                            <span class="close1">×</span>
                                                            <img src="public/hsadmin/images/add.png" class="img11" />
                                                            <img src="" class="img22" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="uploadLayerBottom">
                                            <a class="btn btn-default">确认</a>
                                        </div>
                                    </div>
                                    <!--图标库弹出层-->
                                    <div id="iconLibraryLayer" style="display: none;">
                                        <div class="layerTop">
                                            <div class="layerTop_left">
                                                <a href="#firLayer" style="color: #27f;">我的图片</a>
                                                <a href="#firLayer">图标库</a>
                                            </div>
                                            <div class="layerTop_right">
                                                <a href="##" class="closeImg" style="margin-top: -14px;">
                                                    <img src="public/static/images/chosen-sprite.png"/>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="iconLibraryContent">
                                            <ul id="iconStyleSelect">
                                                <li id="style">风格: <a href="##" class="selected">全部</a><a href="##">普通</a><a href="##">简约</a></li>
                                                <li id="color">颜色: <a href="##" class="selected">全部</a><a href="##">白色</a><a href="##">灰色</a></li>
                                                <li id="type">类型: <a href="##" class="selected">全部</a><a href="##">常规</a><a href="##">购物</a><a href="##">交通</a><a href="##">食物</a><a href="##">商务</a><a href="##">娱乐</a><a href="##">美妆</a></li>
                                            </ul>
                                            <div id="iconImgShow">
                                                <ul id="iconImgSelect">
                                                    <li>1<img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li>2<img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                    <li><img src="public/static/js/kindeditor/plugins/multiimage/images/image.png"/></li>
                                                </ul>
                                            </div>
                                            <div id="pageNum">
                                                共<span>270</span>条，每页27条&nbsp;&nbsp;
                                                <div id="show"></div>
                                                <div class="pagination"><!--分页器--></div>
                                            </div>
                                            <hr />
                                            <div id="iconLibraryLayerBtn">
                                                <button type="button" class="btn btn-default">确定</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="line rtv">
                                <p class="abt">中奖结果说明</p>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">未中奖说明:</label>
                                <div class="col-lg-8">
                                    <textarea name="" rows="3" cols="25" placeholder="哎呀，真可惜，擦身而过！"></textarea>
                                </div>
                            </div>
                        </div>
                        <!--第四部-->
                        <div class="steps step_4 hide">
                            <p style="text-align: center; font-size: 18px; padding-top: 20px;">您已成功创建该活动！</p>
                            <hr />
                            <div class="form-group">
                                <label class="col-lg-4 control-label">链接地址:</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" id="linkCopy" class="form-control" value="25854@12.com">
                                        <span class="input-group-btn">
										        	<button id="copy" class="btn btn-default" type="button">复制</button>
										      	</span>
                                    </div>
                                    <p class="hint">复制该链接给你的粉丝</p>
                                </div>
                            </div>
                            <div class="successPromrt hide">复制成功</div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">二维码:</label>
                                <div class="col-lg-8">
                                    <img src="public/hsadmin/images/二维码.png"/ height="120" width="120">
                                    <a href="public/hsadmin/images/二维码.png" download="picture">下载</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <a href="##">设置带参数的二维码</a>
                                </div>
                            </div>
                        </div>
                        <!--底部按钮部分-->
                        <div class="functionBtn fix">
                            <a href="##" class="btn btn-default prev hide">上一步</a>
                            <a href="##" class="btn btn-primary next">下一步</a>
                            <a href="##" class="btn btn-default reset hide">修改</a>
                            <button type="submit" class="btn btn-primary sure hide">确认</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/marketing_lucky_draw.js.js" type="text/javascript" charset="utf-8"></script>
@endsection