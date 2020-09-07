$(function(){
	var flag = true;
    var index = null;
    $('.reply_action').click(function(e){
        $('.comment_content').fadeIn(300);
        $(".replyBoard").fadeIn(200)
        flag = true;
        index = $(this).data('index');
        // e.stopPropagation;
    })
    //点击遮罩关闭弹框
    $(".replyBoard").click(function(){
    	$('.comment_content').hide();
        $(".replyBoard").hide()
    })
    // 添加评论
    $('.reply_btn').click(function(){
        if(trim($('textarea').val()) == ''){
            return;
        }
        $('.comment_content').hide();
        $(".replyBoard").hide();
        $('.reply_content').show();
		if(flag){
			$.ajax({
	            url:'/merchants/order/evaluateReply',// 跳转到 action
	            data:{
	                'eid':index,
	                'content':$('textarea').val()
	            },
	            type:'post',
	            cache:false,
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            dataType:'json',
	            success:function (response) {
	                if (response.status == 1){
	                    var html = '<p class="reply_content"><span><b>回复</b>：</span><span class="reply_detail">'+ $('textarea').val() +'</span></p>';
	                    $('.shop_title_'+ index).append(html);
	                    $('.shop_title_'+ index).parent().children('td').children('.reply_action').hide();
	                    $('.shop_title_'+ index).parent().children('td').children('.reply_already').html('已回复');
	                    $('textarea').val('');
	                }else{
	                    tipshow(response.info,'warn');
	                }
	            },
	            error : function() {
	                // view("异常！");
	                tipshow("异常！",'warn');
	            }
	        });
	        flag = false;
		}


    })
    //打标签弹窗
    var tag = {};//保存标签信息
    $('.make_tag').click(function(e){
        var html = '';
        tag.pid = $(this).data('pid');
        tag.eid = $(this).data('index');
        $.get('/merchants/order/getEvaluateClassify',function(data){
            $('.tap_container').html('');
            if(data.status==1){
                html += '<span><input type="checkbox" name="tag" value="有图">有图</span><span><input type="checkbox" name="tag" value="一般">一般</span>';
                    html += '<span><input type="checkbox" name="tag" value="好看">好看</span><span><input type="checkbox" name="tag" value="很实用">很实用</span><span><input type="checkbox" name="tag" value="还可以">还可以</span>';
                for(var i = 0;i<data.data.length;i++){
                    html += '<span><input type="checkbox" name="tag" value="'+data.data[i]['title']+'">'+ data.data[i]['title'] +'</span>'
                }
            }
            $('.tap_container').append(html);
        })
        $('.widget-promotion').css('left',$(this).offset().left-$('.widget-promotion').width()-215);
        $('.widget-promotion').css('top',$(this).offset().top-120);
        $('.widget-promotion').show();
        e.stopPropagation();
    })
    // 添加标签
    $('.add_tag').click(function(){
        var title = $('.tag_title').val();
        if(title == ''){
            tipshow('标签名称不能为空','warn');
            return;
        }
        $.get('/merchants/order/addEvaluateClassify',{title:title},function(data){
            if(data.status == 1){
                tipshow(data.info);
                var html = '<span><input type="checkbox" name="tag" value="'+title+'">'+ title +'</span>'
                $('.tap_container').append(html);
                $('.tag_title').val('');
            }
        })
    })
    //评论添加标签保存
    $('.tag_save').click(function(){
        tag.classify_name = [];
        $('input[name="tag"]').each(function(){
            if($(this).is(':checked')){
                tag.classify_name.push($(this).val());
            }
        })
        if(tag.classify_name.length == 0){
            tipshow('请选择标签','warn');
            return;
        }
        $.get('/merchants/order/addProductEvaluateClassify',tag,function(data){
            if(data.status){
                tipshow(data.info);
                setTimeout(function(){
                    window.location.reload();
                },2000)
            }
            $('.widget-promotion').hide();
        })
    })

    //评论添加标签取消
    $('.tag_cancel').click(function(){
        $('.widget-promotion').hide();
    })

    //删除评论
    $('.delete_action').click(function(e){
        e.stopPropagation();//组织事件冒泡
        var that = $(this);
        showDelProver($(this), function(){
            //执行删除
            $.post('/merchants/order/evaluateDelete', {id: that.parent().find('input').val(), '_token': $('meta[name="csrf-token"]').attr('content')}, function (data) {
                tipshow(data.info);
                window.location.reload();
            })
        }, '确定要删除吗?');
    });

}) 
function readFile(){
    var input_file = document.getElementsByClassName('upload_images');
    var reader = new FileReader(); 
    reader.readAsDataURL(input_file[0].files[0]); 
    reader.onload = function(e){ 
       $('.uploadimage img').attr('src',this.result);
    } 
}
function trim(str){
　　return str.replace(/(^\s*)|(\s*$)/g, "");
}