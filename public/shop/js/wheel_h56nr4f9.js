window.onload = function(){
	var type = '1';
	var n = 0;
	var contenta = '';
	var lengtha = '';
	var flag = true;
	var product_id = '';
	var one_click = 1;//禁止重复提交,1可点击，0不可点击
	$("#btnstart").click(function(){
		if(one_click == 1){
			one_click == 0;
			// if(isBind){
			// 	tool.bingMobile(function(){
			// 		isBind = 0;
			// 		startFun();
			// 	})
			// 	return;
			// }
			startFun();
		}
	}) 

	var rotateFunc = function(angle, text ,content ,img, extra) { //awards:奖项，angle:奖项对应的角度
		$('#lotteryBtn').stopRotate();
		$("#lotteryBtn").rotate({
			angle: 0,
			duration: 5000,
			animateTo: angle + 1440,
			callback: function() { 
				if(type==3 || type == 4){
					let txt = img != '' ? '<div class="grade"><p>恭喜您获得'+content+text+extra+'</p><p class="price_image"><img src="'+ imgUrl +img+'"/></p></div>' : '<div class="grade"><p>恭喜您获得'+content+text+'</p><p class="price_image"><img src="'+ _host +'shop/images/lottery-bg.png'+'"/></p></div>';
					layer.open({
						title: '信息提示',
						content: txt,
						btn: ['查看奖品','继续抽奖'],
						yes:function(){ 
							flag = true;
							one_click == 1;
							window.location.href="/shop/activity/myGift/"+wid;
						},
						cancel:function(){
							flag = true;
							one_click == 1;
						}
					}); 					
				}else{
					layer.open({
						title: '信息提示',
						content: '<div class="grade"><p>恭喜您获得'+content+text+extra+'<p></div>',
						btn: ['查看奖品','继续抽奖'],
						yes:function(){
							flag = true;
							one_click == 1;
							window.location.href="/shop/activity/myGift/"+wid;
						},
						cancel:function(){
							flag = true;
							one_click == 1;
						}
					}); 
				};
				var height = window.screen.availHeight;
				var width = window.screen.availWidth;
				var height1 = $('.layui-layer').outerHeight();
				var width1 = $('.layui-layer').outerWidth();
				$('.layui-layer').css('left',width/2 - width1/2);
				$('.layui-layer').css('top',height/2 - height1/2);   
			}
		});
	};
	
	var rotateFunc_0 = function(angle, text) { //未中奖
		$('#lotteryBtn').stopRotate();
		$("#lotteryBtn").rotate({
			angle: 0,
			duration: 5000,
			animateTo: angle + 1440,
			callback: function() {
				flag = true;
				one_click == 1;
				layer.open({
					title: '信息提示',
					content: text,
					btn: ['继续抽奖']
				});    
			}
		});
	};
	var startFun = function(){
		if( !flag ) return;
		flag = false;
		one_click == 1;
		if(data.reduce_integra==0){
			var grade = [0, 1, 2, 3, 4, 5]; //返回的数组
			//后台奖项接口
			$.ajax({
				url:"/shop/activity/wheelPlay/"+wid+"/"+id,
				type:"POST",
				data:'',		
				dataType:'json',
				headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
				success:function(res){
					if(res.status==0){  //无法抽奖
						layer.open({
							title: '提示信息',
							content:res.info,
							btn: ['知道了']
						});
						flag = true;
						one_click == 1;
						return false;
					};
					n ++;
					$(".activity_info num").text(num_times+n); //已抽奖次数
					if(res.data.img){
						lengtha = res.data.img.length;	
						console.log(lengtha)
					}											
					grade = grade[res.data.grade];//几等奖
					if(res.data.length <= 0){											
						grade = 0;
					}
					type = res.data.type;  //奖品类型
					let extra = '';
					if (+res.data.send_integra > 0) {
						extra = '，并额外赠送' + res.data.send_integra + '积分';
					}
					if(type == 1){
						text = '等奖';
						var content = res.data.grade;
					}
					if(type == 2){
						text = '等奖';
						contenta = res.data.grade;
						var content = res.data.grade;
					}
					if(type == 3){
						text = '等奖';
						var content = res.data.grade;
					}
					if( type == 4){
						text = '';
						product_id = res.data.pid;
						var content = res.data.content;
					}
					var img = res.data.img;
					if(grade == 1) {
						rotateFunc(0, text, content, img, extra)
					}
					if(grade == 2) {
						rotateFunc(60, text, content, img, extra);
					}
					if(grade == 3) {
						var angle = [120, 240];
						angle = angle[Math.floor(Math.random() * angle.length)]
						rotateFunc(angle, text, content, img, extra)
					}
					if(grade == 4) {
						rotateFunc(180, text, content, img, extra)
					}
					if(grade == 5) {
						rotateFunc(300, text, content, img, extra)
					}
					if(grade == 0) {
						var angle = [30, 90, 150, 210, 270, 330];
						var msg = res.info;
						angle = angle[res.status];
						rotateFunc_0(angle, msg);
					}
				},
				error:function(res){
					flag = true;
					one_click == 1;
					alert("数据访问错误");
				}
			});	
		}else{
			layer.open({				
				content: "每次抽奖将消耗"+data.reduce_integra+"积分",
				btn: ['赌一把','舍不得'],
				yes:function(index){
					layer.close(index);
					var grade = [0, 1, 2, 3, 4, 5]; //返回的数组
					//后台奖项接口
					$.ajax({
						url:"/shop/activity/wheelPlay/"+wid+"/"+id,
						type:"POST",
						data:'',		
						dataType:'json',
						headers: {
				            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				        },
						success:function(res){					
							if(res.status==0){  //无法抽奖
								layer.open({
									title: '提示信息',
									content:res.info,
									btn: ['知道了']
								});
								flag = true;
								one_click == 1;
								return false;
							};
							let extra = '';
							if (+res.data.send_integra > 0) {
								extra = '，并额外赠送' + res.data.send_integra + '积分';
							}
							$(".activity_info num").text(num_times+n); //已抽奖次数
							n ++;
							console.log(num_times+n)
							$(".activity_info num").text(num_times+n); //已抽奖次数
							if(res.data.img){
								lengtha = res.data.img.length;	
								console.log(lengtha)
							}	
							grade = grade[res.data.grade];//几等奖
							if(res.data.length <= 0){											
								grade = 0;
							}
							type = res.data.type;  //奖品类型
							if(type == 1){
								text = '等奖';
								var content = res.data.grade;
							}
							if(type == 2){
								text = '等奖';
								contenta = res.data.grade;
								var content = res.data.grade;
							}
							if(type == 3){
								text = '等奖';
								var content = res.data.grade;
							}
							if(type == 4){
								text = '';
								product_id = res.data.pid;
								var content = res.data.content;
							}
							var img = res.data.img;
							console.log(res.data)
							if(grade == 1) {
								rotateFunc(0, text, content, img, extra)
							}
							if(grade == 2) {
								rotateFunc(60, text, content, img, extra);
							}
							if(grade == 3) {
								var angle = [120, 240];
								angle = angle[Math.floor(Math.random() * angle.length)]
								rotateFunc(angle, text, content, img, extra)
							}
							if(grade == 4) {
								rotateFunc(180, text, content, img, extra)
							}
							if(grade == 5) {
								rotateFunc(300, text, content, img, extra)
							}
							if(grade == 0) {
								var angle = [30, 90, 150, 210, 270, 330];
								var msg = res.info;
								angle = angle[res.status];
								rotateFunc_0(angle, msg);
							}
						},
						error:function(res){
							flag = true;
							one_click == 1;
							alert("数据访问错误");
						}
					});	
				},
				cancel:function(){
				    flag = true;
				    one_click == 1;
				}
			})			
		}
	}
};