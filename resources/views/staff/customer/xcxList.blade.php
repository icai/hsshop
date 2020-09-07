@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/6.1 potential_customers.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/xcxList.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
<div class="main">
    <!-- 状态区域 -->
    <div class="nav">
        <ul>
            <li class="active">
                <a href="">全部</a>
            </li>
            <li>
                <a href="">无操作</a>
            </li>
            <li>
                <a href="">审核中</a>
            </li>
            <li>
                <a href="">审核被拒</a>
            </li>
            <li>
                <a href="">审核被拒</a>
            </li>
            <li>
                <a href="">已发布</a>
            </li>
            <li>
                <a href="">已提交代码</a>
            </li>
        </ul>
    </div>
    <!-- 状态区域 -->
    <!-- 搜索区域 -->
    <div class="search">
        <form>
            <span>搜索：</span>
            <select>
                <option>名称</option>
                <option>域名简称</option>
                <option>appid</option>
            </select>
            <input type="text" name="search">
            <span class="action_time">操作时间</span>
            <input type="text" name="start_time" id="start_time">
            <span>-</span>
            <input type="text" name="end_time" id="end_time">
            <button class="search_btn">搜索</button>
            <button class="get_host_btn">一键获取域名</button>
        </form>
    </div>
    <!-- 搜索区域 -->
    <!-- 表格区域 -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="success">
                    <th>ID</th>
                    <th>名称</th>
                    <th>域名简称</th>
                    <th>appid</th>
                    <th>进度</th>
                    <th>授权状态</th>
                    <th colspan="3">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr class="tr-id">
                    <th class="xcx-id" scope="row">324</th>
                    <td>苗木园林网</td>
                    <td>huisou</td>
                    <td>wxaf40165d6590bcc2</td>
                    <td>
                        已发布<br /><br/>
                        2017-08-23  09：07：53
                    </td>
                    <td>
                        已发布<br /><br/>
                        2017-08-23  09：07：53
                    </td>
                    <td class="action border-right-none">
                        <a href="javascript:void(0);" data-id="id" id="setting_host">设置域名</a>
                        <a href="javascript:void(0);" id="submit_code">提交审核</a>
                        <a href="javascript:void(0);" id="bind_experiencer">绑定体验者</a>
                        <a href="javascript:void(0);" id="see_news_modei">查看消息模板</a>
                    </td>
                    <td class="action border-right-none brder-left-none">
                        <a href="javascript:void(0);" id="upload_code">上传代码</a>
                        <a href="javascript:void(0);" id="get_category">获取类目</a>
                        <a href="">提交发布</a>
                        <a href="">朕要体验</a>
                    </td>
                    <td class="action brder-left-none">
                        <a href="javascript:void(0);" id="get_page">获取页面</a>
                        <a href="javascript:void(0);" id="see_detail">查看详情</a>
                        <a href="javascript:void(0);" id="cancel_experiencer">解绑体验者</a>
                        <a href="">设置消息模板</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- 表格区域 -->
</div>
<!-- 上传代码 -->
<div class="upload_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span>dshfj</span>
            </div>
        </div>
        <div>
            <div class="info">
                版本号：
            </div>
            <div>
                <input type="text" class="version" name="version" value="">
            </div>
        </div>
        <div>
            <div class="info">
                备注：
            </div>
            <div>
                <input type="text" class="baseinfo" name="baseinfo" value="">
            </div>
        </div>
    </div>
</div>
<!-- 上传代码 -->
<!-- 提交审核 -->
<div class="submit_code_model hide">
    <div class="code_model">
        <div>
            <div class="info">
                名称：
            </div>
            <div>
                <span>dshfj</span>
            </div>
        </div>
        <div class="">
            <div class="info">
                所在服务目录：
            </div>
            <div>
                <select>
                    <option>23432</option>
                </select>
            </div>
        </div>
        <div  class="">
            <div class="info">
                功能页面：
            </div>
            <div>
                <select>
                    <option>23432</option>
                </select>
            </div>
        </div>
        <div>
            <div class="info">
                标签：
            </div>
            <div>
                <input type="text" name="tag">
            </div>
        </div>
        <div>
            <div class="info">
                标题：
            </div>
            <div>
                <input type="text" name="title">
            </div>
        </div>
    </div>
