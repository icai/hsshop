$(function(){
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
        copyToClipboard( obj );
        tipshow('复制成功','info');
        $(this).parents('.del_popover').remove();
    });
    //复制链接框点击事件
    // $("body").on('click','.del_popover',function(e){
    //     e.stopPropagation();//组织事件冒泡
    // });

    //body点击事件
    // $("body").click(function(){
    //     $(".copy_btn").parents(".del_popover").remove();
    // });
    // 新建微页面点击
    $('#add_page').click(function(){
        if(isCreate == 1){
            getTemplateData(0);
            setTimeout(function(){
                $('.widget-feature-template').show();
                $('.modal-backdrop').show();
            },200);
        }else{
            tipshow('页面数量已超过上限，请联系客服升级处理','warn')
        }
    })
     /**
     * @author 黄新琴
     * @desc 模板类型切换
     * @date 2018/9/27
     * @param templateType 模板类型 0:全部 1:美妆配饰 2:服饰衣帽 3:节日活动 4:官网展示 5:课程培训
     * @return
     */
    function getTemplateData(templateType){
        $.get("/merchants/store/getTemplateMarket?type=2&template_type="+templateType,function(res){
            var data = res.data;
            var html = "";
            if(data.length != 0){
                for(var i = 0; i < data.length; i++){
                    html += '<li><div class="img-wrap template-state-2">';
                    html += '<img class="template-screenshot" src="' + SOURCE_URL + data[i].screenshot + '">';
                    html += '<div class="template-cover"><div class="template-action-container">';
                    html += '<a href="' + data[i].url + '" class="zent-btn zent-btn-success js-select-template" style="width: 88px;">' + "使用模板" + '</a>';
                    html += '</div></div></div><p class="template-title">';    
                    html += '<span>' + data[i].title + '</span></p></li>';
                } 
            }
            $(".widget-feature-template-list").html(html);
        })
    }
    $('.js-filter-wraper').on('click','.js-filter',function(){
        var type = $(this).data('type');
        $(this).parent().addClass('active').siblings('.active').removeClass('active');
        getTemplateData(type);
    });
    // 微页面模板弹窗关闭点击
    $('.close').click(function(){
        $('.widget-feature-template').hide();
        $('.modal-backdrop').hide();
    })
    // 搜索
    $(".chzn-select").chosen({
        width:'150px',
        no_results_text: "没有找到",
        allow_single_de: true
    }).change(function(){
        $('form[name="categoryForm"]').submit();
    });
    // 回车提交表单
    $('.search_input').keydown(function(e){
         if(e.keyCode == 13){
            $('form[name="categoryForm"]').submit();
         }
    })
    // 全选、单选
    checkAll( '.check_all' , '.check_single' );

    // 列表复制 
    $('body').on('click','.copy_list',function(e){
        e.stopPropagation();
        var _this = $(this);
		var id=$(this).data('id');
        showDelProver(_this,function(){
				$.ajax({
				type:"post",
				url:'/merchants/store/copyMicroPage',
				data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
				dataType:'json',
				success: function(msg){
					if(msg.errCode==0){  
						tipshow('复制成功！');
						window.location.reload();
					}else{
                        tipshow('复制失败！','warn');
                    }
				},
				error:function(msg){
					tipshow('删除失败！','warn');
				}
			});	
            // var html = _this.parents('tr').prop("outerHTML");       // 要复制的东西
            // // 插入复制的内容
            // $(html).insertAfter( $(_this).parents('tr') );
            // // 设为主页样式修改(只能有一个店铺主页)
            // _this.parents('tr').next().find('.set_homepage span').addClass('blue_38f').text('设为主页');
        },'确定要复制？');           
    });

    // 删除列表
    $('body').on('click','.del_list',function(e){
        e.stopPropagation();
        var _this = this;
		var id=$(this).data('id');
        showDelProver($(_this),function(){
			$.ajax({
				type:"post",
				url:'/merchants/store/deletePage',
				data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
				dataType:'json',
				success: function(msg){
					if(msg.errCode==0){
						tipshow('删除成功！');
					}else{
                        tipshow('删除失败！','warn');
                    }
				},
				error:function(msg){
					tipshow('删除失败！','warn');
				}
			});	
            var setHomePage = $(_this).siblings('.set_homepage').find('span');
                $(_this).parents('tr').remove();
                if( !setHomePage.hasClass('blue_38f') ){             // 删除的是主页
                    // 默认第一个是店铺主页
                    $('table').find('tr').eq(1).find('.set_homepage span').removeClass('blue_38f').text('店铺主页');
                }
                // 删完店铺主页
                if( parseInt($('.data-table tbody tr').length) ==1 ){
                    $('.data-table tbody').append('<tr><td  colspan="7"><div class="no_result">暂无数据</div></td></tr>');
                } 
        })
    });
    // 设为主页
    $('body').on('click','.set_homepage',function(){
		var id=$(this).data('id');
		var _this=this;
		$.ajax({
				type:"post",
				url:'/merchants/store/updateMicroPageHome',
				data:{id:id,_token:$('meta[name="csrf-token"]').attr('content')},
				dataType:'json',
				success: function(msg){
					if(msg.errCode==0){
                        tipshow('设置成功！'); 
                        location.reload();
						var title = $(_this).parent().parent().children().eq(0).children('span').html();
						var date = $(_this).parent().parent().children().eq(1).html();
						$('.homepage_title span').html(title);
						$('.title_des span').html(date);
						$(_this).find('span').removeClass('blue_38f').text('店铺主页');
						$(_this).parents('tr').siblings('tr').find('.set_homepage span').addClass('blue_38f').text('设为主页');
					}else{
                        tipshow('设置失败！','warn'); 
                    }
				},
				error:function(msg){
					tipshow('设置失败！','warn');
				}
			});	
    });
    
    // 批量管理弹框
    // $('.manage_items').focusin(function(){              // 聚焦
    //     if( flagGrounp() ){
    //         $('.manage_tip').stop().fadeIn( 400 );
    //     }else{
    //         tipshow('请选择微页面','wran');
    //     }   
    // });
    // // // 失焦
    // $('.manage_items').focusout(function() {        // 失焦 
    //     $('.manage_tip').stop().fadeOut( 400 );
    // });

    $('.grounp_btn').click(function(){
        $('.grouped_body').find("input[type='radio']").prop('checked',false);
        if( flagGrounp() ){
            $('.manage_tip').show();
        }else{
            tipshow('请选择微页面','wran');
        }  
    })
    $('body').click(function(event){
        var _con = $('.manage_items');   // 设置目标区域
        if(!_con.is(event.target) && _con.has(event.target).length === 0){ 
            $(".manage_tip").hide();
        }
    });

    // 批量改分类
    $('.sure_chanage_category').click(function(){
        var data ='';
        $('.check_single').each(function(key,val){
            if($(this).is(':checked')){
                var id = $(this).val();
                //alert(id);
                category_id = [];
                $('.grouped_body input').each(function(key,val){
                    if($(this).is(':checked')){
                        category_id.push($(this).val());
                    }
                })
              data+= '"' + id + '"'+':'+ '[' + category_id + '],';
                // alert(id);
                // console.log(category_id);
                //data.push({id:category_id})
            }
        })
        // console.log(data)
        if(category_id.length == 0){
            tipshow('请选择分类！','warn');
            return;
        }
        data=data.substring(0,data.length-1);
        data="{"+data+"}";
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.post('/merchants/store/processCategorys',{categorys:data,_token:_token},function(data){
            if(data.errCode == 0){
                tipshow('修改成功！');
                setTimeout(function(){
                    window.location.reload();
                },2000)
            }
        },'json')
    })
    $('.cancel_chanage_category').click(function(){
        $('.manage_tip').stop().fadeOut( 400 );
    })
    //序号列表点击
    var num = '';
    $('.js-change-num').click(function(){
        num = $(this).text();
        $(this).hide();
        $(this).next().val(num);
        $(this).next().show();
        $(this).next().focus();
    })
    // 序号输入框失焦
    $('.input-mini').blur(function(){
        $(this).hide();
        $(this).prev().show();
        var that = $(this);
        num = $(this).val();
        if(num != ''){
            if(num.length>10){
                tipshow('序号不能大于10位','warn');
                return;
            };
            var id = $(this).parent().parent().children('td').children('.check_single').val();
            $.get('/merchants/store/updateSequenceNumber',{id:id,number:num},function(data){
                console.log(data);
                if(data.errCode == 0){
                    that.prev().html(that.val());
                }
            })
        }
    })

        
    $(".grouped_body label").click(function(e){
        e.preventDefault()
        $(this).find("input[type='radio']").prop('checked',true);
        $('.grouped_body input').each(function(key,val){
            if($(this).is(':checked')){
                console.log(key)
            }
        })
    })










    // 点击排序
    // $('.active a').click(function(){
    //     var orderby = $(this).data('orderby');
    //     var pre_host = JSON.stringify(window.location);
    //         pre_host = pre_host.substring(9,pre_host.indexOf('?'));
    //     var ele = $(this).children('span');
    //     var title = GetRequest()['title'];
    //     var type = GetRequest()['type'];
    //     if(ele){
    //         if(ele.hasClass('desc')){
    //             ele.removeClass('desc');
    //             ele.addClass('asc');
    //             // alert(location.host)
    //             if(type !== null && title !== null){
    //                 pre_host = pre_host + '?' + 'title='+ title +'& type=' + type + '&orderby='+ orderby + '&order=asc';
    //             }else{
    //                 pre_host = pre_host + '?' + '&orderby='+ orderby + '&order=asc';
    //             }
    //         }else{
    //             ele.addClass('desc');
    //             ele.removeClass('asc');
    //             if(type !== null && title !== null){
    //                 pre_host = pre_host + '?' + 'title='+ title +'& type=' + type + '&orderby='+ orderby + '&order=desc';
    //             }else{
    //                 pre_host = pre_host + '?' + '&orderby='+ orderby + '&order=desc';
    //             }
    //         }
    //         window.location.href = pre_host;
    //         console.log(pre_host);
    //     }else{
    //         ele.append('<span class="orderby-arrow asc"></span>')
    //     }
    // })
})
/**
 * [flagGrounp 判断列表是否有选中状态]
 * @return {[type]} [返回boolearn true->有选中的，false->无选中的]
 */
