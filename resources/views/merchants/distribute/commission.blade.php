@extends('merchants.default._layouts')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/commission.css" />
@endsection
@section('slidebar')
    @include('merchants.distribute.slidebar')
@endsection
@section('middle_header')

    <div class="middle_header">
        <!-- 二级导航三级标题 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="common_nav"> 
                <li class="">  
                    <a href="">交易和记录</a>
                </li>  
            </ul>
            <!-- 面包屑导航 结束 -->
        </div>   
        <!-- 二级导航三级标题 结束 -->
    </div>
@endsection


@section('content')
<div class="content">
    
    <form class="form">

         <div class="form-group">
             <label class="col-sm-2 control-label">创建时间：</label>
             <input type="text" class="form-control col-xs-4" id="startTime" placeholder="" name="start-time">
             <label class="col-sm-2 control-label">至</label>
             <input type="text" class="form-control col-xs-4" id="endTime" placeholder="" name="end-time">
             <span class="blue-btn set-week">最近7天</span>
             <span class="blue-btn set-month">最近30天</span>
         </div>

         <div class="form-group">
             <label class="col-sm-2 control-label">微信昵称：</label>
             <input type="text" class="form-control col-xs-8" placeholder="微信昵称" name="wxID">
             <label class="col-sm-2 control-label">状态：</label>
             <select class="form-control" name="state">
                 <option value="" checked>全部</option>
                
                 <option value="0">待处理</option>
                 <option value="1">待打款</option>
                 <option value="2">打款成功</option>
                 <option value="3">已拒绝</option>
             </select>
         </div>
         <button type="button" class="btn btn-primary screen">筛选</button>
         <button type="button" class="btn btn-default outputChecked">导出所选记录</button>
         <button type="button" class="btn btn-default outputAll">导出全部记录</button>
    </form>

    <div class="state">
        <span class=" orange-under-line">全部</span>
        <span class="">待处理</span>
        <span class="">待确认打款</span>
        <span class="">打款成功</span>
        <span class="">已拒绝</span>
    </div>

    <table>
        
        


    </table>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                
            </ul>
        </nav>
  
    <div class="no-data">还没有相关数据</div>

    
    <!--打款提醒弹框-->
    <div class="bgcolor">
        <div class="play-money">
            <div class="header">是否确认将<span class="price">¥520.00</span>款项转到申请人<span class="type">余额</span>账户？</div>
            <div class="font-red">注：本表单做标注功能，第三方打款后，请再次确认已打款</div>
            <div class="footer">
                <span class="agree">同意</span>
                <span class="no-agree">取消</span>
            </div>
        </div>

          <!--确认打款-->
        <div class="sure-play-money">
            <div class="header">确认将<span class="price"></span>款项转到申请人<span class="type">银行卡</span>账户？</div>
            <div class="footer">
                <span class="make-sure">确认</span>
                <span class="cancel">取消</span>
            </div>
        </div>

            <!--拒绝打款-->
        <div class="off-money">

            <div class="header">是否拒绝打款</div>
            <div class="footer">
                <span class="yes">确定</span>
                <span class="no">取消</span>
            </div>
        </div>

        <!--确定拒绝打款-->
        <div class="sure-off-money">

            <div class="header">确认拒绝打款</div>
            <div class="footer">
                <span class="two-yes">确定</span>
                <span class="no">取消</span>
            </div>
        </div>
    </div>

  

</div>
@endsection

@section('page_js')
    <script>
        var company_pay = "{{$company_pay}}"
    </script>
	<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <script src="{{ config('app.source_url') }}static/js/moment/moment.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/moment/locales.min.js"></script>    
    <script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.js"></script>
    
    <script src="{{ config('app.source_url') }}mctsource/js/commission.js"></script>
@endsection