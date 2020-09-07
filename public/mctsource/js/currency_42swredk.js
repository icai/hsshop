$(function(){
	$(".tab_nav li").click(function(){
		$(".tab_nav li").each(function(index, ele){
			$(this).removeClass("hover");
		})
		$(this).addClass("hover");
	})
	
	$(".imgShow").hide();
	$(".showed").show();
	//点击列表进行显示隐藏
	for (var i=0; i<$(".tab_nav li").length; i++) {
		$(".tab_nav li:eq("+i+")").click(function(){
			var index = $('.tab_nav li').index($(this));
			for (var j=0; j<$(".imgShow").length; j++) {
				$(".imgShow:eq("+j+")").hide();
			}
			$(".imgShow:eq("+index+")").show();
		})
	}
	//没有数据时，显示没有相关数据；
	for (var i=0; i<$(".imgShow").length; i++) {
		if ($(".imgShow").eq(i).html()=="") {
			$(".imgShow").eq(i).html("没有相关数据"+(i+1))
							   .css({textAlign: "center",padding: "39px 0", border: "1px solid #e5e5e5"});
		}
	}
	
	
	//有数据 点击查看详情
	$(document).on("click", ".imgShow_content .seeDetail", function(){
		var content = $(this).parents(".imgShow_content");
		var statue = $(this).siblings("li:eq(4)").text();
		var creat_time = $(this).siblings("li:eq(1)").text();
		var serve_name = $(this).siblings("li:eq(0)").text();
		var serve_time = $(this).siblings("li:eq(3)").text();
		var contract_amount = $(this).siblings("li:eq(2)").text();
		
		var waiting_html  = '<p class="border padding status">协议状态： <b>'+statue+'</b></p>';
        	waiting_html += '<div class="border creat_info">';
            waiting_html +=     '<p class="creat_time padding">创建时间：<b>'+creat_time+'</b></p>';
            waiting_html += 	'<div class="contract_parties flex_star">';
	        waiting_html +=       	'<div class="first_party padding">';
	        waiting_html +=        		'<b>甲方</b>';
	        waiting_html +=        		'<p class="first_party_name">**</p>';
	        waiting_html +=       		'<p class="info">若甲方信息有误，请先修改您店铺认证中的主体信息</p>';
	        waiting_html +=       		'<a href="##" style="color:dodgerblue;">变更主体</a>';
	        waiting_html +=        	'</div>';
	        waiting_html +=        	'<div class="second_party padding">';
	        waiting_html +=        		'<b>乙方</b>';
	        waiting_html +=        		'<p>杭州会搜科技股份有限公司</p>';
	        waiting_html +=        	'</div>';
            waiting_html +=    	'</div>';
            waiting_html += '</div>';
            waiting_html += '<div class="border serve_info">';
	        waiting_html +=     '<ul class="padding flex_star ul_title serve_info_title">';
	        waiting_html +=      	'<li>服务名称</li>';
	        waiting_html +=      	'<li>服务订购时间</li>';
	        waiting_html +=      	'<li>合同金额</li>';
	        waiting_html +=      	'<li>协议服务时间</li>';
	        waiting_html +=      '</ul>';
            waiting_html +=      '<ul class="padding flex_star">';
            waiting_html +=    		'<li>'+serve_name+'</li>';
            waiting_html +=    		'<li>'+creat_time+'</li>';
            waiting_html +=    		'<li>'+contract_amount+'</li>';
            waiting_html +=    		'<li>'+serve_time+'</li>';
            waiting_html +=    	  '</ul>';
            waiting_html += '</div>';
            waiting_html += '<p class="border padding">协议全文：</p>';
            waiting_html += '<div class="agreement">协议内容</div>';
            waiting_html += '<div class="agree padding flex_between">';
            waiting_html +=     '<label><input type="checkbox" id="sure" name="sure" value=""/>我以阅读并同意以上协议内容，生成后无法修改。</label>';
            waiting_html +=		'<button class="btn btn-warning sure_agteement_btn">生成协议</button>';
            waiting_html += '</div>';
            
        var generated_html  = '<p class="border padding status">协议状态： <b style="color:#12a896">'+statue+'</b></p>';
            generated_html += '<div class="border creat_info">';
            generated_html += 	'<p class="creat_time padding">创建时间：<b>'+creat_time+'</b></p>';
            generated_html +=   '<div class="contract_parties flex_star">';
	        generated_html +=       '<div class="first_party padding">';
	        generated_html +=        	'<b>甲方</b>';
	        generated_html +=        	'<p class="first_party_name">**</p>';
	        generated_html +=        	'<p class="info">若甲方信息有误，请先修改您店铺认证中的主体信息</p>';
	        generated_html +=       '</div>';
	        generated_html +=       '<div class="second_party padding">';
	        generated_html +=        	'<b>乙方</b>';
	        generated_html +=        	'<p>杭州会搜科技股份有限公司</p>';
	        generated_html +=       '</div>';
            generated_html += 	'</div>';
            generated_html += '</div>';
            generated_html +=  '<div class="border serve_info">';
	        generated_html +=    '<ul class="padding flex_star ul_title serve_info_title">';
	        generated_html +=       '<li>服务名称</li>';
	        generated_html +=       '<li>服务订购时间</li>';
	        generated_html +=       '<li>合同金额</li>';
	        generated_html +=       '<li>协议服务时间</li>';
	        generated_html +=    '</ul>';
            generated_html +=    '<ul class="padding flex_star">';
            generated_html +=    	'<li>'+serve_name+'</li>';
            generated_html +=    	'<li>'+creat_time+'</li>';
            generated_html +=    	'<li>'+contract_amount+'</li>';
            generated_html +=    	'<li>'+serve_time+'</li>';
            generated_html +=    '</ul>';
            generated_html += '</div>';
            generated_html += '<p class="border padding">协议全文：<a href="##" style="color: dodgerblue;">打印电子合同</a></p>';
            generated_html +=   '<b>协议下载记录</b>';
            generated_html +=   '<div class="border agteement_record">';
            generated_html +=    '<ul class="padding flex_star ul_title">';
            generated_html +=    	'<li>协议名称</li>';
            generated_html +=    	'<li>下载时间</li>';
            generated_html +=    	'<li>操作人</li>';
            generated_html +=    '</ul>';
            generated_html +=    '<ul class="padding flex_star">';
            generated_html +=    	'<li>有赞微商城代理销售服务和结算协议</li>';
            generated_html +=    	'<li>2017-02-20 10：47：50</li>';
            generated_html +=    	'<li>zhangsan</li>';
            generated_html +=    '</ul>';
            generated_html += '</div>'
		
		var cancellation_html  = '<p class="border padding status">协议状态： <b>'+statue+'</b></p>';
			cancellation_html += '<div class="border creat_info">';
			cancellation_html +=	'<div class="creat_time padding flex_between"><p style="display:inline-block">创建时间：<b>'+creat_time+'</b></p><p style="display:inline-block"><rew style="color:red">失效时间</rew>：<b>2017-02-21 10:20:20</b></p></div>';
			cancellation_html +=	'<div class="contract_parties flex_star">';
			cancellation_html +=		'<div class="first_party padding">';
			cancellation_html +=			'<b>甲方</b>';
			cancellation_html +=			'<p class="first_party_name">**</p>';
			cancellation_html +=			'<p class="info">若甲方信息有误，请先修改您店铺认证中的主体信息</p>';
			cancellation_html +=		'</div>';
			cancellation_html +=		'<div class="second_party padding">';
			cancellation_html +=			'<b>乙方</b>';
			cancellation_html +=			'<p>杭州会搜科技股份有限公司</p>';
			cancellation_html +=		'</div>';
			cancellation_html +=	'</div>';
			cancellation_html +='</div>';
			cancellation_html +='<div class="border serve_info">';
			cancellation_html +=	'<ul class="padding flex_star ul_title serve_info_title">';
			cancellation_html +=		'<li>服务名称</li>';
			cancellation_html +=		'<li>服务订购时间</li>';
			cancellation_html +=		'<li>合同金额</li>';
			cancellation_html +=		'<li>协议服务时间</li>';
			cancellation_html +=	'</ul>';
			cancellation_html +=	'<ul class="padding flex_star">';
			cancellation_html +=		'<li>'+serve_name+'</li>';
			cancellation_html +=		'<li>'+creat_time+'</li>';
			cancellation_html +=		'<li>'+contract_amount+'</li>';
			cancellation_html +=		'<li>'+serve_time+'</li>';
			cancellation_html +=	'</ul>';
			cancellation_html +='</div>';
            cancellation_html +='<p class="border padding">失效说明：服务以退订，协议失效</p>';
            
		if(statue == "等待生效"){
			$(this).parents(".imgShow").html(waiting_html);
		}else if(statue == "已生成"){
			$(this).parents(".imgShow").html(generated_html);
		}else{
			$(this).parents(".imgShow").html(cancellation_html);
		}
	})
});
