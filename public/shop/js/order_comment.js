$(function () {
    //点击删除图标删除图片
    $("body").on("click", ".delete", function(e){
        var id = $(this).data('id');
        $("#ip_"+id).remove();
		$(this).parents(".img_item").remove()
    })
	
	//总体感受评价
	$(".evaluate .btn").each(function(index, ele){
		$(this).click(function(){
			$(".evaluate .btn").removeClass("choosed")
			$(this).addClass("choosed")
            var _html = '<input type="hidden" name="status" value="'+$(this).data('placement')+'" />'
            $("#text input[name='status']").remove();
            $("#text").append(_html);
		})
	})
	
    // 评价
    var describe = 0;   //商品描述描述
    var serice = 0;     //商家服务
    var speed = 0;      //发货速度
    $(".describe").find('span').click(function(){
        console.log(1);
        var that = $(this);
        var _html = '<input type="hidden" name="depict" value="'+that.data('index')+'" />'
        $("#text input[name='depict']").remove();
        $("#text").append(_html);
        var spans = $(".describe").find('span')
        describe = stars(spans,that);
    })
    $(".serice").find('span').click(function(){
        console.log(2);
        var that = $(this);
        var _html = '<input type="hidden" name="service" value="'+that.data('index')+'" />'
        $("#text input[name='service']").remove();
        $("#text").append(_html);
        var spans = $(".serice").find('span')
        serice = stars(spans,that);
    })
    $(".speed").find('span').click(function(){
        console.log(3);
        var that = $(this);
        var _html = '<input type="hidden" name="speed" value="'+that.data('index')+'" />'
        $("#text input[name='speed']").remove();
        $("#text").append(_html);
        var spans = $(".speed").find('span')
        speed = stars(spans,that);
    })
    // 星星
    function stars(ele,that){
        var index = that.attr("data-index");
        for(var i = 0; i < 5; i++){
            var $el = $(ele[i]);
            if(i < index){
                $el.addClass("red_xing");
            }else{
                $el.removeClass("red_xing");
            }
        }
//      return index;
    }
    $(".input_order").on('click',function () {
        var id = $(this).attr('data-id')
        if(id == 0){
            $(this).addClass('input_order_gou')
            $(this).attr('data-id','1')
        }else{
            $(this).removeClass('input_order_gou')
            $(this).attr('data-id','0')
        }
    })
    //点击提交按钮
    $(".submit").click(function(){
    	var choosed = $(".choosed").length;
    	if(choosed == 1){
    	    var content = $("textarea[name='content']").val();
    	    if (content == ''){
                tool.tip("说点什么吧，你的感受对其他朋友很重要");
                return false;
            }
            //商品描述
            var depict = $("#text input[name='depict']").val();
            var service = $("#text input[name='service']").val();
            var speed = $("#text input[name='speed']").val();
            if (typeof(depict) == 'undefined' || depict == ''){
                tool.tip("对商品描述打个分吧");
                return false;
            }
            if (service == '' || typeof(service) == 'undefined'){
                tool.tip("对商家服务打个分吧");
                return false;
            }
            if (speed == '' || typeof(speed) == 'undefined' ){
                tool.tip("对发货速度打个分吧");
                return false;
            }

            $.ajax({
                url:'/shop/order/comment/'+$('#wid').val(),// 跳转到 action
                data:$('#myForm ').serialize(),
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        var choosed = $(".choosed").length;
                        var share = $(".input_order").attr("data-id");
                        if(choosed == 1){ 
                            tool.tip("评价提交成功<br />页面跳转中")
                            setTimeout(function(){
                               if (share == 1) {
                                    window.location.href = "/shop/product/evaluateDetail/"+response.data.wid+"?eid="+response.data.id+"&share=1";
                                }else{
                                    window.location.href = "/shop/product/evaluateDetail/"+response.data.wid+"?eid="+response.data.id+"&share=2";
                                } 
                            },2000)
                            
                        }
                    }else{
                        alert(response.info);
                    }
                },
                error : function() {
                    // view("异常！");
                    alert("异常！");
                }
            });
    	}else{
    		tool.tip("请选择总体感受~");
    	}
    })
    
    

})

//图片上传
function getObjectURL(file) {
    var url = null;
    if (window.createObjectURL != undefined) {
        url = window.createObjectURL(file)
    } else if (window.URL != undefined) {
        url = window.URL.createObjectURL(file)
    } else if (window.webkitURL != undefined) {
        url = window.webkitURL.createObjectURL(file)
    }
    return url
};