</div>
<!-- 提交审核 -->
<!-- 查看消息模板 -->
<div class="see_code_model hide">
    <div class="see_model">
        <div>
            <div class="info">
                模板id：
            </div>
            <div>
                <p>dshfj</p>
            </div>
        </div>
        <div>
            <div class="info">
                通知对象：
            </div>
            <div>
                <p>sfsdfdsf</p>
            </div>
        </div>
        <div>
            <div class="info">
                模板内容：
            </div>
            <div>
                <p>sfsdfdsf</p>
            </div>
        </div>
        <div>
            <div class="info">
                模板示例：
            </div>
            <div>
                <p>sfdsfdsf</p>
            </div>
        </div>
    </div>
    <div class="see_model">
        <div>
            <div class="info">
                模板id：
            </div>
            <div>
                <p>dshfj</p>
            </div>
        </div>
        <div>
            <div class="info">
                通知对象：
            </div>
            <div>
                <p>sfsdfdsf</p>
            </div>
        </div>
        <div>
            <div class="info">
                模板内容：
            </div>
            <div>
                <p>sfsdfdsf</p>
            </div>
        </div>
        <div>
            <div class="info">
                模板示例：
            </div>
            <div>
                <p>sfdsfdsf</p>
            </div>
        </div>
    </div>
</div>
<!-- 查看消息模板 -->
<div class="detail_model hide">
    <div class="table-detail">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="table_title">名称</td>
                    <td>电饭锅电饭锅</td>
                    <td class="table_title">域名简介</td>
                    <td>zdyd</td>
                    <td class="table_title">域名绑定</td>
                    <td>未绑定</td>
                </tr>
                <tr>
                    <td class="table_title">授权方接口调用凭据(令牌)</td>
                    <td>gdjgkdfljgkdfjgkldfjgkldfjgkl反倒是艰苦奋斗数据库坚实的咖啡机圣诞快乐手机打开封建时代开了房圣诞节疯狂圣诞节疯狂dfjgkldfjgkldfjgkldfjgkldfjgkljkljlk</td>
                    <td class="table_title">令牌过期时间</td>
                    <td>2017-03-22 18:23:34</td>
                    <td class="table_title">接口调用凭据刷新令牌</td>
                    <td>gdjgkfdjgkjkdsjgkldfjgklfdjgkldfgjdflkgjlkdfgjklfdjgklfdjglkdfgj</td>
                </tr>
                <tr>
                    <td class="table_title">授权功能</td>
                    <td>234豆腐干豆腐水电费</td>
                    <td class="table_title">授权方公众号类型</td>
                    <td>订阅号(小程序默认为0)</td>
                    <td class="table_title">授权方认证</td>
                    <td>微信认证</td>
                </tr>
                <tr>
                    <td class="table_title">小程序原始ID</td>
                    <td>GGGDSGFDSFDSF</td>
                    <td class="table_title">授权方公众号所设置的微信号</td>
                    <td>订阅号(小程序默认为0)</td>
                    <td class="table_title">二维码图片URL</td>
                    <td>微信认证</td>
                </tr>
                <tr>
                    <td class="table_title">小程序主题名称</td>
                    <td>GGGDSGFDSFDSF</td>
                    <td class="table_title">账号介绍</td>
                    <td>订阅号(小程序默认为0)</td>
                    <td class="table_title">idc</td>
                    <td>微信认证</td>
                </tr>
                <tr>
                    <td class="table_title">服务器域名</td>
                    <td>GGGDSGFDSFDSF</td>
                    <td class="table_title">服务类目</td>
                    <td>订阅号(小程序默认为0)</td>
                    <td class="table_title">业务功能</td>
                    <td>微信认证</td>
                </tr>
                <tr>
                    <td class="table_title">小程序页面</td>
                    <td rowspan="3">GGGDSGFDSFDSF</td>
                    <td class="table_title">visit_status</td>
                    <td>微信认证</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/xcxList.js" type="text/javascript" charset="utf-8"></script>
@endsection