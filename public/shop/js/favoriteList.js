new Vue({
  el: '#main',
  data:{
    nav:[
        {title:'商品',isActive:true,type:'PRODUCT'},
        {title:'活动',isActive:false,type:'ACTIVITY'}
    ],
    favoriteList:[],
    imgUrl:imgUrl,
    status:0,
    isdel:1,//是否删除
    relative_id:''//关联ID
  },
  methods: {
	  chooseTab:function(nav){
	    for(var i = 0; i<this.nav.length;i++){
	        this.nav[i]['isActive'] = false;
	    }
	    nav.isActive = true;
	    this.type = nav.type;
	    //切换时获取活动、商品数据
	    hstool.load();
	    this.favoriteList = [];
	    this.$http.get('/shop/member/favoriteListApi/'+ wid + '?type=' + this.type).then(function(res){
	        hstool.closeLoad();
	        if(res.body.data.data.length){
	            for(var i = 0;i<res.body.data.data.length;i++){
	                this.favoriteList.push(res.body.data.data[i]);
	            }
	            
	        }
	    })
	  },
	  CancelCollect:function(relative_id,type,index){
	  	this.$http.post('/shop/member/cancelFavorite',{
					type: type,
		    	relativeId: relative_id,
		    	_token: $("meta[name='csrf-token']").attr("content"),
			}).then(function(res){
				if(res.body.status == 1){
					this.favoriteList.splice(index,1)
					$('.deltip').show()
					setTimeout(function(){
						$('.deltip').hide()
					},1500)
				}
			})
	 	},
		judgeGo:function(num,relative_id,share_product_id){
			switch(num.toString()){
				case '0':return host + 'shop/product/detail/' + wid + '/' + relative_id;break;
					case '1':return host + 'shop/seckill/detail/' + wid + '/' + relative_id;break;
					case '2':return host + 'shop/grouppurchase/detail/' + relative_id + '/' + wid;break;
					case '3':return host + 'shop/product/detail/' + wid + '/' + share_product_id + '?activityId=' + relative_id;break;
				default://code area 
			}
		},
		noGo:function(){
			return false
		}
  },
  beforeCreate: function () {
    
  },
  created: function () {
    //页面加载时获取商品数据
    hstool.load();
    this.$http.get('/shop/member/favoriteListApi/'+ wid + '?type=PRODUCT').then(function(res){
        hstool.closeLoad();
        if(res.body.data.data.length){
            for(var i = 0;i<res.body.data.data.length;i++){
                this.favoriteList.push(res.body.data.data[i]);
            }
        }
        
    })
    //下拉分页
    var stop=true;//触发开关，防止多次调用事件
    var that = this;
    var page = 1;
    $(window).scroll( function(event){
        if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
            if (stop == true) {
                stop = false;
                page = page + 1;//当前要加载的页码
                // 获取分类数据
                that.$http.get('/shop/member/favoriteListApi/'+ wid + '?type=' + that.type + '&page=' + page).then(function(res){
                    if(res.body.data.data.length){
                        for(var i = 0;i<res.body.data.data.length;i++){
                            that.favoriteList.push(res.body.data.data[i]);
                        }
                        stop = true;
                    }
                })
               
            }
        }
    });
  },
  mounted: function () {
  	
  }
})