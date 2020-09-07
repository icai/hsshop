@if( $list == 'salesman' )
<div id="salesman" >
    <!-- 页面导航 开始 -->
    <ul class="sale_nav">
        <li class="active">
            <a href="$status=1">销售员</a>
        </li>
        <li>
            <a href="$status=1">审核信息</a>
        </li>
    </ul>
    <!-- 页面导航 结束 -->
    <!-- 筛选模块(销售员) 开始 -->
    <div class="screen_module">
        <form class="form-horizontal f12" action="" method="post">
            <!-- 验证时间： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">验证时间：</label>
                <div class="col-sm-3 center_start">
                    <!-- 开始时间 -->
                    <div id='start_time' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                    &nbsp;&nbsp;至
                </div>
                <div class="col-sm-3">
                    <!-- 结束时间 -->
                    <div id='end_time' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="7">最近7天</a>
                    &nbsp;<a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="30">最近30天</a>
                </div>
            </div>
            <!--  销售员： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员：</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" name="" value="" />

                </div>
                <div class="center_start col-sm-3">
                    <a class="btn btn-primary mgl10" href="javascript:void(0);">筛选</a>
                    <a class="btn btn-default mgl10">导出验证记录</a>
                </div>
            </div>
            <!--  提示 -->
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    <a class="recharge blue_38f f12" href="资产/充值.html">余额不足？补充余额</a>
                </div>
            </div>
        </form>
    </div>
    <!-- 筛选模块 结束 -->
    <!-- 列表模块（销售员） 开始 -->
    <table class="table table-hover f12">
        <tr class="active">
            <td>时间</td>
            <td>优惠码</td>
            <td>核销码</td>
            <td>卡券名称</td>
            <td>使用限制</td>
            <td>验证人员</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
    </table>
    <!-- 列表模块 结束 -->
    <!-- 筛选模块(审核信息) 开始 -->
    <div class="screen_module">
        <form class="form-horizontal f12" action="" method="post">
            <!-- 手机号： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">手机号：</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" name="" value="" />
                </div>
            </div>
            <!-- 验证时间： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">申请时间：</label>
                <div class="col-sm-3 center_start">
                    <!-- 开始时间 -->
                    <div id='start_day' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                    &nbsp;&nbsp;至
                </div>
                <div class="col-sm-3">
                    <!-- 结束时间 -->
                    <div id='end_day' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                </div>
            </div>
            <!--  累计： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    <select class="form-control" name="" >
                        <option value="1" selected>累计消费笔数</option>
                        <option value="2">累计消费金额</option>
                    </select>
                </label>
                <div class="col-sm-3 center_start">
                    <input class="form-control" type="text" name="" value="" />
                    &nbsp;&nbsp;至
                </div>
                <div class="col-sm-3">
                    <input class="form-control" type="text" name="" value="" />
                </div>
            </div>
            <!--  审核状态： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">审核状态：</label>
                <div class="col-sm-2">
                    <select class="form-control" name="status">
                        <option value="1" selected>待审核</option>
                        <option value="3">审核不通过</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <!-- 筛选模块 结束 -->
    <!-- 列表模块（销售员） 开始 -->
    <table class="table table-hover f12">
        <tr class="active">
            <td>时间</td>
            <td>优惠码</td>
            <td>核销码</td>
            <td>卡券名称</td>
            <td>使用限制</td>
            <td>验证人员</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
    </table>
    <!-- 列表模块 结束 -->
    <!-- 空列表 开始 -->
    <div class="no_result">还没有相关数据</div>
    <!-- 空列表 结束 -->
</div>
@elseif( $list == 'second' )
<div id="second" >
    <!-- 升级模块 开始 -->
    <div class="upgrade_module">
        <p class="mgb15">使用【二级销售】功能必须升级到高级版本&nbsp;<a class="f12 blue_38f" href="javascript:void(0);" target="_blank">了解二级销售</a></p>
        <a class="btn btn-success" href="应用订购.html">我要升级</a>
    </div>
    <!-- 升级模块 结束 -->
