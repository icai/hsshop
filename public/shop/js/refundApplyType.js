var app = new Vue({
	el: ".apply_afterSales_type",
	methods:{
		returnGoods:function(){
			location.href = "/shop/order/refundApplyView/"+wid+"/"+oid+"/"+pid+"/"+isEdit+"/"+propID+"?type=2"
		},
		refunds:function(){
			location.href = "/shop/order/refundApplyView/"+wid+"/"+oid+"/"+pid+"/"+isEdit+"/"+propID+"?type=1"
		}
	}
})