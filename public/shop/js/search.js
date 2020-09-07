$(function () {
    var wid = $("#wid").val();
   
    var title = getRequest("title");
    if(title.hasOwnProperty("title")){
        title = title.title
    }else{
        title = "";
    }
    title = title.replace(/\+/g," ");
    $("input[name='title']").val(title);

    getData(wid, title);


    $("input[name='title']").keyup(function (e) {

        if (e.key == "Enter" || e.keycode == "13") {
            var title = $("input[name='title']").val() || "";
            getData(wid, title);
        }
    })

    
    //下拉加载
    // 下拉加载更多
    var page = 2;
    var loading = false;  //状态标记
    var hasData = true;
    window.onscroll = function () {
        var title = $("input[name='title']").val() || "";
        if (scrollTop() + windowHeight() >= (documentHeight() - 50)) {
            if (loading) return;
            loading = true;
            if (!hasData) {
                return;
            }

            $.get("/shop/product/list/" + wid, { page: page, title: title, distribute_grade_id:distribute_grade_id }, function (res) {

                if (res.data.data.length == 0) {
                    hasData = false;
                    return;
                }
                var data = res.data.data;
                forData(data);
                $(".js-goods-list").append(forData(data));
                page++;
                loading = false;
            })
        }
    }


    // 获取url参数
   
   function getRequest() {   
       var url = window.location.search; //获取url中"?"符后的字串   
       var theRequest = new Object();   
       if (url.indexOf("?") != -1) {   
          var str = url.substr(1);   
          strs = str.split("&");   
          for(var i = 0; i < strs.length; i ++) {   
             theRequest[strs[i].split("=")[0]]=decodeURI(strs[i].split("=")[1]); 
          }   
       }   
       return theRequest;   
    }
    // 获取数据
    function getData(wid, title) {
        $.get("/shop/product/list/" + wid, { title: title, distribute_grade_id:distribute_grade_id }, function (res) {
            var data = res.data.data;
            var html = forData(data);
            $(".js-goods-list").empty();
            $(".js-goods-list").append(html);
        })
    }
    // 循环数据
    function forData(data) {
        if (data.length == 0) {
            var html = '<li class="text-center empty-list">';
            html += '<p class="desc">没有找到相关的商品～</p>';
            html += '<a href="' + host + "/shop/product/index/" + wid + '"class="tag tag-orange tag-home">去逛逛</a></li>';

            return html;
        }

        var html = "";

        for (var i = 0; i < data.length; i++) {
            console.log(data[i])
            html += '<li class="js-goods-card goods-card card">';
            html += '<a href="' + host + "shop/product/detail/" + wid + "/" + data[i].id + '" class="js-goods link clearfix" data-goods-id="' + data[i].id + '" title="测试商品002">';
            html += '<div class="photo-block" style="background-color: rgb(255, 255, 255);">';
            html += '<img class="goods-photo js-goods-lazy" src="' + img_url + data[i].img + '"></div>';
            html += '<div class="info">';
            html += '<p class="goods-title">' + data[i].title + '</p>';
            if(data[i]['is_price_negotiable'] == 1){
                html += '<p class="goods-price"><em>面议</em></p>';
            }else{
                html += '<p class="goods-price"><em>¥' + data[i].price + '</em></p>';
            }
            html += '<div class="goods-buy btn1"></div>';
            html += '<div class="js-goods-buy buy-response"></div></div></a></li>';

        }
        return html;
    }
    //获取页面顶部被卷起来的高度
    function scrollTop() {
        return Math.max(
            //chrome
            document.body.scrollTop,
            //firefox/IE
            document.documentElement.scrollTop);
    }
    //获取页面文档的总高度
    function documentHeight() {
        //现代浏览器（IE9+和其他浏览器）和IE8的document.body.scrollHeight和document.documentElement.scrollHeight都可以
        return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    }
    function windowHeight() {
        return (document.compatMode == "CSS1Compat") ?
            document.documentElement.clientHeight :
            document.body.clientHeight;
    }
})