</div>
@elseif( $list == 'goods' )
<div id="goods" >
    <!-- 列表过滤部分 开始 -->
    <div class="widget-list-filter clearfix">
        <!-- 左边 开始 -->
        <div class="pull-left">
            <select name="">
                <option value="0" selected>全部商品</option>
                <option value="1">不参与推广的商品</option>
                <option value="2">自定义比例的商品</option>
            </select>
        </div>
        <!-- 左边 结束 -->
        <!-- 右边 开始 -->
        <div class="pull-right relative w350">
            <!-- 搜索 开始 -->
            <label class="search_items">
                <input class="search_input" type="text" name="" value="" placeholder="搜索"/>
            </label>
            <!-- 搜索 结束 -->
        </div>
        <!-- 右边 结束 -->
    </div>
    <!-- 列表过滤部分 结束 -->
    <!-- 列表模块（销售员） 开始 -->
    <table class="table table-hover f12">
        <thead>
        <tr class="active">
            <td><input class="check_all" type="checkbox" name="" value=""><span>商品</span></td>
            <td>库存</td>
            <td>总销量</td>
            <td>是否参与推广</td>
            <td>佣金比例</td>
            <td>操作</td>
            <td>操作时间</td>
        </tr>
        </thead>
        <tr>
            <td>
                <input class="check_single" type="checkbox" name="" value="" />
                <div class="img_wrap">
                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fs2_zeLW79XAF9YDcESRmEgWwl1e.jpg!100x100.jpg" />
                </div>
                <div>
                    <a class="blue_38f" href="javascript:void(0);">标题标题</a>
                    <p class="orange_f60">￥123.00</p>
                </div>
            </td>
            <td>588</td>
            <td>0</td>
            <td>参与</td>
            <td>－</td>
            <td><a class="blue_38f" href="javascript:void(0);" data-toggle="modal" data-target="#setModule">设置</a></td>
            <td>－</td>
        </tr>
        <tr>
            <td>
                <input class="check_single" type="checkbox" name="" value="" />
                <div class="img_wrap">
                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fs2_zeLW79XAF9YDcESRmEgWwl1e.jpg!100x100.jpg" />
                </div>
                <div>
                    <a class="blue_38f" href="javascript:void(0);">标题标题</a>
                    <p class="orange_f60">￥123.00</p>
                </div>
            </td>
            <td>588</td>
            <td>0</td>
            <td>参与</td>
            <td>－</td>
            <td><a class="blue_38f" href="javascript:void(0);" data-toggle="modal" data-target="#setModule">设置</a></td>
            <td>－</td>
        </tr>
        <tr>
            <td>
                <input class="check_single" type="checkbox" name="" value="" />
                <div class="img_wrap">
                    <img src="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/Fs2_zeLW79XAF9YDcESRmEgWwl1e.jpg!100x100.jpg" />
                </div>
                <div>
                    <a class="blue_38f" href="javascript:void(0);">标题标题</a>
                    <p class="orange_f60">￥123.00</p>
                </div>
            </td>
            <td>588</td>
            <td>0</td>
            <td>参与</td>
            <td>－</td>
            <td><a class="blue_38f" href="javascript:void(0);" data-toggle="modal" data-target="#setModule">设置</a></td>
            <td>－</td>
        </tr>
    </table>
    <!-- 列表模块 结束 -->
    <!-- 设置&分页模块 开始 -->
    <div class="pageSet_module">
        <!-- 设置模块 -->
        <button class="set_batch btn btn-default" type="button" data-toggle="modal">批量设置</button>
        <!-- 分页 -->
        <div class="page_module">
            <nav>
                <ul class="pagination">
                    <li class="disabled"><a href="#">&laquo;</a></li>
                    <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li><a href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- 设置&分页模块 结束 -->
    <!-- 空列表 开始 -->
    <div class="no_result">还没有相关数据</div>
    <!-- 空列表 结束 -->
</div>

