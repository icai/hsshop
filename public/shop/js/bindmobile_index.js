$(function(){
    //获取验证码
    $(".get_code").on('click',function(){
        var num = $(".ver_phone").val();
        if(!(/^1[345789]\d{9}$/.test(num))){
            tool.tip("手机号码有误，请重填");
            return false;
        }
        $(".phone_code").attr('disabled',true);
        $.ajax({
            type:"GET",
            url:"/shop/bindmobile/sendCode",
            data:{
                phone:$(".ver_phone").val()
            },
            async:true,
            success:function(res){
                if(res.status == 1){
                    $(".phone_code").addClass("col-ccc").val("60s");
                    tool.tip(res.info,'warn');
                    var n = 59;
                    function succs(){
                        $(".phone_code").val(n+"s"); // 显示倒计时
                        if(n == 0){
                            clearInterval(interval)
                            $(".phone_code").removeAttr('disabled').removeClass("col-ccc").val("获取验证码");
                        }
                        n--;
                    };
                    var interval = setInterval(succs,1000);
                }else{
                    $(".phone_code").removeAttr('disabled')
                    tool.tip(res.info);
                }
            },
            error:function(){
                $(".phone_code").removeAttr('disabled');
                alert('数据访问错误')
            }
        })
    })


    //确定提交
    $(".phone-up").click(function(){
        var num = $(".ver_phone").val();
        if(!(/^1[345789]\d{9}$/.test(num))){
            tool.tip("手机号码有误，请重填");
            return false;
        }
        $.ajax({
            type:"POST",
            url:"/shop/bindmobile/index/"+wid,
            data:{
                phone:$(".ver_phone").val(),
                code:$(".ver_code").val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async:true,
            success:function(res){
                if(res.status == 1){
                    window.location.href=url;
                }else{
                    tool.tip(res.info,'warn')
                }
            },
            error:function(){
                alert('数据访问错误')
            }
        });
    })
});