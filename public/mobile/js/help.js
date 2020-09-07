$(function(){
    /*
	* @auther 赵彬
	* @desc 移动端帮助中心搜索功能
	* @date 2018-7-13
	* */
    $(".search-pic").click(function(){
        var val = $(".search-box input").val()
        if(!val){
            return false
        }
        window.location.href = appUrl+'home/index/helpList?keywords=' + val;

    })
})