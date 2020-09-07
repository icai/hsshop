
var vm = new Vue({
  el: ".container",
  delimiters: ['[[', ']]'],//修改边界符
  data: {
    search: '',
    storeList: [],
    downLength: 0,
  },
  created: function () {
    this.$http.get("/shop/store/getStoreList").then(function (res) {
      var data = res.body;

      if (data.status != 1) {
        return false;
      }
      var list = data.data.data
      for (var i = 0; i < list.length; i++) {
        var index = list[i].phone.indexOf("-")
        var arr = list[i].start_time.split(",");
        var arrEnd = list[i].end_time.split(",");
        if (!arr[arr.length - 1]) {
          arr[arr.length - 1] = "00:00"
        }
        if (!arrEnd[arrEnd.length - 1]) {
          arrEnd[arrEnd.length - 1] = "23:59"
        }
        list[i]["open_time"] = arr[arr.length - 1]
        arr.splice(arr.length - 1, 1)
        if (index == 0) {
          list[i].phone = list[i].phone.replace('-', '')
        }
        var markers = [{
          id: 1,
          latitude: list[i].latitude,
          longitude: list[i].longitude,
          name: list[i].title,
        }]
        list[i]["markers"] = markers
        list[i]["week"] = arr.join(",")
        list[i]["close_time"] = arrEnd[arrEnd.length - 1]
      }
      this.storeList = data.data.data;
      this.downLength = data.data.data.length;
    });
    downUpload(this.search)
  },
  methods: {
    // searchBut: function(){
    // 	this.$http.get("/shop/store/getStoreList?word="+this.search).then(function(res){
    // 		var data = res.body;
    // 		if(data.status!=1){
    // 			return false;
    // 		}
    // 		this.storeList = data.data.data;
    // 		this.downLength = data.data.data.length;
    // 	});
    // 	page=1;
    // },
    openModal: function (tel) {
      var content = "确定拨打电话：" + tel + "吗？";
      var sureTitle = "确定";
      var cancleTitle = "取消";
      tool.notice(1, "", content, sureTitle, sureBtn, cancleTitle, cancleBtn)
      function sureBtn() {
        if (reqFrom == 'aliapp') {
          my.postMessage({ phone_number: tel });
        } else {
          window.location.href = "tel:" + tel;
        }

      }
      function cancleBtn() {
        $("#mask").remove();
      }
    },
    // add by zhaobin 2018-8-6
    /**
     * @author: 搜索
     * @description: 
     * @param {type} 
     * @return: 
     * @update: 倪凯嘉（nikaijia@dingtalk.com）2019-09-03 16:21:37 增加数据处理
     */    
    searchInput: function () {
      this.$http.get("/shop/store/getStoreList?word=" + this.search).then(function (res) {
        var data = res.body;
        if (data.status != 1) {
          return false;
        }
        // 搜索请求的数据处理 add by 倪凯嘉 2019-09-03 start
        var list = data.data.data
        for (var i = 0; i < list.length; i++) {
          var index = list[i].phone.indexOf("-")
          var arr = list[i].start_time.split(",");
          var arrEnd = list[i].end_time.split(",");
          if (!arr[arr.length - 1]) {
            arr[arr.length - 1] = "00:00"
          }
          if (!arrEnd[arrEnd.length - 1]) {
            arrEnd[arrEnd.length - 1] = "23:59"
          }
          list[i]["open_time"] = arr[arr.length - 1]
          arr.splice(arr.length - 1, 1)
          if (index == 0) {
            list[i].phone = list[i].phone.replace('-', '')
          }
          var markers = [{
            id: 1,
            latitude: list[i].latitude,
            longitude: list[i].longitude,
            name: list[i].title,
          }]
          list[i]["markers"] = markers
          list[i]["week"] = arr.join(",")
          list[i]["close_time"] = arrEnd[arrEnd.length - 1]
        }
        // 搜索请求的数据处理 add by 倪凯嘉 2019-09-03 end
        this.storeList = data.data.data;
        this.downLength = data.data.data.length;
      });
      page = 1;
      downUpload(this.search)
    }
    // end
  },
});
var page = 1;
/**
 * @author: 
 * @description: 
 * @param {type} 
 * @return: 
 * @update: 倪凯嘉（nikaijia@dingtalk.com） 2019-09-03 16:22:19 增加分页请求数据处理
 */
function downUpload(search) {
  window.onscroll = function () {
    var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
    var sH = document.documentElement.clientHeight;
    if (scrollTop + sH >= document.body.scrollHeight && vm.downLength != 0) {//判断滚动条是否到底部
      page++;
      if (search) {
        var url = "/shop/store/getStoreList?word=" + search + "&page=" + page;
      } else {
        var url = "/shop/store/getStoreList?page=" + page;
      }
      Vue.http.get(url).then(function (res) {
        var data = res.body;
        if (data.status != 1) {
          return false;
        } 
        // 分页请求的数据处理 add by 倪凯嘉 2019-09-03 start
        var list = data.data.data
        for (var i = 0; i < list.length; i++) {
          var index = list[i].phone.indexOf("-")
          var arr = list[i].start_time.split(",");
          var arrEnd = list[i].end_time.split(",");
          if (!arr[arr.length - 1]) {
            arr[arr.length - 1] = "00:00"
          }
          if (!arrEnd[arrEnd.length - 1]) {
            arrEnd[arrEnd.length - 1] = "23:59"
          }
          list[i]["open_time"] = arr[arr.length - 1]
          arr.splice(arr.length - 1, 1)
          if (index == 0) {
            list[i].phone = list[i].phone.replace('-', '')
          }
          var markers = [{
            id: 1,
            latitude: list[i].latitude,
            longitude: list[i].longitude,
            name: list[i].title,
          }]
          list[i]["markers"] = markers
          list[i]["week"] = arr.join(",")
          list[i]["close_time"] = arrEnd[arrEnd.length - 1]
        }
        // 分页请求的数据处理 add by 倪凯嘉 2019-09-03 end
        vm.downLength = data.data.data.length;
        vm.storeList = vm.storeList.concat(data.data.data);
      });
    }
  }
}