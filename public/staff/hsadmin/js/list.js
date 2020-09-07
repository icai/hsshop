$(function(){
        	// 删除列表
            $('body').on('click','.del',function(e){  
                var _this=this;	 	
                 e.stopPropagation();
                 var id = $(this).data('id');
                showDelProver($(_this),function(){
                    $.ajax({
                        type:"post",
                        url:'/staff/weixin/case_del',
                        data:{
                            id:id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res){
                            if(res.status===1){
                                tipshow('删除成功','info');
                                $(_this).parents('.table_body').remove();
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

            $('.zent-dialog-r-wrap').on('click',function(){
                $('.zent-dialog-r-wrap').hide();
                $('.zent-dialog-r-wrap img').attr('src','')
            })
            $('.table_body img').on('click',function(){
                $('.zent-dialog-r-wrap').show();
                var imgurl =$(this).attr('src');
                $('.zent-dialog-r-wrap img').attr('src',imgurl)
            })
})
