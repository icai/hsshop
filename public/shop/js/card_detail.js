$(function () {
    // add by zhaobin 2018-9-12
    // 新会员卡标识回调 
    $.ajax({
        url:'/shop/member/newMemberCardCallBack',
        type:'POST',
        data:{
            recordId:window.location.search.split('=')[1]
        },
        dataType:'json',
        headers: {
            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
        },
        success:function(res){
        }
    })
    // end
	//包邮 打折 优惠券部分的点击下拉显示
    $(".membership-region").on("click", function () {
        if ($(".membership ~ .block-sub-desc").css("display") == "block") {
            $(".membership ~ .block-sub-desc").hide();
            $(".membership-region ul").removeClass("down-arrow-right");
        } else {
            $(".membership ~ .block-sub-desc").show();
            $(".membership-region ul").addClass("down-arrow-right");
        }
    })
	//使用须知点击下拉
    $("a.js-show-sub-info").on("click", function () {
        if ($("p.arrow-right ~ .block-sub-desc").css("display") == "block") {
            $("p.arrow-right ~ .block-sub-desc").hide(); 
            $("p.arrow-right").removeClass("down-arrow-right");
        } else {
            $("p.arrow-right ~ .block-sub-desc").show(); 
            $("p.arrow-right").addClass("down-arrow-right");
        }
    })
    
    //点击领取会员卡按钮
    $(".btn-1-1 .js-obtain-card-btn").click(function(){
        var url = $(this).data('placement');
        var obj = $(this);  
        var wid = $("#wid").val();
        var encrypt_cardId = $("#card-id").val();
        if($(this).html() == "领取会员卡"){
            $.ajax({
                url:"/shop/member/getCardAction/" + wid + "/" + encrypt_cardId,
                type:'get',
                cache:false,
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tool.tip(response.info);
                         window.location.href =  "/"+response.url;
                        obj.remove();
                    }else{
                        tool.tip(response.info);
                    }
                },
                error : function() {
                    tool.tip("异常！");
                }
            });
        }else{
            var url = $(this).data("placement");
            $.ajax({
                url: url,
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function (response) {
                    if (response.status == 1){
                        tool.tip(response.info);
                        obj.remove();
                    }else{
                        tool.tip(response.info);
                    }
                },
                error : function() {
                    tool.tip("异常！");
                }
            });
        }

    })


    $("#del").click(function () {
        var url = $(this).data('route');
        var wid = $(this).data('wid');
        $.ajax({
            url:url,// 跳转到 action
            data:'',
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tool.tip(response.info)
                    window.location.href='/shop/member/mycards/'+wid;
                }else{
                    tool.tip(response.info)
                }
            },
            error : function() {
                tool("异常！");
            }
        });
    })
    
    //点击二维码标识和出示会员凭证
    $(".js-show-code").click(function(){
    	// tool.qrCode()
        $("#qcode").css("display", "block");
        $("#rnHzDb7ym0, #qcode .close").click(function () {
            $("#qcode").css("display", "none");
        })
    });
    
})