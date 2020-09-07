<?php
/**
 * 微论坛移动端
 *
 * @author mfd
 */

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, DB;

class MicroForumController extends Controller
{
    public function userIndex(Request $request, $id_type, $user_id)
    {
    	$inputs = $request->all();
		$forumInstance = $inputs['_forumInstance'];
		$forumService = $inputs['_forumService'];
		$categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
		if ($id_type == 1) {
			$userInfo['nickname'] = '坛主';
			$userInfo['headimgurl'] = imgUrl() . $forumInstance->img_path;
			$userInfo['id'] = $forumInstance->id;
		} else {
			$userInfos = $forumService->getMemberByIds([$user_id])->toArray();
			$userInfo = $userInfos[0];
		}
        return view('shop.microforum.user_index', [
        		'categoriesDatas' => $categoriesDatas,	
				'userInfo' => $userInfo,
				'id_type' => $id_type,
				'title' => '用户主页',
        	]);
    }

    public function forumIndex(Request $request)
    {
		$inputs = $request->all();
		$forumInstance = $inputs['_forumInstance'];
		$forumService = $inputs['_forumService'];
		$discussionsInfo = $forumService->getDiscussionByFid($forumInstance->id)->toArray();
		$postsCount = $forumService->setModel('post')->count(['forum_id' => $forumInstance->id]);
		$usersCount = $forumService->setModel('userlist')->count(['forum_id' => $forumInstance->id, 'is_block' => 0]);
		$notificationCount = $forumService->notificationGetNotificationsCountByTid($inputs['_mid']);
		$forumService->forumIncrementViewCount($inputs['_wid']);
		$forumService->recordLoginLog($forumInstance->id, 1, $inputs['_mid']);
		return view('shop.microforum.forum_index', [
				'forumInfo' => ['title' => $forumInstance->title, 'img_path' => $forumInstance->img_path, 'postsCount' => $postsCount, 'usersCount' => $usersCount, 'viewsCount' => $forumInstance->view_count],
				'discussionsInfo' => json_encode($discussionsInfo),
				'notificationCount' => $notificationCount,
				'title' => '论坛主页',
		]);
    }

	public function postsList(Request $request)
	{
		$inputs = $request->all();	
		$validator = Validator::make($inputs, [
				'discussions_id' => 'required|numeric',
				'user_id' => 'numeric',
				'id_type' => 'numeric',
		], [
				'discussions_id.required' => '分类ID不能为空',
				'discussions_id.numeric' => '分类ID必须是数字',
				'user_id.numeric' => '用户ID必须是数字',
				'id_type.numeric' => 'ID类型必须是数字',
		]);
		if ($validator->fails()) {
			error($validator->messages()->first());
		}

		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		$where['discussions_id'] = $inputs['discussions_id'];
		$where['forum_id'] = $forumInstance->id;
		if ($request->has('user_id')) {
			$where['user_id'] = $inputs['user_id'];
			$where['id_type'] = $request->input('id_type', 0);
		}
		list($list, $pageHtml) = $forumService->setModel('post')->getListWithPage($where, 'is_top', 'desc,id desc');	
		$list['data'] = $forumService->groupPostExtraInfos($list['data']);
		$ids = array_column($list['data'], 'id');
		if (!empty($ids)) {
			$favorsInfo = $forumService->favorGetFavorsByPidsAndUids($ids, [$inputs['_mid']]);
			$favorsInfo = array_column($favorsInfo, 'posts_id');
		}
		foreach ($list['data'] as &$v) {
			if (in_array($v['id'], $favorsInfo)) {
				$v['isFavors'] = 1;	
			}
			if ($v['id_type'] == 1) {
				$v['headimgurl'] = $forumInstance->img_path;
			}
			$v['is_my'] = 0;
			$v['id_type'] == 0 && $v['user_id'] == $inputs['_mid'] && $v['is_my'] = 1;
			$v['created_at'] = substr($v['created_at'], 0, -3);
		}
		success('', '', $list);
	}

