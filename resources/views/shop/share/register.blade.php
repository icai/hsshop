<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>会搜注册页面</title>
		<style>
			input{outline:none}
			ul,ol,li{
				list-style: none;
			}
			.red{color:red}
			.grey{color:#7b7b7b}
			*{
				padding:0;margin:0
			}
			@media screen and (min-width: 700px){
			  	html{
						font-size:20vw;
					}
			}
			@media screen and (max-width: 700px){
			  	html{
						font-size:25vw;
					}
			}
			html,body,article{
				height:100%;
				}
			body{
				background-color:#be1731
			}
			article{
				background-image: url({{config('app.url')}}static/images/share_backPage.png);
				background-repeat: no-repeat;
				background-size:100% 100%;
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
			}
			

			
				#app{
					font-size:0.14rem;
					padding-top:1rem
				}
			

			.formVal ul{
				display: flex;
				flex-direction: column;
			}
			.formVal ul li{
				border:1px solid #fe6b3f;
				border-radius: 0.5rem;
				background-color: #fff8f5;
				position:relative;
				padding:0.1rem;
				margin-bottom: 0.15rem
			}
			.formVal ul li input{
				font:0.16rem/0.3rem "微软雅黑";
				background-color: #fff8f5;
				padding-left:0.1rem;
				width: 3rem;
			}
			#app .formVal .focus{
				border:none;
				outline: none;
			}
			#getvalidate{
				position: absolute;
				top:0;
				bottom:0;
				right:0.07rem;
				width: 1.2rem;
				height:0.4rem;
				padding-left:0;
				margin:auto;
				background: linear-gradient(to left,#ff7826,#ffc01b);
				border:none;
				border-radius: 0.5rem;
				color:white
			}
			#submit{
				background: #ff6d3e;
				border:0.5rem;
				color:white;
				width: 100%;
				border-radius: 0.5rem;
				height:0.5rem;
				margin-top:0.1rem;
				font:0.2rem/0.4rem "微软雅黑";
				}
			
			input[type="checkbox"] + label::before {
			    content: "\a0"; /*不换行空格*/
			    display: inline-block;
			    vertical-align: middle;
			    font-size: 14px;
			    width: .9em;
			    height: .85em;
			    margin-right: .4em;
			    border-radius: 50%;
			    border: 1px solid #fc7c4d;
			    text-indent: .15em
			}
			input[type="checkbox"]:checked + label::before {
			    background-image: url({{config('app.url')}}static/images/share_checked.png);
			    background-size: 100% 100%;
			    border:none;
			    font-size:14px;
			    width: 1.05em;
			    height: 1em;
			}
			input[type="checkbox"] {
			    position: absolute;
			    clip: rect(0, 0, 0, 0);
			}
			.agreememt-margin{
				padding-top:0.25rem
			}
			.pop{
				width:100%;
				height:100%;
				position: absolute;
			}
			.shade{
				width:100%;
				height:100%;
				background: black;
				opacity: .7;
				position: absolute;
				top:-60px
			}
			.protocol{
				font-size:14px;
				position: absolute;
				z-index:100;
				background:white;
				height:4rem;width:2.9rem;
				top:0;bottom:0;left:0;right:0;
				margin:auto;
				border-radius:0.1rem;
				padding:0.05rem 0.15rem;
				display: flex;
				flex-direction: column;
			}
			.protocol h3{
				text-align: center;
				line-height:0.5rem;
				border-bottom:1px solid #e6e6e6;
				color:#333333
			}
			.cancel{
				position:absolute;
				top:-0.43rem;
				right:-0.08rem;
				height:0.4rem;
				width:0.4rem;
				border-radius: 50%;
				border:none;
				background: rgba(0,0,0,.7);
			}
			.cancel img{
				height:0.4rem;
				width:0.4rem;
			}
			.agreement-text{
				font:14px/0.3rem "微软雅黑";
				padding-top:0.2rem;
				overflow-y: scroll;
				
			}
			.info{
				position: absolute;
				top:0;bottom:0;left:0;right:0;
				height:0.4rem;
				text-align: center;
				border-radius: 0.1rem;
				width:3rem;
				margin:auto;
				color:white;
				background:black;
				opacity: .5;
				font:14px/0.4rem "微软雅黑"
			}
			.hide{
				display: none;
			}
		</style>
	</head>
	<body>
		<article>
			<section id="app">
				<form name="regSuccess" class="formVal" action="{{config('app.url')}}/shop/auth/share/regSuccess">
					<ul>
						<li>
							<input type="number" class="focus" id="tel" name="tel" placeholder="请输入手机号码"/>
						</li>
						<li>
							<input type="text" class="focus" name="validate" id="validate" placeholder="短信验证码"/>
							<input type="button" id="getvalidate" name="getvalidate" value="获取验证码"/>
						</li>
						<li>
							<input type="text" class="focus" id="nickname" name="nickname" placeholder="请输入个人昵称"/>
						</li>
						<li>
							<input type="password" class="focus" name="password" id="pw" placeholder="请设置6-12位字母和数字组合的密码"/>
						</li>
						<div class="agreememt-margin agree-group">
						  <input type="checkbox" disabled  name="agreement" id="agreement" checked/>
						  <label for="agreement" class="grey">阅读并同意<span class="red">会搜云《服务条款》</span></label>
						</div>
						<div><input type="button" name="submit" id="submit" value="立即注册"/></div>
					</ul>
				</form>
			</section>
			<div class="pop hide">
				<div class="shade"></div>
				<div class="protocol">
					<button class="cancel"><img src="{{config('app.url')}}static/images/share_x.png"/></button>
					<h3>服务条款</h3>
					<div class="agreement-text">
