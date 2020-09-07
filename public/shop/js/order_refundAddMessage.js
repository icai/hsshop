var _response="";
var imgArr = [];
$(function() {
    /* 上传图片 */
	$(".js-add-picture").click(function(){
		if(imgArr.length>=3){
			tool.tip("最多添加3张图片");
			return false;
		}
		$("#upload_img").click();

	})
	$("#upload_img").change(function(){
		var data = new FormData($('#form1')[0]);  
        $.ajax({  
            url: '/shop/order/upfile/'+wid,  
            type: 'POST',  
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,  
            dataType: 'JSON',  
            cache: false,  
            processData: false,  
            contentType: false,
            success: function(response){
            	var response = JSON.parse(response);
            	if( response.status == 1 ){
		        	var _html = '<div class="img_item relative"><a class="small" src="javascript:void(0);" style="cursor:pointer;background-size:20px 20px;"></a>';
	                    _html +='<img src="/'+response.data.s_path+'" width="100%" height="80" />';
	                    _html +='</div>';
	                $('.uploader').append(_html);
	                //上传一张图片生成一个图片隐藏域           
	                imgArr.push(response.data.s_path);
		        } else {
		        	alert("失败")
		        }
            }  
        });  
	}) 
    //删除图片
    $(document).on('click','.small',function(){                 
        var index = $(this).parent().index() -1;
        $(this).parent(".relative").remove();
        imgArr.splice(index,1);
    }) 
    $('#submitMessage').click(function(){
        $.post('/shop/order/refundAddMessage/' + wid + '/' + rid  + '/' + oid,
            {content: $('#content').val(), imgs: imgArr, '_token':$('meta[name="csrf-token"]').attr('content')},
        function (data) {
            if (data.status == 1) {
                // 调转退款详情
             window.location.href = '/shop/order/refundDetailView/' + wid + '/' + oid + '/' + pid + '/' + propID;
            } else {
                alert(data.info)
            }
        });   		
	});
})