	public function notificationList(Request $request)
	{
		$inputs = $request->all();
		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		$where['forum_id'] = $forumInstance->id;
		$where['to_type'] = 0;
		$where['to_id'] = $inputs['_mid'];
		list($list, $pageHtml) = $forumService->setModel('notification')->getListWithPage($where);
		foreach ($list['data'] as &$v) {
			if ($v['event_type'] == 0) {
				//favor
				$favorInfo = $forumService->getFavorById($v['event_id']);
				$postInfo = $forumService->getPostById($favorInfo['posts_id']);
				$v['content'] = '赞了你的帖子';
				$v['title'] = $postInfo['title'];
				$v['pid'] = 0;
				$v['rid'] = 0;
			} elseif ($v['event_type'] == 1) {
				//reply
				$replyInfo = $forumService->getReplyById($v['event_id']);
				$parentReplyInfo = $forumService->getReplyById($replyInfo['parent_id'], true);
				$v['content'] = $replyInfo['content'];
				$v['title'] = '回复了我的评论：' . $parentReplyInfo['content'];
				$v['pid'] = $replyInfo['posts_id'];
				$v['rid'] = $v['event_id'];
			} else {
				//post
				$replyInfo = $forumService->getReplyById($v['event_id']);
				$postInfo = $forumService->getPostById($replyInfo['posts_id']);
				$v['content'] = $replyInfo['content'];
				$v['title'] = '回复了我的帖子：' . $postInfo['title'];
				$v['pid'] = $replyInfo['posts_id'];
				$v['rid'] = $v['event_id'];
			}
			if ($v['from_type'] == 1) {
				$v['nickname'] = '坛主';
				$v['headimgurl'] = imgUrl() . $forumInstance->img_path;
			} else {
				$userInfo = $forumService->getMemberByIds([$v['from_id']])->toArray()[0];
				$v['nickname'] = $userInfo['nickname'];
				$v['headimgurl'] = $userInfo['headimgurl'];
			}
		}
		success('', '', $list);
	}

