require(["jquery","layer","chosen","bootstrapValidator","lazyload"],function(jquery,layer,chosen,bootstrapValidator,lazyload){
    //序号排序
    $(".serialNumber").click(function(e){
        $(this).siblings("input").removeClass("no");
        $(this).addClass("no");
        e.stopPropagation();
    })
    // $('body').on('click','.product_detail_url',function(e){
    //     e.stopPropagation(); //阻止事件冒泡
    // 	var _this = $(this);
    //     var _url = $(this).data('url');             // 要复制的连接
    //     var html ='<div class="input-group">';
    //     html +='<input type="text" class="link_copy form-control" value="'+_url+'" disabled >';
    //     html +='<a class="copy_btn input-group-addon">复制</a>';
    //     html +='</div>';
    //     showDelProver(_this,function(){},html,'false');             // 跟随效果
    // });

    // 复制链接
    // $('body').on('click','.copy_btn',function(e){
    //     e.stopPropagation(); //阻止事件冒泡
    //     var obj = $(this).siblings('.link_copy');
    //     copyToClipboard( obj );
    //     tipshow('复制成功','info');
    //     $(this).parents('.del_popover').remove();
    // });

    $('.shop_kind').chosen(); 
    $(".shop_grounp").chosen({
        width:'150px',
        no_results_text: "没有找到",
        allow_single_de: true
    }).change(function(){ 
      if(location.href.indexOf("?")>0){ 
        location.href = location.href.split("?")[0]+"?group_id="+this.value;
      }else{
        location.href = location.href+"?group_id="+this.value;
      }
    });
    // 修改模板
   function center(obj){
        var window_height = $(document).height();
        var height = obj.height();
        obj.css('margin-top',window_height/2-height/2);
    }
    function showModel(obj,obj2){
        obj.show();
        $('.modal-backdrop').show();
        center(obj2);
    }
    function hideModel(obj){
        obj.hide();
        $('.modal-backdrop').hide();
    }
    function showtip(){
         $('.tip').show();
         setTimeout(function(){
            $('.tip').hide();
         },2000)
    }
   
    //修改模板
    $('.change_model').click(function(){
        if(!$('.shop').is(':checked')){
            tipshow('请先选择商品！','warn')
            return;
        }
        var html = '';
        $('.modal-body table tbody').html('')
        $.get('/merchants/product/getGoodsTemplates',function(data){
          console.log(data);
          if(data.status == 1){
             for(var i=0;i<data.data.length;i++){
                html += '<tr><td style="text-align:left;">'+ data.data[i]['template_name']+'</td><td>'+data.data[i]['created_at']+'</td>';
                html += '<td class="text-right"> <a href="#">编辑</a> - <a href="javascript:void(0);" class="useModel" data-id="'+data.data[i]['id']+'">使用</a>';
                html += '</td></tr>';
             }
             if(data.data.length==0){
                html = '<tr><td colspan="3"><div class="nodate">还没有相关数据</div></tr></td>';
             }
             $('#myModal1 table tbody').append(html);
             // showModel($('#myModal1'),$('#modal-dialog1'));
             // 居中屏幕弹窗
             $('.modal-backdrop').show();
             $('#myModal1').show();
             $('#modal-dialog1').css({
                top:$(window).height()/2-$('#modal-dialog1').height()/2,
                left:$(window).width()/2 - $('#modal-dialog1').width()/2
             })
          }
        },'json')
    });
    //修改模板确认
    $(document).bind().on('click','.useModel',function(){
        $('input[name="tpl_id"]').val($(this).data('id'));
        $('input[name="idc"]').val();
        $.post('/merchants/product/updateGoodsTpl',$('form[name="shop_form"]').serialize(),function(data){
            if(data.status == 1){
                tipshow("模板修改成功");
                window.location.reload();
            }else{
                tipshow("模板修改失败",'warn');
            }
            hideModel($('#myModal1'));
        })
    })
    $('#all_shop').change(function(){
        if($(this).is(':checked')){
            $(this).prop("checked",true); 
            $('.shop').prop("checked",true);
        }else{
            $(this).prop("checked",false);   
            $('.shop').prop("checked",false);
        }
    })
    //运费模板
    $.get("/merchants/product/getfreights",function(res){
        console.log(res)
        if(res.status == 1){
            var _html = '<option value="">请选择</option>';
            for(var i = 0;i < res.data.length;i ++){
                _html += '<option value="'+res.data[i].id+'">'+res.data[i].title+'</option>'
            }
            $(".freight").append(_html);
        }
    });
    $(".js-refresh-tag").click(function(){
        $.get("/merchants/product/getfreights",function(res){
            console.log(res)
            if(res.status == 1){
                $(".freight").empty();//先清空再添加
                var _html = '<option value="">请选择</option>';
                for(var i = 0;i < res.data.length;i ++){
                    _html += '<option value="'+res.data[i].id+'">'+res.data[i].title+'</option>'
                }
                $(".freight").append(_html);
            }
        });
    })
   // prover
   function hideprover(obj){
        obj.hide();
   }
   function showprover(obj){
        obj.show();
   }
   //修改组
   $('#change_grounp').click(function(e){
      if(!$('.shop').is(':checked')){
          tipshow('请先选择商品！','warn')
          return;
      }
      // showprover($('#popover1'));
      $('#popover1').show();
      var position = $(this).offset();
      $('#popover1').css({
          top:position.top - $('#popover1').height() -8,
          left:position.left - $('#popover1').width()/2 + $(this).width()/2
      })
      e.stopPropagation();
   })
   $('.js-btn-confirm').click(function(e){
        $.post('/merchants/product/modgroup',$('form[name="shop_form"]').serialize(),function(data){
          if(data.status==1){
            $('input[type="checkbox"]').prop('checked',false);
            tipshow('修改分组成功！');
            window.location.reload();
          }else{
            tipshow('修改分组失败！','warn');
          }
          //居中弹窗
          $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
        hideprover($('#popover1'));
        e.stopPropagation();
   })
   $('.js-btn-cancel').click(function(e){
        hideprover($('#popover1'));
        e.stopPropagation();
   })
   //上架保存
   $('.js-up-save').click(function(e){
         $.post('/merchants/product/onoffsale',$('form[name="shop_form"]').serialize(),function(data){
          if(data.status==1){
              $('input[type="checkbox"]').prop('checked',false);
              tipshow('上架成功！');
              window.location.reload();
          }else{
              tipshow('上架失败！','warn');
          }
          //居中弹窗
          $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
        hideprover($('.ui-popover'));
        e.stopPropagation();
   })
   //下架
    $('.down_shop').click(function(e){
        if(!$('.shop').is(':checked')){
            tipshow('请先选择商品！','warn')
            return;
        }
        showprover($('.down_shop .ui-popover'));
        e.stopPropagation();
   })
   $('.js-save').click(function(e){
        $.post('/merchants/product/onoffsale',$('form[name="shop_form"]').serialize(),function(data){
          console.log(data);
          if(data.status==1){
            $('input[type="checkbox"]').prop('checked',false);
            tipshow('下架成功！');
              window.location.reload();
          }else{
            tipshow(data.info,'warn');
          }
          //居中弹窗
          $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
        hideprover($('.ui-popover'));
        e.stopPropagation();
   })
   $('.js-cancel').click(function(e){
        hideprover($('.ui-popover'));
        e.stopPropagation();
   })

   //删除
  $('.delete').click(function(){
      if(!$('.shop').is(':checked')){
          tipshow('请先选择商品！','warn')
          return;
      }
      showprover($('#delete_prover'));

  })
   $('.delete_sure').click(function(e){
      $.post('/merchants/product/delbatch',$('form[name="shop_form"]').serialize(),function(data){
          if(data.status==1){
              $('input[type="checkbox"]').prop('checked',false);
              tipshow(data.info);
              window.location.reload();
          }else{
              tipshow(data.info,'warn');
          }
        //居中弹窗
        $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
      },'json')
      hideprover($('#delete_prover'));
      e.stopPropagation();
   })
   $('.delete_cancel').click(function(e){
        hideprover($('#delete_prover'));
        e.stopPropagation();
   })
   
   //会员折扣
   $('.associator').click(function(){
        if(!$('.shop').is(':checked')){
            tipshow('请先选择商品！','warn')
            return;
        }
        showprover($('.associator .popover'));
   })
   //运费模板
   $('.discount-fare').click(function(){
        if(!$('.shop').is(':checked')){
            tipshow('请先选择商品！','warn')
            return;
        }
        showprover($('.discount-fare .popover'));
   })
    
    // 开启积分
    $('.is_point').click(function(){
        if(!$('.shop').is(':checked')){
            tipshow('请先选择商品！','warn')
            return;
        }
        showprover($('.is_point .popover'));
    });
    
   $('.discount_confirm').click(function(e){
        $.post('/merchants/product/moddiscount',$('form[name="shop_form"]').serialize(),function(data){
          if(data.status==1){
            $('input[type="checkbox"]').prop('checked',false);
            tipshow('修改成功！');
            window.location.reload();
          }else{
            tipshow('修改失败！','warn');
          }
          //居中弹窗
          $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
        hideprover($('.discount .popover'));
        hideprover($('.discount-fare .popover'));
        e.stopPropagation();
   })

    // 批量开启积分
    $('.is_point_confirm').click(function(e){
        $.post('/merchants/product/batchEdit',$('form[name="shop_form"]').serialize(),function(data){
            if(data.status==1){
                $('input[type="checkbox"]').prop('checked',false);
                tipshow('修改成功！');
                window.location.reload();
            }else{
                tipshow('修改失败！','warn');
            }
            //居中弹窗
            $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
        hideprover($('.is_point .popover'));
        e.stopPropagation();
    });

   $('.discount_cancel,.discount-fare').click(function(e){
        hideprover($('.associator .popover'));
        e.stopPropagation();
   })
   $('.cancel-fare,.associator').click(function(e){
        hideprover($('.discount-fare .popover'));
        e.stopPropagation();
   })
    $('.is_point_cancel').click(function(e){
        hideprover($('.is_point .popover'));
        e.stopPropagation();
    })
   $(".associator .popover").click(function(e) {  
	    $(this).show();  
        e.stopPropagation();  
	});  
	$(".is_point .popover").click(function(e) {
	    $(this).show();  
        e.stopPropagation();  
	});  
	$(document).click(function(event) {	  
	    $(".associator .popover").hide();
        $(".is_point .popover").hide();
	});	
	$(".discount-fare .popover").click(function(e) {  
	    $(this).show();  
        e.stopPropagation();  
	});  
	$(document).click(function(event) {	  
        $(".discount-fare .popover").hide();
	});		
//	点击确定取更多弹出窗消失		
	$(".cancel-fare,.sure-fare,.discount_confirm,.discount_cancel, .is_point_confirm, .is_point_cancel").click(function(e) {
	    $('.dropdown').removeClass("open");
	});
	$(".sure-fare").click(function(){
        $.post("/merchants/product/setFreight",$('form[name="shop_form"]').serialize(),function(res){
            if(res.status == 1){
                tipshow("修改运费模板成功");
                window.location.reload();
            }else{
                tipshow(res.info);
            }
        })
    })
    $('.out_delete').click(function(e){
        var id = $(this).data('id');
        var _token = $('input[name="_token"]').val();
        showDelProver($(this),function(){
          $.post('/merchants/product/del',{id:id,_token:_token},function(data){
            if(data.status==1){
              tipshow(data.info);
              window.location.reload();
            }else{
                tipshow(data.info,'warn');
            }
            //居中弹窗
            $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
          },'json')
          $('.del_popover').hide();
        })
        // alert($(this).offset().top);
        // $('.out_delete_promote').show();
        // $('.out_delete_promote').css('top',$(this).offset().top-15);
        // $('.out_delete_promote').css('left',$(this).offset().left-260);
        // e.stopPropagation();
    })


   
    //点击推广start
    //update by 黄新琴 2018-8-27
    var wsc_url = ''
    $('body').on('click','.ads',function(e){
        e.stopPropagation(); //阻止事件冒泡
        id = $(this).attr("data-id");
        wsc_url = $(this).attr('data-url');
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        //设置产品id的值
        $('input[name="product_id"]').val(id);
        $(".widget-promotion1").css({"top":top-70,"left":left-376});
        $(".widget-promotion1").show();
        //获取二维码微商城
        $.ajax({
            url:"/merchants/product/getQRCode",
            type:"get",
            data:{id:id},
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    $(".qrcode-right-sidebar .qr_img").html(res.data);
                    $(".widget-promotion-content .link_url_wsc").val(wsc_url);
                    $('.down_qrcode_wsc').attr('data-id',id);
                }
            }
        });
        //获取小程序二维码
        $.ajax({
            url:"/merchants/product/getLiteAppQRCode",
            type:"get",
            data:{id:id},
            dataType:"json",
            success:function(res){
                if(res.status==1){
                    if(res.data.errCode == 0 && res.data.data){
                        var html = '<img src="data:image/png;base64,'+res.data.data+'" />';
                        $(".qrcode-right-sidebar .qr_img_xcx").html(html);
                        $('.down_qrcode_xcx').attr('data-id',id);
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
            }
        });
        //设置复制链接
        $(".widget-promotion-content .link_url_xcx").val('pages/main/pages/product/product_detail/product_detail?id='+id);
    });
    
    // 复制链接
    $('body').on('click','.code-copy-a',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('input');
        copyToClipboard( obj );
        tipshow('复制成功','info'); 
    });
    

    //下载微商城二维码
    $('.down_qrcode_wsc').click(function(){
        var id = $(this).attr('data-id');
        window.location.href= '/merchants/product/downloadQRCode?id='+id;
    });
    
    //下载小程序二维码
    $('.down_qrcode_xcx').click(function(){
        var id = $(this).attr('data-id');
        window.location.href= '/merchants/product/downloadLiteAppQRCode?id='+id;
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
    // 点击推广end

    
    // 输入折扣改变价格
    $('input[name="discount_num"]').blur(function(){
        if(/^\d+(\.\d{1,2})?$/.test($(this).val())){
           $('.js-final-price').html(price*$(this).val());
        }
    })
    // 产品推广商品保存
    $('.js-submit-btn').click(function(){    	
        $.post('/merchants/product/setqrdiscount',$('form[name="goodsForm"]').serialize(),function(data){
          if(data.status==1){
            $('.widget-promotion').hide();
            $('.js-qrcode-content').show();
        	$('.qrcode-create-content').hide();
            tipshow(data.info);
          }
          //居中弹窗
          $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        },'json')
    })
    //产品推广商品取消
    $('.js-cancel-btn').click(function(){    	
        $('.widget-promotion').hide();
        $('.js-qrcode-content').show();
        $('.qrcode-create-content').hide();
    })

  
    $('.qrcode-left-lists ul li').click(function(){
        $('.qrcode-left-lists ul li').removeClass('active');
        if($(this).index()==0){
            $('.qrcode').show();
            $('.qrcode-info').hide();
        }else{
            $('.qrcode').hide();
            $('.qrcode-info').show();
        }
        $(this).addClass('active');
    })
    $('.js-create-qrcode').click(function(){
        $('.js-qrcode-content').hide();
        $('.qrcode-create-content').show();
    })
    // 修改会员价格
     var origin_price;
     //update by 赵彬 2018-7-3
     var vip_discount_way = 1;
     var status; //定义请求开关
     //end
     var vip_list;
    $(document).on('click','.member_price',function(){
        vip_list = [];
        origin_price = [];
        $('.setting_list table').html('');
        var html = '';
        var html1 = '';
        var productId = $(this).data('id');
        $(".js-batch-box").show();
        status = false;
        // alert(1);
        $.get('/merchants/product/getvipprop',{product_id: productId, title: $(this).data('title')},function(data){
            if(status){
                return;
            }
            if(data.status == 0){
             tipshow('设置会员价格失败！','warn');
              //居中弹窗
              $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
              return;
            }

            //设置商品标题和商品详情链接
            $('#memberPriceTitle').text(data.data.title).prop('href', '/shop/product/detail/' + $('#wid').val() + '/' + productId);
            $('#productId').val(productId);
            finalData = data;
            html += '<thead><tr>';
            html += data.data.prop_level_title[1] ? '<th>'+data.data.prop_level_title[1]+'</th>' : '';
            html += data.data.prop_level_title[2] ? '<th>'+data.data.prop_level_title[2]+'</th>' : '';
            html += data.data.prop_level_title[3] ? '<th>'+data.data.prop_level_title[3]+'</th>' : '';
            html += '<th>'+data.data.prop_level_title[4]+'</th>';
            for(var i=5;i<data.data.prop_level_title.length;i++){
                if(i%2 == 0){
                  html += '<th>'+ data.data.prop_level_title[i] +'</th>';
                }
            }
            html += '</tr></thead>';
            html += '<tbody>';
            for(var j=0;j<data.data.prop_level_values.length;j++){
              origin_price.push(data.data.prop_level_values[j][4]);
              html += '<tr>';
              html += '<input type="hidden" name="prop_id" value="'+data.data.prop_level_values[j][0]+'">';
              html += data.data.prop_level_values[j][1] ? '<td class="vip_td form-group">'+data.data.prop_level_values[j][1]+'</td>' : '';
              html += data.data.prop_level_values[j][2] ? '<td class="vip_td form-group">'+data.data.prop_level_values[j][2]+'</td>' : '';
              html += data.data.prop_level_values[j][3] ? '<td class="vip_td form-group">'+data.data.prop_level_values[j][3]+'</td>' : '';
              html += '<td class="vip_td form-group">'+data.data.prop_level_values[j][4]+'</td>';
              for(var i=5;i<data.data.prop_level_title.length;i++){
                if(i%2 == 0){
                  html += '<td class="vip_td form-group"><div class="td-discount" style="width:100px"><span class="vip_td_text">减</span><input style="margin:0 5px" type="text" class="form-control" value="'+data.data.prop_level_values[j][i] +'" datatype="s5-16" errormsg="昵称至少5个字符,最多16个字符！"><span class="vip_td_text">元</span></div></td>';
                }else{
                  html += '<input type="hidden" name="'+data.data.prop_level_title[i]+'" value="'+data.data.prop_level_values[j][i]+'">元';
                }
              }
              html += '</tr>'
            }
            html += '</tbody>'
            // console.log(html);
            html1 += '<thead><tr>';
            html1 += data.data.prop_level_title[1] ? '<th>'+data.data.prop_level_title[1]+'</th>' : '';
            html1 += data.data.prop_level_title[2] ? '<th>'+data.data.prop_level_title[2]+'</th>' : '';
            html1 += data.data.prop_level_title[3] ? '<th>'+data.data.prop_level_title[3]+'</th>' : '';
            html1 += '<th>'+data.data.prop_level_title[4]+'</th>';
            for(var i=5;i<data.data.prop_level_title.length;i++){
                if(i%2 == 0){
                  html1 += '<th>'+ data.data.prop_level_title[i] +'</th>';
                }
            }
            html1 += '</tr></thead>';
            html1 += '<tbody>';
            for(var j=0;j<data.data.prop_level_values.length;j++){
              html1 += '<tr>';
              html1 += '<input type="hidden" name="prop_id" value="'+data.data.prop_level_values[j][0]+'">';
              html1 += data.data.prop_level_values[j][1] ? '<td class="vip_td form-group">'+data.data.prop_level_values[j][1]+'</td>' : '';
              html1 += data.data.prop_level_values[j][2] ? '<td class="vip_td form-group">'+data.data.prop_level_values[j][2]+'</td>' : '';
              html1 += data.data.prop_level_values[j][3] ? '<td class="vip_td form-group">'+data.data.prop_level_values[j][3]+'</td>' : '';
              html1 += '<td class="vip_td form-group">'+data.data.prop_level_values[j][4]+'</td>';
              for(var i=5;i<data.data.prop_level_title.length;i++){
                if(i%2 == 0){
                  html1 += '<td class="vip_td form-group"><input type="text" class="form-control" value="'+data.data.prop_level_values[j][i] +'" datatype="s5-16" errormsg="昵称至少5个字符,最多16个字符！"></td>';
                }else{
                  html1 += '<input type="hidden" name="'+data.data.prop_level_title[i]+'" value="'+data.data.prop_level_values[j][i]+'">元';
                }
              }
              html1 += '</tr>'
            }
            html1 += '</tbody>'
            $('.reduce').append(html);
            $('.appointPrice').append(html1);
            // 吴杨（wuyang4576@dingtalk.com）2019-11-22 15:39:16 非会员是否显示会员价
            if (data.data.is_show_vip_price === '0') {
              $('input:radio[name="is_show_vip_price"][value=0]').prop("checked", true);
            } else {
              $('input:radio[name="is_show_vip_price"][value=1]').prop("checked", true);
            }
            if(data.data.vip_discount_way==1){
                vip_discount_way = data.data.vip_discount_way;
                $('.setting_list input[name="count_method"][value="1"]').prop("checked",true);
                $('.appointPrice input[type="text"]').val(0) //未选项数据清空
                $('.appointPrice').hide();
                $('.reduce').show();
            }else if(data.data.vip_discount_way==2){
                vip_discount_way = data.data.vip_discount_way;
                $('.setting_list input[name="count_method"][value="2"]').prop("checked",true);
                $('.reduce input[type="text"]').val(0)
                $('.reduce').hide();
                $('.appointPrice').show();
            }else if(data.data.vip_discount_way==0){
                $('.setting_list input[name="count_method"][type="1"]').prop("checked",false);
                $('.appointPrice').hide();
                $('.reduce').show();
            }
            showModel($('#member_price'),$('#modal-dialog-adv'));
            status = true;
            //add by 赵彬 2018-8-15
            $(".btn-box").show();
            $(".batch-box").hide();
            $(".select-result").text('请选择')
            data.data.prop_level_title.splice(0,5)
            for(var i=0;i<data.data.prop_level_title.length;i++){
                if(data.data.prop_level_title[i] != "vip_id"){
                    vip_list.push(data.data.prop_level_title[i])
                }
            }
            //end
        },'json')
    })  
    
    // 选择优惠方式
    $('.setting_list input[type="radio"]').click(function(){
        if($(this).val()==1){
          $('.reduce').show();
          $('.appointPrice').hide();
          vip_discount_way = 1;
          $(".discount-type").show()

        }else if($(this).val()==2){
          $('.reduce').hide();
          $('.appointPrice').show();
          vip_discount_way = 2;
          $(".discount-type").hide()
        }
    })

    //关闭设置会员价格model
    $('.close').click(function(){
       $('#member_price').hide();
       hideModel($('#myModal1'));
    })
    // 会员价格设置取消
    $('.js-cancel').click(function(){
       hideModel($('#member_price'));
    })
    // 会员价格设置确认
    var prop_values = [];
    $('.js-confirm').click(function() {
        var checkError = false;//判断是否有验证错误
        prop_values = [];
        if(vip_discount_way==1){
            var subClass = $('.setting_list .reduce tbody tr')
        }else{
            if(vip_discount_way==2){
                var subClass = $('.setting_list .appointPrice tbody tr')
            }
        }
        subClass.each(function(key,val){
            // console.log($($(val).find('input').get(0)).val());
            $($(val).find('input[type="text"]')).each(function(key1,val1){  
                // alert(origin_price[key])
                if(vip_discount_way==1){
                    if($(val1).val()!='' && parseInt($(val1).val())>parseInt(origin_price[key])){
                        $(val1).addClass('Validform_error');
                        checkError = true
                    }else{
                        // alert('a')
                        $(val1).removeClass('Validform_error')
                    }
                }
                // console.log(key1);
            })
            var prop = {};
            var ster = '';
            prop.prop_id = $($(val).find('input').get(0)).val();
            for(var i=1;i<$(val).find('input').length;i++){
              // console.log(i);
              if(i%2!=0){
                // console.log($($(val).find('input').get(i)).val());
                // console.log($($(val).find('input').get(i+1)).val());
                ster += $($(val).find('input').get(i)).val()+','
                ster += $($(val).find('input').get(i+1)).val()+',';
                // prop.prop_values[$($(val).find('input').get(i)).val()] = $($(val).find('input').get(i+1)).val();
              }
            }
            ster = ster.substring(0,ster.length-1)
            prop.prop_values = ster;
            prop_values.push(prop);
        })
         // 验证不通过显示弹出
          if(checkError){
            tipshow('减去价格不能大于原价！','warn');
            //居中弹窗
            $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
            return;
          }
        $('input[name="count_method"]').each(function(key,val){
            if($(val).attr('checked')=='checked'){
               var vip_discount_way = $(val).val();
            }
        })
        var _token = $('input[name="_token"]').val();
        $.post('/merchants/product/setvipprop',
          { 
            productId: $('#productId').val(), 
            prop_values: prop_values,
            vip_discount_way: vip_discount_way,
            // 吴杨（wuyang4576@dingtalk.com） 2019-11-22 15:19:22  新增非会员是否展示会员价
            is_show_vip_price: $('input:radio[name="is_show_vip_price"]:checked').val(),
            _token:_token
          }, function(data){
          if(data.status==1){
            tipshow('价格设置成功！');
            hideModel($('#member_price'));
          }else{
            tipshow(data.info,'warn');
          }
          //居中弹窗
          $('.layui-layer').css('top',window.screen.availHeight /2-$('.layui-layer').height()/2)
        })
    })
    $("body").on('click','.product-copy',function(){
        var id = $(this).attr("data-id");
        var _token = $('input[name="_token"]').val();
        $.post('/merchants/product/copy',{id: id,_token:_token},function(data){
            if (data.status==1) {
                tipshow(data.info);
                setTimeout(function(){
                  window.location.reload();
                },2000)  
            }else{
                tipshow(data.info,'warn');
            } 
        })
    });

    //图片懒加载 新增
    $("img.lazy").lazyload({effect: "fadeIn"});
    $("body").click(function(e){
        var target = $(e.target);
        if(!target.hasClass("int-sort")){
            $('.int-sort').addClass("no");
            $(".serialNumber").removeClass("no");
        }
    });
    //商品排序
    $('.int-sort').blur(function(){
    	var leng = $(".int-sort").length;
		var data = {};
		data.sort = $(this).val();
    	var id = $(this).attr("data-id");
        var _token = $('meta[name="csrf-token"]').attr('content');
        var op = {
        	"data":data,
        	"id":id
        };
    	$.ajax({
            url:'/merchants/product/set',
            //traditional: true,
            data:op,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function (response) {
            	console.log(response)
                if(response.status == 1){
                    tipshow("排序成功");
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }
            },     
            error : function() {
                alert("异常！");
            }
        });
    })
    
    //更多下拉菜单点击不消失
    $('.dropdown-menu a.removefromcart').click(function(e) {
	    e.stopPropagation();
	});
    $("ul.dropdown-menu").on("click", "[data-stopPropagation]", function(e) {
        e.stopPropagation();
    });
    
    //打印销售单   
    	//批量打印
    var orderIds=[];  //订单号的集合
    $('.batchPrint').click(function(){
    	orderIds=[]
    	$('input[name="ids[]"]').each(function(index,obj){
    		if(obj.checked){
    			orderIds.push( $(obj).data('id') )
    		}
    	});
    	if(orderIds.length==0){
    		layer.tips('请选择商品','.batchPrint',{
    			tips:[1,'#08BDB7']
    		});
    		return;
    	}
    	if($('#all_shop').is(':checked')){
    		$('#all_shop').removeAttr('checked');
    	}
    	$('input[name="ids[]"]').removeAttr('checked');
	  	window.location.href='/merchants/product/exportXlsApi?orderids='+orderIds+'&all=1';
    })
    	//所有商品打印
    $('.allPrint').click(function(){
    	$('.printall').css('display','block')
    })   
    $('.print_cancel').on('click',function(){
    	$('.printall').css('display','none')
    })
    $('.print_sure').on('click',function(){
    	$('.printall').css('display','none')
	  	window.location.href='/merchants/product/exportXlsApi?status='+printStatus+'&all=2';
    })

    // add by 赵彬 2018-8-15
    //点击批量进行设置
    $("body").on('click','.js-batch',function(e){
        e.stopPropagation();//阻止事件冒泡
        $(".btn-box").hide()
        $(" .batch-box").show()
        $(".discount-desc").hide()
        $(".discount-num input").val('')
    })
    //清空所有自定义会员价
    $("body").on('click','.js-empty',function(){
        $(".setting_list").find('input[type="text"]').val(0)
    })
    // 确认批量设置
    $("body").on('click','.btn-batch-confirm',function(e){
        e.stopPropagation();//阻止事件冒泡
        $(".btn-box").show();
        $(".batch-box").hide();
        for(var j = 0; j<vip_list.length; j++){
            if(vip_list[j] == $(".select-result").text()){
                var newVal = $(".discount-num input").val()
                if(vip_discount_way == 1){
                    $(".reduce tbody tr").each(function(key,val){
                        $(val).find('input[type="text"]').eq(j).val(newVal)
                    })
                }else if(vip_discount_way == 2){
                    $(".appointPrice tbody tr").each(function(key,val){
                        $(val).find('input[type="text"]').eq(j).val(newVal)
                    })
                }
            }   
        } 
    })
    // 取消批量设置
    $("body").on('click','.btn-batch-cancel',function(e){
        e.stopPropagation();//阻止事件冒泡
        $(".btn-box").show();
        $(".batch-box").hide();
    })
    // 弹出会员卡列表
    $("body").on('click','.select-result',function(e){
        e.stopPropagation();//阻止事件冒泡
        var selectContent = '';
        for(var i=0; i<vip_list.length;i++){
            selectContent += '<div class="item-select">'+vip_list[i]+'</div>'
        }
        $(".search-list").html(selectContent)
        $(".discount-desc").is(':hidden') ? $(".discount-desc").show() : $(".discount-desc").hide()
        $(".search-vip input").val('')
        $(".discount-desc").css({"top":"-203px","max-height":"200px"})
    })
    //点击空白处隐藏弹出列表
    $('body').click(function(event){
        var _con = $('.discount-desc');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
           $(".discount-desc").hide();
        }
    });
    // 搜索会员卡
    $(".search-vip").on('keyup','input',function(e){
        if(!e.currentTarget.value){
            var selectContent = '';
            for(var i=0; i<vip_list.length;i++){
                selectContent += '<div class="item-select">'+vip_list[i]+'</div>'
            }
            $(".search-list").html(selectContent)
            $(".discount-desc").css({"top":"-203px","max-height":"200px"})
        }else{
            $.ajax({
                url:'/merchants/member/getMemberCardList?title=' + e.currentTarget.value,
                // data:{
                //     title:e.currentTarget.value
                // },
                type:'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(res){
                    console.log(res)
                    if(res.status == 1){
                        var selectContent = '';
                        if(res.data.length == 0){
                            selectContent = '<div style="color:#999;padding-left:5px">没有找到匹配项</div>'
                        }else{
                            for(var i = 0; i< res.data.length; i++){
                                selectContent += '<div class="item-select">'+res.data[i].title+'</div>'
                            }
                        }   
                    }
                    $(".search-list").html(selectContent)
                    $(".discount-desc").css({"top":"32px","max-height":"100px"})
                }
            })
        }
        
    })
    $("body").on('click','.item-select',function(){
        $(".select-result").text($(this).text())
        $(".discount-desc").hide()
    })
    //end
    // add by zhaobin 2018-10-24
    // 商品数量超出上限，不能再发布商品
    // update 许立 2019年01月03日 新建商品保留原来的跳转到新标签页
    $(".release-product").click(function(){
        if(isCreate != 1){
            tipshow('商品数量已超过上限，请联系客服升级处理','warn')
        }
    })
    // end
    // add by zhaobin
    $('.first_code').click(function(e){
        e.stopPropagation();
        $('.more-code').hide();
        $(this).siblings('.more-code').show()

    })
    //点击空白处隐藏弹出层
    $('body').click(function(event){
        var _con = $('.more-code');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){
            $(".more-code").hide();
        }
    });

})



