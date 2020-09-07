@extends('shop.common.template')
@section('head_css') 
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/helplist.css"/>
@endsection

@section('main')
	<div class="container">
	     <div class="orderquestion">
    		<div class="bottom-line">
    			<div class="order-question">
        			<div class="order-question-header">
           				 <h2>常见订单问题</h2> 
           				 <div class="tips">(若仍有疑问可点击下方链接联系有赞客服哦~)</div>
     			    </div>
					<ul>
			    	    <li>
			       			<a href="list1.html">         
			        		1.我已经付款了，商家怎么还不发货？ 
			       			</a>
			    		</li>
			            <li>
			        		<a href="list2.html">         
			        		2.我并没有收到货，为什么订单交易完成了？
			        		</a>
			    		</li>
			            <li>
					        <a href="list3.html">        
					        3.订单信息填写错了，如何修改？                   
					        </a>
				    	</li>
			            <li>
			            	<a href="list4.html">         
			        		4.交易完成了，如何申请维权？               
			            	</a>
			   			</li>
			            <li>
			       			<a href="list5.html">        
			       			5.订单已经退款，钱还没有收到？                  
			       			</a>
			    		</li>
			            <li>
					        <a href="list6.html">        
					        6.联系不上商家，怎么办？
					        </a>
			   			</li>
			            <li>
				       		<a href="list7.html">        
				        	7.找不到历史订单？                    
				       		</a>
			    		</li>
			            <li>
			        		<a href="list8.html">        
			        		8.找不到换货的地方，怎么办？                    
			        		</a>
			    		</li>
			        </ul>        
    			</div>
   			</div>
		</div> 
	</div>
@include('shop.common.footer')
@endsection
@section('page_js')
@endsection