require(['jquery', 'base','layer'], function(jquery,base,layer) {　　　
    $('.delete').click(function(e){
        e.stopPropagation();//阻止事件冒泡
        var that = $(this);
        var id = $(this).attr("data-id");
        showDelProver($(this), function(){
            $.ajax({
                url:'/merchants/grouppurchase/del/'+id,
                data:{},
                type:"get",
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
        }, '确定要删除吗?');
    });
    $(".invalid").click(function(){
        var id = $(this).attr("data-id");
        var t_index = layer.open({
            type: 1,
            title:"确定让这个多人拼团活动失效?",
            btn:["确定","取消"],
            yes:function(){  
                $.ajax({
                    url:'/merchants/grouppurchase/invalid/'+id,
                    data:{},
                    type:"get",
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
            content:'<p style="margin:10px 15px;">进行中的商品活动一经失效，活动立即结束且不可再编辑。未成团订单将自动关闭并退款，已成团订单仍需及时处理。</p>'
        });
        /*移除事件绑定并绑定取消订单关闭按钮*/
        $(".layui-layer-setwin").unbind('click').click(function(){
            if(t_index)
                layer.close(t_index);
        }); 
    });  

    //推广按钮点击事件
    $(".spread").click(function(e){
        $('.spread-popup').remove(); //关闭前面打开的弹窗
        // e.stopPropagation();
        var id=$(this).data('id');
        var obj={};
        obj.path1 = $(this).attr("data-img1");
        obj.path2 = $(this).siblings('.qrcode').html();
        obj.path3 = '';
        obj.url = $(this).attr("data-url");
        obj.url1 = 'pages/activity/pages/grouppurchase/detail/detail?ruleId='+id;
        obj.title = $(this).attr("data-title");
        obj.groupNum = $(this).attr("data-groupNum");
        obj.price = $(this).attr("data-price");
        obj.left = $(this).position().left;
        obj.path3='';
        $.ajaxSettings.async = false;
        $.get("/merchants/shareEvent/getMinAppQRCode?from=groups&id="+id, function(result){
            if (result.status == 1){
                obj.path3 = result.data;
            }
        });

        var html = getSpreadPopup(obj); 
        $(this).parent().append(html); 
    });
    $("body").on("click",".copy_url",function(){
        var text = $(this).attr("data-url");
        var obj ={
            val:text,
            text:function(){
                return this.val;
            }
        }
        copyToClipboard(obj);
        tipshow("复制成功");
    });
    /*
    * @auther 邓钊
    * @desc tab栏切换
    * @date 2018-7-20
    * */
    $("body").on("click",".span_btn",function(){
        $(this).addClass('spanActive').siblings().removeClass("spanActive")
        var id = $(this).attr("data-id");
        if(id == 1){
            $(".wei_code").removeClass('hide');
            $(".wei_url").removeClass('hide');
            $(".xcx_code").addClass('hide');
            $(".xcx_url").addClass('hide');
        }else if(id == 2){
            $(".wei_code").addClass('hide');
            $(".wei_url").addClass('hide');
            $(".xcx_code").removeClass('hide');
            $(".xcx_url").removeClass('hide');
        }
    });
    //推广弹窗点击元素以外的地方关闭
    $("body").click(function(e){
        var el = $('.spread-popup');   // 设置目标区域
        var el1 = $(".spread-popup").parent().find(".spread"); //触发按钮
        if((!el.is(e.target) && el.has(e.target).length === 0) && !el1.is(e.target)){ 
            el.remove();
        }
    });


    //推广弹窗 updata by 邓钊 2018-7-20 添加tab切换栏
    function getSpreadPopup(obj){
        var left = obj.left -250;
        var html = '<div class="spread-popup" style="left:'+left+'px;">';
        html+='<div class="span_tab clearfix"><span class="spanActive span_btn" data-id="1">公众号</span><span class="span_btn" data-id="2">小程序</span></div>'
        html+='<div class="spread-popup-content">';
        html+='<div class="spread-popup-img">';
        html+='<img src="'+obj.path1+'" /></div>'; 
        html+='<div class="spread-popup-sub-content">';
        html+='<p>'+obj.title+'</p>';
        html+='<p class="text-right">'+obj.groupNum+'人团购价：<span class="color-red">￥'+obj.price+'</span></p>';
        html+='<div class="text-center wei_code">'+obj.path2+'</div>'
        if (obj.path2){
            html+='   <div class="qrcode  hide xcx_code"><img src="data:image/png;base64,'+obj.path3+'" /></div>';
        }
        html+='<div class="text-center mb-10 wei_url"><a data-url="'+obj.url+'" class="copy_url" href="javascript:;">复制活动链接</a></div>';
        html+='<div class="text-center mb-10 hide xcx_url"><a data-url="'+obj.url1+'" class="copy_url" href="javascript:;">复制活动链接</a></div>';
        html+='</div>';
        return html;
    }
	
	
	
    //查看数据弹窗
    $('.watch_data').click(function(){
    	$('.data_model').css('display','block')
    	var rule_id = $(this).attr("data-id");
    	var now_title = $(this).attr("data-title");
    	console.log(now_title)
    	$('.data_model_name span').html(now_title);
    	$.ajax({
    		type:"get",
    		url:dcUrl+"/api/v1/groupsAnalysisData?rule_id="+rule_id+"&wid="+wid,
            success:function(res){
            	if(res.err_code == 0){
            		console.log(res)
            		var htmls = ''
                    var totalprice = 0;
            		for(var i = 0; i < res.data.length; i++){
            			if(res.data[i].title == '订单实付金额'){
                            totalprice = res.data[i].value;
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>￥'+res.data[i].value+'</span>'+
        							'<div class="data_tip">拼团活动带来的总付款金额（包含退款）</div>'+
        						'</li>'
            			}else if(res.data[i].title == '优惠总金额'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>￥'+res.data[i].value+'</span>'+
        							'<div class="data_tip">该拼团活动优惠的总金额（商品原价—活动价）</div>'+
        						'</li>'
            			}else if(res.data[i].title == '费效比'){
            			    //何书哲 2018年7月18日 修改费效比，支付金额等于0为无效
                            if (totalprice == 0) {
                                htmls += '<li>'+
                                    '<p>'+res.data[i].title+'</p>'+
                                    '<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
                                    '<span>无效</span>'+
                                    '<div class="data_tip">优惠总金额 / 订单实付金额</div>'+
                                    '</li>'
                            } else {
                                htmls += '<li>'+
                                    '<p>'+res.data[i].title+'</p>'+
                                    '<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
                                    '<span>'+res.data[i].value+'%</span>'+
                                    '<div class="data_tip">优惠总金额 / 订单实付金额</div>'+
                                    '</li>'
                            }
            			}else if(res.data[i].title == '新成交用户数'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>'+res.data[i].value+'</span>'+
        							'<div class="data_tip">拼团活动带来的首次在店铺成交的客户数</div>'+
        						'</li>'
            			}else if(res.data[i].title == '老成交用户数'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>'+res.data[i].value+'</span>'+
        							'<div class="data_tip">在店铺有过付款订单，参与该平团活动的客户数</div>'+
        						'</li>'
            			}else if(res.data[i].title == '参团总人数'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>'+res.data[i].value+'</span>'+
        							'<div class="data_tip">发起批团和参与拼团的总人数；</div>'+
        						'</li>'
            			}else if(res.data[i].title == '总开团数'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>'+res.data[i].value+'</span>'+
        							'<div class="data_tip">发起开团的用户总数</div>'+
        						'</li>'
            			}else if(res.data[i].title == '总成团数'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>'+res.data[i].value+'</span>'+
        							'<div class="data_tip">拼团成功的用户总数</div>'+
        						'</li>'
            			}
            			else if(res.data[i].title == '已退款金额'){
            				htmls += '<li>'+
            						'<p>'+res.data[i].title+'</p>'+
            						'<i class="glyphicon glyphicon-question-sign gray_bbb f14 note_tip"></i>'+
        							'<span>￥'+res.data[i].value+'</span>'+
        							'<div class="data_tip">退款成功的金额</div>'+
        						'</li>'
            			}
            			
            		}
            		$('.data_model_list ul').html(htmls);
        			$('.note_tip').on('mouseover',function(){
        				$(this).siblings(".data_tip").show()
						
					})
        			$('.note_tip').on('mouseleave',function(){
        				$(this).siblings(".data_tip").hide()
						
					})
        			
            	}
            }
    	});
    });
    $('.data_model').click(function(){
    	$('.data_model').css('display','none')
    });
    $('.data_model_content').click(function(){
    	return false;
    });
    $('.close').click(function(){
    	$('.data_model').css('display','none')
    });	
    $('.note_tip').on('mouseover',function(){
		$(this).siblings(".data_tip").show()
	})
	$('.note_tip').on('mouseleave',function(){
		$(this).siblings(".data_tip").hide()
	})
});

