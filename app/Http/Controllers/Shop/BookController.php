<?php 
/**
 * Created by wuxiaoping.
 * User: wuxiaoping
 * Date: 2017/10/26
 */
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Jobs\SendTplMsg;
use App\Module\MessagePushModule;
use App\S\MarketTools\MessagesPushService;
use Validator;
use Illuminate\Http\Request;
use App\S\Book\BookService;
use App\S\Book\UsersBookService;

class BookController extends Controller
{
	
	public function __construct()
	{

	}

	/**
	 * 移动端预约列表页
	 * @return [type] [description]
	 */
	public function index($wid,Request $request)
	{
		$input = $request->input() ?? [];
		$bookService = new BookService();
		list($list,$pageHtml) = $bookService->getAllList($wid,$input);
		return view('shop.book.index',[
			'title'	=> '预约列表',
			'list'	=> $list,
			'wid'	=> $wid,
		]);
	}

	/**
	 * 预约详情页
	 * @param  [type] $wid [description]
	 * @param  [type] $id  [description]
	 * @return [type]      [description]
	 * @update 吴晓平 增加详情的分享 2018年07月06日
	 */
	public function detail($wid,$id,Request $request)
	{
		$mid = session('mid');
		$bookService = new BookService();
		$usersBookService = new UsersBookService();
		$bookDatas = $bookService->getRowById($id);
		if (empty($bookDatas)) {
			error('该预约不存在或已被删除');
		}
		$whereData['book_id'] = $id;
		$whereData['mid'] = $mid;
		$whereData['is_delete'] = 0;
		$userBookDatas = $usersBookService->getAllList($wid,$whereData,'',1,false);
		//数据处理 
		$statistics = $usersBookService->statistics($wid,$mid,$bookDatas['id']);
		if ($request->isMethod('post')) {
			$input = $request->input();
			//限制重复提交问题
			if ($userBookDatas) {
				error('您已预约成功，不能重复提交');
			}
			//预约限制
			if ($bookDatas['limit_type'] == 0) {
				if ($bookDatas['end_time'] < time()) {
					error('抱歉'.$bookDatas['title'].'预约已结束，不能再进行预约');
				}
			}else if ($bookDatas['limit_type'] == 1) {
				$newStatistics = $usersBookService->statistics($wid,0,$bookDatas['id'],$input['book_date']);
                if ($bookDatas['limit_num']) {
                    if ($newStatistics['currentTotal'] == $bookDatas['limit_num']) {
                        error('抱歉' . $bookDatas['title'] . '当天预约人数已满，请改天再试');
                    }
                }
			}else if ($bookDatas['limit_type'] == 2) {
				$newStatistics = $usersBookService->statistics($wid,0,$bookDatas['id']);
                if ($bookDatas['limit_total']) {
					if ($newStatistics['bookTotal'] == $bookDatas['limit_total']) {
						error('抱歉'.$bookDatas['title'].'预约人数已满，请预约类似的主题');
					}
                }
			}
			$data['mid'] = $mid;
			$data['wid'] = $wid;
			$data['book_id'] = $id;
			$data['form_content'] = isset($input['content']) ? json_encode($input['content'],JSON_UNESCAPED_UNICODE) : '';
			$data['book_date'] = $input['book_date'];
			$data['book_time'] = $input['book_time'];
			$data['remark']    = $input['remark'];
			$rs = $usersBookService->add($data);
			if ($rs) {
			    //预约模板消息 add MayJay
			    $jobData = [
			        'mid'       => $mid,
                    'book_id'   => $id,
                    'book_time' => $input['book_date'].'   ' .$input['book_time'],
					'book_type' => 'new_mic_subscribe'
                ];
				(new MessagePushModule($wid, MessagesPushService::ActivityBook))->sendMsg($jobData);
			    //end
				success('预约成功');
			}
			error('预约失败');
		}
		
		$returnData = $formDataContent = [];
		$formData = json_decode($bookDatas['content'],true);
		if ($formData) {
			foreach ($formData as &$items) {
				if ($items['itype'] == 'select') {
					$items['option'] = explode(',',$items['ival']);
				}
			}
		}
		
		$book_date = $book_time = $remark = '';
		if ($userBookDatas) {
			$book_date = $userBookDatas[0]['book_date'] ?? '';
			$book_time = $userBookDatas[0]['book_time'] ?? '';
			$remark    = $userBookDatas[0]['remark'] ?? '';
			$formDataContent = json_decode($userBookDatas[0]['form_content'],true);
			if ($formDataContent) {
				foreach ($formData as &$value) {
					foreach ($formDataContent as $item) {
						if ($value['ikey'] == $item['ykey']) {
							$value['icontent'] = $item['yval'];
						}
					}
				}
			}
			
		}

		$returnData['id']		 = $bookDatas['id'];
		$returnData['title']     = $bookDatas['title'];
 		$returnData['bookNum']   = $statistics['pendingTotal'];  //統計未處理的預約
		$returnData['formData']  = $formData;
		$returnData['detail']	 = $bookDatas['details'];
		$returnData['address']   = $bookDatas['address'];
		$returnData['phone']	 = $bookDatas['phone'];
		$returnData['book_date'] = $book_date;
		$returnData['book_time'] = $book_time;
		$returnData['remark']	 = $remark;
		$returnData['banner_img'] = $bookDatas['banner_img'];
		$returnData['details']   = $bookDatas['details'];
		$returnData['limit_type'] = $bookDatas['limit_type'];
		$returnData['start_time'] = $bookDatas['start_time'] ? date('Y-m-d',$bookDatas['start_time']) : 0;
		$returnData['end_time']   = $bookDatas['end_time'] ? date('Y-m-d',$bookDatas['end_time']) : 0;
		if ($bookDatas['limit_type'] == 0) {
			$returnData['limit_text'] = '预约时间：'.date('Y-m-d',$bookDatas['start_time']).'--'.date('Y-m-d',$bookDatas['end_time']);
		}else if ($bookDatas['limit_type'] == 1) {
			if ($bookDatas['limit_num'] == 0) {
				$returnData['limit_text'] = '预约限定：不限制';
			}else {
				$returnData['limit_text'] = '预约限定：每日最多限制'.$bookDatas['limit_num'].'个预约';
			}
		}else if ($bookDatas['limit_type'] == 2) {
			if ($bookDatas['limit_total'] == 0) {
				$returnData['limit_text'] = '预约限定：不限制';
			}else {
				$returnData['limit_text'] = '预约限定：全部最多限制'.$bookDatas['limit_total'].'个预约';
			}	
		}

		/**预约详情页增加分享内容 update by 吴晓平 2018.07.06**/
		$ahareImg = $data['banner_img'] ?? config('app.source_url').'shop/images/book_detail.png';
		$shareData = ['share_title' => $bookDatas['title'] ?? '预约详情','share_desc' => '','share_img' => $ahareImg];
		return view('shop.book.detail',[
			'title'         => $bookDatas['title'] ?? '预约详情',
			'data'          => $returnData,
			'formData'      => $formData,
			'wid'           => $wid,
			'userBookDatas' => $userBookDatas,
			'shareData'		=> $shareData
		]);
	}

