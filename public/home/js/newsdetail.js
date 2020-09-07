$(function(){
    var boxWidth = $(".xue-right").width();
    var imgs = $(".xue-right").find('img')
    for(var i=0; i<imgs.length; i++){
    	var imgWidth = $(imgs[i]).width();
    	if(imgWidth > boxWidth){
            $(imgs[i]).css('width',"100%")
        }
    }
})