	public function postsFavorsed(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'posts_id' => 'required|numeric',	
		], [
			'posts_id.required' => '帖子ID不能为空',
			'posts_id.numeric' => '帖子ID必须是数字',	
		]);	
		if ($validator->fails()) {
			error($validator->messages()->first());	
		}

		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		if ($forumService->checkIfBlocked($forumInstance->id, $inputs['_mid'])) {
			error('你已被拉黑');	
		}
		DB::beginTransaction();
		try {
			$result = $forumService->favoredPost($forumInstance->id, $inputs['posts_id'], $inputs['_mid']);
			if ($result === false) {
				DB::rollback();
				error('点赞失败');	
			}
			DB::commit();
			success('点赞成功');
		} catch (\Exception $e) {
			DB::rollback();	
			error('点赞失败');
		}
	}
	
	public function postsUnfavorsed(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'posts_id' => 'required|numeric',	
		], [
			'posts_id.required' => '帖子ID不能为空',
			'posts_id.numeric' => '帖子ID必须是数字',	
		]);	
		if ($validator->fails()) {
			error($validator->messages()->first());	
		}

		$forumService = $inputs['_forumService'];
		DB::beginTransaction();
		try {
			$result = $forumService->unfavoredPost($inputs['posts_id'], $inputs['_mid']);
			if ($result === false) {
				DB::rollback();
				error('取消点赞失败');
			}
			DB::commit();
			success('取消点赞成功');
		} catch (\Exception $e) {
			DB::rollback();	
			error('取消点赞失败');
		}
	}	

    public function postsDetail(Request $request, $pid)
    {
		$inputs = $request->all();
		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		$postsInfo = $forumService->getPostById($pid)->toArray();
		$repliesInfo = $forumService->getRepliesByPid($pid);
		foreach ($repliesInfo as &$v) {
			if ($v['id_type'] == 1) {
				$v['headimgurl'] = imgUrl() . $forumInstance->img_path;
			}
			if ($v['id_type'] == 0 && $v['user_id'] == $inputs['_mid']) {
				$v['is_owner'] = 1;
			} else {
				$v['is_owner'] = 0;
			}
		}
		if ($postsInfo['id_type'] == 1) {
			$userInfo['nickname'] = '坛主';
			$userInfo['headimgurl'] = imgUrl() . $forumInstance->img_path;
		} else {
			$userInfos = $forumService->getMemberByIds([$postsInfo['user_id']])->toArray();
			$userInfo = $userInfos[0];
		}
		$postsInfo['created_at'] = substr($postsInfo['created_at'], 0, -3);
		$postsInfo['favorCount'] = $forumService->favorCount(['posts_id' => $pid]);
		$userInfo['isFavored'] = $forumService->checkIfFavored($pid, $inputs['_mid'], 0);
		return view('shop.microforum.posts_detail', [
			'repliesInfo' => $repliesInfo,
			'postsInfo' => $postsInfo,
			'userInfo' => $userInfo,
			'title' => '帖子详情',
		]);
    }

	public function postsOwner(Request $request)
	{
		$inputs = $request->all();	
		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		$userInfos = $forumService->getMemberByIds([$inputs['_mid']])->toArray();
		$userInfo = $userInfos[0];
		$categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
		$notificationCount = $forumService->notificationCount(['forum_id' => $forumInstance->id, 'to_type' => 0, 'to_id' => $inputs['_mid']]);
		$postCount = $forumService->setModel('post')->count(['forum_id' => $forumInstance->id, 'id_type' => 0, 'user_id' => $inputs['_mid']]);
		return view('shop.microforum.posts_owner', [
			'notificationCount' => $notificationCount,	
			'postCount' => $postCount,
			'categoriesDatas' => $categoriesDatas,	
			'userInfo' => $userInfo,
			'title' => '我的',
		]);
	}

	public function postsRelease(Request $request)
	{ 
		$inputs = $request->all();	
		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		$categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
		return view('shop.microforum.posts_release', [
			'categoriesDatas' => $categoriesDatas,	
		]);
	}
	
	public function postsReleased(Request $request)
	{
		$inputs = $request->all();	
		$validator = Validator::make($inputs, [
			'discussions_id' => 'required|numeric',
			'title' => 'required|string',
			'content' => 'required_without:imgids|string',
			'imgids' => 'required_without:content|string',
		], [
			'discussions_id.required' => '分类ID不能为空',
			'discussions_id.numeric' => '分类ID必须是数字',
			'title.required' => '标题不能为空',
			'title.string' => '标题必须是字符串',
			'content.required_without' => '内容不能为空',
			'content.string' => '内容必须是字符串',
			'imgids.required_without' => '图片ID不能为空',
			'imgids.string' => '图片ID必须是字符串',
		]);
		if ($validator->fails()) {
			error($validator->messages()->first());
		}

		$forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		if ($forumService->checkIfBlocked($forumInstance->id, $inputs['_mid'])) {
			error('你已被拉黑');	
		}

		$mid = $inputs['_mid'];
		$wid = $inputs['_wid'];
        $datas['title'] = $inputs['title'];
        $datas['discussions_id'] = $inputs['discussions_id'];
        $datas['imgids'] = $inputs['imgids'];
        $datas['content'] = $inputs['content'];
        $datas['id_type'] = 0;
        $datas['user_id'] = $mid;
		$datas['forum_id'] = $forumInstance->id;

        $result = $forumService->postCreate($datas);
        if (is_null($result)) {
            error('发布失败');
        } else {
			$forumService->userlistCreateRelation($forumInstance->id, $mid);
			$forumService->recordReleasePostLog($forumInstance->id, $inputs['discussions_id'], $inputs['_mid']);
            success('发布成功');
        }
	}

	public function postsDeleted(Request $request)
	{
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required|numeric',
        ], [
            'id.required' => '主键ID不能为空',
            'id.numeric' => '主键ID必须是数字',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		DB::beginTransaction();
		try {
			$result = $forumService->postDelete(['id' => $inputs['id']]);
			if ($result === false) {
				DB::rollback();
				error('删除失败');
			}
			DB::commit();
			$forumService->recordDeletePostLog($forumInstance->id, 1, $inputs['_mid']);
			success('删除成功');
		} catch (\Exception $e) {
			DB::rollback();
            error('删除失败');
		}
	}

	public function postsReplies(Request $request)
	{
		$inputs = $request->all();	
		$data = [
				'pid' => $inputs['pid'],
				'name' => $inputs['name'],
		];
		if ($request->has('rid')) {
			$data['rid'] = $inputs['rid'];
		}
		return view('shop.microforum.posts_replies', $data);
	}
	
	public function postsRepliesed(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'pid' => 'required|numeric',
			'rid' => 'numeric',
			'content' => 'required|string',
		], [
			'pid.required' => '帖子ID不能为空',
			'pid.numeric' => '帖子ID必须是数字',
			'rid.numeric' => '回复ID必须是数字',
			'content.required' => '帖子内容不能为空',
			'content.string' => '帖子内容必须是字符串',
		]);
		if ($validator->fails()) {
			error($validator->messages()->first());
		}

		$createData['posts_id'] = $inputs['pid'];
		$createData['parent_id'] = $request->input('rid', 0);
		$createData['content'] = $inputs['content'];
		$createData['id_type'] = 0;
		$createData['user_id'] = $inputs['_mid'];
        $forumService = $inputs['_forumService'];
		$forumInstance = $inputs['_forumInstance'];
		if ($forumService->checkIfBlocked($forumInstance->id, $inputs['_mid'])) {
			error('你已被拉黑');	
		}
		DB::beginTransaction();
		try {
			$result = $forumService->replyCreate($createData);
			if (is_null($result)) {
				DB::rollback();	
				error('回复失败');
			}
			$notificationCreateData = [
				'forum_id' => $forumInstance->id,
				'event_id' => $result->id,
				'from_type' => $result->id_type,
				'from_id' => $result->user_id,
			];
			if ($result->parent_id == 0) {
				$postInfo = $forumService->getPostById($result->posts_id);
				$notificationCreateData['event_type'] = 2;
				$notificationCreateData['to_type'] = $postInfo->id_type;
				$notificationCreateData['to_id'] = $postInfo->user_id;
			} else {
				$replyInfo = $forumService->getReplyById($result->parent_id);
				$notificationCreateData['event_type'] = 1;
				$notificationCreateData['to_type'] = $replyInfo ->id_type;
				$notificationCreateData['to_id'] = $replyInfo->user_id;
			}
			$forumService->notificationCreate($notificationCreateData);
			DB::commit();
			success('回复成功');
		} catch (\Exception $e) {
			DB::rollback();	
			error('回复失败');
		}
	}

	public function repliesDeleted(Request $request)
	{
		$inputs = $request->all();		
		$validator = Validator::make($inputs, [
			'id' => 'required|numeric',
		], [
			'id.required' => '主键ID不能为空',
			'id.numeric' => '主键ID必须是数字',
		]);
		if ($validator->fails()) {
			error($validator->messages()->first());
		}

        $forumService = $inputs['_forumService'];
		$where['id'] = $inputs['id'];
		$where['id_type'] = 0;
		$where['user_id'] = $inputs['_mid'];
		DB::beginTransaction();
		try {
			$result = $forumService->replyDelete($where);
			if ($result === false) {
				DB::rollback();
				error('删除失败');
			}
			DB::commit();
			success('删除成功');
		} catch (\Exception $e) {
			DB::rollback();
            error('删除失败');
		}
	}
}
