$(function(){
	pageAiax()

	//	设置日期
	var start = {
		elem: '#start_time',
		format: 'YYYY-MM-DD hh:mm:ss',
		min: laydate.now(), //设定最小日期为当前日期
		max: '2099-06-16 23:59:59', //最大日期
		istime: true,
		istoday: false,
		choose: function(datas) {
			end.min = datas; //开始日选好后，重置结束日的最小日期
			end.start = datas //将结束日的初始值设定为开始日
			$('.start_time').text(datas);
		}
	};
	var end = {
		elem: '#end_time',
		format: 'YYYY-MM-DD hh:mm:ss',
		min: laydate.now(),
		max: '2099-06-16 23:59:59',
		istime: true,
		istoday: false,
		choose: function(datas) {
			start.max = datas; //结束日选好后，重置开始日的最大日期
			$('.end_time').html(datas);;
		}
	};
	
	/**
	 * 封装下拉选择小程序页面路径的插入函数
	 * @param {any} thisDom 当前Dom的this 相当于$(this)
	 * @param {any} targetDom 目标DOM
	 */
	function getXcxRes(thisDom, targetDom) {
		targetDom.empty()
		const this_verResult = thisDom.data("ver")
		if (this_verResult.page_list) {
			const xcx_page = JSON.parse(this_verResult.page_list)
			let options = ''
			for(let i = 0; i < xcx_page.length; i++) {
				options = `<option>${xcx_page[i]}</option>`
				targetDom.append(options);
			}
		}
	}
	laydate(start);
	laydate(end);
	
	layer.config({
			extend: 'extend/layer.ext.js'
    }); 
    
   

	


	var globalPage = {
		total: 0,
		pageSize: 0,
	}
	/*
	* 小程序自动更新
	*/
	// 全选按钮的事件
	var aIds = [];
	$("#allCheck").click(function(){
		if ($(this).prop("checked")) {
			$(".xcx-id-box").prop("checked", true)
		}else{
			$(".xcx-id-box").prop("checked", false)
			aIds = [];
		}
	})
	//按钮-是否付费
	$(document).on('click','#isFee,#isFree,#isGive',function(){
		var judgeFree = [];
		var changeLogo = [];
		$(".xcx-id-box").each(function(){
			if($(this).prop("checked")){
				judgeFree.push($(this).parents('tr').find('th').attr('data-wid'))
			}
		})
		console.log(judgeFree);
		if(judgeFree.length==0){
			return false
		}
		var free_ids = JSON.stringify(judgeFree);
		if($(this).attr('id') == 'isFee'){
			transFreeAjax(free_ids,1);
		}
		else if($(this).attr('id') == 'isFree'){
			transFreeAjax(free_ids,0);
		}
		else if($(this).attr('id') == 'isGive'){
			transFreeAjax(free_ids,2);
		}
		judgeFree = [];
		changeLogo = [];
		return;
	})
	
	
	// 付费免费
	function transFreeAjax(ids,isFee){
		if(ids != '[]' && ids != ''){
			$.ajax({
				url:'/staff/store/updateForFee',
				data:{ids,isFee},
				type:'POST',
				headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				dataType:'json',
				success:function(res){
					location.reload()
				}
			})
		}
	}
})