@elseif( $list == 'result' )
<div id="result" >
    <!-- 筛选模块(销售员) 开始 -->
    <div class="screen_module">
        <form class="form-horizontal f12" action="" method="post">
            <!-- 验证时间： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">起止时间：</label>
                <div class="col-sm-3 center_start">
                    <!-- 开始时间 -->
                    <div id='start_time' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                    &nbsp;&nbsp;至
                </div>
                <div class="col-sm-3">
                    <!-- 结束时间 -->
                    <div id='end_time' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="7">最近7天</a>
                    &nbsp;<a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="30">最近30天</a>
                </div>
            </div>
            <!--  销售员： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员：</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" name="" value="" />
                </div>
                <label class="col-sm-1 control-label">订单号：</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" name="" value="" />
                </div>
                <div class="center_start col-sm-3">
                    <a class="btn btn-primary" href="javascript:void(0);">筛选</a>
                    <a class="btn btn-default">导出验证记录</a>
                </div>
            </div>
            <!--  提示 -->
            <div class="form-group">
                <label class="col-sm-1 control-label"></label>
                <div class="col-sm-8">
                    <a class="recharge blue_38f f12" href="资产/充值.html">余额不足？补充余额</a>
                </div>
            </div>
        </form>
    </div>
    <!-- 筛选模块 结束 -->
    <!-- 列表模块（销售员） 开始 -->
    <table class="table table-hover f12">
        <tr class="active">
            <td>时间</td>
            <td>优惠码</td>
            <td>核销码</td>
            <td>卡券名称</td>
            <td>使用限制</td>
            <td>验证人员</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
    </table>
    <!-- 列表模块 结束 -->
    <!-- 空列表 开始 -->
    <div class="no_result">还没有相关数据</div>
    <!-- 空列表 结束 -->
</div>

@elseif( $list == 'balance' )
<div id="balance" >
    <!-- 筛选模块(销售员) 开始 -->
    <div class="screen_module">
        <form class="form-horizontal f12" action="" method="post">
            <!-- 验证时间： -->
            <div class="form-group">
                <label class="col-sm-1 control-label">起止时间：</label>
                <div class="col-sm-3 center_start">
                    <!-- 开始时间 -->
                    <div id='start_time' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                    &nbsp;&nbsp;至
                </div>
                <div class="col-sm-3">
                    <!-- 结束时间 -->
                    <div id='end_time' class='input-group'>
                        <input class="form-control" type='text' />
                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="7">最近7天</a>
                    &nbsp;<a class="fastSelect_time blue_38f f12" href="javascript:void(0);" data-day="30">最近30天</a>
                </div>
            </div>
            <!--  销售员： -->
            <div class="form-group">
                <label class="col-sm-1 control-label">销售员：</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" name="" value="" />
                </div>
                <div class="center_start col-sm-4">
                    <a class="btn btn-primary mgl10" href="javascript:void(0);">筛选</a>
                    <a class="btn btn-default mgl10">导出验证记录</a>
                </div>
            </div>
            <!--  提示 -->
            <div class="form-group">
                <label class="col-sm-1 control-label"></label>
                <div class="col-sm-8">
                    <a class="recharge blue_38f f12" href="资产/充值.html">余额不足？补充余额</a>
                </div>
            </div>
        </form>
    </div>
    <!-- 筛选模块 结束 -->
    <!-- 列表模块（销售员） 开始 -->
    <table class="table table-hover f12">
        <tr class="active">
            <td>时间</td>
            <td>优惠码</td>
            <td>核销码</td>
            <td>卡券名称</td>
            <td>使用限制</td>
            <td>验证人员</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
    </table>
    <!-- 列表模块 结束 -->
    <!-- 空列表 开始 -->
    <div class="no_result">还没有相关数据</div>
    <!-- 空列表 结束 -->
</div>

