'use strict';//严格模式 
$(function(){
	// 帖子和消息
	$(".post-msg-list li a").click(function(){ 
		$(this).addClass('active').parent().siblings('li').find("a").removeClass('active');
		var index = $(this).parent().attr("data-index");
		if(index=='2'){ //帖子
			$(".post-nav-list").hide();
			$(".msg-wrapper").show();
			$(".aricle-wrapper").hide();
		}else{ //消息
			$(".msg-wrapper").hide();
			$(".aricle-wrapper").show();
			$(".post-nav-list").show();
		}
	});
});
Vue.component('nav-list',{
	props:['list','activeIndex','discussions_id'], 
	template:`
		<nav>
			<ul class="post-nav-list" >
				<li :class="[activeIndex==index ? 'active' : ''] "  v-for="(vo,index) in list"><a href="javascript:;" @click="clickNavList(index,vo.id)" v-html="vo.title"></a></li> 
			</ul>
		</nav>
	`,
	created:function(){  
	},
	methods:{
		/*帖子类型点击函数*/
		clickNavList:function(index,id){  
			this.activeIndex = index;   
			this.$emit('setid',id,index);
		}
	}
});

/*设置请求头部*/ 
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = true;
//post要求的请求token
Vue.http.options.headers = {'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")}; 

var app = new Vue({
	el:"#app",
	data:{
		categoriesDatas:categoriesDatas, //分类数据
		discussions_id:categoriesDatas[0].id, //当前分类id
		list:{}, //帖子数据
		page:1,//分页
		user_id:user_id,
		id_type: 0, 
		host:_host, // 网址域名
		imgUrl:imgUrl, //动态图片前缀
		activeIndex:0, //导航选中的索引
		deletePost:false, //是否删除帖子
		delPostId:0, //删除的帖子id 0表示无删除的帖子
		deleteIndex:-1, //删除索引
		msgList:{}, //消息数据
		msgPage:1, //消息当前页码
		isRequest:1, //加载帖子数据状态 0.数据请求中 1.数据请求中 2.无数据了
		postText:"正在加载数据请稍后...", //加载帖子数据时显示的提示
		isMsgRequest:1,  //加载消息数据状态 0.数据请求中 1.数据请求中 2.无数据了
		msgText:"正在加载数据请稍后...",//加载消息数据时显示的提示 
		isSuccess:true, //ajax请求是否成功
	},
	beforeCreate:function(){ 
		//console.log("loading...");
	},
	created:function(){ 
		this.getList();
		this.getMsgList();
	},  
	watch:{ //监听数据发生变化触发函数
		discussions_id:function(val,oldval){ 
		}
	},
	methods:{ 
		/*获取数据*/
		getList:function(isPush){ 
			var that = this;
			if(typeof isPush ==="undefined")
				that.list = {};
			var url = "/shop/microforum/post/list";
			var data = {
				discussions_id:that.discussions_id,
				user_id:that.user_id,
				id_type:that.id_type,
				page:that.page 
			}; 
			that.isRequest = 0; 
			that.postText = "正在加载数据请稍后...";
			this.$http.post(url,data).then(
			function(res){
				if(res.data.status==1){
					//微信头像和本地头像需要做判断 
					var list = res.data.data;
					for(var i=0;i<list.data.length;i++){ 
						if(list.data[i].headimgurl.indexOf('http')==-1){
							list.data[i].headimgurl = that.imgUrl+ list.data[i].headimgurl;
						}
						if(typeof isPush !=="undefined")
							that.list.data.push(list.data[i]);
					} 
					if(typeof isPush ==="undefined")
						that.list = list;
					if(list.data.length>0){
						that.isRequest = 1; //数据请求成功
						that.postText="";
					}else{
						that.isRequest = 2;  
						if(typeof isPush ==="undefined")
							that.postText = "还没有帖子哦";
						else
							that.postText = "没有更多数据了";
					}
				} 
			},function(err){
				that.isRequest = 2; //请求失败了等于没有更多数据了
				console.log(err);
			});
		},
		getMsgList:function(isPush){
			var that = this;
			var url = "/shop/microforum/notification/list";
			var data = { 
				page:that.msgPage 
			}; 
			that.isMsgRequest = 0; 
			that.msgText ="正在加载数据请稍后...";
			this.$http.post(url,data).then(
			function(res){ 
				if(res.data.status==1){
					if(typeof isPush !=="undefined"){
						var list = res.data.data.data;
						for(var i=0;i<list.length;i++){
							that.msgList.data.push(list[i]);
						}
					}else{
						that.msgList = res.data.data; 
					} 
					if(res.data.data.data.length>0){
						that.isMsgRequest = 1; //数据请求成功
						that.msgText ="";
					}else{
						that.isMsgRequest = 2;  
						if(typeof isPush ==="undefined")
							that.msgText = "还没有消息哦";
						else
							that.msgText = "没有更多数据了";
					}
				} 
			},function(err){
				that.isMsgRequest = 2;  //请求失败了等于没有更多数据了
				console.log(err);
			});
		},
		/*回复*/
		replyAricle:function(pid,rid,name){
			var that = this;
			t_layer.replyLayer({ 
				pid:pid,
				rid:rid,
				name:name
			});
		},
		/*设置帖子类型*/
		setTypeId:function(id,index){   
			this.discussions_id = id;
			this.activeIndex = index;
			this.page = 1;
			this.postText ="正在加载数据请稍后...";
			this.getList();
		},
		/*1张图片加载完成回调函数*/
		imgLoadCallBack1:function(img){
			var h = $(img).height();
			var w = $(img).width();   
			if(w-h>0){
				$(img).addClass('w-img');
			}else{
				$(img).addClass('h-img');
			}
			$(img).css("visibility","visible"); 
		},
		/*2张以上图片加载完成回调函数*/
		imgLoadCallBack3:function(img){
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
		},
		/*图片加载函数*/
		imgLoad:function(img,callback){
			var timer =setInterval(function(){
				if(img.complete){
					callback(img);
					clearInterval(timer);
				}
			},50);
		},
		/*判断点赞还是取消点赞*/
		favorsedOrUnfavorsed:function(posts_id,index,isFavors){
			if(this.isSuccess){
				if(isFavors==0){
					this.favorsed(posts_id,index);
				}else{
					this.unfavorsed(posts_id,index);
				}
			} 
		},
		/*删除帖子弹框*/
		aricleClose:function(id,index){
			this.delPostId = id;
			this.deletePost = true; 
			this.deleteIndex = index;
		},
		/*确定删除*/
		yesDelete:function(){
			var that = this; 
			var url = "/shop/microforum/post/deleted";
			var data = {
				id:that.delPostId
			};  
			this.$http.post(url,data).then(
			function(res){
				that.delPostId = 0;
				that.deletePost = false; 
				that.list.data.splice(that.deleteIndex,1);
				tool.tip(res.data.info);   
				$("#post_count").html(parseInt($("#post_count").html())-1);
			},function(err){
				console.log(err);
			});
		},
		/*取消删除*/
		cancelDelete:function(){
			this.delPostId = 0;
			this.deletePost = false; 
		},
		/*帖子点赞*/
		favorsed:function(posts_id,index){ 
			var that = this;
			var url = "/shop/microforum/post/favorsed";
			var data = {
				posts_id:posts_id
			}; 
			that.isSuccess = false;
			this.$http.post(url,data).then(
			function(res){
				if(res.data.status==1){
					that.list.data[index].isFavors = 1;
					that.list.data[index].favorsCount+=1;
				}else{
					tool.tip(res.data.info);
				}
				that.isSuccess = true;
			},function(err){
				console.log(err);
			});
		},
		/*取消点赞*/
		unfavorsed:function(posts_id,index){
			var that = this;
			var url = "/shop/microforum/post/unfavorsed";
			var data = {
				posts_id:posts_id
			}; 
			that.isSuccess = false;
			this.$http.post(url,data).then(
			function(res){
				if(res.data.status==1){
					that.list.data[index].isFavors = 0;
					that.list.data[index].favorsCount -=1;
				} 
				that.isSuccess = true;
			},function(err){
				console.log(err);
			});
		},
		/*图片预览初始化函数*/
		initPhotoSwipeFromDOM:function(gallerySelector) { 
		    // 解析来自DOM元素幻灯片数据（URL，标题，大小...）
		    // (children of gallerySelector)
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
		        // find index of clicked item by looping through all child nodes
		        // alternatively, you may define index via data- attribute
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
		    for (var i = 0,l = galleryElements.length; i < l; i++) {
		        galleryElements[i].setAttribute('data-pswp-uid', i + 1);
		        galleryElements[i].onclick = onThumbnailsClick;
		    }
		    // Parse URL and open gallery if it contains #&pid=3&gid=1
		    var hashData = photoswipeParseHash();
		    if (hashData.pid && hashData.gid) {
		        openPhotoSwipe(hashData.pid, galleryElements[hashData.gid - 1], true, true);
		    }
		}
	}, 
	updated:function(){ 
		if(typeof this.list.data !="undefined"){
			//数据渲染完成执行
			var that = this;
			//2张图片以上的循环
			$(".aricle-list-img .aricle-img-3 img").each(function(key,el){  
				that.imgLoad(this,that.imgLoadCallBack3);
			});
			//1张图片的循环
			$(".aricle-list-img .aricle-img-1 img").each(function(key,el){
				that.imgLoad(this,that.imgLoadCallBack1);
			});
			//调用图片预览初始化函数
			that.initPhotoSwipeFromDOM('.aricle-list-img'); 
		} 
	}
});  

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
		console.log(t_config.id);
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
						$("#msg_count").html( parseInt($("#msg_count").html())-1 );
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

$(window).scroll(function(){
	var scrollTop = $(this).scrollTop();    //滚动条距离顶部的高度
    var scrollHeight = $(document).height();   //当前页面的总高度
    var clientHeight = $(this).height();    //当前可视的页面高度 
    var postIndex = $(".post-msg-list li a.active").parent().attr("data-index");
    if(postIndex=="1"){
    	if(app.isRequest==1){ //上一次请求成功
	    	if(scrollTop + clientHeight >= scrollHeight){ 
		    	app.page +=1;
				app.getList(true);  
		    } 
	    } 
    }else{
    	if(app.isMsgRequest==1){ //上一次请求成功
	    	if(scrollTop + clientHeight >= scrollHeight){ 
		    	app.msgPage +=1;
				app.getMsgList(true);  
		    } 
	    } 
    }
}); 