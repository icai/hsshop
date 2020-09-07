"use strict";
$(function(){
	//删除按钮点击事件
	$("body").on('click','.img-close',function(){    
		$(this).parent().remove(); 
	});
	//添加图片按钮点击事件
	$("#add_img").click(function(event) { 
		var num = 9-$(".img-box img").length;
		if(num>0){
			imgCommon(num);
		}else{
			tipshow("图片最多9张","warn");
		}
	});
	//点击保存事件
	$(".js-submit").click(function(){
		$(this).attr("disabled","disabled");
		var data ={}, 
			title = $("#title").val(),
			content = ueditor.getContent(),
			discussions_id = $("#discussions_id").val(),
			id = $("#id").val(); 
		var imgids = "";
		$(".img-box img").each(function(){
			imgids +=$(this).attr("data-id") + ","; 
		});
		if(imgids!="")
			imgids = imgids.substr(0,imgids.length-1);

		if(!discussions_id){
			$(this).removeAttr("disabled"); 
			tipshow("请选择帖子分类!","warn");
			return;
		}
		if(title==""){
			$(this).removeAttr("disabled"); 
			tipshow("请填写帖子标题!","warn");
			return;
		} 
		data.imgids = imgids;
		data.title = title;
		data.discussions_id = discussions_id;
		data.content = content;
		var url ="";
		if(id){ //编辑
			url ='/merchants/microforum/posts/edited';
			data.id = id;
		}else{//新增
			url ='/merchants/microforum/posts/released';
		}
	  	$.ajax({
		    url: url,
		    type: 'POST',
		    cache: false,
		    data: data, 
		    headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
		    success:function(res) {
		    	if(res.status==1){
		    		tipshow(res.info);
		    		setTimeout(function(){
		    			location.href="/merchants/microforum/posts/list"; 
		    		},1000)
		    	}else{
		    		tipshow(res.info,"wran");
		    	}
			},
			error:function(){
				console.log("异常");
			}
		});
		$(this).removeAttr("disabled");
	}); 

	var ueditor = initUeditor('ueditor');

});

/**
 * 发帖上传图片 
 * @param  {[int]} fileNumLimit 选择数 1-n 填写1为单选，其他多选
 */
function imgCommon(fileNumLimit){
    layer.open({
        type: 2,
        title:false,
        closeBtn:false, 
        // skin:"layer-tskin", //自定义layer皮肤 
        move: false, //不允许拖动 
        area: ['860px', '660px'], //宽高
        content: '/merchants/order/clearOrder/'+fileNumLimit+"?callback=selImgCallBack"
    }); 
}

/**
 * 图片选择后的回调函数
 */
function selImgCallBack(resultSrc){
	console.log(resultSrc);
	if(resultSrc.length>0){
		var html ="";
		for(var i=0;i<resultSrc.length;i++){
			if($(".img-box img").length<9){ 
				html+='<div class="img-box"><img data-id="'+resultSrc[i].id+'" src="/'+resultSrc[i].s_path+'" width="50" height="50" /><span class="img-close">x</span></div>';
			}else{
				tipshow("图片最多9张","warn");
			}
		}
		$("#img_previwe").append(html);
	} 
}