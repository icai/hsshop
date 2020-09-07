$(function(){
	$(".tip_show").popover({ 
        trigger:'hover',
        container:'body',  
        placement : 'bottom',   
        html: 'false',  
        content: function() {  
            return $(this).data('content');  
        },  
        animation: false,
    });
})