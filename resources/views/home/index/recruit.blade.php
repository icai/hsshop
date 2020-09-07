@extends('home.base.head')
@section('head.css')
    <!--bootstrap的css样式-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"/>
    <!--base.css-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/base.css"/>
    <!--页面的css样式-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/aboutCommon.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}home/css/recruit.css">
@endsection
@section('content')
    <input id="source" type="hidden" value="{{ config('app.source_url') }}home/">
	<div class="top_bg">
		{{--<img src="{{ config('app.source_url') }}home/image/about_banner.png" alt="">--}}
		<h2>选择会搜云&nbsp;&nbsp;&nbsp;&nbsp;值得信赖</h2>
		<p>爱、感恩、责任、坚持、创新</p>
	</div>
    <!--内容导航-->
    <div class="content_nav">
        <ul>
            <li><a href="{{ config('app.url') }}home/index/about"><img src="{{ config('app.source_url') }}home/image/intro.png"/><div class="nav_name"><h5>了解会搜云</h5><p>全面了解会搜公司</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/growth"><img src="{{ config('app.source_url') }}home/image/history.png"/><div class="nav_name"><h5>发展历程</h5><p>会搜的一路走来</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/culture"><img src="{{ config('app.source_url') }}home/image/culture.png"/><div class="nav_name"><h5>企业文化</h5><p>爱与感恩的理念</p></div></a></li>
            <li class="have"><a href="{{ config('app.url') }}home/index/recruit"><img src="{{ config('app.source_url') }}home/image/recruit_1.png"/><div class="nav_name"><h5>招贤纳士</h5><p>伯乐寻找千里马</p></div></a></li>
            <li><a href="{{ config('app.url') }}home/index/honor"><img src="{{ config('app.source_url') }}home/image/linkus.png"/><div class="nav_name"><h5>资质荣誉</h5><p>荣誉奖项及资质</p></div></a></li>
        </ul>
    </div>
    <!--主要内容-->
    <div class="main_part">
        <!--招贤纳士-->
        <div class="content" id="content_4">
            <div class="recruit">
            <img class="order_num" src="{{ config('app.source_url') }}home/image/01.png">
            	<h2>会搜科技 招贤纳士</h2>
            	<p>我们拒绝高端、大气、上档次，只会说不会干</p>
            	<p>我们欢迎低调、奢华、有内涵，为梦想而努力.不问出处，不问长相</p>
            	<p>你可以没有硕果累累的文凭&魅惑的三围, 但你须有一颗积极进取的心</p>
            	<p>这里并不安逸，内心脆弱者请绕道而行</p>
            	<p>如果你依然决心尝试,来吧，我们会给你最最最热情的拥抱</p>
            	<p>这里，充满活力与激情感受年轻的力量</p>
            	<p>这里，拥有平等的晋升空间</p>
                <p>加入会搜，一起收获友爱、尊重和温暖的事业伙伴</p>
                <ul class="recruit_nav">
                    <li class="active">客服<div class="arrow"></div></li>
                    <li>技术<div class="arrow"></div></li>
                    <li>销售<div class="arrow"></div></li>
                    <li>职能<div class="arrow"></div></li>
                </ul>
                
            </div>
            <div class="recruit_info">
				<div class="recruit_type">
					<div class="recruit_item">
						<h4>售后服务专员</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、通过电话、邮件、在线系统解答客户的咨询和疑问，帮助客户解决问题，做好售后服务；</p>
							<p>2、妥善处理顾客投诉，调节客户与公司之间的关系，提升客户满意度；</p>
							<p>3、从客户角度协调各部门之间的工作，促成各部门之间的工作流程的优化，提升客户的服务体验；</p>
							<p>4、完成上级领导安排的其他工作。</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、服务意识强，有客户服务经验优先，有APP或电商平台客服工作经验尤佳；</p>
							<p>2、具备较强的语言表达能力及综合分析能力，思维敏捷，善于倾听客户问题、准确答疑；</p>
							<p>3、有责任心，有亲和力，踏实仔细，具有较强的理解、沟通及协调能力；</p>
							<p>4、抗压能力强，在面对客户误解或不满时，能保证服务水准；</p>
							<p>5、应变能力强，具备处理突发或紧急问题的能力；</p>
							<p>6、诚实守信，为人谦虚、勤奋努力，具有高度的团队合作精神和高度的工作热情；</p>
						</div>
					</div>
					<div class="recruit_item">
						<h4>客服</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、处理业务人员提交的申请单并在苹果系统上进行注册；</p>
							<p>2、APP图标发与业务人员进行确认及反馈修改方案;</p>
							<p>3、将确认好的全套图标制作成素材包并上传生成至APP生成系统</p>
							<p>4、对已生成的APP进行数据填充，确保每个APP数据完整</p>
							<p>5、对已填充好的APP进行截图，用于应用市场发布</p>
							<p>6、在苹果电脑上发布APP上传至苹果市场待审核，审核通过后更新ios地址，用于二维码扫描下载</p>
							<p>7、处理业务人员反馈的bug及建议</p>
							<p>8、接听来电及沟通协调和其他配合工作等</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、大专及以上学历</p>
							<p>2、性格开朗，积极向上，普通话标准，具备较强的沟通能力及耐力；</p>
							<p>3、喜欢从事客服工作,&nbsp;从事过电话客服工作经验者优先；</p>
							<p>4、经验不限；</p>
						</div>
					</div>
				</div>
				
				<div class="recruit_type">
					<div class="recruit_item">
						<h4>前端开发</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、负责公司重点业务的产品设计研发和优化；</p>
							<p>2、参与公司产品前端技术架构、流程标准等的规划设计；</p>
							<p>3、研究探索前沿前端技术与公司业务的结合创新；</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、对原生JS有深刻理解，掌握浏览器渲染原理；</p>
							<p>2、熟练使用Html5，CSS3，JavaScript以及响应式布局；</p>
							<p>3、具有面向对象编程思想，理解MVC&MMVM设计模式，熟练掌握Ajax，DOM，JSON等前端相关技术；</p>
							<p>4、熟悉W3C标准，对表现与数据分离、Web语义化等有深刻理解；</p>
							<p>5、熟悉至少一种主流JS框架（jQuery除外），熟悉React/Vuejs并有相关项目经验者优先；</p>
							<p>6、有移动端开发经验，熟悉移动端页面性能调优；/Vuejs并有相关项目经验者优先；</p>
							<p>7、富于探索创新精神，良好的沟通能力与团队合作意识；/Vuejs并有相关项目经验者优先；</p>
							<p>8、对前端开发有自己的独特的见解；/Vuejs并有相关项目经验者优先；</p>
							<p>9、追求前端的交互体验和页面效果；/Vuejs并有相关项目经验者优先；</p>
							<p>10、本科及以上学历，在互联网相关的前端开发行业从业专注3年以上；/Vuejs并有相关项目经验者优先；</p>
						</div>
					</div>
					<div class="recruit_item">
						<h4>深资Java开发工程师</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、参与公司核心平台产品的开发、调试工作，撰写相关技术文档 ；</p>
							<p>2、搭建系统开发环境，负责系统框架和核心代码的实现； </p>
							<p>3、参与制定设计及实现规范，软件部署工作；</p>
							<p>4、协助持续改进软件系统架构、核心算法或者核心技术模块等，使产品在高性能和高可用性方面发挥作用；</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、计算机专业，2年以上大型软件项目开发工作经验；</p>
							<p>2、具有丰富J2EE架构设计经验；熟悉java编程、设计模式和组件技术，熟悉关系型数据库，精通通讯协议和面向对象编程思想；</p>
							<p>3、精通SQL语言，熟练使用MySQL；</p>
							<p>4、具备良好的文档编制习惯和代码书写规范；</p>
							<p>5、至少在2个项目中应用过常见的开源组件Struts，Spring，Hibernate, mybatis等其中之一，深入了解主流开发技术和开源开发框架；</p>
							<p>6、思路清晰，善于思考，能独立分析和解决问题；</p>
							<p>7、善于沟通、责任心强、能快速进入工作状态，具良好的团队合作精神，工作认真、细致、负责，并能在一定压力下完成工作；</p>
							<p>8、有移动互联网成功产品开发经验者优先考虑。</p>
						</div>
					</div>
				</div>
				<div class="recruit_type">
					<div class="recruit_item">
						<h4>营销顾问</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、负责公司产品的推广和销售；</p>
							<p>2、了解、分析、引导客户需求，为客户提供相对应的产品方案，推动成交，达成销售目标；</p>
							<p>3、建立良好的客户关系，提供优质专业的售后服务；</p>
							<p>4、执行公司各项销售政策，达成业绩目标；</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、性格开朗，沟通能力良好；</p>
							<p>2、优秀的沟通协调能力和学习能力；</p>
							<p>3、喜欢销售的工作，具有极强的责任感和敬业度，以及团队合作精神；</p>
							<p>4、有上进心，能接受挑战；</p>
						</div>
					</div>
					<div class="recruit_item">
						<h4>销售助理</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、操作微信，及时向客户推送微信内容和文章；</p>
							<p>2、接受客户的报名；</p>
							<p>3、协调与其他部门的合作；</p>
							<p>4、能够发现实际工作中的问题，并结合情况做出合理性的建议；</p>
							<p>5、完成临时交办的其他工作。</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、具备较强的工作责任心，出色的语言表达能力与沟通协调能力；</p>
							<p>2、具备独立处理复杂问题和危急事件的能力；</p>
							<p>3、具备较强的工作积极性和主动服务的意识；</p>
							<p>4、性格活泼开朗，愿意出外勤；</p>
						</div>
					</div>
				</div>
				<div class="recruit_type">
					<div class="recruit_item">
						<h4>行政前台</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、负责前台电话接听和处理，前台访客接待；</p>
							<p>2、负责快递、信函、报刊杂志的收发和登记处理；</p>
							<p>3、负责公司公用区域卫生、绿化、会议室使用管理，同时负责各部门卫生的督促和监督工作；</p>
							<p>4、负责公共设备如打印机、复印件、空调、冰箱、微波炉等的维护和保养；</p>
							<p>5、管理办公物资申领，采购和统计；</p>
							<p>6、处理日常行政管理，协助领导不断完善各项规章管理制度，使公司趋于规范化的管理；</p>
							<p>7、承办员工的考勤，协助公司的招聘工作</p>
							<p>8、完成领导交办的其他工作事项；</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、形象气质佳，有亲和力；</p>
							<p>2、认真负责，能较好地执行上级交办的工作；</p>
							<p>3、熟悉前台工作流程，熟练使用相关办公软件；</p>
							<p>4、工作热情积极、细致耐心，具有良好的沟通能力、协调能力，性格开朗，相貌端正，待人热诚；</p>
							<p>5、有前台接待工作经验者优先。</p>
						</div>
					</div>
					<div class="recruit_item">
						<h4>音控师</h4>
						<div class="job_info">
							<p class="job_type">工作性质：全职</p>
							<p class="job_addr">工作地点：杭州</p>
						</div>
						<div class="job_title">
							<div class="left_line"></div>
							<p>职位描述</p>
							<div class="right_line"></div>
						</div>
						<div class="job_desc">
							<p>1、培训会议现场音控。</p>
							<p>2、负责公司培训现场视频处理。</p>
							<p>3、具备视频音频处理能力、能独立完成节目视频剪辑制作。</p>
							<p>4、工作认真，责任心强，具有团队合作精神。</p>
							<p>5、根据脚本和文案进行后期制作，有自己的想法，熟练掌握视觉表现。</p>
							<p>6、具备良好的理解能力，很好的表达视频意图。</p>
							<p>7、片头片尾，字幕，剪辑 ，特效动画制作技能全覆盖。</p>
						</div>
						<div class="job_ability">
							<p>任职要求：</p>
							<p>1、一年以上的音控及视频剪辑工作经验；</p>
							<p>2、热爱生活，对音控、视频职业真的有热情；</p>
							<p>3、熟悉掌握音控技术，精通AE等后期合成软件，对调色有一定基础，会使用相应设备；</p>
							<p>4、有创新精神，具备深厚的艺术修养，独立的艺术风格和审美感悟，思维敏锐，风格鲜明。</p>
						</div>
					</div>
				</div>
            </div>
        	<!-- <div class="zhaopin-nav zhao-s">
	        	<div class="job-service">
					
					<p>职位描述：</p>
					
				</div>
    		</div>
    		<div class="zhaopin-nav">
	        	<div class="job-service">
					<h4>iOS开发 </h4>
					<p>职位描述：</p>
					<p>1.基于IOS平台进行移动应用程序的系统分析与设计工作，承担核心功能代码编写，开发与维护系统公用核心模块；</p>
					<p>2.参与移动应用软件框架的研究，设计和实现、关键技术验证和选型等工作；</p>
					<p>3.移动应用软件性能优化，技术难题攻关，解决各类潜在技术风险，保证系统的安全、稳定、快速运行；</p>
					<p>4.带领并指导开发工程师、程序员进行代码开发/单元测试等工作，参与移动规范制订、技术文档编写。</p>
					<p>任职资历：</p>
					<p>1.1年及以上手机应用实际开发经验，1年以上IOS开发经验，1年以上C/C+/Java开发经验，具备敏捷编程思想，精通IOS及Mysql，有高并发，大数据量经验优先；</p>
					<p>2.精通Objective-C、Mac OS X、Xcode， 精通IOS SDK中的UI、网络、数据库、XML/JSON解析等开发技巧；</p>
					<p>3.有多个完整的IOS项目经验，至少参加过一个完整的商业级手机应用或游戏开发项目；</p>
					<p>4.熟悉各种主流手机特性，深刻理解手机客户端软件及服务端开发特点；</p>
					<p>5.精通常用软件架构模式，熟悉各种算法与数据结构，多线程，网络编程（Socket、http/web service）等；</p>
					<p>6.具有很强的学习能力和对新技术的追求精神，能够独立承担项目开发工作，具有比较强的责任心；严谨、主动，良好的团队意识和沟通能力 ；</p>
				</div>
				<div class="job-service">
					<h4>前端开发 </h4>
					<p>职位描述：</p>
					<p>1.精通各种Web前端技术（HTML/CSS/Javascript等)，熟练跨浏览器、跨终端的开发；</p>
					<p>2.熟知浏览器对页面加载的整个过程，并能根据具体情况提出具体的优化方案；</p>
					<p>3.熟练使用数据可视化相关工具，并对大数据呈现有自己独到的见解；</p>
					<p>4.熟练使用RequireJS、seaJS，以及jQuery或者angular；</p>
					<p>5.个性乐观开朗，逻辑性强，善于沟通和合作，有一定的项目管理、团队管理经验；</p>
					<p>6.对模块化和组件有相关的见解</p>
					<p>7.熟悉前端构建工具基本用法（wepack、gulp、grunt）</p>
					<p>8.有移动设备上前端开发经验优先</p>
					<p>任职资历：</p>
					<p>1.有多年的前端开发经验，技术功底深厚</p>
					<p>2.熟悉前端相关的技术标准、规范、前沿发展等</p>
					<p>3.对前端架构有深入理解，有规模化的前端开发经历</p>
					<p>4.良好的设计、建模和表达能力，良好的逻辑分析和沟通能力</p>
					<p>5.对产品设计、用户研究、视觉交互等有一定研究的优先考虑</p>		
				</div>
				<div class="job-service" >
					<h4>中级C++开发工程师 </h4>
					<p class="job-title">职位描述：</p>
					<p>1.c/c++ 3年以上的实际开发经历</p>
					<p>2.了解栈,队列,链表等基本数据结构及其操作；</p>
					<p>3.拥有多线程编程经验,了解多线程环境下的资源冲突及其解决方案；</p>
					<p>4.具备一定的跨平台开发的知识,有实际跨平台开发经验者优先；</p>
					<p>5.具备良好的编程习惯,注重编程规范,对代码质量有精益求精的态度.具备随时对代码进行重构优化的态度和能力；	</p>
					<p>6.具备一定的性能分析和瓶颈定位能力。</p>
					<p class="job-title">任职资历：</p>
					<p>1.熟悉linux下c/c++开发、调试、调优；</p>
					<p>2.熟悉网络编程，熟悉常见的网络库及其实现；</p>
					<p>3.熟悉TCP/IP，熟悉linux多进程/多线程；</p>
					<p>4.熟悉架构设计相关方法；</p>
					<p>5.精通TCP, UDP, HTTP等网络协议；</p>
					<p>6.对工作充满激情，具有强烈的创新精神，富于责任心;</p>
				</div>
				<div class="job-service">
					<h4>中级java </h4>
					<p>职位描述：</p>
					<p>1.熟悉web开发技术, 常用的Java开源项目，有web开发经验；</p>
					<p>2.熟悉j2ee开发框架，熟悉Ibatis,Hibernate,Spring MVC、Javascript,Ajax等常用技术;</p>
					<p>3.熟悉oracle，mysql数据库中一个，能熟练编写sql语句，常规性能优化；</p>
					<p>4.熟悉jetty等Web应用服务器部署、优化；熟悉html,javascript等网页技术 ;</p>
					<p>5.熟悉linux操作系统，会基本维护和优化；</p>
					<p>6.良好的沟通技巧和团队合作精神；</p>
					<p>任职资历：</p>
					<p>1.扎实的Java编程基础，熟悉各种设计模式，熟练掌握Spring/Struts或其他主流Java框架</p>
					<p>2.具备大数据处理的架构经验，熟悉典型业务场景下的数据架构方式，有关系型数据库、NOSQL数据库及内存数据库的综合运用经验</p>
					<p>3.具备分布式架构的理论和实践经验，熟悉常见的分布式计算平台Hadoop、MPI或Storm等</p>
					<p>4.具备良好的沟通技能及团队协作意识，有能力针对特定场景或要求给出合理的技术解决方案，并跨部门协调完成</p>
					<p>5.具有灵活解决问题能力和抗压能力</p>			
				</div>
    		</div>
    		<div class="zhaopin-nav">
	        	<div class="job-service">
					<h4>微信运营专员 </h4>
					<p>职位描述：</p>
					<p>1.帮助客户建立微信公众平台，并指导如何通过平台营销产品；</p>
					<p>2.熟练操作智能手机（苹果IOS系统和安卓系统);</p>
					<p>3.常用微信，并了解微信各项功能；</p>
					<p>4.学习能力强，能吃苦耐劳，有团队协作精神。</p>
					<p>任职资历：</p>
					<p>1.高中及以上学历，年龄20—28岁之间；</p>
					<p>2.性格开朗，积极向上，普通话标准，具备较强的沟通能力及耐力；</p>
				</div>
    		</div>
    		<div class="zhaopin-nav">
	        	<div class="job-service">
					<h4>产品经理</h4>
					<p>职位描述：</p>
					<p>1.根据公司战略，抓住核心需求，对无线端产品线（如手机 APP、微信公众号等）的规划、架构，并推动上线。</p>
					<p>2.负责无线端产品的用户体验与交互设计。</p>
					<p>3.协调公司各部门的资源，提高工作效率，确保产品准时发布上线。 </p>
					<p>4.对产品的运营提供支持，并根据市场的反馈以及业务的发展需要，以数据为导向，不断推进产品的更新换代。</p>
					<p>5.协调业务需求与开发进程。</p>
					<p></p>
					<p>任职资历：</p>
					<p>1.本科及以上学历，对用户需求把握精准，重视产品的细节体验，热爱互联网产品设计。</p>
					<p>2. 2年及以上互联网相关的产品经验，2年以上微信端页面规划或移动端产品设计及运营经验者优先。</p>
					<p>3.熟悉产品设计开发流程，能独立负责一个产品从无到有，从市场分析、了解需求到产品策划、落实开发等一系列流程。</p>
					<p>4.具备优秀的沟通能力、表达能力，执行力强，有良好的团队精神。</p>
				</div>
    		</div> -->
    	</div>
    </div>
@endsection
@section('foot.js')
	<script src="{{ config('app.source_url') }}home/js/aboutUs.js" type="text/javascript" charset="utf-8"></script>
	<!-- 页面js -->
	<script src="{{ config('app.source_url') }}home/js/recruit.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}home/js/aboutCommon.js" type="text/javascript" charset="utf-8"></script>
@endsection