@elseif( $list == 'relation' )
<div id="relation" >
    <!-- 筛选模块(销售员) 开始 -->
    <div class="screen_module">
        <form class="form-horizontal f12" action="" method="post">
            <!--  订单号： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">订单号：</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" name="" value="" />

                </div>
                <div class="center_start col-sm-3">
                    <a class="btn btn-primary mgl10" href="javascript:void(0);">查询</a>
                </div>
            </div>
        </form>
    </div>
    <!-- 筛选模块 结束 -->
    <!-- 列表模块（销售员） 开始 -->
    <table class="table table-hover f12">
        <tr class="active">
            <td>时间</td>
            <td>优惠码</td>
            <td>核销码</td>
            <td>卡券名称</td>
            <td>使用限制</td>
            <td>验证人员</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
        <tr>
            <td>2016-12-05 11:40:59</td>
            <td>774231967734</td>
            <td></td>
            <td>
                测试优惠码
                <p class="gray_999">价值：100.00元</p>
            </td>
            <td>200.00</td>
            <td>ronglinfang</td>
        </tr>
    </table>
    <!-- 列表模块 结束 -->
    <!-- 空列表 开始 -->
    <div class="no_result">还没有相关数据</div>
    <!-- 空列表 结束 -->
</div>

@elseif( $list == 'plan' )
<div id="plan" >
    <!-- app设计模块 开始 -->
    <div class="app_design">
        <!-- 设计主体 开始 -->
        <div class="design_body">
            <!-- 预览模块 开始 -->
            <div class="app_preview">
                <!-- 预览头部 开始 -->
                <div class="preview_header"></div>
                <!-- 预览头部 结束 -->
                <!-- 预览主体 开始 -->
                <div class="preview_body">
                    <!-- app标题区域 开始 -->
                    <div class="app_header editting">
                        <p class="app_title">销售员推广计划</p>
                    </div>
                    <!-- app标题区域 结束 -->
                    <!-- app主体区域 开始 -->
                    <div class="app_body">
                        <div class="preview_group editting">
                            <!-- 预览内容 开始 -->
                            <div class="group_content">
                                <div class="group_content_tip">
                                    <h4>计划说明</h4>
                                    <p>点击进行编辑</p>
                                </div>
                            </div>
                            <!-- 预览内容 结束 -->
                            <!-- 操作模块 开始 -->
                            <div class="actions_module">
                                <span class="edit_btn action_btn">编辑</span>
                            </div>
                            <!-- 操作模块 结束 -->
                        </div>
                    </div>
                    <!-- app主体区域 结束 -->
                </div>
                <!-- 预览主体 结束 -->
            </div>
            <!-- 预览模块 结束 -->
            <!-- 编辑模块 开始 -->
            <div class="app_edit">
                <!-- 编辑块 开始  -->
                <div class="edit_module">
                    <!-- 控制块 -->
                    <div class="edit_group">
                        <span class="group_name">链接地址：</span>
                        <div class="group_content">
                            <input class="link_copy box_flex1" type="text" value="www.baidu.com1" disabled /><div class="copy_btn btn btn-default">复制</div>
                        </div>
                    </div>
                </div>
                <!-- 编辑块 结束 -->
                <!-- 编辑块 开始 -->
                <div class="edit_module">
                    <!-- 箭头 -->
                    <div class="module_arrow"></div>
                    <!-- 控制块 -->
                    <div class="edit_group">
                        <span class="group_name">页面标题：</span>
                        <div class="group_content">
                            <input class="app_title box_flex1" type="text" value="www.baidu.com1" />
                        </div>
                    </div>
                </div>
                <!-- 编辑块 结束 -->
                <!-- 编辑块 开始 -->
                <div class="edit_module">
                    <!-- 箭头 -->
                    <div class="module_arrow"></div>
                    <!-- 控制块 -->
                    <div class="edit_group">
                        <div id="container" name="content"></div>
                    </div>
                </div>
                <!-- 编辑块 结束 -->
            </div>
            <!-- 编辑模块 结束 -->
        </div>
        <!-- 设计主体 结束 -->
        <!-- 设计底部 开始 -->
        <div class="design_bottom">
            <button type="button" class="btn btn-primary">保存</button>
            <button type="button" class="btn btn-default">预览</button>
        </div>
        <!-- 设计底部 结束 -->
    </div>
    <!-- app设计模块 结束 -->
</div>