	/**
	 * [userBookList description]
	 * @return [type] [description]
	 */
	public function userBookList($wid,$id)
	{
		$mid = session('mid');
		$usersBookService = new UsersBookService();
		$bookService = new BookService();
		$bookDatas = $bookService->getRowById($id);
		$whereData['mid'] = $mid;
		$whereData['book_id'] = $id;
		$whereData['is_delete'] = 0;
		list($list,$pageHtml) = $usersBookService->getAllList($wid,$whereData);
		//数据处理
		foreach ($list['data'] as $key => &$value) {
			$value['form_content'] = json_decode($value['form_content'],true);
		}
		return view('shop.book.bookList',[
			'title'     => '我的预约',
			'list'      => $list,
			'wid'       => $wid,
			'bookId'    => $id,
			'bookDatas' => $bookDatas
		]);
	}

	/**
	 * 用户删除预约
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function userBookDel(Request $request)
	{
		$id = $request->input('id') ?? 0;
		if (!$id) {
			error('数据异常');
		}
		$usersBookService = new UsersBookService();
		$res = $usersBookService->update($id,['is_delete'=>1]);
		if ($res) {
			success('删除成功');
		}
		error('删除失败');
	}

	/**
	 * 预约修改
	 * [int] $id [用户预约表主键id]
	 * @return [type] [description]
	 */
	public function bookSave($id,Request $request)
	{
		$usersBookService = new UsersBookService();
		$mid = session('mid');
		$userBookDatas = $usersBookService->getRowById($id);
		if (empty($userBookDatas)) {
			error('数据异常');
		}
		$bookService = new BookService();
		$bookDatas = $bookService->getRowById($userBookDatas['book_id']);
		$formContent = [];
		if ($bookDatas['content']) {
			$formContent = json_decode($bookDatas['content'],true);
			if ($formContent) {
				foreach ($formContent as &$items) {
					if ($items['itype'] == 'select') {
						$items['option'] = explode(',',$items['ival']);
					}
				}
			}
			
		}
		$formData = $timeFormat = [];
		if ($userBookDatas['form_content']) {
			$formData = json_decode($userBookDatas['form_content'],true);
			if ($formData) {
				foreach ($formData as $key => &$value) {
					foreach ($formContent as $fkey => $fvalue) {
						if ($value['ykey'] == $fvalue['ikey'] && $fvalue['itype'] == 'select') {
							$value['option'] = $fvalue['option'];
						}
					}
				}
			}
		}

		//提交修改
		if ($request->isMethod('post')) {
			$input = $request->input();

			if ($userBookDatas) {
				$data['form_content'] = isset($input['content']) ? json_encode($input['content']) : '';
				$data['remark']       = $input['remark'] ?? '';
				$data['book_date']    = $input['book_date'] ?? '';
				$data['book_time']    = $input['book_time'] ?? '';
				$result = $usersBookService->update($id,$data);
				if ($result) {
					success();
				}
				error();
			}
		}
		return view('shop.book.bookSave',[
			'title'         => '我的预约修改',
			'formData'      => $formData,
			'userBookDatas' => $userBookDatas,
			'bookDatas'     => $bookDatas
		]);
	}
	
	
	  /**
     * 预约数据接口
     */
	public function getBookListApi(Request $request)
	{
		$wid = session('wid');
		$page = $request->input('page') ?? 1;
        $bookService = new BookService();
        list($data,$page) = $bookService->getAllList($wid);
        success('','',$data['data']);
	}

}



 ?>
