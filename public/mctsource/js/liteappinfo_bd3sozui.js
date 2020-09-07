$(function(){
    //小程序信息
    $.ajax({
        type:"get",
        url:"/merchants/xcx/config/query?id="+ id,
        async:true,
        success:function(res){
            if(res.code == 40000){
                $(".res_title").html(res.list.title);
                $(".res_version").html(res.list.version);
                $(".res_time").html(res.list.updated_at);
                $(".reason").val(res.list.reason);
//				$("input[name=merchant_name]").val(res.list.merchant_name);
                $("input[name=app_id]").val(res.list.app_id);
                $("input[name=app_secret]").val(res.list.app_secret);
                $("input[name=merchant_no]").val(res.list.merchant_no);
                $("input[name=app_pay_secret]").val(res.list.app_pay_secret);
                $("input[name=unit_id]").val(res.list.unit_id);
                $(".state_p").html(res.list.statusName);
                //自动更新
                if(res.list.is_auth_submit == 0){//0 关闭
                    $(".switch").removeClass("actived");
                }else{//1开启
                    $(".switch").addClass("actived");
                }
                $(".update_form .name").text(res.list.title);
                $(".update_form .edition").text(res.list.version);
                $(".update_form .time").text(res.list.updated_at);
                // $(".update_form .status").text(res.list.statusName);
                //提交小程序版本
                if(res.list.statusName == "审核中"){
                    $(".update_form .btn-d").hide();
                }else if(res.list.statusName == "审核被拒"){
                    $(".update_form .btn-d").text("重新提交稳定版");
                }
                if(res.list.status == 3){
                    var txt = res.list.reason.replace(/&gt;/g, ">")
                    txt = txt.replace(/&lt;/g, "<")
                    $("#audit_box").html(txt)
                    $("#audit_span").html(res.list.version)
                    $("#audit_failure").removeClass('hide')
                    $("#updata").removeClass('hide')
                }
            }else{
                tipshow(res.hint,"warn")
            }

        },
        error:function(){
            console.log("数据访问错误")
        }
    });

    //	解除绑定
    $(".mt_lab").click(function(){
        if($(".mt_che").prop("checked")){
            $(".bangd").removeClass("form_remov").addClass("btn_up").attr("disabled",false);
            $(".phide").removeClass("hide");
        }else{
            $(".bangd").addClass("form_remov").removeClass("btn_up").attr("disabled",true);
            $(".phide").addClass("hide");
        }
    });

    //解除绑定按钮点击 update 梅杰 20180726 解绑后跳转链接修改
    $("body").on('click','.btn_up',function(){
        $.ajax({
            type:"get",
            url:"/merchants/xcx/cancelAuthorizer?id="+id,
            data:"",
            async:true,
            success:function(res){
                console.log(res);
                if(res.errCode == 0){
                    tipshow("解除绑定成功")
                    window.location.href="/merchants/marketing/xcx/list";
                }else{
                    tipshow(res.errMsg,"warn")
                }
            },
            error:function(){
                alert("数据访问错误");
            }
        });
    })

    //	修改配置
    $("#form input").attr("disabled",true);
    $(".modify").click(function(){
        $("#form input").attr("disabled",false);
        $("#form input").removeClass("disabled").addClass("form-control");
        $(".checkbox input").removeClass("form-control");
        $(this).hide();
        $(".save-mod").show();
        $(".checkbox").removeClass("hide")
        $(".point").removeClass("hide")
        if(!$(".checkbox input").prop("checked")){
            $(".save-mod").css({"background":"rgba(0,0,0,0.3)","border-color":"rgba(0,0,0,0.3)"})
        }
    });
    $(".checkbox input").click(function(){
        if(!$(".checkbox input").prop("checked")){
            $(".save-mod").css({"background":"rgba(0,0,0,0.3)","border-color":"rgba(0,0,0,0.3)"})
        }else{
            $(".save-mod").css({"background":"#3197FA","border-color":"#3197FA"})
        }
    })
    $("body").on('click','.save-mod',function(){
        if(!$(".checkbox input").prop("checked")){
            return;
        };
        $.ajax({
            type:"post",
            url:"/merchants/xcx/config/processData?id="+id,
            async:true,
            data:$("#form").serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                console.log(res);
                if(res.code == 40000){
                    tipshow(res.hint);
                    $(".modify").show()
                    $(".save-mod").hide();
                    $("#form input").attr("disabled",true).removeClass("form-control").addClass("disabled");
                    $(".checkbox").addClass("hide")
                    $(".point").addClass("hide")
                }else{
                    tipshow(res.hint,'warn');
                }
            },
            error:function(){
                alert("数据访问错误")
            }
        });

    })
    /**
     * 自动更新小程序
     * @author huoguanghui
     * @created 2017年12月18日09:34:11
     */
    $(".switch").click(function(){
        var isAuthSubmit = 0;
        if($(this).hasClass("actived")){//已开启  去关闭
            isAuthSubmit = 0;
        }else{//已关闭 去开启
            isAuthSubmit = 1;
        }
        $.get("/merchants/marketing/isAuthAuditing?id="+id,{"isAuthSubmit":isAuthSubmit},function(res){
            if(res.status == 1){
                if(isAuthSubmit == 0){//0 关闭
                    $(".switch").removeClass("actived");
                    tipshow("已关闭自动更新");
                }else{//1 开启
                    $(".switch").addClass("actived");
                    tipshow("已开启自动更新");
                }
            }else{
                tipshow(res.info,"warn");
            }
        })
    })
    /**
     * 手动提交小程序
     * @author huoguanghui
     * @created 2017年12月19日10:26:26
     * @update 20180712 按指定id提交小程序
     */
    $(".btn-d").click(function(){
        $(".loadding").removeClass('hide')
        $.get("/merchants/marketing/nomalSubmitSave",{"xcxConfigId": id,"isSyncWeixin":true},function(res){
            $(".loadding").addClass('hide')
            if(res.status == 1){
                tipshow("小程序已提交审核");
                setTimeout(function(){
                    location.reload();
                },2000);
            }else{
                tipshow(res.info,"warn");
            }
        })
    });

    /**
     * 重新授权
     * @author wuxiaoping
     * 2018.01.23
     */
    $(".updateauthorized").click(function(){
        $.ajax({
            type: "GET",
            url: "/merchants/xcx/authorizer?type=updateauthorized",
            data:"",
            async: true,
            success: function(res) {
                console.log(res)
//				$('.set').attr('href',res.data);
                window.open(res.data)
            },
            error:function(){
                alert("数据访问错误")
            }
        })
    })


    //流量主设置
    $("#form_unit input").attr("disabled",true);
    $("body").on('click','.modify_unit',function(){
        $("#form_unit input").attr("disabled",false);
        $("#form_unit input").removeClass("disabled").addClass("form-control");
        $(this).hide();
        $(".save-mod-unit").show();
    });
    $("body").on('click','.save-mod-unit',function(){
        $.ajax({
            type:"post",
            url:"/merchants/xcx/unitData?id="+id,
            async:true,
            data:$("#form_unit").serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                console.log(res);
                if(res.code == 40000){
                    tipshow(res.hint);
                    $(".modify_unit").show()
                    $(".save-mod-unit").hide();
                    $("#form_unit input").attr("disabled",true).removeClass("form-control").addClass("disabled");
                }else{
                    tipshow(res.hint,'warn');
                }
            },
            error:function(){
                alert("数据访问错误")
            }
        });

    })


})