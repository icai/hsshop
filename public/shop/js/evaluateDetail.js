$(function(){
    var share = $('#share').val();
    if(share==1){
		$("#js-share-guide").hasClass("hide")?$("#js-share-guide").removeClass("hide"):"";
    }else if (share == 2){
        var wid = $("#wid").val();
        tool.custom("已评论成功", "你的使用心得对朋友的购买有很大的帮助哦~， 快给他们看看吧！","分享给好友", "再逛逛",'','/shop/index/'+wid)
        $(document).on("click", ".js_confirm_cancel", function(){
            $("#js-share-guide").removeClass("hide")
        })
    }
    //点击分享的蒙板使之消失
	$("#js-share-guide").click(function(){
		$("#js-share-guide").addClass("hide")
	})
	
	//点赞
	$("body").delegate( ".unlike","click", function(){
		var num = parseInt($(this).siblings("span").text())
		$(this).addClass("like").removeClass("unlike");
		$(this).siblings("span").css("color","#fe5722").text(num+=1);
        $.ajax({
            url:'/shop/product/evaluatePraise/'+$('#wid').val()+'/'+$('#eid').val(),// 跳转到 action
            data:'',
            type:'get',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                tool.tip("已点赞");
            },
            error : function() {
                // view("异常！");
                alert("异常！");
            }
        });

	})
	
    // 评论
    $(".publish").click(function(){
        $(".modal input[name='comment']").attr("placeholder","我有话说");
        $(".modal").show("300");
    })
	//评论取消
    $(".cover").click(function(){
        $(".modal input[name='comment']").attr("placeholder","我有话说");
        $(".modal").hide("300");
    })
    function replay () {
        //点击回复消息图片
        $(".replay").on('click',function(){
            $(".modal").show("300");
            var name = $(this).parent().prev().text();
            id = $(this).data('placement');
            $("#reply").attr('value',id);
            $(".modal input[name='comment']").attr("placeholder","回复" + name + ": ");

        })
        //发送评论
    }
    replay()
    $(".send").click(function(){
        var obj = $(this);
        if ($("input[name='comment']").val() == ''){
            tool.tip("说点什么吧");
            return false;
        }
        var  str = 'eid='+$('#eid').val()+'&content='+$("input[name='comment']").val();
        if ($("#reply").val() != ''){
            str = str+"&reply_id="+$("#reply").val();
        }

        $.ajax({
            url:'/shop/product/evaluateReply/'+$('#wid').val(),// 跳转到 action
            data:str,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    if (response.data.reply == null){
                        var content = response.data.content;
                    }else {
                        var content = '回复'+response.data.reply.nickname+':'+response.data.content;
                    }
                    var _html ='<div class="info"> ' +
                        '<div class="head"> ' +
                        '<img src="'+response.data.member.headimgurl+'" alt=""> ' +
                        '</div> ' +
                        '<div class="mation"> ' +
                        '<p>'+response.data.member.nickname+'</p> ' +
                        '<p class="time">'+response.data.created_at+'<span class="replay" data-placement="'+response.data.member.id+'"><img src="'+$('#source').val()+'shop/images/xx.png" alt="" class="xx-img"></span> </p> ' +
                        '<p class="info-comment">'+content+'</p> ' +
                        '</div> ' +
                        '</div>'
                    $('.list').append(_html);
                    $(".modal").hide("300");
                    tool.tip("评论成功");
                    $("input[name='comment']").val('');
                    replay()
                }else{
                    alert(response.info);
                }
            },
            error : function() {
                // view("异常！");
                alert("异常！");
            }
        });

    })
    //点击分享按钮
    $(".share").click(function(){
	    $("#js-share-guide").hasClass("hide")?$("#js-share-guide").removeClass("hide"):"";
    })


    //下拉加载更多
    var str = '';
    var page='';
    if(page==""){
        page=1;
    }
    var stop=true;//触发开关，防止多次调用事件
    $(window).scroll( function(event){
        if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
            if (stop == true) {
                stop = false;
                var url = "/shop/product/evaluateDetail/" + $("#wid").val();
                var eid = $('#eid').val();
                var _token = $('meta[name="csrf-token"]').attr('content');
                page = page + 1;//当前要加载的页码
                var parm = {'page': page, 'eid': eid,'_token':_token};
                $("#showlists").append("<li class='ajaxtips'><div style='font-size:2em'>Loding…..</div><>");
                $.post(url, parm, function (data) {
                	var data = JSON.parse (data);
                    if (data.data.data == '') {
                        return;
                    }
                    $.each(eval(data.data.data), function (data, val) {
                        if (val.reply == null){
                            var content = val.content;
                        }else {
                            var content = '回复'+val.reply.nickname+':'+val.content;
                        }
                        var _html ='<div class="info"> ' +
                            '<div class="head"> ' +
                            '<img src="'+val.member.headimgurl+'" alt=""> ' +
                            '</div> <div class="mation"> ' +
                            '<p>'+val.member.nickname+'</p>' +
                            '<p class="time">'+val.created_at+'<span class="replay" data-placement="'+val.member.id+'"><img src="'+$('#source').val()+'shop/images/xx.png" alt="" class="xx-img"></span> </p> ' +
                            ' <p class="info-comment">'+content+'</p> ' +
                            '</div> ' +
                            '</div>'
                        $('.list').append(_html);

                    });
                    stop = true;
                    replay()
                }, 'JSON')
            }
        }
    });


})