function flagGrounp(){
    var flag = false
    $('table .check_single').each(function(){
        if( $(this).prop('checked') ){
            flag = true;
        }
    });
    return flag;
}
/**
    * 获取所有url上的参数
    * 修改 并返回 对应 url的参数值
    */
   function getallparam(obj){
       var sPageURL = window.location.search.substring(1);
       var sURLVariables = sPageURL.split('&');
       var flag = 0;
       for(var i = 0; i< sURLVariables.length; i++){
           var sParameterName = sURLVariables[i].split('=');
           if (undefined != obj[sParameterName[0]]){
               sParameterName[1] = obj[sParameterName[0]];
               flag++;
           }
           sURLVariables[i] = sParameterName.join('=');
       }
       var newquery = sURLVariables.join('&');
       for(var key in obj){
           if(-1 === newquery.indexOf(key)){
               newquery += '&'+key+'='+obj[key];
           }
       }
       return newquery;
   }

   //点击排序
   var SORT = [0,0,0,0];
   var ORDER_BY = ['created_at','sequence_number','goods_num'];
   var ORDER = ['asc','desc'];
   var FLAG = ['↑','↓'];
   var NAME = ['创建时间','商品数','序号'];
   function sort_desc(index,sort){
       var params = getallparam({order:ORDER[sort],orderby:ORDER_BY[index]});
       window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
   }

