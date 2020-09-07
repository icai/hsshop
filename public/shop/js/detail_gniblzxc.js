'use strict';//严格模式 
$(function(){  
	// 处理横竖图片适应问题
	$(".aricle-list-img .aricle-img-3 img").each(function(key,el){
		imgLoad(this,imgLoadCallBack3);
	});
	// 处理横竖图片适应问题
	$(".aricle-list-img .aricle-img-1 img").each(function(key,el){
		imgLoad(this,imgLoadCallBack1);
	});

	//点赞
	$(".aricle-zan").click(function(){
		var that = this;   
		if(typeof $(that).attr("disabled")=="undefined"){
			$(that).attr("disabled","disabled");
			var posts_id = $(that).attr("data-id");
			var isf= parseInt($(that).attr("data-isf")) || 0;
			var data = {
				posts_id:posts_id
			}; 
			var url = "";
			if(!isf){
				url="/shop/microforum/post/favorsed";
			} else{
				url="/shop/microforum/post/unfavorsed";
			}
			$.ajax({
	            url:url,// 跳转到 action
	            data:data,
	            type:'post',
	            cache:false,
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            dataType:'json',
	            success:function (res) {
	                if (res.status == 1){
	                    if(!isf){
	                    	$(that).attr("data-isf",1);
	                    	$(that).removeClass('aricle-zan-02').addClass('aricle-zan-01'); 
	                    	$(that).html(parseInt($(that).html())+1);
	                    }else{
	                    	$(that).attr("data-isf",0);  
	                    	$(that).removeClass('aricle-zan-01').addClass('aricle-zan-02');
	                    	$(that).html(parseInt($(that).html())-1); 
	                    }
	                } else{
	                	tool.tip(res.info);
	                }
					$(that).removeAttr("disabled");
	            },
	            error : function() {
	                console.log("异常！");
	            }
	        });
		}
		
	});

	var isSuccess = true;
	//发送回复
	$(".comment-outer-btn").click(function(){
		var url ="/shop/microforum/post/repliesed"; 
		var pid = $(this).attr("data-pid");
		var content =$(".comment-outer-txt").val(); 
		var data={
			pid:pid,
			rid:0,
			content:content
		}; 
		if(content!="" && isSuccess){ 
			isSuccess = false;
			$.ajax({
	            url:url,// 跳转到 action
	            data:data,
	            type:'post',
	            cache:false,
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            dataType:'json',
	            success:function(res) {
	                if(res.status == 1){  
	                	tool.tip("发布成功");  
	                	setTimeout(function(){ 
	                		location.reload(true);  
	                	},1000);
	                }else{
	                	isSuccess = true; 
						tool.tip(res.info); 
	                }
	            },
	            error : function() {
	            	isSuccess = true;
	                console.log("异常！");
	            }
	        });  
		} 
	}); 
	//回复
	$(".aricle-reply").click(function(){
		var that = this;
		t_layer.replyLayer({
			id:$(that).attr("data-id"),
			pid:$(that).attr("data-pid"),
			rid:$(that).attr("data-rid"),
			name:$(that).attr("data-name"),
		});
	});
	//删除
	$(".aricle-delete").click(function(){
		var that = this;
		t_layer.deleteLayer({ 
			id:$(that).attr("data-id"),
			pid:$(that).attr("data-pid"),
			rid:$(that).attr("data-rid"),
			callBack:function(data){
				$(that).parents(".aricle-list").remove(); 
			}
		});
	});


	//图片加载函数（一直到图片加载完成以后）
	function imgLoad(img,callback){
		var timer =setInterval(function(){
			if(img.complete){
				callback(img);
				clearInterval(timer);
			}
		},50);
	}
	//1张图片
	function imgLoadCallBack1(img){
		var h = $(img).height();
		var w = $(img).width();   
		if(w-h>0){
			$(img).addClass('w-img');
		}else{
			$(img).addClass('h-img');
		}
		$(img).css("visibility","visible"); 
	}
	//2-3张图片加载完成回调函数
	function imgLoadCallBack3(img){
		var h = $(img).height();
		var w = $(img).width();   
		if(w-h>0){
			$(img).addClass('h-img');
			w = $(img).width();
			var ml = - (w-$(img).parent().width())/2;
			$(img).css("margin-left",ml+"px");
			
		}else{
			$(img).addClass('w-img');
			h = $(img).height();
			var mt = - (h-$(img).parent().height())/2;
			$(img).css("margin-top",mt+"px"); 
		}
		$(img).css("visibility","visible"); 
	}

	// 弹框
	(function(){
		var t_config ={};
		var t_layer = {
			//回复层
			replyLayer:function(config){
				t_config = config;
				var html =` <div class="t-mask"></div> 
				<div class="t-layer1">
				    <button class="t-layer1-btn t-layer1-btn-yes">回复</button>
				    <button class="t-layer1-btn t-layer1-btn-cancel">取消</button>
				</div>`;
				$("body").append(html);
				$(".t-mask").show();
				$(".t-layer1").show();
			},
			//删除层
			deleteLayer:function(config){
				t_config = config;
				var html =` <div class="t-mask"></div> 
				<div class="t-layer1">
				    <button class="t-layer1-btn t-layer1-btn-delete">删除</button>
				    <button class="t-layer1-btn t-layer1-btn-cancel">取消</button>
				</div>`;
				$("body").append(html);
				$(".t-mask").show();
				$(".t-layer1").show();
			},
			remove:function(){
				$(".t-mask").remove();
				$(".t-layer1").remove();
			}, 
		}
		//取消
		$("body").on("click",".t-layer1-btn-cancel",function(){
			t_layer.remove();
		});
		//回复
		$("body").on("click",".t-layer1-btn-yes",function(){
			location.href="/shop/microforum/post/replies?pid="+t_config.pid+"&rid="+t_config.rid+"&name="+t_config.name;
			t_layer.remove();
		});
		//删除 
		$("body").on("click",".t-layer1-btn-delete",function(){
			$.ajax({
	            url:'/shop/microforum/replies/deleted',// 跳转到 action
	            data:{id:t_config.id},
	            type:'post',
	            cache:false,
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            dataType:'json',
	            success:function (response) {
	                if (response.status == 1){
	                    tool.tip("删除成功"); 
	                    if(typeof t_config.callBack !="undefined"){
							var data ={};
							t_config.callBack(data);
						}
	                }else{
	                    tool.tip("删除失败"); 
	                }
	            },
	            error : function() {
	                // view("异常！");
	                alert("异常！");
	            }
	        });
	        t_layer.remove();  

			
		});
		window.t_layer=t_layer;
	})();
	
	//图片预览效果
	(function(){
		//photoswipe 配置代码
	    var initPhotoSwipeFromDOM = function(gallerySelector) {

		    var parseThumbnailElements = function(el) {
		        var thumbElements = el.childNodes,
		        numNodes = thumbElements.length,
		        items = [],
		        figureEl,
		        linkEl,
		        size,
		        item;

		        for (var i = 0; i < numNodes; i++) {

		            figureEl = thumbElements[i]; // <figure> element
		            // 仅包括元素节点
		            if (figureEl.nodeType !== 1) {
		                continue;
		            }
		            linkEl = figureEl.children[0]; // <a> element
		            size = linkEl.getAttribute('data-size').split('x');

		            // 创建幻灯片对象
		            item = {
		                src: linkEl.getAttribute('href'),
		                w: parseInt(size[0], 10),
		                h: parseInt(size[1], 10)
		            };

		            if (figureEl.children.length > 1) {
		                // <figcaption> content
		                item.title = figureEl.children[1].innerHTML;
		            }

		            if (linkEl.children.length > 0) {
		                // <img> 缩略图节点, 检索缩略图网址
		                item.msrc = linkEl.children[0].getAttribute('src');
		            }

		            item.el = figureEl; // 保存链接元素 for getThumbBoundsFn
		            items.push(item);
		        }

		        return items;
		    };

		    // 查找最近的父节点
		    var closest = function closest(el, fn) {
		        return el && (fn(el) ? el: closest(el.parentNode, fn));
		    };

		    // 当用户点击缩略图触发
		    var onThumbnailsClick = function(e) {
		        e = e || window.event;
		        e.preventDefault ? e.preventDefault() : e.returnValue = false;

		        var eTarget = e.target || e.srcElement;

		        // find root element of slide
		        var clickedListItem = closest(eTarget,
		        function(el) {
		            return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
		        });

		        if (!clickedListItem) {
		            return;
		        }

		        var clickedGallery = clickedListItem.parentNode,
		        childNodes = clickedListItem.parentNode.childNodes,
		        numChildNodes = childNodes.length,
		        nodeIndex = 0,
		        index;

		        for (var i = 0; i < numChildNodes; i++) {
		            if (childNodes[i].nodeType !== 1) {
		                continue;
		            }

		            if (childNodes[i] === clickedListItem) {
		                index = nodeIndex;
		                break;
		            }
		            nodeIndex++;
		        }

		        if (index >= 0) {
		            // open PhotoSwipe if valid index found
		            openPhotoSwipe(index, clickedGallery);
		        }
		        return false;
		    };

		     

		    // parse picture index and gallery index from URL (#&pid=1&gid=2)
		    var photoswipeParseHash = function() {
		        var hash = window.location.hash.substring(1),
		        params = {};

		        if (hash.length < 5) {
		            return params;
		        }

		        var vars = hash.split('&');
		        for (var i = 0; i < vars.length; i++) {
		            if (!vars[i]) {
		                continue;
		            }
		            var pair = vars[i].split('=');
		            if (pair.length < 2) {
		                continue;
		            }
		            params[pair[0]] = pair[1];
		        }

		        if (params.gid) {
		            params.gid = parseInt(params.gid, 10);
		        }

		        return params;
		    };

		    var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
		        var pswpElement = document.querySelectorAll('.pswp')[0],
		        gallery,
		        options,
		        items;

		        items = parseThumbnailElements(galleryElement);

		        // 这里可以定义参数
		        options = {
		            barsSize: {
		                top: 100,
		                bottom: 100
		            },
		            showHideOpacity:true,
		            // hideAnimationDuration:0, 
		            // showAnimationDuration:0,
		            tapToClose:  true,
		            //点击图片关闭 
		            fullscreenEl: false,
		            // 是否支持全屏按钮
		            shareButtons: [{
		                id: 'wechat',
		                label: '分享微信',
		                url: '#'
		            },
		            {
		                id: 'weibo',
		                label: '新浪微博',
		                url: '#'
		            },
		            {
		                id: 'download',
		                label: '保存图片',
		                url: '',
		                download: true
		            }],
		            // 分享按钮
		            // define gallery index (for URL)
		            galleryUID: galleryElement.getAttribute('data-pswp-uid'),

		            getThumbBoundsFn: function(index) {
		                // See Options -> getThumbBoundsFn section of documentation for more info
		                var thumbnail = items[index].el.getElementsByTagName('img')[0],
		                // find thumbnail
		                pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
		                rect = thumbnail.getBoundingClientRect();

		                return {
		                    x: rect.left,
		                    y: rect.top + pageYScroll,
		                    w: rect.width
		                };
		            }

		        };
		     

		        // PhotoSwipe opened from URL
		        if (fromURL) {
		            if (options.galleryPIDs) {
		                // parse real index when custom PIDs are used 
		                for (var j = 0; j < items.length; j++) {
		                    if (items[j].pid == index) {
		                        options.index = j;
		                        break;
		                    }
		                }
		            } else {
		                // in URL indexes start from 1
		                options.index = parseInt(index, 10) - 1;
		            }
		        } else {
		            options.index = parseInt(index, 10);
		        }

		        // exit if index not found
		        if (isNaN(options.index)) {
		            return;
		        }

		        if (disableAnimation) {
		            options.showAnimationDuration = 0;
		        }

		        // Pass data to PhotoSwipe and initialize it
		        gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
		        gallery.init();
		    };

		    // loop through all gallery elements and bind events
		    var galleryElements = document.querySelectorAll(gallerySelector);

		    for (var i = 0,
		    l = galleryElements.length; i < l; i++) {
		        galleryElements[i].setAttribute('data-pswp-uid', i + 1);
		        galleryElements[i].onclick = onThumbnailsClick;
		    }

		    // Parse URL and open gallery if it contains #&pid=3&gid=1
		    var hashData = photoswipeParseHash();
		    if (hashData.pid && hashData.gid) {
		        openPhotoSwipe(hashData.pid, galleryElements[hashData.gid - 1], true, true);
		    }
		};
		// execute above function
    	initPhotoSwipeFromDOM('.aricle-list-img');
	})();

});