@elseif( $list == 'set' )
<div id="set" >
    <!-- 表单模块(审核信息) 开始 -->
    <div class="form_module f12">
        <form class="form-horizontal" action="" method="post">
            <div class="form_title">销售员招募与管理</div>
            <!-- 二级销售 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">二级销售：</label>
                <div class="col-sm-8">
                    <label class="radio-inline">
                        <input type="radio" name="sale2" value="" checked> 开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="sale2" value=""> 关闭
                    </label>
                </div>
            </div>
            <!-- 销售员招募 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员招募：</label>
                <div class="col-sm-8">
                    <label class="radio-inline">
                        <input type="radio" name="" value="" checked /> 开启
                    </label>&nbsp;&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="" value="" />关闭
                    </label>
                </div>
            </div>
            <!-- 销售员审核 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员审核：</label>
                <div class="col-sm-8">
                    <div class="center_start">
                        <label class="radio-inline">
                            <input type="radio" name="" value="" checked /> 开启
                        </label>&nbsp;&nbsp;&nbsp;
                        <label class="radio-inline">
                            <input type="radio" name="" value="" />关闭
                        </label>&nbsp;&nbsp;&nbsp;
                    </div>
                    <p class="gray_999 mgt5">开启销售员审核功能后，消费者申请成为本店销售员需要经过商家审核。</p>
                </div>
            </div>
            <!-- 有效期设置 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">有效期设置：</label>
                <div class="col-sm-8">
                    <label class="radio-inline">
                        <input type="radio" name="" value="" checked /> 短期
                    </label>&nbsp;&nbsp;&nbsp;
                    <label class="radio-inline">
                        <input type="radio" name="" value="" />永久
                    </label>&nbsp;&nbsp;&nbsp;
                    <a class="tip_show blue_38f" href="javascript:void(0);">了解长/短期</a>
                    <div class="js_tip no">
                        <p class="mgb20">短期销售员是根据分享的有效期，跟踪分享链接7天，如果15天内分享出去的链接产生了订单，将计入销售员的业绩内；超过7天，后续产生的订单不再计入销售员业绩。</p>
                        <p>设置长期销售员，一旦销售员通过分享链接为您带来了客户，后续客户所有在您店铺内的消费都算作销售员的业绩。</p>
                        <p>但绑定关系后，如果A销售员的客户通过B销售员的分享链接购买，则客户算做B销售员的客户，所产生的订单将不再计入A销售员的业绩。</p>
                    </div>
                </div>
            </div>
            <!-- 销售员保护期 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员保护期：</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="">
                            开启销售员保护期
                        </label>
                    </div>
                    <div class="mgt10">
                        <input class="col-sm-2 product_date" type="text" name="" value="" disabled>&nbsp;&nbsp;天内，销售员发展的会员不会变更绑定关系
                    </div>&nbsp;&nbsp;&nbsp;
                    <p class="gray_999 mgt5">商家开启销售员保护期设置后，在保护期内，销售员发展的客户不会变更绑定关系。</p>
                </div>
            </div>
            <!-- 销售员建立客户关系： -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员建立客户关系：</label>
                <div class="col-sm-8">
                    <div class="radio">
                        <label class="radio-inline">
                            <input type="radio" name="" value="" checked /> 允许
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="" value="">不允许
                        </label>
                    </div>
                    <p class="gray_999 mgt5">设置允许后，商家允许销售员之间建立客户关系。</p>
                </div>
            </div>
            <div class="form_title">佣金结算与发放</div>
            <!-- 销售员购买权限 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">销售员购买权限：</label>
                <div class="col-sm-8">
                    <div class="radio">
                        <label class="radio-inline">
                            <input type="radio" name="" value="" checked /> 开启
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="" value="">关闭
                        </label>
                    </div>
                    <p class="gray_999 mgt5">设置允许后，商家允许销售员之间建立客户关系。</p>
                </div>
            </div>
            <!-- 结算方式 -->
            <div class="form-group mgb90">
                <label class="col-sm-2 control-label">结算方式：</label>
                <div class="col-sm-8">
                    <label class="radio-inline">
                        <input class="settlement_method" type="radio" name="settlement" value="0" checked data-class="settlement_time" /> 自动结算
                        <div class="js_tip center_start radio">
                            <label>
                                <input type="radio" name="" value="" checked>&nbsp;&nbsp;一级佣金比例
                            </label>
                            <label>
                                <input class="small" type="text" name="" value="" />&nbsp;%
                            </label>
                        </div>
                    </label>
                    <label class="radio-inline">
                        <input class="settlement_method" type="radio" name="settlement" value="1">人工结算
                        <div class="js_tip no radio">
                            <label class="center_start">
                                <input class="commission" type="radio" name="cps_select" value="" checked>&nbsp;&nbsp;一级佣金比例&nbsp;&nbsp;<input class="commission_input small" type="text" name="" value="" />&nbsp;%
                            </label>
                            <label class="center_start">
                                <input class="commission" type="radio" name="cps_select" value="">自定义
                            </label>
                        </div>
                    </label>&nbsp;&nbsp;&nbsp;
                    <a class="tip_show blue_38f" href="javascript:void(0);">了解长/短期</a>
                    <div class="js_tip no">
                        <p>开启自动结算后，默认按店铺佣金比例计算佣金（即此处您设置的比例）。您也可以在［商品列表］对单个商品进行佣金设置，设置后单商品的佣金比例优先级高于店铺佣金比例。</p>
                    </div>
                </div>
            </div>
            <!-- 结算时间 -->
            <div class="form-group settlement_time">
                <label class="col-sm-2 control-label">结算时间：</label>
                <div class="col-sm-8">
                    <div class="radio">
                        <label>
                            <input class="commission" type="radio" name="" value="" checked>交易完成结算
                        </label>
                    </div>
                    <div class="gray_999 mgb10">一般情况下发货后7天内（含7天）给销售员结算佣金，期间发生的退款会自动扣除（微信支付－自有除外）</div>
                    <div class="radio">
                        <label>
                            <input class="commission" type="radio" name="" value="" >售后维权处理期结束后再结算
                        </label>
                    </div>
                    <div class="gray_999">交易完成后需要再等7天，直到不会产生售后退款或处理完售后退款再给销售员结算佣金</div>
                </div>
            </div>
            <div class="form_title">页面信息展示</div>
            <!-- 佣金显示方式 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">佣金显示方式：</label>
                <div class="col-sm-8">
                    <div class="checkbox">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="" value="" checked /> 显示佣金
                        </label>&nbsp;&nbsp;&nbsp;
                        <label class="checkbox-inline">
                            <input type="checkbox" name="" value="" checked>显示佣金比例
                        </label>
                    </div>
                    <p class="gray_999 mgt5">设置允许后，商家允许销售员之间建立客户关系。</p>
                </div>
            </div>
            <!-- 商品列表排序 -->
            <div class="form-group">
                <label class="col-sm-2 control-label">商品列表排序：</label>
                <div class="col-sm-8">
                    <select name="goods_ordering_type" class="select inline">
                        <option value="1" selected="">商品佣金越高越靠前</option>
                        <option value="2">商品价格越高越靠前</option>
                        <option value="3">商品销量越高越靠前</option>
                        <option value="4">商品上架越晚越靠前</option>
                    </select>
                </div>
            </div>
            <div class="form_title">二级销售</div>
            <!-- 升级模块 开始 -->
            <div class="upgrade_module">
                <p class="mgb15">使用【二级销售】功能必须升级到高级版本&nbsp;<a class="f12 blue_38f" href="javascript:void(0);" target="_blank">了解二级销售</a></p>
                <a class="btn btn-success" href="应用订购.html">我要升级</a>
            </div>
            <!-- 升级模块 结束 -->
            <hr />
            <!-- 保存 -->
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-8">
                    <input class="submit_btn btn btn-primary" type="submit" name="" value="保存" />
                </div>
            </div>
        </form>
    </div>
    <!-- 表单模块 结束 -->
</div>

@elseif( $list == 'poster' )
<div id="poster" >
    <!-- 升级模块 开始 -->
    <div class="upgrade_module">
        <p class="mgb15">使用【二级销售】功能必须升级到高级版本&nbsp;<a class="f16 blue_38f" href="javascript:void(0);" target="_blank">了解二级销售</a></p>
        <a class="btn btn-success" href="应用订购.html">我要升级</a>
    </div>
    <!-- 升级模块 结束 -->
</div>
@endif