杭州会搜科技股份有限公司系会搜科技股份有限公司的运营商，本协议由您与杭州会搜科技股份有限公司签订，具有合同效力。注册时，请您认真阅读本协议，并选择接受或不接受本协议，注册成功及使用行为即表示您已充分阅读、理解并接受本协议的全部内容，并自愿接受本协议各项条款的约束。
<p>一、用户资格</p>
1.1 本协议所称“用户”，特指符合本协议所规定的条件，同意遵守会搜科技股份有限公司各种规则、条款（包括但不限于本协议），并使用会搜科技股份有限公司的自然人或单位。<br/>
1.2 只有符合下列条件之一的自然人或单位才能申请成为会搜科技股份有限公司用户，可以使用本会搜科技股份有限公司的服务：<br/>
a）年满十八岁，并具有完全民事权利能力和民事行为能力的自然人（未满十八岁的自然人需经其监护人书面同意）；<br/>
b）根据中华人民共和国法律或注册地法律、法规有效成立并合法存在的公司、企事业单位、社团组织和其他组织。<br/>
1.3 无民事行为能力的人以及无经营或特定经营资格的单位不得注册为会搜科技股份有限公司用户，其与会搜科技股份有限公司之间的用户协议自始无效，且一经发现，会搜科技股份有限公司有权立即注销该用户，并追究其使用会搜科技股份有限公司的一切法律责任。<br/>                                                                                                        
二、用户的权利和义务<br/>
2.1 用户有权根据本协议的约定及会搜科技股份有限公司发布的相关规则，使用会搜科技股份有限公司提供的服务，包括但不限于利用会搜科技股份有限公司网上交易平台发布信息、参与会搜科技股份有限公司的有关活动等。<br/>
2.2 用户有义务确保向会搜科技股份有限公司提供的所有注册信息真实准确，包括但不限于姓名、身份证号、银行卡号、支付账号、联系电话、电子邮箱等，并保证会搜科技股份有限公司可以通过上述联系方式及时与用户取得联系。同时，用户有义务在相关资料变更时及时更新注册信息。<br/>
2.3 用户不得以任何形式擅自转让或许可他人使用自己在会搜科技股份有限公司的用户账号。<br/>
2.4 用户有义务确保在会搜科技股份有限公司发布的信息真实、准确，无误导性。<br/>
2.5 用户不得在会搜科技股份有限公司平台发布国家禁止发布的信息，不得发布侵犯他人知识产权或其他合法权益的信息，也不得发布违背社会公共利益或公共道德的信息。<br/>
2.6 用户在使用会搜科技股份有限公司进行交易的过程中应当遵守诚实信用原则，不得在交易过程中采取不正当竞争行为，不得扰乱网上交易的正常秩序，不得从事与网上交易无关的行为。<br/>
2.7 用户承诺自己在会搜科技股份有限公司实施的所有行为遵守国家法律、法规和会搜科技股份有限公司的相关规定以及各种社会公共利益或公共道德。用户应对以该用户账号进行的所有行为负全部责任。<br/>
2.8 用户应自行承担因使用会搜科技股份有限公司、通过会搜科技股份有限公司进行交易等所产生的相关费用。<br/>
2.9 用户不得使用以下方式登录会搜科技股份有限公司或破坏会搜科技股份有限公司所提供的服务：<br/>
a）以任何机器人软件、蜘蛛软件、蠕虫软件、刷屏软件或其它自动方式访问或登录会搜科技股份有限公司；<br/>
b）通过任何方式对会搜科技股份有限公司内部结构造成或可能造成不合理或不合比例的重大负荷的行为；<br/>
c）通过任何方式干扰或试图干扰会搜科技股份有限公司的正常工作。<br/>
2.10 用户同意接收来自会搜科技股份有限公司的信息，包括但不限于活动信息、交易信息、促销信息等。<br/>
三、会搜科技股份有限公司的权利和义务<br/>
3.1 会搜科技股份有限公司将在现有技术水平的基础上努力确保正常运行，尽力避免服务中断或将中断时间限制在可行的最短时间内，保证用户使用的顺利进行。<br/>
3.2 会搜科技股份有限公司将对用户在注册使用会搜科技股份有限公司中所遇到的问题和反馈及时作出回复。<br/>
3.3 会搜科技股份有限公司有权对用户的注册信息进行查阅，对相关注册信息的真实性、合法性存在合理怀疑时，会搜科技股份有限公司有权发出通知询问用户并要求用户做出解释、改正，或不通知用户直接做出删除信息、删除账号等处理。<br/>
3.4 用户在会搜科技股份有限公司与其他用户产生纠纷的，用户通过司法部门或行政部门依照法定程序要求会搜科技股份有限公司提供相关资料，会搜科技股份有限公司将积极配合并提供有关资料；若用户请求会搜科技股份有限公司从中协调，或会搜科技股份有限公司知悉纠纷情况的，经审核后，会搜科技股份有限公司有权通过电子邮件及电话联系向纠纷双方了解纠纷情况，并将所了解的情况通过电子邮件通知纠纷双方。<br/>
3.5 因网络平台的特殊性，会搜科技股份有限公司没有义务对所有用户的行为进行事前审查。但若根据会搜科技股份有限公司所掌握的事实依据，可以认定用户存在违法、违反本协议规定或其他在会搜科技股份有限公司的不当行为，会搜科技股份有限公司有权向用户核实有关情况，删除相关信息，限制用户活动，发出警告通知以及终止向该用户提供服务。<br/>
3.6 会搜科技股份有限公司有权在不通知用户的前提下删除或采取其他限制性措施处理下列信息：包括但不限于以规避费用为目的、以炒作信用为目的、存在欺诈等恶意或虚假内容、违反公共利益或可能严重损害会搜科技股份有限公司和其他用户合法利益的。<br/>
四、隐私权政策<br/>
4.1 您了解并同意，以下信息适用本隐私权政策：<br/>
a）在您注册会搜科技股份有限公司帐号时，根据会搜科技股份有限公司要求提供的个人注册信息；<br/>
b）在您使用会搜科技股份有限公司的服务时，会搜科技股份有限公司自动接收并记录的信息（包括但不限于用户IP地址、使用的语言、访问日期和时间、软硬件特征信息及您需求的网页记录等数据）；<br/>
c）会搜科技股份有限公司通过合法途径从商业伙伴处取得的用户个人数据。<br/>
4.1 您了解并同意，以下信息不适用本隐私权政策：<br/>
a）您在使用会搜科技股份有限公司平台提供的搜索服务时输入的关键字信息；<br/>
b）会搜科技股份有限公司收集到的您在会搜科技股份有限公司发布的有关信息数据（包括但不限于参与活动、成交信息及评价详情）；<br/>
c）违反法律规定或违反会搜科技股份有限公司规则行为及会搜科技股份有限公司已对您采取的措施。<br/>
4.2 会搜科技股份有限公司保护用户信息安全：<br/>
a）会搜科技股份有限公司不会向任何无关第三方提供、出售、出租、分享或交易用户个人信息，除非事先得到您许可，或该第三方和会搜科技股份有限公司单独或共同为您提供服务，且在该服务结束后，其将被禁止访问包括其以前能够访问的所有这些资料；<br/>
b）会搜科技股份有限公司亦不允许任何第三方以任何手段收集、编辑、出售或者无偿传播用户个人信息，任何会搜科技股份有限公司用户如从事上述活动，一经发现，会搜科技股份有限公司有权立即终止与该用户的服务协议；<br/>
c）为服务用户的目的，会搜科技股份有限公司可能通过使用用户个人信息，向您提供您感兴趣的信息，包括但不限于向您发出产品和服务信息，或者与会搜科技股份有限公司合作伙伴共享信息以便他们向您发送有关其产品和服务的信息（后者需要用户事先同意）。<br/>
4.3 在如下情况下，会搜科技股份有限公司将依据用户个人意愿或法律的规定全部或部分的披露用户个人信息：<br/>
a）经您事先同意，向第三方披露；为提供您所要求的产品和服务，而必须和第三方分享用户个人信息；<br/>
b）根据法律的有关规定，或者行政或司法机构的要求，向第三方或者行政、司法机构披露；<br/>
c）如您出现违反有关法律、法规或者会搜科技股份有限公司服务协议或相关规则的情况，需要向第三方披露；<br/>
d）如您是适格的知识产权投诉人并已提起投诉，应被投诉人要求，向被投诉人披露，以便双方处理可能的权利纠纷；<br/>
e）在会搜科技股份有限公司平台上创建的某一交易中，如交易任何一方履行或部分履行了交易义务并提出信息披露请求的，会搜科技股份有限公司有权决定向该用户提供其交易对方的联络方式等必要信息，以促成交易的完成或纠纷的解决；<br/>
f）其它会搜科技股份有限公司根据法律、法规或者网站政策认为合适的披露。<br/>
4.4 会搜科技股份有限公司帐号均有安全保护功能，请妥善保管用户用户名及密码信息。会搜科技股份有限公司将通过对用户密码进行加密等安全措施确保用户信息不丢失，不被滥用和变造。尽管有前述安全措施，但同时也请您注意在信息网络上不存在“完善的安全措施”。在使用会搜科技股份有限公司网络服务进行网上交易时，您不可避免的要向交易对方或潜在的交易对方披露自己的个人信息，如联络方式或者地址。请您妥善保护自己的个人信息，仅在必要的情形下向他人提供。如您发现自己的个人信息泄密，尤其是会搜科技股份有限公司用户名及密码发生泄露，请您立即联络会搜科技股份有限公司客服，以便会搜科技股份有限公司采取相应措施。<br/>
4.5 会搜科技股份有限公司会不时更新本隐私权政策，您在同意会搜科技股份有限公司服务使用协议之时，即视为您已经同意本隐私权政策全部内容。<br/>
五、服务的中断和终止<br/>
5.1 在会搜科技股份有限公司未向用户收取相关服务费用的情况下，会搜科技股份有限公司可自行全权决定以任何理由（包括但不限于会搜科技股份有限公司认为用户已违反本协议，或用户超过一定期限未登录会搜科技股份有限公司等情况）终止对用户的服务，并不再保存用户在会搜科技股份有限公司的全部资料，而无需通知该用户。服务终止后，会搜科技股份有限公司没有义务为用户保留原用户资料或与之相关的任何信息，或转发任何未曾阅读或发送的信息给用户或第三方。<br/>
5.2 如用户向会搜科技股份有限公司提出注销本网站注册用户身份，需经会搜科技股份有限公司审核同意，由会搜科技股份有限公司注销该注册用户，用户即解除与会搜科技股份有限公司的协议关系，但会搜科技股份有限公司仍保留下列权利：<br/>
a）用户注销后，会搜科技股份有限公司有权保留该用户的资料，包括但不限于用户注册信息、交易记录等；<br/>
b）用户注销后，如用户在注销前在会搜科技股份有限公司交易平台上存在违法行为或违反本协议的行为，会搜科技股份有限公司仍可行使本协议所规定的权利。<br/>
5.3 如存在下列情况，会搜科技股份有限公司可以通过注销用户的方式终止服务：<br/>
a）根据会搜科技股份有限公司所掌握的事实依据，可以认定用户存在违法、违反本协议相关规定或其他在会搜科技股份有限公司的不当行为；<br/>
b）会搜科技股份有限公司发现用户注册信息中主要内容是虚假的；<br/>
c）其它会搜科技股份有限公司认为需终止服务的情况。<br/>
六、免责声明<br/>
6.1 当用户接受该协议时，用户应明确了解并同意：用户使用会搜科技股份有限公司之风险由用户自行承担。会搜科技股份有限公司依据现有技术提供服务，尽管会搜科技股份有限公司将尽合理努力维护正常运行，但会搜科技股份有限公司无法随时预见到任何技术上的问题或其他困难，该等困难可能导致用户的使用行为受到影响，请用户谨慎考虑使用会搜科技股份有限公司所提供服务可能带来的风险。<br/>
6.2 是否经由会搜科技股份有限公司下载或取得任何资料，由用户自行考虑、衡量并且自负风险，因下载任何资料而导致用户手机系统的任何损坏或资料流失，由用户自行负责。<br/>
6.3 用户经由会搜科技股份有限公司取得的建议和资讯，无论其形式或表现，绝不构成本协议未明示规定的任何保证。<br/>
6.4 基于以下原因而造成用户的损失，会搜科技股份有限公司不承担任何直接或间接赔偿责任：<br/>
a）会搜科技股份有限公司的使用或无法使用；<br/>
b）用户的资料遭到未获授权的存取或变更；<br/>
c）会搜科技股份有限公司中任何第三方之声明或行为。<br/>
6.5 对于用户所发布信息的合法性、真实性及其品质，以及用户履行交易的能力等，会搜科技股份有限公司无事先审查义务且不承担任何连带责任。<br/>
6.6 会搜科技股份有限公司提供与其它网站或资源的链接，用户可能会因此连结至其它运营商经营的网站，但不表示会搜科技股份有限公司与这些运营商有任何关系，会搜科技股份有限公司不对上述链接网站及其网页内容进行管理监督。因使用或依赖任何此类网站或资源发布的或经由此类网站或资源获得的任何内容、物品或服务所产生的任何损害或损失，会搜科技股份有限公司不负任何直接或间接赔偿责任。<br/>
七、知识产权<br/>
7.1 所有出现在会搜科技股份有限公司上的内容，包括但不限于作品、图片、档案、资料、网站构架、网站版面的安排、网页设计、软件、经由会搜科技股份有限公司呈现的推广信息或资讯，均由会搜科技股份有限公司或相关权利人依法享有相应知识产权（包括但不限于著作权、商标权、专利权或其它专属权利等），用户仅在符合使用目的的前提下被许可浏览和使用会搜科技股份有限公司。未经会搜科技股份有限公司或相关权利人明示授权，用户不得复制、修改、出租、出借、出售、传送、删除、添加会搜科技股份有限公司的内容，或根据上述网站资料和资源制作成任何种类物品。<br/>
7.2 会搜科技股份有限公司授予用户不可转移及非专属的使用权，用户可以通过手机使用会搜科技股份有限公司的目标代码（以下简称“软件”），但不得自行或许可任何第三方，复制、修改、创作衍生作品、进行还原工程、反向组译，或以其它方式破译或试图破译源代码，或出售、转让“软件”或对“软件”进行再授权，或以其它方式移转"软件"之任何权利。用户同意不以任何方式修改“软件”，或使用修改后的“软件”。<br/>
7.3 用户不得经由非会搜科技股份有限公司所提供的界面使用会搜科技股份有限公司。<br/>
八、不可抗力<br/>
8.1 会搜科技股份有限公司对因超出我们合理控制之外的原因、事件或其他因素导致的未能履行本协议下的任何义务不承担责任。该等原因、事件或其他因素包括但不限于战争、台风、水灾、火灾、雷击或地震、罢工、暴动、黑客攻击、网络病毒、电信部门技术管制、政府行为或任何其它自然或人为造成的灾难等情况。<br/>
九、法律适用与争议解决<br/>
9.1 本协议之订立、生效、解释、履行、修订与争议解决均适用中华人民共和国法律。<br/>
9.2 如果发生因本协议或其违约、终止或效力而引起的或与之有关的任何争议、纠纷或索赔（以下简称“争议”），均应提交杭州仲裁委员会进行仲裁，仲裁地点为杭州。相关争议应单独仲裁，不得与任何其它方的争议在任何仲裁中合并处理，该仲裁裁决是终局，对各方均有约束力。如果所涉及的争议不适于仲裁解决，用户同意一切争议由杭州是江干区法院管辖。<br/>
					</div>
				</div>
			</div>
			<div class="info hide">
				
			</div>
		</article>
		<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
		var thisurl = "{{$appurl}}";
			(
				function(){
					var vTap = [];
					var telT;
					
					$(window).on('load',function(){
						console.log($(this).height(),$(this).width()*2.03+'px')
						$('article').css({'height':$(this).width()*2.03+'px'})
						$('.shade').css({'width':$(this).width()+'px','height':$('article').height()+180+'px'})
					})
					//手机验证
					{{--$('#tel').on('blur',function(){--}}
						{{--if($(this).val()!='' && $(this).val().match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/g)){--}}
							{{--$.ajax({--}}
								{{--url:thisurl+'auth/share/share/isRegister',--}}
								{{--type:'get',--}}
								{{--data:{--}}
									{{--tel:$('#tel').val()--}}
								{{--},--}}
								{{--success:function(res){--}}
									{{--console.log(res);--}}
									{{--if(res.info == "该账号可以注册"){--}}
										{{--vTap.push(true)--}}
									{{--}else{--}}
										{{--$('.info').text('该账号已注册').show();--}}
									{{--}--}}
									{{--telT=setTimeout(function(){--}}
										{{--$('.info').hide();--}}
										{{--clearTimeout(telT);--}}
									{{--},1000)--}}
								{{--}--}}
							{{--});--}}
						{{--}else if($(this).val()==''){--}}
						{{--}else{--}}
							{{--$('.info').text('手机号码输入错误').show();--}}
						{{--}--}}
						{{--telT=setTimeout(function(){--}}
							{{--$('.info').hide();--}}
							{{--clearTimeout(telT);--}}
						{{--},1000)--}}
					{{--});--}}
					
					//密码验证
					$('#pw').on('blur',function(){
						let pwValue = $(this).val();
						if(pwValue.match(/^([a-zA-Z0-9]){6,12}$/g)){
							vTap.push(true)
						}else if($(this).val()==''){
						}else{
							$('.info').text('请输入6-12位密码').show();
						}
						telT=setTimeout(function(){
							$('.info').hide();
							clearTimeout(telT);
						},1000)
					});

					//获取验证码
					var flag = false;
					$('#getvalidate').on('click',function(){
						var sendFlag = false;
						if($('#tel').val() && $('#tel').val().match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9]|19[0|1|2|3|5|6|7|8|9]|16[0|1|2|3|5|6|7|8|9])\d{8}$/g)){
							if(flag){
								$('.info').text('短信已发送').show();
								telT=setTimeout(function(){
									$('.info').hide();
									clearTimeout(telT);
								},1000)
								return false
							}
                            $.ajax({
                                url:thisurl+'auth/share/isRegister',
                                type:'get',
                                async:false,
                                data:{
                                    tel:$('#tel').val()
                                },
                                success:function(res){
                                    console.log(res);
                                    if(res.status == '1'){
                                        sendFlag = true;
                                    }else{
                                        $('.info').text('该账号已注册').show();
                                    }
                                    telT=setTimeout(function(){
                                        $('.info').hide();
                                        clearTimeout(telT);
                                    },1000)
                                }
                            });
							if (!sendFlag){
							    return false;
							}
							$.ajax({
								url:thisurl+'/auth/sendcode',
								type:'get',
								data:{
									mphone:$('#tel').val()
								},
								success:function(res){
									console.log(res);
									if(res.status == 1){
										//status
										vTap.push(true)
									}
								}
							})
							$(this).val('60s后重试');
							countDown($(this));
						}else{
							$('.info').text('手机号码格式不正确').show();
						}
						telT=setTimeout(function(){
							$('.info').hide();
							clearTimeout(telT);
						},1000)
					})
					
					//协议
					$('label.grey').on('click',function(){
						$('.pop').show()
					})
					$('.cancel').on('click',function(){
						$('.pop').hide()
					})
					
					//立即注册
					$('#submit').on('click',function(){

                       if(!$('#tel').val().match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9]|19[0|1|2|3|5|6|7|8|9]|16[0|1|2|3|5|6|7|8|9])\d{8}$/g)){
                           $('.info').text('手机号码格式不正确').show();
					   }
					   if (!$("#agreement").is(':checked')){
                           $('.info').text('您还没有同意协议').show();
                           return false;
					   }
						if($('#tel').val() && $('#validate').val() && $('#nickname').val() && $('#pw').val()){
                            $.ajax({
                                url:thisurl+'auth/share/register',
                                data:{
                                    'mphone':$('#tel').val(),
                                    'sms_code':$('#validate').val(),
                                    'nickname':$('#nickname').val(),
                                    'password':$('#pw').val(),
								},
                                type:'post',
                                cache:false,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType:'json',
                                success:function (res) {
                                    if (res.status == 1){
                                        window.location.href=thisurl+'auth/share/regSuccess'
                                    }else {
                                        $('.info').text(res.info).show();
                                    }
                                },
                                error : function() {
                                    $('.info').text('服务器打盹了……').show();
                                }
                            })
						}else{
							$('.info').text('请完善注册信息信息').show();
						}	
						telT=setTimeout(function(){
							$('.info').hide();
							clearTimeout(telT);
						},1000)	
					})

					//倒计时
					var t;
					
					function countDown(that) {
				        var time = 60;
				        var _this = that;
				        if(!flag){
				        	flag=true;
				        	t = setInterval(function () {
					            --time;
					            var html = time + "s后重试";
					            $("#getvalidate").val(html);
					            if (time == 0) {
					                clearInterval(t);
					                $("#getvalidate").val("获取验证码")
					                flag=false;
					            }
					        }, 1000)
				        }
				        
				    }
				}
			)()
