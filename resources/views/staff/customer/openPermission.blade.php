@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.4.1 addCount.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-开通权限</span>
                <span><a href="/staff/openPermissionLog">操作日志</a></span>
            </div>
            <div class="main_content">
                <form class="form-horizontal" name="thereForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="userName" class="col-sm-2 control-label">手机号码(多个电话号码以"回车符"隔开)：</label>
                        <div class="col-sm-3">
                            <textarea name="phone" class="form-control" rows="8" value=""></textarea>
                        </div>
                    </div>

                     <div class="form-group clearfix">
                        <label for="isRegisterUser" class="col-sm-2 control-label">是否发送短信：</label>
                        <label class="radio-inline pull-left">
                            <input type="radio" name="isSendMsg" id="noSendMsg" value="0"> 不发送
                        </label>
                        <p class="pull-left"> 
                            <label class="radio-inline">
                                <input type="radio" name="isSendMsg" id="sendMsg" value="1"> 发送
                            </label>
                            <select name="msgTemplateId" class="form-control hidden templatesList">
                                <option value= "11">小程序领取短信</option>
                                <option value= "14">微商城领取短信</option>
                            </select> 
                        </p>
                    </div>
                    

                    <div class="form-group">
                        <label for="isRegisterUser" class="col-sm-2 control-label">是否创建账号：</label>
                        <label class="radio-inline">
                            <input type="radio" name="isRegisterUser" id="registerUser" value="1"> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="isRegisterUser" id="noRegisterUser" value="0"> 否
                        </label>
                    </div>
                    

                    <div class="form-group">
                        <label for="isCreateShop" class="col-sm-2 control-label">是否创建店铺：</label>
                        <label class="radio-inline">
                            <input type="radio" name="isCreateShop" id="createShop" value="1"> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="isCreateShop" id="noCreateShop" value="0"> 否
                        </label>
                    </div>


                    <div class="form-group shop">
                        <label for="permission" class="col-sm-2 control-label">权限列表：</label>
                        @foreach($data['permission'] as $v)
                        <label class="checkbox-inline">
                            <input type="radio" name="permission" value="{{ $v['id']}}"> {{ $v['name']}}
                        </label>
                        @endforeach
                    </div>
                    
                   
                
                    <!-- <div class="form-group shop">
                        <label for="isRegisterUser" class="col-sm-2 control-label">开通权限时间设置：</label>
                        <label class="radio-inline">
                            <input type="radio" name="timeType" id="inlineRadio1" value="1"> 免费版
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="timeType" id="inlineRadio2" value="2"> 一年
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="timeType" id="inlineRadio2" value="3"> 两年
                        </label>
                    </div> -->

                    <div class="form-group">
                        <label for="phoneNumber" class="col-sm-2 control-label"></label>
                        <div class="col-sm-3">
                            <input type="submit" value="确定" class="btn btn-primary sure sureSubmit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection
@section('foot.js')
        <!-- <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script> -->
        <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>

        <script type="text/javascript">


            $("#sendMsg").click(function(){
                $(".templatesList").removeClass('hidden')
            });
            $("#noSendMsg").click(function(){
                $(".templatesList").addClass('hidden')
            });




            // $(".shop").addClass('hidden')
            $("#noCreateShop").click(function(){
                $('input[name="permission"]').prop('checked',false)
                $('input[name="timeType"]').prop('checked',false)
                $(".shop").addClass('hidden')
            });

            $("#createShop").click(function(){
                $(".shop").removeClass('hidden')
            });

            $('body').on('click','.sureSubmit',function(e){
                e.stopPropagation();
                e.preventDefault();
                var d = {};
                var t = $('form[name="thereForm"]').serializeArray();
                $.each(t, function() {
                    d[this.name] = this.value;
                });
                submitData(d)
            })
            
            function submitData(params){
                $.ajax({
                    url:'/staff/openPermission',
                    data:params,
                    type:'post',
                    success:function(res){
                        if(res.status == 1){
                            tipshow(res.info,'info')
                            var timer = setTimeout(() => {
                                location.href=host+ '/staff/openPermission';
                                clearTimeout(timer)
                            }, 1000);
                        }else if(res.status == 0){
                            tipshow(res.info,'warn')
                        }
                    }
                })
            }
        </script>

@endsection