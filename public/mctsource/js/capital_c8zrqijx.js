var swiper1 = new Swiper('.swiper_product .swiper-container', {
	loop:true,
	grabCursor:true,
	pagination : '.swiper_product .swiper-pagination',
	paginationClickable :true,
})
var swiper2 = new Swiper('.swiper_service .swiper-container', {
	loop:true,
	grabCursor:true,
	pagination : '.swiper-pagination',
	paginationClickable :true,
})
$(function(){
	var url = window.location.href;
	url=decodeURI(decodeURI(url)) 
	console.log(url);
})