require(['jquery', 'base','layer'], function(){
    //新建微页面点击
    $('#add_page').click(function(){
        $.get("/merchants/marketing/getResearchTemplateList",function(res){
            // add by 赵彬 2018-8-8
            var type = $('#add_page').data('type') 
            var data = res.data;
            var newData = [];
            for(var i=0;i<data.length;i++){
                if(data[i].activity_type == type || data[i].activity_type == 99){
                    newData.push(data[i])
                }
            }
            //end  
            var html = "";
            if(newData.length != 0){
                for(var i = 0; i < newData.length; i++){
                    html += '<li ><div class="img-wrap template-state-2">';
                    html += '<img class="template-screenshot" src="' + SOURCE_URL + newData[i].thumb_url + '">';
                    html += '<div class="template-cover"><div class="template-action-container">';
                    html += '<a href="' + newData[i].url+'&type='+ type + '" class="zent-btn zent-btn-success js-select-template" style="width: 88px;">' + "使用模板" + '</a>';
                    html += '</div></div></div><p class="template-title">';    
                    html += '<span>' + newData[i].title + '</span></p></li>';        
                } 
                
            }
            $(".widget-feature-template-list").empty().append(html);
            setTimeout(function(){
                $('.widget-feature-template').show();
                $('.modal-backdrop').show();
            },200)
       })
    })
    // 微页面模板弹窗关闭点击
    $('.close').click(function(){
        $('.widget-feature-template').hide();
        $('.modal-backdrop').hide();
    })
    // 复制链接跟随效果
    $('body').on('click','.link_btn',function(e){
        e.stopPropagation();//组织事件冒泡
        var _this = $(this);
        var _url = $(this).data('url');             // 要复制的连接
        var html ='<div class="input-group">';
        html +='<input type="text" class="link_copy form-control" value="'+_url+'" disabled >';
        html +='<a class="copy_btn input-group-addon">复制</a>';
        html +='</div>';
        showDelProver(_this,function(){},html,'false');             // 跟随效果
    });
    // 复制链接
    $('body').on('click','.copy_btn',function(e){
        e.stopPropagation();//组织事件冒泡
        var obj = $(this).siblings('.link_copy');
        console.log(obj)
        copyToClipboard( obj );
        tipshow('复制成功','info');
        $(this).parents('.del_popover').remove();
    });

    //二维码
    $("body").on("click",".two-code",function(e){
        $(".t-pop").remove();
        var _this = $(this);
        e.stopPropagation();//阻止事件冒泡 
        var div = document.createElement("div");
        div.className ="t-pop";
        div.style.top =$(this).offset().top-10+"px";
        div.style.left=$(this).offset().left-191+"px";
        var html ='<div class="t-pop-header">活动二维码<div class="flo-rig">X</div></div><div class="t-pop-content">';
        html +='</div><div class="t-pop-footer"><p class="xiazai">扫一扫立即查看</p></div>'
        div.innerHTML=html;      
        $("body").append(div);
        var id = $(this).data('id');
        // $.ajax({
        //     type:"get",
        //     url:"/merchants/marketing/vote/createQrcode?id="+id,
        //     async:true,
        //     success:function(res){
        //         var content = '<img src="'+res.data+'" width="140" height="140" />';
        //         $('.t-pop-content').html(content)
        //         showDelProver(_this,function(){},$('.t-pop').html(),'false');
        //     },
        //     error:function(){
        //         alert('数据访问错误');
        //     }
        // });
        $(".t-pop").hide();
        
    });

     $("body").on("click",".flo-rig",function(e){
    	console.log(2)
    	$(".popover").remove();
    });

    // 删除列表
    $('body').on('click','.delBtn',function(e){            
        e.stopPropagation();
        var _this = this;
        // var id=$(this).data('id');
        console.log(id);
        var id = []
        id.push($(this).data('id'))
        console.log(id);
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/marketing/researchDelete',
                data:{
                    ids:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    console.log(res);
                    if(res.status===1){
                        tipshow('删除成功','info');
                        // $(_this).parents('.data_content').remove();
                        setTimeout(function () {
                            location.reload();
                        },1500)
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            });
        })
    });

    //批量删除
    $("#close_all").click(function(){
        var id = [];
        var inps = $('.close_inp_id')
        for(var i = 0; i < inps.length; i++){
            if($(inps[i]).prop('checked')){
                id.push($(inps[i]).val())
            }
        }
        console.log(id);
        var t_index = layer.open({
            type: 1,
            title:"提示",
            btn:["确定","取消"],
            yes:function(){
                $.ajax({
                    url:'/merchants/marketing/researchDelete',
                    data:{
                        ids:id
                    },
                    type:"post",
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        layer.closeAll();
                        //保存成功后 移除新增栏目 插入新的ul
                        console.log(res);
                        if(res.status===1){
                            tipshow('删除成功','info');
                            // $(_this).parents('.data_content').remove();
                            setTimeout(function () {
                                location.reload();
                            },1500)
                        }else{
                            tipshow('删除失败','warn');
                        }
                    },
                    error:function(){
                        layer.closeAll();
                        tipshow("异常","wram");
                    }
                });
            },
            closeBtn:false,
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤
            area: ['250px', 'auto'], //宽高
            content:'<p style="margin:20px 15px;">确定批量删除吗?</p>'
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        });
    });

    //使失效
    $(".closeBtn").click(function(){
        var id = $(this).attr("data-id");
        var t_index = layer.open({
            type: 1,
            title:"确定让该活动失效?",
            btn:["确定","取消"],
            yes:function(){
                $.ajax({
                    url:'/merchants/marketing/researchInvalidate',
                    data:{id:id},
                    type:"post",
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(json){
                        layer.closeAll();
                        //保存成功后 移除新增栏目 插入新的ul
                        console.log(json);
                        if(json.status==1){
                            tipshow(json.info);
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }else{
                            tipshow(json.info,"wram");
                        }
                    },
                    error:function(){
                        layer.closeAll();
                        tipshow("异常","wram");
                    }
                });
            },
            closeBtn:false,
            move: false, //不允许拖动
            skin:"layer-tskin", //自定义layer皮肤
            area: ['300px', 'auto'], //宽高
            content:'<p style="margin:20px 15px;">“使失效”即活动立即结束。</p>'
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        });
    });

    $(".look").on('click',function () {
        var id = $(this).attr('data-id');
        console.log(id);
        window.location.href = '/merchants/marketing/researchMembers/' + id
    });
    $(".par_close").on('click',function () {
        $(".particulars").addClass('hide')
    });

    //搜索
    $("#search_txt").on('blur',function () {
        var val = $(this).val();
        search(val)
    })
    $(document).on('keydown',function (e) {
        if(e.keyCode == 13){
            var val = $('#search_txt').val();
            search(val)
        }
    })
    function search(txt) {
        if(txt){
            window.location.href = '/merchants/marketing/researches?title=' + txt
        }else{
            window.location.href = '/merchants/marketing/researches'
        }
    }

    // add by 赵彬 2018-8-9
    // 推广弹窗
    $("body").on('click','.extension',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var url = '';
        var id = $(this).data('id');
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(".widget-promotion1").css({"top":top-130,"left":left-590});
        $(".widget-promotion1").show();
        $(".down_qrcode_wsc").attr("data-id",id)
        // 获取微商城二维码
        $.ajax({
            url:'/merchants/marketing/researchQrCode/' + id,
            type:'get',
            dataType:'json',
            success:function(res){
                if(res.status==1){
                    $(".qrcode-right-sidebar .qr_img").html(res.data);
                    $(".link_url_wsc").attr('value', host + 'shop/activity/researchDetail/' + wid +'/' + id)
                }
            }
        })
        // 获取小程序二维码
        $.ajax({
            url:'/merchants/marketing/researchXcxQrCode/'+id,
            type:'get',
            dataType:'json',
            success:function(res){
                console.log(res)
                if(res.data.errCode == 0){
                    var xcxImg = '<img src="data:image/png;base64,'+res.data.data+'" />';
                    $(".qr_img_xcx").html(xcxImg);
                    $(".down_qrcode_xcx").attr('data-id',id)
                    $(".link_url_xcx").attr('value','/pages/main/pages/research/research?id='+id)
               }else{//无小程序
                    $("body").on("click",".xcx_code",function(){
                        $('.js-tab-content-wsc').css('display','block')
                        $('.js-tab-content-xcx').css('display','none')
                        $(this).removeClass('active');
                        $('.wsc_code').addClass('active');
                        tipshow('该店铺未开通小程序','warn');
                    });
               }
            }
        })
    })
    // 复制微商城链接
    $('body').on('click','.js-btn-copy-wsc',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.link_url_wsc');
        copyToClipboard( obj );
        tipshow('复制成功','info'); 
    });
    
    // 复制小程序链接
    $('body').on('click','.js-btn-copy-xcx',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.link_url_xcx');
        copyToClipboard( obj );
        tipshow('复制成功','info'); 
    });

    //下载微商城二维码
    $('.down_qrcode_wsc').click(function(e){
        window.location.href= '/merchants/marketing/researchQrCodeDownload/' + e.currentTarget.dataset.id;
    });
    //下载小程序二维码
    $('.down_qrcode_xcx').click(function(e){
        window.location.href= '/merchants/marketing/researchXcxQrCodeDownload/'+e.currentTarget.dataset.id;
    });
    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.widget-promotion');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
            $(".widget-promotion").hide();
        }
    });
    // 点击小程序二维码
    $("body").on("click",".xcx_code",function(){
    	$('.js-tab-content-wsc').css('display','none')
    	$('.js-tab-content-xcx').css('display','block')
        $(this).addClass('active').siblings().removeClass('active');
    });
    // 点击微商城二维码
    $("body").on("click",".wsc_code",function(){
    	$('.js-tab-content-wsc').css('display','block')
    	$('.js-tab-content-xcx').css('display','none')
    	$(this).addClass('active').siblings().removeClass('active');
    });
    //end
});