// 
var url = location.href.split('#').toString();
            $.get(thisurl+'auth/share/getShareData',{"url": url},function(data){
                if(data.errCode == 0){
                    wx.config({
                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                        appId: data.data.appId, // 必填，公众号的唯一标识
                        timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                        nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                        signature: data.data.signature,// 必填，签名，见附录1
                        jsApiList: [
                            'checkJsApi',
                            'onMenuShareTimeline',
                            'onMenuShareAppMessage',
                            'onMenuShareQQ',
                            'chooseWXPay'
                        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                    });
                    
                }
            })
	
            wx.ready(function () {
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title: "快来领取超值福利，使用小程序商城", // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        //alert('分享成功');
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享给朋友
                wx.onMenuShareAppMessage({
                    title: '快来领取超值福利，使用小程序商城', // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: url, // 分享链接
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                //分享到QQ
                wx.onMenuShareQQ({
                    title: '快来领取超值福利，使用小程序商城', // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: url, // 分享链接
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
                    success: function () {
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                       // 用户取消分享后执行的回调函数
                    }
                });

                //分享到腾讯微博
                wx.onMenuShareWeibo({
                    title: '快来领取超值福利，使用小程序商城', // 分享标题
                    desc: '快点注册，免费领取会搜云小程序和微商城吧！', // 分享描述
                    link: url, // 分享链接
                    imgUrl: thisurl+'static/images/sharepic.png', // 分享图标
                    success: function () {
                       // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.error(function(res){
                    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                    //alert("errorMSG:"+res);
                });
            });
        
		</script>
	</body>
</html>

            
