$(function(){
	/*取消*/
	$('.bd_btnq').on('click',function(){
		window.location.href='/merchants/wechat/userList/'+book_id
	})
	/*提交*/
	$('.bd_btnt').on('click',function(){
		var data={}
		var selects=$('.selects option:checked').val()
		var txtarea=$('.txtarea').val()
		var id = $('.bd_btnt').attr('id')
	//	var book_id = $('.bd_btnt').attr('book_id')
		data.status=selects
		data.content=txtarea
		data.id=id
	//	data.book_id = book_id
		console.log(selects)
        console.log(txtarea)
        console.log(id)
        console.log(book_id)
        $.ajax({
			headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
			type:"post",
			url:"/merchants/wechat/usersAlter",
			async:true,
			data:data,
			success:function(response){
				if(response.status == 1 ){
                    tipshow(response.info);
                    setTimeout(function(){
                    	window.location.href='/merchants/wechat/userList/'+book_id;
                    },1000);
               } else {
                   tipshow(response.info,'warn')
                }
			}
		});
	})
	
})
