$(function(){
    $.ajax({
		url:'/merchants/shareEvent/ShareEventDataStatistics',
        type:'get',
        data:{
			id:id,
		},
        dataType:'json',
        success:function(res){
           if (res.status==1) {
               var data = res.data;
               var descArr = [
                   data.title,
                   data.product_name,
                   data.start_time + ' 至 ' + data.end_time
               ];
               var amountArr = [
                   data.sale_price,
                   data.unit_amount,
                   data.lower_price,
                   data.total_payed_num,
                   data.total_payed_price
               ];
               var itemArr = [
                    data.pv,
                    data.uv,
                    data.total_payed_num,
                    data.convert_ration,
                    data.total_payed_price,
                    // data.new_member_num,
                    // data.old_member_num,
                    data.total_source_num,
                    data.total_actor_num
               ];
            //    var newMember =[
            //        data.new_payed_num,
            //        data.new_source_num
            //    ];
               $('.J_activity-desc').each(function(i,e){
                    $(this).html(descArr[i]);
               });
               $('.activity-amount').each(function(i){
                   $(this).html(amountArr[i]);
               });
               $('.item-amount').each(function(i){
                    $(this).html(itemArr[i]);
               });
            //    $('.J_new-mwmber').each(function(i){
            //        $(this).html(newMember[i]);
            //    });
           }
		}
    })
    getList(1,5);
    function getList(page,pageSize){
        $.ajax({
            url: '/merchants/shareEvent/ShareEventMemberAnalysis',
            data:{
                id: id,
                page: page,
                pageSize: pageSize
            },
            success:function(res){
                if (res.status==1){
                    if(res.data.data.length>0){
                        var data=res.data.data,html='',item,index;
                        for (var i=0;i<data.length;i++){
                            item = data[i];
                            index = (i+1)+(page-1)*5;
                            html += '<ul class="data_content">';
                            html += '<li>' + index + '</li>';
                            html += '<li>' + item.share_at + '</li>';
                            html += '<li><img src="' + item.headimgurl + '" class="avatar"><span>' + item.nickname + '</span><span class="data-source">'+ item.source + '</span></li>';
                            if(item.is_purchased){
                                html += '<li>是</li>';
                            } else {
                                html += '<li class="emphasize">否</li>';
                            }
                            html += '<li>' + item.actor_num + '</li>';
                            html += '<li>' + item.complete_time + '</li></ul>';
                        }
                        $('.J_data-content').html(html);

                        var pageHtml = '<ul class="pagination">',pageIndex;
                        if (page==1){
                            pageHtml += '<li class="disabled"><span>«</span></li>';
                        } else {
                            pageHtml += '<li data-page="'+ (page-1) +'"><span>«</span></li>';
                        }
                        for (var i=0;i<res.data.pages;i++){
                            pageIndex = i+1;
                            if (pageIndex==page) {
                                pageHtml += '<li class="active"><span>'+pageIndex+'</span></li>';
                            } else {
                                pageHtml += '<li data-page="' + pageIndex + '"><span>'+pageIndex+'</span></li>';
                            }
                        }
                        if (page==res.data.pages){
                            pageHtml += '<li class="disabled"><span>»</span></li>';
                        } else {
                            pageHtml += '<li data-page="'+(page+1)+'"><span>»</span></li>';
                        }
                        pageHtml += '</ul>';
                        $('.J_page').html(pageHtml);
                    } else {
                        $('.J_data-content').html('<ul class="data_content">暂无数据</ul>');
                    }
                }
            }
        })
    }
    $('.J_page').on('click','li:not(.disabled):not(.active)',function(){
        var page = $(this).data('page');
        getList(page,5);
    });
    $('.export').click(function(){
        window.location.href = '/merchants/shareEvent/ShareEventDataExport?id=' + id;
    });
    $('.data-tips').hover(function(){
        $(this).children('.tips-content').toggle();
    });
})