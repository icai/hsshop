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
        $.ajax({
            type:"get",
            url:"/merchants/marketing/vote/createQrcode?id="+id,
            async:true,
            success:function(res){
                var content = '<img src="'+res.data+'" width="140" height="140" />';
                $('.t-pop-content').html(content)
                showDelProver(_this,function(){},$('.t-pop').html(),'false');
            },
            error:function(){
                alert('数据访问错误');
            }
        });        
        $(".t-pop").hide();
        
    });

     $("body").on("click",".flo-rig",function(e){
    	$(".popover").remove();
    });
    // 删除列表
    $('body').on('click','.delBtn',function(e){            
        e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/marketing/vote/del',
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow('删除成功','info');
                        $(_this).parents('.data_content').remove();
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
});