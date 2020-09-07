$('.subBtn').on('click', function(){
		$.ajax({
			url: '/staff/uploadFile',
			type: 'POST',
			cache: false,
			data: new FormData($('#uploadForm')[0]),
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success:function(res) {
				if(res.status == 1){
					tipshow(res.info,'info');
					setTimeout(function(){
						window.location.reload();
				　　},2000);
				}else{
					tipshow(res.info,'warn');
				}
			},
			error:function(){

			}
		})
	});

