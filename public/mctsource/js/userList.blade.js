$(function(){
	$(".t_tou").on('click',function(){
		var enroll_id = $(this).parents('.t_content_con').data('id');
		var vote_id = $(this).parents('.t_content_con').data('voteid');
		window.location.href='/merchants/marketing/vote/voteUserList?enroll_id=' + enroll_id + '&vote_id=' + vote_id
	})
	
	$('body').on('click','.t_shan',function(e){
		e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/marketing/vote/enrollUserDel',
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow('删除成功','info');
                        $(_this).parents('.t_content_con').remove();
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            }); 
        })
	})
	
	
	$('.clear_conditions').on('click',function(){
		console.log(33)
		$('#t_top ul input').val('')
	})
	
})