//封装ajax请求数据
 function pageAiax(){
	 var appUrl = _host;
	 $.ajax({
		url:"/staff/aliapp/config/select/all",
		type:"POST",
		// data:{
		// 	page:currentPage
		// },
		dataType:"json",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success:function(res){
			console.log(res)
			var data = res[0].data;
			console.log(data)
			var html = '';
			var page = '';
			for(var i=0;i<data.length;i++){
				html+='<tr class="tr-id">'+
							'<th class="xcx-id" scope="row" data-wid="'+data[i].wid+'">'+
							'<input type="checkbox" class="xcx-id-box" value="'+data[i].id+'"/>'+
							data[i].id+
							'</th>'+
							'<td class="title_merchant">美秀大师</td>'+
							'<td>'+data[i].template_id+'</td>'+
							'<td class="shop_name">'+
								'<span>'+data[i].widName+'</span>'+
								'<img class="logo" src= "'+appUrl+'staff/hsadmin/images/mian@2x.png"/>'+
							'</td>'+
							'<td>'+data[i].service_phone+'</td>'+
							'<td>https://hsshop2.huisou.cn</td>'+
							'<td>'+data[i].user_id+'</td>'+
							'<td class="xcx_app_id">'+data[i].auth_app_id+'</td>'+
							'<td class="updata_code">'+
							data[i].statusName+'<br />'+
								'<span>'+data[i].updated_at+'</span>'+
							'</td>'+
							'<td>'+
							data[i].created_at+
							'</td>'+
							'<td class="operation">'+
								'<a class="tab_operation xcx_tab" href="javascript:void(0);">小程序</a>'+
								'<a class="tab_operation test_tab" href="javascript:void(0);">测试及体验</a>'+
								'<a class="tab_operation set_tab" href="javascript:void(0);">通用设置</a>'+
								'<div class="item_box xcx_box">'+
									'<a href="javascript:void(0);" id="upload_code" class="upload_code">版本上传</a>'+
									'<a href="javascript:void(0);" id="submit_code" class="submit_code">提交审核</a>'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="online_xcx" class="online_xcx">提交上架</a>'+
									'<a href="javascript:void(0);">审核查询</a>'+
									'<a href="javascript:void(0);" id="see_detail" class="see_detail" data-id="'+data[i].id+'">查看详情</a>'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="offline_xcx" class="offline_xcx">下架</a>'+
								'</div>'+
								'<div class="item_box test_box">'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="bind_experiencer" class="bind_experiencer">添加体验者</a>'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="cancel_experiencer" class="cancel_experiencer">删除体验者</a>'+
									// '<a href="">提交灰度</a>'+
									// '<a href="">结束灰度</a>'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="create_experience" class="create_experience">生成体验版</a>'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="cancel_experience" class="cancel_experience">删除体验版</a>'+
									'<a href="javascript:void(0);" id="get_qrcode" class="get_qrcode" data-id="'+data[i].id+'">二维码</a>'+
								'</div>'+
								'<div class="item_box set_box">'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" data-domain="'+data[i].safe_domain+'" id="setting_host" class="setting_host">域名设置</a>'+
									'<a href="javascript:void(0);" data-id="'+data[i].id+'" id="add_remark_btn" class="add_remark_btn">添加备注</a>'+
								'</div>'+
							'</td>'+
						'</tr>'
			}
			page += '<div class="prev"><a href="javascript:void(0);"><span>《</span></a></div><ul>';
			var showPage = 4;
			if(res[0].last_page<showPage){
				for(var i=0;i<res[0].last_page;i++){
					page+='<li class="active"><a href="javascript:void(0);">'+(i+1)+'</a></li>'
				}	
			}else{
				for(var i=0;i<4;i++){
					page+='<li class="active"><a href="javascript:void(0);">'+(i+1)+'</a></li>'
				}
			}	
			page+='</ul><div class="next"><a href="javascript:void(0);"><span>》</span></a></div>'
			$(".page").html(page);
			$(".table tbody").html(html);
			$(".record span").text(res[0].data.length)
			pagination(res)
			allEvent()
			

		}
	 })
 }   
 //分页
 function pagination(res){
	 // add by 赵彬 2018-7-30
			// 操作tab切换
			$(".tab_operation").mouseover(
				function(){
					var idx = $(".tab_operation").index($(this))
					$(".item_box").removeClass("active")
					$(".item_box").eq(idx).addClass("active")
				}
			)
			$(".operation").mouseleave(
				function(){
					$(".item_box").removeClass("active")
				}
			)
			//上一页
			$(".page .prev").click(function(){
				var activePage = $(".page").find('li[class="active"]');
				var currentPage = activePage.find("a").html();
				if(currentPage == 1){
					return false;
				}else if(currentPage>1){
					currentPage = currentPage-1;
					$(".page li").removeClass("active");
					activePage.prev().addClass("active");
				}
				pageAiax(currentPage);
			})
			//下一页
			$(".page .next").click(function(){
				var activePage = $(".page").find('li[class="active"]');
				var currentPage = activePage.find("a").html();
				totalPage = res[0].total
				if(totalPage == 1 || currentPage == totalPage){
					return false
				}else if(currentPage<totalPage){
					currentPage++;
					$(".page li").removeClass("active");
					activePage.next().addClass("active");
				}
				pageAiax(currentPage);
			})
 }
 //操作事件
 function allEvent(){
	 /**
	 * 获取当前行的id和title
	 * @param {any} thisDom 
	 * @returns 返回当前行的wid
	 */
	function getWid(thisDom) {
		return { 
			wid: thisDom.parents(".tr-id").find(".xcx-id").data("wid"),
			title: thisDom.parents(".tr-id").find(".title_merchant").html()
		}
	}
	// 上传代码
	$('.upload_code').click(function(){
		var id = $(this).parents(".tr-id").find(".xcx-id-box").val();
		var title = $(this).parents(".tr-id").find(".title_merchant").text();
	//	var res = $(this).data("ver");
		$(".upload_title").text(title)
		//弹出一个页面层
		layer.open({
				type: 1,
				area: ['600px', 'auto'],
				shadeClose: true, //点击遮罩关闭
				content: $('.upload_code_model').html(), 
				btn: ['确认', '取消'],
				yes:function(idnex,layero){
					hstool.load();
					var data = {
						appVersion:$(layero).find(".version").val(),
						templateId:$(layero).find(".template_id").val(),
						configId:id
					};
					$.ajax({
						url:"/staff/aliapp/versionUpload",
						type:"POST",
						data:data,		
						dataType:'json',
						headers: {
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success:function(res){
							console.log(res)
							hstool.closeLoad();						
							if(res.status == 1){
								tipshow(res.info,"info");
								window.location.reload();
							}else{
								tipshow(res.info,"warn")
							}
						},
						error:function(res){
							alert("数据访问错误");
						}
					});
					layer.closeAll();
				}
		});
	})
	// 设置域名
	$('body').on('click','.setting_host',function(){
			var id = $(this).data('id')
			//弹出一个页面层
			layer.open({
					type: 1,
					title: '请输入域名',
					shadeClose: true, //点击遮罩关闭
					content: $('.set_code_model').html(),
					btn: ['确认', '取消'],
					success:function(index,layero){
					},
					yes:function(idnex,layero){
						console.log($(layero).find(".set_zhost").val())
						hstool.load();
						var data = {
							id:id,
							safeDomain:$(layero).find(".set_zhost").val()
						}
						$.ajax({
								url:"/staff/aliapp/safedomain/create",
								type:"POST",
								data:data,
								dataType:'json',
								headers: {
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},
								success:function(res){
									console.log(res)
									hstool.closeLoad();
									if(res.status ==1){
										tipshow(res.info,"info");
										window.location.reload();
									} else {
										tipshow(res.info,"warn")
									}
								},
								error:function(res){
									hstool.closeLoad();
									alert("数据访问错误");
								}
						}),
						layer.closeAll();
					}
			});
	})
	// 添加体验者
	$('.bind_experiencer').click(function(){
		var id = $(this).data('id');	
		layer.prompt({title: '请填写支付宝账号', formType: 3}, function(pass, index){
				content:
				hstool.load();
				var data = {
					role:"EXPERIENCER",
					logonId:$(".layui-layer-input").val(),
					id:id
				}
				$.ajax({
					url:"/staff/aliapp/member/create",
					type:"POST",
					data:data,		
					dataType:'json',
					headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success:function(res){
						hstool.closeLoad();
						if(res.status == 1){
							tipshow(res.info,"info")
						}else{
							tipshow(res.info,"warn")
						}
					},
					error:function(res){
						alert("数据访问错误");
					}
				}),
				layer.close(index);
		});
	})
	//删除体验者
	$('.cancel_experiencer').click(function(){
		var id = $(this).data('id');	
		layer.prompt({title: '请填写支付宝账号', formType: 3}, function(pass, index){
				content:
				hstool.load();
				var data = {
					role:"EXPERIENCER",
					logonId:$(".layui-layer-input").val(),
					id:id
				}
				$.ajax({
					url:"/staff/aliapp/member/delete",
					type:"POST",
					data:data,		
					dataType:'json',
					headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success:function(res){
						hstool.closeLoad();
						if(res.status == 1){
							tipshow(res.info,"info")
						}else{
							tipshow(res.info,"warn")
						}
					},
					error:function(res){
						alert("数据访问错误");
					}
				}),
				layer.close(index);
		});
	})
	//查看详情
	$('.see_detail').click(function(){
		var id = $(this).data('id');
		//弹出一个页面层
		// layer.open({
		// 	type: 1,
		// 	area: ['1200px', '700px'],
		// 	title: '查看备注',
		// 	shadeClose: true, //点击遮罩关闭
		// 	content: $('.detail_model').html(),
		// });
		// $(".table-detail").show()
		// $(".remark-table").hide()
		// $(".detail_tab").addClass("active_tab")
		// $(".remark_tab").removeClass("active_tab")
		$.ajax({
			url:"/staff/aliapp/detail/" + id,
			type:"GET",
			// data:data,		
			dataType:'json',
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success:function(res){
				console.log(res)
				hstool.closeLoad();
				if(res.status == 1){
					tipshow("查看成功","info")
				}else{
					tipshow(res.info,"warn")
				}
			},
			error:function(res){
				alert("数据访问错误");
			}
		})
	})
	// 获取二维码
	$(".get_qrcode").click(function () {
		var id = $(this).data('id');
		const this_title = getWid($(this)).title;
		$(".qr_code_title").text(this_title)
	//	getXcxRes($(this), $(".qr_code_path"))
		$("#img_qrcode").attr("src", '');
		$(".qr_code_model").show()
		$(".qr_code_img").hide()
		// 弹出二维码弹窗
		layer.open({
			type: 1,
			area: ['500px', '320px'],
			title: '获取二维码',
			shadeClose: true, // 点击遮罩层关闭弹窗
			btn: ['朕已确认', '朕再想想'],
			content: $(".get_qrcode_model").html(),
			yes: (index, layero) => {
				hstool.load();
				const data = {
					id: id
					// wid : getWid($(this)).wid,
					// width: $(layero).find(".qr_code_width").val(),
					// path: $(layero).find(".qr_code_path option:selected").val(),
					// query: $(layero).find(".qr_code_params").val()
				};
				$.ajax({
					url: "/staff/aliapp/experience/query",
					type: "GET",
					data: data,
					dataType:'json',
					headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
					success: function(result) {
						console.log(result)
						if (result.errCode === 0) {
							$(".xcx-xcximg").attr("src", `data:image/png;base64,${result.data}`);
							if ($("#img_qrcode").attr("src")) {
								$(".qr_code_model").hide()
								$(".qr_code_img").show()
								tipshow("领取二维码成功", "info")
							}
						} else {
							tipshow(result.info, "error")
						}
						hstool.closeLoad()
					},
					error: (e) => {
						hstool.closeLoad()
						tipshow(e, "error")
					}
				})
			},
			cancel: (index, layero) => {
				setTimeout(() => {
					$(".qr_code_model").show()
					$(".qr_code_img").hide()
				}, 200)
			},
		})
	})
	// 添加备注
	$(".add_remark_btn").click(function() {
		const this_wid = getWid($(this)).wid;
		const this_title = getWid($(this)).title;
		console.log(this_wid,this_title)
		$(".remark_add_title").text(this_title)
		layer.open({
			type: 1,
			area: ['500px', 'auto'],
			title: '添加备注',
			shadeClose: true,
			btn: ['朕已确认', '朕再想想'],
			content: $(".add_remark_model").html(),
			yes: (index, layero) => {
				hstool.load();
				if ($(layero).find(".remark_add_cont").val()) {
					// 参数需要配置
					const data = {
						appId: $(this).parents(".tr-id").find(".xcx_app_id").text(),
						wid: this_wid,
						appName: this_title,
						content:  $(layero).find(".remark_add_cont").val(),
					}
					$.ajax({
						url: "/staff/xcx/log/add",
						type: "GET",
						data: data,
						async: true,
						success: (result) => {
							hstool.closeLoad()
							if (result.errCode === 0) {
								tipshow("添加备注成功", "info")
							} else {
								tipshow(result.errMsg, "warn")
						}
						},
					})
					layer.closeAll();
				} else {
					hstool.closeLoad()
					tipshow("请输入备注信息", "warn")
				}
			}
		})
	})
	//提交上架
	$(".online_xcx").click(function(){
		$.ajax({
			type: "GET",
			url: "/staff/aliapp/version/online",
			data: {
				id:$(this).data('id'),
			},
			async: true,
			success: (result) => {
				console.log(result)
				if (result.status === 1) {
					tipshow("小程序上架成功", "info")
				} else {
					tipshow(result.info, "error")
				}
			},
			error: (e) => {
				tipshow(e.errCode, "error")
			},
		})
	})
	// 下架小程序
	$(".offline_xcx").click(function() {
		$.ajax({
			type: "GET",
			url: "/staff/aliapp/version/offline",
			data: {
				id:$(this).data('id'),
			},
			async: true,
			success: (result) => {
				if (result.status === 1) {
					tipshow("小程序下架成功", "info")
				} else {
					tipshow(result.info, "error")
				}
			},
			error: (e) => {
				tipshow(e.errCode, "error")
			},
		})
	})
	// 提交审核
	$('.submit_code').click(function(){
		var id = $(this).parents(".tr-id").find(".xcx-id-box").val();
		var title = $(this).parents(".tr-id").find(".title_merchant").text();
		var sub_tag = '';
		$(".title_up").text(title);
		//弹出一个页面层
		layer.open({
				type: 1,
				area: ['600px', 'auto'],
				title: '提交审核',
				shadeClose: true, //点击遮罩关闭
				content: $('.submit_code_model').html(),
				btn: ['确认', '取消'],
				yes:function(idnex,layero){
					hstool.load();
					var data = {
						configId:id,
						appVersion:$(layero).find(".version").val(),
						licenseValidDate:$(layero).find(".licenseValid_date").val(),
						versionDesc:$(layero).find(".version_desc").val(),
					};
					$.ajax({
						type:"POST",
						url:"staff/aliapp/versionAudit",
						data:data,
						headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
						async:true,
						success:function(res){
							hstool.closeLoad();
							if(res.status == 1){
								tipshow(res.info,"info");
								window.location.reload();
							}else{
								tipshow(res.info,"warn");
							}
	
						},
						error:function(){
							alert("数据访问错误")
						}
					});	
					layer.closeAll();
				},
				end:function(){
						$('.sub_code_model').remove()
				}
		});
	})
	//创建体验版
	$(".create_experience").click(function(){
		hstool.load();
		var id = $(this).data('id');
		$.ajax({
			url:'/staff/aliapp/experience/create',
			type:'POST',
			data:{
				id:id
			},
			dataType:'json',
			headers:{
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success:function(res){
				hstool.closeLoad()
				if (res.status === 1) {
					tipshow(res.info, "info")
				} else {
					tipshow(res.info, "warn")
				}
			},
			error:function(res){
				alert("数据访问错误");
			}
		})
	})
	//删除体验版
	$(".cancel_experience").click(function(){
		hstool.load();
		var id = $(this).data('id');
		$.ajax({
			url:'/staff/aliapp/experience/cancel',
			type:'POST',
			data:{
				id:id
			},
			dataType:'json',
			headers:{
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success:function(res){
				hstool.closeLoad()
				if (res.status === 1) {
					tipshow(res.info, "info")
				} else {
					tipshow(res.info, "warn")
				}
			},
			error:function(res){
				alert("数据访问错误");
			}
		})
	})
	}

/*
loading加载状态
*/
var hstool =(function(){
    var hstool ={};
		hstool.config= {//默认配置
				type: 0, //类型 0.msg 1.tips提示框 2.选择商品
				title: '信息', //标题
				opacity: 0.7, //遮罩层透明度
				message: "",
				zIndex: 19891014, 
				time: 0, //0表示不自动关闭
				content:"",
				isMask: false, //是否添加点击遮罩层事件 
				done: null, //完成操作的回调函数 
				host: "",//域名
				area: [],//区域 参数 width,height
				skin:"default" //皮肤设定 后期扩展使用
		}
		/*
    * 初始化参数
    */
    hstool.init=function(config){
        for(var key in config){
            this.config[key] = config[key]; 
        } 
    }
	/*
    * loading 加载层
    * 
    */
    hstool.load = function(config){
        config = config || {};
        var that = this;
        that.init(config);  
        $("body").append('<div class="all_load"><div class="hstool-dialog-loading"></div></div>'); 
        $(".all_load").css({"z-index":that.config.zIndex+2});
        if(that.config.time>0){
            setTimeout(function(){
                that.closeLoad();
            }, that.config.time);
        }
    }
    /*
    * 关闭加载层 
    */
    hstool.closeLoad = function(){
        $(".all_load").remove();
    }
		return hstool;
})();