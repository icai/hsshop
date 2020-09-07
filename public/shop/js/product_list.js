$(function () {
    var wid = $('#wid').val();
    var sourceUrl = $('#sourceUrl').val();
    function addData(data) {
        var url = location.host;
        var mid = $("#mid").val();
        for (var i = 0; i < data.length; i++) {
            var html = "";
            html += '<li class="js-goods-card goods-card card">';
            html += '<a href="http://' + url + "/shop/product/detail/" + data[i].wid + "/" + data[i].id + '?_pid_='+mid+' " class="js-goods link clearfix" title=' + data[i].title + ">";
            html += '<div class="photo-block" style="background-color: rgb(255, 255, 255);">';
            html += '<img class="goods-photo js-goods-lazy" src=" ' + sourceUrl + data[i].img + '">';
            html += '</div><div class="info"><p class="goods-title">' + data[i].title + "</p>";
            html += '<p class="goods-price"><em>￥' + data[i].price + '</em></p>';
            html += '<div class="goods-buy btn1"></div><div class="js-goods-buy buy-response"';
            html += ' data-title=' + data[i].title + ' data-price=' + data[i].price + "";
            html += ' data-wid=' + data[i].wid + ' data-goods-id=' + data[i].id + '></div></div></a></li>';
    // 点击x图标
    $(document).on("click", ".js-cancel", function () {
        $("#Xms3Sq4JR6").hide();
        $("#LBqDHKuruf").hide();
    })
})