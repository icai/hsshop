/*设置请求头部*/
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = true;
//post要求的请求token
Vue.http.options.headers = { 'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content") };
var app = new Vue({
  el: '#main',
  data:{
  	imgUrl:imgUrl,
    addressShow:false, //确认地址弹窗
    detail:null, //接口数据
    timer:null, //计时时间
    now_time:null, //当前时间
    group_people:null,
    _token:$('meta[name="csrf-token"]').attr('content'),
    isShowGroupPeople:false,
    yaoshow:false    
  },
  methods: {
    // 设置是否显示拼团人列表
	setShowGroupPeoplea: function (index) {
	    var that = this;
	    this.isShowGroupPeople = true;
	    this.group_people = this.detail[index].groupData.member
	},
	setShowGroupPeople: function () {
	    this.isShowGroupPeople = false
	},
//	点击更换
	clickset:function(){
		var that = this;
		that.detail = [];
		that.yaoshow = true;
		this.$http.get('/shop/web/groups/groupsList').then(function(res){
		clearInterval(loadset);
      	this.detail = res.body.data;
		// 倒计时
        var timeb = new Date(res.body.data[0].now_time);
	 	var timec = timeb.getTime();
      	that.timer = timec;
      	cliset = setInterval(function (){
            var list = res.body.data;
            that.now_time += 1000;
            var now_time = that.detail.now_time;

            that.timer += 1000;
            var timer = that.timer;
            for (var i = 0; i < list.length; i++) {                
              var now = new Date(timer);                
              var end = new Date(res.body.data[i].groupData.end_time);
              now = now.getTime();    
              end = end.getTime();             
              list[i].days = "00";
              list[i].hours = "00";
              list[i].minutes = "00";
              list[i].seconds = "00";
              var a = end - now;
              getrtime(a);    
              //显示
              function evenNum(num) {
                num = num < 10 ? "0" + num : num;
                return num;
              }
              //倒计时显示
              function getrtime(timeup) {
                var t = timeup;
                if (t >= 0) {
                  var d = evenNum(Math.floor(t / 1000 / 60 / 60 / 24));
                  var h = evenNum(Math.floor(t / 1000 / 60 / 60 % 24));
                  var m = evenNum(Math.floor(t / 1000 / 60 % 60));
                  var s = evenNum(Math.floor(t / 1000 % 60));
                  list[i].days = d;                  
                  list[i].hours = h;
                  list[i].minutes = m;
                  list[i].seconds = s;
                }
              }
            }
            that.detail = [];
            that.detail = list;
          	that.now_time = that.now_time;
          	that.yaoshow = false;
          },1000);          
       })
    }
  },
  beforeCreate: function () {
      
  },
  created: function () {
    var that = this;
    this.$http.get('/shop/web/groups/groupsList').then(function(res){
      	this.detail = res.body.data;
		// 倒计时
        var timeb = new Date(res.body.data[0].now_time);
	 			var timec = timeb.getTime();
      	that.timer = timec;
      	loadset = setInterval(function (){
            var list = res.body.data;
            that.now_time += 1000;
            var now_time = that.detail.now_time;

            that.timer += 1000;
            var timer = that.timer;
            for (var i = 0; i < list.length; i++) {                
              var now = new Date(timer);                
              var end = new Date(res.body.data[i].groupData.end_time);
              now = now.getTime();    
              end = end.getTime();             
              list[i].days = "00";
              list[i].hours = "00";
              list[i].minutes = "00";
              list[i].seconds = "00";
              var a = end - now;
              getrtime(a);    
              //显示
              function evenNum(num) {
                num = num < 10 ? "0" + num : num;
                return num;
              }
              //倒计时显示
              function getrtime(timeup) {
                var t = timeup;
                if (t >= 0) {
                  var d = evenNum(Math.floor(t / 1000 / 60 / 60 / 24));
                  var h = evenNum(Math.floor(t / 1000 / 60 / 60 % 24));
                  var m = evenNum(Math.floor(t / 1000 / 60 % 60));
                  var s = evenNum(Math.floor(t / 1000 % 60));
                  list[i].days = d;                  
                  list[i].hours = h;
                  list[i].minutes = m;
                  list[i].seconds = s;
                }
              }
            }
            that.detail = [];
            that.detail = list;
          	that.now_time = that.now_time;
          },1000);          
       });
		}
  	
})