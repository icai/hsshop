$('.J_verify-button').click(function(){
	var that = $(this)
	if($('.is_weiquan').children().length != 0){
		layer.confirm('该订单有售后记录，确定核销吗？', {
		  btn: ['确定','取消'] //按钮
		},function(){
			Hexiao(that)
		})
    }else{
  		Hexiao(that)
   }
});

function Hexiao(that) {
	var data = {};
	data._token = $('meta[name="csrf-token"]').attr('content');
    data.orderId = that.data('oid');
    data.code= $('.search-input').val();
    $.get("/merchants/order/finishOrder",data,function(res){
        if(res.errCode == 0){
            location.reload();
        }else{
            tipshow(res.errMsg,"warn");
        }
    });
}
