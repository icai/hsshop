$(function(){
	$('.print-btn a').click(function(){
		prints();
	});
})
function prints(){
		document.body.innerHTML=document.getElementById('print').innerHTML;
		window.print(); 
	}