require(['jquery', 'base','layer'], function(jquery,base,layer) {　　　
    $('.delete').click(function(e){
        e.stopPropagation();//阻止事件冒泡
        var that = $(this);
        var id = $(this).attr("data-id");
        showDelProver($(this), function(){
            $.ajax({
                url:'/merchants/marketing/seckill/delete',
                data:{id:[id]},
                type:"post",
                dataType:"json",
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                },
                success:function(json){ 
                    //保存成功后 移除新增栏目 插入新的ul 
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
                    tipshow("异常","wram");
                }
            });  
        }, '确定要删除吗?');
    });
    
    //点击复选
    var chooseArr = [];
    $(document).on('click', ".batchChoose", function(){
    	var check = $(this).prop("checked");
    	var checkVal = $(this).val();
    	if(check){
    		chooseArr.push(checkVal)
    	}else{
    		var valIndex = chooseArr.indexOf(checkVal);
    		chooseArr.splice(valIndex, 1)
    	}
    	console.log(chooseArr)
    })
    
    //批量删除
    $(".batch-delet").click(function(){
    	layer.open({
    		title: '提示', 
    		content:"确定批量删除吗?",
    		skin: 'batchDel',
		  	time: 0, //不自动关闭
		  	btn: ['确定', '取消'],
		  	yes: function(index){
			    layer.close(index);
			    $.ajax({
	                url:'/merchants/marketing/seckill/delete',
	                data:{id: chooseArr},
	                type:"post",
	                dataType:"json",
	                headers: {
	                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
	                },
	                success:function(json){ 
	                    //保存成功后 移除新增栏目 插入新的ul 
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
	                    tipshow("异常","wram");
	                }
	            }); 
			}
		});
            
            
    });
    
    $(".invalid").click(function(){  
        var id = $(this).attr("data-id"); 
        var t_index = layer.open({
            type: 1,
            title:"确定让秒杀活动失效?",
            btn:["确定","取消"],
            yes:function(){   
                $.ajax({
                    url:'/merchants/marketing/seckill/invalidate',
                    data:{id:id},
                    type:"post",
                    dataType:"json",
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(json){
                        layer.closeAll(); 
                        //保存成功后 移除新增栏目 插入新的ul 
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
            content:'<p style="margin:20px 15px;">“使失效”即活动立即结束且不可再编辑。</p>'
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        }); 
    });

    /**
     * 推广链接和二维码
     */
    $('body').on('click','.seckill_url',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var id = $(this).attr("data-id");
        var url = $(this).attr('data-url');
        var type = '';
        initLayer(id,url,type); //初始化弹框效果
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(".widget-promotion1").css({"top":top-68,"left":left-370});
        $(".widget-promotion1").show();
    });

    //初始化弹框效果
    function initLayer(id,url,type){
        $(".widget-promotion-tab li").eq(1).addClass('active').siblings().removeClass('active');
        $(".js-tab-content").eq(1).show().siblings().hide();
        //获取二维码微商城
        $.ajax({
            url:"/merchants/getSeckillQRCode",
            type:"get",
            data:{id:id},
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    $(".qrcode-right-sidebar .qr_img").html(res.data);
                }
            }
        });
        $(".down_qrcode").attr("data-id",id);
        //设置复制链接
        $(".widget-promotion-content .link_copy").val(url);
    }

    // 复制链接
    $('body').on('click','.js-btn-copy',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('.link_copy');
        copyToClipboard( obj );
        tipshow('复制成功','info');
    });

    //下载二维码
    $('.down_qrcode').click(function(){
        var id = $(this).attr('data-id');
        window.location.href= '/merchants/downloadSeckillQRCode?id='+id;
    });

    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.widget-promotion');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){
            $(".widget-promotion").hide();
        }
    });

    // 普通优惠券二维码
    $("body").on("click",".widget-promotion-tab1 li",function(){
        var index = $(this).index();
        $(this).addClass('active').siblings().removeClass('active');
        $(".widget-promotion1 .js-tab-content").eq(index).show().siblings().hide();
    });

});