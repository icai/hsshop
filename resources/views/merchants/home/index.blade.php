@extends('merchants.default._layouts')

@section('title',$title)

@section('content')
	<!-- 中间 开始 -->
    <div class="middle">
        <div class="middle_header">
           <!-- 三级导航 开始 -->
            <div class="third_nav">
                <!-- 普通导航 开始 -->
                <ul class="common_nav">
                    <li class="hover">
                        <a href="微页面.html">微页面</a>
                    </li>
                    <li>
                        <a href="微页面草稿.html">微页面草稿</a>
                    </li>
                </ul>
                <!-- 普通导航 结束 -->
            </div>   
            <!-- 三级导航 结束 -->
            <!-- 帮助与服务 开始 -->
            <div class="help_btn">
                <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
            </div>
            <!-- 帮助与服务 结束 -->
        </div>
        <!-- 主体 开始 -->
        <div class="main">
            <div class="content">
                <!-- logo区域 开始 -->
                <div class="content_header">
                    <img src="images/1.jpg" width="50" height="50" />
                    <!-- 店铺名称 开始 -->
                    <div class="title_content">
                        <p class="homepage_title">
                            <strong>店铺主页</strong> (店铺主页)
                        </p>
                        <p class="title_des">创建时间：2016-09-22 15:57:22</p>
                    </div>
                    <!-- 店铺名称 结束 -->
                    <!-- 链接 开始 -->
                    <div class="link_itmes">
                        <div class="link_tab">
                            <span>编辑</span>
                        </div>
                        <div class="link_tab customTip_items">
                            <span>链接</span>
                            <!-- 店铺链接 开始 -->
                            <div class="custom_tip">
                                <input class="link_copy" type="text" value="www.baidu.com1" disabled /><div class="copy_btn">复制</div>    
                            </div>
                            <!-- 店铺链接 结束 -->
                        </div>
                        <div class="QRcode_items link_tab">
                            <span>二维码</span> 
                            <!-- 二维码 开始 -->
                            <div class="shop_QRcode">
                                <p class="items_title">手机扫码访问</p>
                                <div class="RQ_code img_wrap">
                                    <img src="" />
                                </div>
                                <div class="QRcode_bottom">
                                    <a href="javascript:void(0);">下载二维码</a>
                                </div>
                            </div>
                            <!-- 二维码 结束 -->
                        </div>
                    </div>
                    <!-- 链接 结束 -->
                </div>
                <!-- logo区域 结束 -->
                <!-- 新建模板 开始 -->
                <div class="model_itmes mgb20">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#micro_page">新建微页面</button>
                    <!-- 弹框 开始 -->
                    <div class="modal fade" id="micro_page" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- 弹框标题 开始 -->
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title" id="myModalLabel">选择新建模板</h4>
                                </div>
                                <!-- 弹框标题 结束 -->
                                <!-- 弹框主体 开始 -->
                                <div class="modal-body">
                                    主体
                                </div>
                                <!-- 弹框主体 结束 -->
                            </div>
                        </div>
                    </div>
                    <!-- 弹框 结束 -->
                    <!-- 分类&搜索 开始 -->
                    <div class="category_search">
                        <!-- 分类 开始 -->
                        <select class="chzn-select category_items" data-placeholder="Choose a Country" style="width:350px;" tabindex="1"> 
                            <option value=""></option>  
                            <option value="United States">United States</option>  
                            <option value="United Kingdom">United Kingdom</option>  
                            <option value="Afghanistan">Afghanistan</option>  
                            <option value="Albania">Albania</option>  
                        </select>
                        <!-- 分类 结束 -->
                        <!-- 搜索 开始 -->
                        <label class="search_items">
                            <input class="search_input" type="text" name="" value="" placeholder="搜索"/>   
                        </label>
                        <!-- 搜索 结束 -->
                    </div>
                    <!-- 分类&搜索 结束 -->
                </div>
                <!-- 新建模板 结束 -->
                <!-- 列表 开始 -->
                <table class="table table-hover">
                    <!-- 标题 -->
                    <tr class="active">
                        <td><input class="check_all" type="checkbox" name="" value="" >标题</td>
                        <td class="blue_38f">创建时间↓</td>
                        <td class="blue_38f">商品数</td>
                        <td>浏览UV/PV</td>
                        <td>到店UV/PV</td>
                        <td class="blue_38f">序号</td>
                        <td>操作</td>
                    </tr>
                    <!-- 列表 -->
                    <tr>
                        <td class="blue_00f"><input class="check_single" type="checkbox" name="" value=""/>你吗1</td>
                        <td>20161109</td>
                        <td>12</td>
                        <td>0</td>
                        <td>0</td>
                        <td class="blue_38f">0</td>
                        <td class="opt_wrap">
                            <a class="copy_list" href="javascript:void(0);">
                                <span class="blue_38f">复制</span>
                            </a>
                            <a href="javascript:void(0);">
                                <span class="blue_38f">编辑</span>
                            </a>
                            <a class="del_list" href="javascript:void(0);">
                                <span class="blue_38f">删除</span>
                            </a>
                            <a class="link_btn customTip_items" href="javascript:void(0);">
                                <span class="blue_38f">链接</span>
                                <!-- 店铺链接 开始 -->
                                <div class="custom_tip">
                                    <input class="link_copy" type="text" value="www.baidu.com4" disabled /><div class="copy_btn">复制</div>    
                                </div>
                                <!-- 店铺链接 结束 -->
                            </a>
                            <a class="set_homepage" href="javascript:void(0);">
                                <span>店铺主页</span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="blue_00f"><input class="check_single" type="checkbox" name="" value=""/>你吗</td>
                        <td>20161109</td>
                        <td>12</td>
                        <td>0</td>
                        <td>0</td>
                        <td class="blue_38f">1</td>
                        <td class="opt_wrap">
                            <a class="copy_list" href="javascript:void(0);">
                                <span class="blue_38f">复制</span>
                            </a>
                            <a href="javascript:void(0);">
                                <span class="blue_38f">编辑</span>
                            </a>
                            <a class="del_list" href="javascript:void(0);">
                                <span class="blue_38f">删除</span>
                            </a>
                            <a class="opt_btn customTip_items" href="javascript:void(0);">
                                <span class="blue_38f">链接</span>
                                <!-- 店铺链接 开始 -->
                                <div class="custom_tip">
                                    <input class="link_copy" type="text" value="www.baidu.com4" disabled /><div class="copy_btn">复制</div>    
                                </div>
                                <!-- 店铺链接 结束 -->
                            </a>
                            <a class="set_homepage" href="javascript:void(0);">
                                <span class="blue_38f">设为主页</span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="blue_00f"><input class="check_single" type="checkbox" name="" value=""/>你吗</td>
                        <td>20161109</td>
                        <td>12</td>
                        <td>0</td>
                        <td>0</td>
                        <td class="blue_38f">1</td>
                        <td class="opt_wrap">
                            <a class="copy_list" href="javascript:void(0);">
                                <span class="blue_38f">复制</span>
                            </a>
                            <a class="del_list" href="javascript:void(0);">
                                <span class="blue_38f">编辑</span>
                            </a>
                            <a class="del_list" href="javascript:void(0);">
                                <span class="blue_38f">删除</span>
                            </a>
                            <a class="opt_btn customTip_items" href="javascript:void(0);">
                                <span class="blue_38f">链接</span>
                                <!-- 店铺链接 开始 -->
                                <div class="custom_tip">
                                    <input class="link_copy" type="text" value="www.baidu.com4" disabled /><div class="copy_btn">复制</div>    
                                </div>
                                <!-- 店铺链接 结束 -->
                            </a>
                            <a class="set_homepage" href="javascript:void(0);">
                                <span class="blue_38f">设为主页</span>
                            </a>
                        </td>
                    </tr>
                </table>
                <!-- 列表 结束 -->
                <!-- 管理和分页 开始 -->
                <div class="manage_page">
                    <!-- 管理 开始 -->
                    <div class="manage_items">
                        <!-- 按钮 开始 -->
                        <button type="button" class="grounp_btn btn btn-default">批量改分类</button>
                        <!-- 按钮 结束 -->
                        <!-- 提示框 开始 -->
                        <div class="manage_tip">
                            <!-- 未分组 开始 -->
                            <div class="ungrouped_items">
                                <p class="items_title">您未创建分类</p>
                                <a class="blue_38f" href="javascript:void(0);">管理分类</a>
                            </div>
                            <!-- 未分组 结束 -->
                            <!-- 分组管理 开始 -->
                            <div class="grouped_items">
                                <!-- 分组头 开始 -->
                                <div class="grouped_header">
                                    <a class="items_title" href="javascript:void(0);">修改分类</a>
                                    <a class="blue_38f" href="javascript:void(0);">管理</a>
                                </div>
                                <!-- 分组头 结束 -->
                                <!-- 分组body 开始 -->
                                <div class="grouped_body">
                                    <label>
                                        <input type="checkbox" name="" value="" />
                                        分组1
                                    </label>
                                    <label>
                                        <input type="checkbox" name="" value="" />
                                        分组2
                                    </label>
                                </div>
                                <!-- 分组body 结束 -->
                                <!-- 分组底部 开始 -->
                                <div class="grouped_footer">
                                    <button type="button" class="btn btn-info btn-sm">确定</button>
                                    <button type="button" class="btn btn-default btn-sm">取消</button>
                                </div>
                                <!-- 分组底部 结束 -->
                            </div>
                            <!-- 分组管理 结束 -->
                        </div>
                        <!-- 提示框 结束 -->
                    </div>
                    <!-- 管理 结束 -->
                    <!-- 分页 开始 -->
                    <div class="page_items">
                        <nav>
                            <ul class="pagination">
                                <li class="disabled">
                                    <a href="javascript:void(0);">&laquo;</a>
                                </li>
                                <li class="active">
                                    <a href="javascript:void(0);">1 <span class="sr-only">(current)</span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">2</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">3</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">4</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">5</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">&raquo;</a>
                                </li>
                            </ul>
                        </nav> 
                        <div class="page_num">共 <span>2</span> 条，每页 20 条</div>   
                    </div>
                    <!-- 分页 结束 -->
                </div>
                <!-- 管理和分页 结束 -->
            </div>
            <!-- 底部logo 开始 -->
            <div id="app-footer" class="footer">
                <a href="javascript:void(0);" class="logo" target="_blank">HUISOU</a>
            </div>
            <!-- 底部logo 结束 -->
        </div>
        <!-- 主体 结束 -->
    </div>
    <!-- 中间 结束 -->
@endsection