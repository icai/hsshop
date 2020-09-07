$(function () {
    $('.team-icon').mouseover(function () {
        $(this).find('.team-opt-wrap').show();
    }).mouseout(function () {
        $(this).find('.team-opt-wrap').hide();
    });

    $(document).on('click', '.team-icon', function () {
        window.location.href = $(this).data('href');
    })

    // 删除
    $('.delete-team').click(function () {
        wid = $(this).data('id');
        $(".del-shop").show();
        url = $(this).data('url');
        return false;
    })


    //   倒计时
    var t;
    function countDown() {
        var time = 60;
        $(".getCode").addClass("del");
        t = setInterval(function () {
            time--;
            var html = time + "s后重试";
            $(".getCode").html(html);
            if (time == 0) {
                clearInterval(t);
                $(".getCode").html("获取验证码").removeClass("del");
            }
        }, 1000)

    }


    var wid = "";
    // 获取验证码
    $(".getCode").click(function () {
        if ($(this).text() == "获取验证码") {
            countDown();
            $.get("/merchants/team/sendcode/"+wid,{"phone":$(".phone").text(),SMS_code:2},function(res){
                if(res.status==1){
                    tipshow(res.info);
                }else{
                    tipshow(res.info,'warn');
                }
                
                console.log(res);
            })
                
        }
    })

    // 是否选中删除风险
    $(".del-shop .sure input").click(function(){
        if($(this).is(":checked")){
            $(".del-shop .all-btn .delshop").addClass("red").removeAttr("disabled");
        }
        else{
            $(".del-shop .all-btn .delshop").removeClass("red").attr("disabled","disabled");
        }
    })

    // 取消删除
    $(".del-shop .close-btn ,.del-shop .cancel").click(function(){
        $(".del-shop").hide();
        if(t){
            clearInterval(t);
        }
        $(".del-shop .all-btn .delshop").removeClass("red").attr("disabled","disabled");
        $(".getCode").html("获取验证码").removeClass("del");
        $(".del-shop .sure input").removeAttr("checked");
        $("input[type='num']").val("");
    })

    // 确认删除
    var url = "";
    $(".del-shop .delshop").click(function(){
        var code = $("input[type='num']").val();
        if(code.length < 4){
            tipshow("请输入4位短信验证码");
            return;
        }
        var _token = $('input[name="_token"]').val();
        var code = $("input[type='num']").val();
        $.post(url, { _token: _token,code:code,wid: wid}, function (data) {
            tipshow(data.info);
            if (data.status == 1) {
                /* 后台验证通过 */
                if (data.url) {
                    /* 后台返回跳转地址则跳转页面 */
                    window.location.href = data.url;
                } else {
                    /* 后台没有返回跳转地址 */
                    // to do somethings
                    window.location.reload();
                }
            } else {
                /* 后台验证不通过 */
                $('input[type="submit"]').prop('disabled', false);
                // to do somethings
            }
        });
    })
    $('.adNav .host_3').click(function(){
        $('.modal-backdrop').show();
        $('.model_bg').show();
    })
    $('.model_bg .close_mode_bg').click(function(){
        $('.model_bg').hide();
        $('.modal-backdrop').hide();
    })
})
var ajax = function (ajaxInfo) {
    //定义默认值
    var defaultInfo = {
        type: "GET",                        //访问方式：如果dataPata不为空，自动设置为POST；如果为空设置为GET。
        dataType: 'json',      //数据类型：JSON、JSONP、text。由配置信息来搞定，便于灵活设置
        cache: true,                        //是否缓存，默认缓存
        xhrFields: {
            //允许跨域访问时添加cookie。cors跨域的时候需要设置
            withCredentials: true
        },
        urlPata: {},//url后面的参数。一定会加在url后面，不会加到form里。
        formPata: {},//表单里的参数。如果dataType是JSON，一定加在form里，不会加在url后面；如果dataType是JSONP的话，只能加在url后面。

        //url:  //依靠上层指定

        //timeout: 2000,
        error: function () {
        },  //如果出错，停止加载动画，给出提示。也可以增加自己的处理程序

        success: function () {
        } //成功后显示debug信息。也可以增加自己的处理程序
    };

    //补全ajaxInfo
    if (typeof ajaxInfo.dataType == "undefined") {
        ajaxInfo.dataType = defaultInfo.dataType;
    }

    if (typeof ajaxInfo.formPata == "undefined") {
        ajaxInfo.type = "GET";
    } else {
        if (ajaxInfo.dataType == "JSON") {
            ajaxInfo.type = "POST";
        } else {    //get或者jsonp
            ajaxInfo.type = "POST";
        }
        ajaxInfo.data = ajaxInfo.formPata;

    }

    if (typeof ajaxInfo.cache == "undefined") {
        ajaxInfo.cache = defaultInfo.cache;
    }
    //处理URL
    if (typeof ajaxInfo.urlPata != "undefined") {
        var tmpUrlPara = "";
        var para = ajaxInfo.urlPata;
        for (var key in para) {
            tmpUrlPara += "&" + key + "=" + para[key];
        }

        if (ajaxInfo.url.indexOf('?') >= 0) {
            //原地址有参数，直接加
            ajaxInfo.url += tmpUrlPara;
        } else {
            //原地址没有参数，变成?再加
            ajaxInfo.url += tmpUrlPara.replace('&', '?');
        }
    }
    //开始执行ajax
    $.ajax({
        type: ajaxInfo.type,
        dataType: ajaxInfo.dataType,
        cache: ajaxInfo.cache,
        xhrFields: {
            //允许跨域访问时添加cookie
            withCredentials: true
        },
        url: ajaxInfo.url,
        data: ajaxInfo.data,
        //timeout: 2000,
        error: function () { //访问失败，自动停止加载动画，并且给出提示
            alert("提交" + ajaxInfo.title + "的时候发生错误！");
            if (typeof top.spinStop != "undefined")
                top.spinStop();
            if (typeof ajaxInfo.error == "function") ajaxInfo.error();
        },

        success: function (data) {
            // if (typeof(parent.DebugSet) != "undefined")
            //     parent.DebugSet(data.debug);　　//调用显示调试信息的函数。

            // if (typeof (ajaxInfo.ctrlId) == "undefined")
            //     ajaxInfo.success(data);
            // else {
            //     ajaxInfo.success(ajaxInfo.ctrlId, data);
            // }
            if (typeof (ajaxInfo.success) != "undefined") {
                ajaxInfo.success(data);
            }
            tipshow(data.info);
            if (data.status == 1) {
                /* 后台验证通过 */
                if (data.url) {
                    /* 后台返回跳转地址则跳转页面 */
                    window.location.href = data.url;
                } else {
                    /* 后台没有返回跳转地址 */
                    // to do somethings
                    window.location.reload();
                }
            } else {
                /* 后台验证不通过 */
                $('input[type="submit"]').prop('disabled', false);
                // to do somethings
            }
        }
    });
};