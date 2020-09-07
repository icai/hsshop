$(function(){
	$('#datetimepicker1, #datetimepicker2').datetimepicker({
    	format: 'YYYY-MM-DD HH:mm:ss',               
	    dayViewHeaderFormat: 'YYYY 年 MM 月DD日',
	    useCurrent: true, 
    	showClear:true,                               
	    showClose:true,                               
	    showTodayButton:true,
	    locale:'zh-cn',
	    allowInputToggle:true, 
	    focusOnShow: true,
        useCurrent: false 				//必须要设置的
    });
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });



    $("#one").on('change',function () {
        var id = $(this).val();
        if ( id ) {
            var secdata = categoryData[id];
            var sec = '<option value="">二级分类</option>';
            if (secdata){
                for ( var i = 0; i < secdata.length; i++ ) {
                    sec +='<option value="' + secdata[i]['id'] + '">' + secdata[i]['title'] + '</option>';
                }
			}

            $('#sec').html(sec);
        }else{
            $('#sec').html('<option value="">二级分类</option>');
        }
    })

    var button = $('#btnUp');
    new AjaxUpload(button, {
        action: '/staff/fileUpload',
        name: 'file',
        data: {'_token':$('meta[name="csrf-token"]').attr('content')},
        onSubmit: function (file, ext) {
            if (!(ext && /^(jpg|jpeg|JPG|JPEG|png)$/.test(ext))) {
                tipshow("图片格式不正确")
                return false;
            }
            // return false;
            // change button text, when user selects file
            button.text('上传中');

            interval = window.setInterval(function () {
                var text = button.text();
                if (text.length < 10) {
                    button.text(text + '');
                } else {
                    button.text('上传中');
                }
            }, 200);
        },
        onComplete: function (file, response) {
            //file 本地文件名称，response 服务器端传回的信息
            // button.text('上传图片(只允许上传JPG格式的图片,大小不得大于150K)');

            window.clearInterval(interval);

            response =JSON.parse(response)
            if (response.status == 1){
                $("#btnUp").css("width",330);
                $("#btnUp").css("height",90);
                button.attr('src',imgUrl+response.data.s_path);
                $("#logo_path").val(response.data.s_path);
            }else{

            }

        }
    });

    $('input[type=radio][name=logo_type]').change(function() {
        if (this.value == 1) {
            $(".upImg").css("display","block");
            $("select[name='is_logo_open']").val(0);
        } else {
            $(".upImg").css("display","none");
            $("select[name='is_logo_open']").val(is_logo_open);
        }
    });


    $("#sub").click(function(){
        if ($("#sec").val() == 0){
            $("#sec").val($("#one").val());
        }
        $.ajax({
            url:'/staff/modifyShop',// 跳转到 action
            data:$("#myForm").serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){

                    $('#myModal').modal('hide')
                    tipshow(response.info, "info", 1000)
                    window.location.reload();
                }else{
                    tipshow(response.info, "warn", 1000)
                }
            },
            error : function() {
                // view("异常！");
                tipshow("异常！");
            }
        });



    });


    $("select[name='is_sms']").change(function (event) {
        if (this.value == 1){
            var result = confirm('短信验证账号打通 功能关闭后不能再次开启\n是否确认关闭该功能?');
            if (!result){
                $(this).find('option').eq(0).prop('selected',true);
            }
        }


    })

    //修改登录帐号
    $(".editAccount").click(function(e){
        var uid = $(this).data('uid');
        e.stopPropagation();//阻止事件冒泡
        var t_index = layer.open({
            type: 1,
            title:"修改登录账号",
            btn:["确认","取消"],
            yes:function(){
                var tt_index = layer.load(2, {time: 2000});
                $.ajax({
                    url:'/staff/userModify',
                    data:{
                        "uid":uid,
                        "phone":$('input[name="modify_phone"]').val(),
                    },
                    type:'post',
                    cache:false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    //dataType:'json',
                    success:function (data) {
                        layer.close(t_index);
                        if (data.status == 1){
                            tipshow(data.info,'info');
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }else{
                            tipshow(data.info, 'warn');
                        }
                    },
                    error : function() {
                        // view("异常！");
                        tipshow("异常！", 'warn');
                    },
                    complete : function(){
                        layer.close(tt_index);
                    }
                });
            },
            offset: '250px',
            closeBtn:false,
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤
            area: ['350px', 'auto'], //宽高
            content: $(".div_modify_account")
        });
    });

    // 修改登录密码
    $(".editPasswd").click(function(e){
        var uid = $(this).data('uid');
        e.stopPropagation();//阻止事件冒泡
        var t_index = layer.open({
            type: 1,
            title:"修改登录密码",
            btn:["确认","取消"],
            yes:function(){
                var tt_index = layer.load(2, {time: 2000});
                $.ajax({
                    url:'/staff/passwordModify',
                    data:{
                        "uid":uid,
                        "password":$('input[name="modify_password"]').val()
                    },
                    type:'post',
                    cache:false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    //dataType:'json',
                    success:function (data) {
                        layer.close(t_index);
                        if (data.status == 1){
                            tipshow(data.info,'info');
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }else{
                            tipshow(data.info, 'warn');
                        }
                    },
                    error : function() {
                        // view("异常！");
                        tipshow("异常！", 'warn');
                    },
                    complete : function(){
                        layer.close(tt_index);
                    }
                });
            },
            offset: '250px',
            closeBtn:false,
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤
            area: ['350px', 'auto'], //宽高
            content: $(".div_modify_password")
        });
    });

    
    // add by zhaobin 2018-9-20
    //添加地址；
    var area = "<option value=''>选择区县</option>";
    /*省市区三级联动*/
    $('.js-province').change(function(){
        var dataId = $('.js-province option:selected').val();
        var province = json[dataId];
        var city = "<option value=''>选择城市</option>";
        for(var i = 0;i < province.length;i ++){
            city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
        }
        $('.js-city').html(city);
        $('.js-area').html(area);
    });
    $('.js-city').change(function(){
        var dataId = $('.js-city option:selected').val();
        var city = json[dataId];

        for(var i = 0;i < city.length;i ++){
            area += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
        }
        $('.js-area').html(area);
    });
    // end
})