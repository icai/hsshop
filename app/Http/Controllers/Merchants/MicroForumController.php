<?php
/**
 * 微论坛PC端
 *
 * @author mfd
 */

namespace App\Http\Controllers\Merchants;

use App\Model\MicroForumForum;
use App\S\MicroForumService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 

class MicroForumController extends Controller
{
	/**
	 * 论坛设置列表页
	 *
	 * @author mafanding
	**/
    public function settingsList(Request $request)
    {
        $wid = session('wid');
        if(!$wid){
            error('来源错误');
        }
        $forumService = new MicroForumService('forum');
        $forumData = $forumService->getForumByWid($wid);
        return view('merchants.microforum.settings_list', [
            'title' => '社区设置列表',
            'leftNav' => 'marketing',
            'slidebar' => 'settingsList',
            'forumData' => $forumData,
        ]);
    }

	/**
	 * 论坛设置保存
	 *
	 * @author mafanding
     * @update 张永辉 2019年9月24日 简介可以为空
	**/
    public function settingsListed(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'imgid' => 'required',
            'title' => 'required',
        ], [
            'imgid.required' => '图片路径不能为空',
            'title.required' => '标题不能为空',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $wid = session('wid');
        if(!$wid){
            error('来源错误');
        }
        $forumService = new MicroForumService('forum');
        $forumData = $forumService->getForumByWid($wid);
		DB::beginTransaction();
		try {
				if (is_null($forumData)) {
					$createData['wid'] = $wid;
					$createData['title'] = $inputs['title'];
					$createData['introduction'] = $inputs['introduction'] ?? '';
					$createData['imgid'] = $inputs['imgid'];
					$newForum = $forumService->forumCreate($createData);
					if (is_null($newForum)) {
						DB::rollback();
						error('保存失败');
					}
					$result = $forumService->discussionCreate(['forum_id' => $newForum->id, 'title' => '首页', 'sort' => 10]);
					if (is_null($result)) {
						DB::rollback();
						error('保存失败');
					}
					$result = $forumService->discussionCreate(['forum_id' => $newForum->id, 'title' => '推荐', 'sort' => 20]);
					if (is_null($result)) {
						DB::rollback();
						error('保存失败');
					}
					$result = $forumService->discussionCreate(['forum_id' => $newForum->id, 'title' => '话题', 'sort' => 30]);
				} else {
					$data['imgid'] = $inputs['imgid'];
					$data['title'] = $inputs['title'];
					$data['introduction'] = $inputs['introduction'] ?? '';
					$where['wid'] = $wid;
					$result = $forumService->updateForum($where, $data);
				}
				if ($result === false || is_null($result)) {
					DB::rollback();
					error('保存失败');
				} else {
					DB::commit();
					success('保存成功');
				}
		} catch (\Exception $e) {
			DB::rollback();
			error('保存失败');	
		}
    }


	/**
	 * 帖子列表页
	 *
	 * @author mafanding
	**/
    public function postsList(Request $request)
    {
        $inputs = $request->all();
        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
		$where = [];
		if (isset($inputs['title']) && !empty($inputs['title'])) {
			$where['title'] = $inputs['title'];
		}
		if (isset($inputs['discussions_id']) && $inputs['discussions_id'] > 0) {
			$where['discussions_id'] = $inputs['discussions_id'];
		}
		if (isset($inputs['is_top']) && $inputs['is_top'] != 2) {
			$where['is_top'] = $inputs['is_top'];
		}
		if (isset($inputs['nickname']) && !empty($inputs['nickname'])) {
			if ($inputs['nickname'] == '坛主') {
				$where['id_type'] = 1;
			} else {
				$uids = $forumService->getMemberIdByNickname($inputs['nickname'])->toArray();
				if (!empty($uids)) {
					$where['user_id'] = ['in', $uids];
					$where['id_type'] = 0;
				}
			}
		}
		$search = $where;
		if ((isset($inputs['start_time']) && !empty($inputs['start_time'])) || (isset($inputs['end_time']) && !empty($inputs['end_time']))) {
			if ($inputs['start_time'] && $inputs['end_time']){
				$where['created_at'] = ['between', [$inputs['start_time'], $inputs['end_time']]];
			} elseif ($inputs['start_time']) {
				$where['created_at'] = ['>=', $inputs['start_time']];
			} else {
				$where['created_at'] = ['<=', $inputs['end_time']];
			}
		}
        $where['forum_id'] = $forumInstance->id;
        list($list, $pageHtml) = $forumService->setModel('post')->getListWithPage($where);
		$categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
        $uids = array_column($list['data'], 'user_id');
        $memberInfo = [];
        if (!empty($uids)) {
            $memberInfo = $forumService->getMemberByIds($uids)->toArray();
        }
		$ids = array_column($list['data'], 'id');
		if (!empty($ids)) {
			$repliesCount = $forumService->buildAssociativeArray($forumService->replyMultCounts('posts_id', ['posts_id' => ['in', $ids]])->toArray(), 'posts_id');	
			$favorsCount = $forumService->buildAssociativeArray($forumService->favorMultCounts('posts_id', ['posts_id' => ['in', $ids]])->toArray(), 'posts_id');
		}
        $discussionsInfo = $forumService->getDiscussionByFid($where['forum_id'])->toArray();
        foreach ($list['data'] as &$v) {
            $v['nickname'] = '';
			$v['favorCount'] = $v['replyCount'] = 0;
            foreach ($memberInfo as $item) {
                if ($v['id_type'] == 0 && $item['id'] == $v['user_id']) {
                    $v['nickname'] = $item['nickname'];
                }
            }
            foreach ($discussionsInfo as $item) {
                if ($item['id'] == $v['discussions_id']) {
                    $v['discussions_id'] = $item['title'];
                }
            }
            if ($v['id_type'] == 1) {
                $v['nickname'] = '坛主';
            }
			isset($favorsCount[$v['id']]) && $v['favorCount'] = $favorsCount[$v['id']]['num'];
			isset($repliesCount[$v['id']]) && $v['replyCount'] = $repliesCount[$v['id']]['num'];
        }
		$search['nickname'] = $request->has('nickname') ? $inputs['nickname'] : '';
		$search['start_time'] = $request->has('start_time') ? $inputs['start_time'] : '';
		$search['end_time'] = $request->has('end_time') ? $inputs['end_time'] : '';
        return view('merchants.microforum.posts_list', [
            'title' => '帖子管理列表',
            'leftNav' => 'marketing',
            'slidebar' => 'postsList',
            'list'       => $list['data'],
            'pageHtml'   => $pageHtml,
			'search' => $search,
			'categoriesDatas' => $categoriesDatas,
        ]);
    }

	/**
	 * 忒之置顶
	 *
	 * @author mafanding
	**/
    public function postsTopped(Request $request)
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
        $result = $forumService->toppedPost($forumInstance->id, $inputs['id']);
        if ($result === false) {
            error('置顶失败');
        } else {
            success('置顶成功');
        }
    }

	/**
	 * 帖子取消置顶
	 *
	 * @author mafanding
	**/
    public function postsUntopped(Request $request)
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
        $result = $forumService->untoppedPost($inputs['id']);
        if ($result === false) {
            error('取消置顶失败');
        } else {
            success('取消置顶成功');
        }
    }

	/**
	 * 帖子删除
	 *
	 * @author mafanding
	**/
    public function postsDeleted(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required',
        ], [
            'id.required' => '主键ID不能为空',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
		DB::beginTransaction();
		try {
			if (is_array($inputs['id'])) {
				$result = $forumService->postDelete(['id' => ['in', $inputs['id']]]);
			} else {
				$result = $forumService->postDelete(['id' => $inputs['id']]);
			}
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

	/**
	 * 帖子发布页
	 *
	 * @author mafanding
	**/
    public function postsRelease(Request $request)
    {
		$inputs = $request->all();
        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
		$categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
        return view('merchants.microforum.posts_release_or_edit', [
            'title' => '帖子管理发布',
            'leftNav' => 'marketing',
            'slidebar' => 'postsList',
			'categoriesDatas' => $categoriesDatas,
        ]);
    }

	/**
	 * 帖子发布保存
	 *
	 * @author mafanding
	**/
    public function postsReleased(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'discussions_id' => 'required|numeric',
            'title' => 'required|string',
            'imgids' => 'required_without:content|string',
            'content' => 'required_without:imgids|string',
        ], [
            'discussions_id.required' => '分类ID不能为空',
            'discussions_id.numeric' => '分类ID必须是数字',
            'title.required' => '标题不能为空',
            'title.string' => '标题必须是字符串',
            'imgids.required_without' => '图片路径不能为空',
            'imgids.string' => '图片路径必须是字符串',
            'content.required_without' => '内容不能为空',
            'content.string' => '内容必须是字符串',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
        $datas['title'] = $inputs['title'];
        $datas['discussions_id'] = $inputs['discussions_id'];
        $datas['imgids'] = $inputs['imgids'];
        $datas['content'] = $inputs['content'];
        $datas['id_type'] = 1;
        $datas['user_id'] = $forumInstance->id;
        $datas['forum_id'] = $forumInstance->id;
        $result = $forumService->postCreate($datas);
        if (is_null($result)) {
            error('发布失败');
        } else {
            success('发布成功');
        }
    }

	/**
	 * 帖子编辑页
	 *
	 * @author mafanding
	**/
    public function postsEdit(Request $request, $pid)
	{
		$inputs = $request->all();
		$forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
		$categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
        $postsInfo = $forumService->getPostById($pid);
        return view('merchants.microforum.posts_release_or_edit', [
            'title' => '帖子管理编辑',
            'leftNav' => 'marketing',
            'slidebar' => 'postsList',
			'categoriesDatas' => $categoriesDatas,
            'postsInfo' => $postsInfo,
        ]);
    }

	/**
	 * 帖子编辑保存
	 *
	 * @author mafanding
	**/
    public function postsEdited(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required|numeric',
            'discussions_id' => 'required|numeric',
            'title' => 'required|string',
            'imgids' => 'required_without|string',
            'content' => 'required_without|string',
        ], [
            'id.required' => '主键ID不能为空',
            'id.numeric' => '主键ID必须是数字',
            'discussions_id.required' => '分类ID不能为空',
            'discussions_id.numeric' => '分类ID必须是数字',
            'title.required' => '标题不能为空',
            'title.string' => '标题必须是字符串',
            'imgids.required_without' => '图片路径不能为空',
            'imgids.string' => '图片路径必须是字符串',
            'content.required_without' => '内容不能为空',
            'content.string' => '内容必须是字符串',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
        $datas['title'] = $inputs['title'];
        $datas['discussions_id'] = $inputs['discussions_id'];
        $datas['imgids'] = $inputs['imgids'];
        $datas['content'] = $inputs['content'];
        $result = $forumService->postUpdate(['id' => $inputs['id']], $datas);
        if ($result === false) {
            error('编辑失败');
        } else {
            success('编辑成功');
        }
    }

	/**
	 * 评论列表页
	 *
	 * @author mafanding
	**/
    public function evaluatesList(Request $request, $pid)
    {
        $inputs = $request->all();
        $forumService = $inputs['_forumService'];
        $where['posts_id'] = $pid;
        list($list, $pageHtml) = $forumService->setModel('reply')->getListWithPage($where);
        $ids = array_column($list['data'], 'user_id');
        $memberInfo = [];
        if (!empty($ids)) {
            $memberInfo = $forumService->getMemberByIds($ids)->toArray();
        }
        foreach ($list['data'] as &$v) {
            $v['nickname'] = '';
            foreach ($memberInfo as $item) {
                if ($v['id_type'] == 0 && $item['id'] == $v['user_id']) {
                    $v['nickname'] = $item['nickname'];
                }
            }
            if ($v['id_type'] == 1) {
                $v['nickname'] = '坛主';
            }
        }
        return view('merchants.microforum.evaluates_list', [
            'title' => '评价管理列表',
            'leftNav' => 'marketing',
            'slidebar' => 'postsList',
            'list'       => $list['data'],
            'pageHtml'   => $pageHtml,
        ]);
    }

	/**
	 * 评论删除
	 *
	 * @author mafanding
	**/
    public function evaluatesDeleted(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required',
        ], [
            'id.required' => '主键ID不能为空',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

		$forumService = $inputs['_forumService'];
		DB::beginTransaction();
		try {
			if (is_array($inputs['id'])) {
				$result = $forumService->replyDelete(['id' => ['in', $inputs['id']]]);
			} else {
				$result = $forumService->replyDelete(['id' => $inputs['id']]);
			}
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

	public function evaluatesContent(Request $request)
	{
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required',
        ], [
            'id.required' => '主键ID不能为空',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

		$forumService = $inputs['_forumService'];
		$replyInfo = $forumService->getReplyById($inputs['id']);
		success('', '', is_null($replyInfo) ? '' : $replyInfo->content);
	}

	/**
	 * 分类列表页
	 *
	 * @author mafanding
	**/
    public function categoriesList(Request $request)
    {
        $forumService = $request->input('_forumService');
        $forumInstance = $request->input('_forumInstance');
        $categoriesDatas = $forumService->getDiscussionByFid($forumInstance->id);
        return view('merchants.microforum.categories_list', [
            'title' => '分类管理列表',
            'leftNav' => 'marketing',
            'slidebar' => 'categoriesList',
            'categoriesDatas' => $categoriesDatas,
        ]);
    }

	/**
	 * 分类新增页
	 *
	 * @author mafanding
	**/
    public function categoriesAdd(Request $request)
    {
        return view('merchants.microforum.categories_add_or_edit', [
            'title' => '分类管理新增',
            'leftNav' => 'marketing',
            'slidebar' => 'categoriesList',
        ]);
    }

	/**
	 * 分类新增保存
	 *
	 * @author mafanding
	**/
    public function categoriesAdded(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'sort' => 'required|numeric',
            'title' => 'required|max:3',
        ], [
            'sort.required' => '排序不能为空',
            'sort.numeric' => '排序必须是数字',
            'title.required' => '标题不能为空',
            'title.max' => '标题不能超过3个字',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
        $result = $forumService->discussionCreate(['forum_id' => $forumInstance->id, 'title' => $inputs['title'], 'sort' => $inputs['sort']]);
        if (is_null($result)) {
            return error('新增失败');
        } else {
            return success('新增成功');
        }
    }

	/**
	 * 分类编辑页
	 *
	 * @author mafanding
	**/
    public function categoriesEdit(Request $request, $id)
    {
		$forumService = $request->input('_forumService');
		$categoryInfo = $forumService->getdiscussionById($id);
        return view('merchants.microforum.categories_add_or_edit', [
            'title' => '分类管理编辑',
            'leftNav' => 'marketing',
            'slidebar' => 'categoriesList',
            'categoryInfo' => $categoryInfo,
        ]);
    }

	/**
	 * 分类编辑保存
	 *
	 * @author mafanding
	**/
    public function categoriesEdited(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required|numeric',
            'sort' => 'required|numeric',
            'title' => 'required|max:3',
        ], [
            'id.required' => '主键ID不能为空',
            'id.numeric' => '主键ID必须是数字',
            'sort.required' => '排序不能为空',
            'sort.numeric' => '排序必须是数字',
            'title.required' => '标题不能为空',
            'title.max' => '标题不能超过3个字',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
        $datas['sort'] = $inputs['sort'];
        $datas['title'] = $inputs['title'];
        $where['id'] = $inputs['id'];
        $where['forum_id'] = $forumInstance->id;
        $result = $forumService->discussionUpdate($where, $datas);
        if ($result === false) {
            return error('编辑失败');
        } else {
            return success('编辑成功');
        }
    }

	/**
	 * 分类删除
	 *
	 * @author mafanding
	**/
    public function categoriesDeleted(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'id' => 'required',
        ], [
            'id.required' => '主键ID不能为空',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
		if (is_array($inputs['id'])) {
			$result = $forumService->discussionDelete(['id' => ['in', $inputs['id']]]);
		} else {
			$result = $forumService->discussionDelete(['id' => $inputs['id']]);
		}
        if ($result === false) {
            return error('删除失败');
        } else {
            return success('删除成功');
        }
    }

	/**
	 * 用户列表页
	 *
	 * @author mafanding
	**/
    public function usersList(Request $request)
    {
        $inputs = $request->all();
        $where = [];
        isset($inputs['id']) && !empty($inputs['id']) && $where['id'] = $inputs['id'];
        if (!isset($inputs['is_block']) || $inputs['is_block'] == '2') {
            //do nothing
        } else {
            $where['is_block'] = $inputs['is_block'];
        }
		$search = $where;
        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
		if (isset($inputs['nickname']) && !empty($inputs['nickname'])) {
			$uids = $forumService->getMemberIdByNickname($inputs['nickname'])->toArray();
			if (!empty($uids)) {
				$where['member_id'] = ['in', $uids];
			}
		}
        $where['forum_id'] = $forumInstance->id;
        list($list, $pageHtml) = $forumService->setModel('userlist')->getListWithPage($where);
        $uids = array_column($list['data'], 'member_id');
        $postCounts = $memberInfo = [];
        if (!empty($uids)) {
            $memberInfo = $forumService->getMemberByIds($uids)->toArray();
			$postCounts = $forumService->buildAssociativeArray($forumService->postMultCounts('user_id', ['id_type' => 0, 'user_id' => ['in', $uids]])->toArray(), 'user_id');
        }
        foreach ($list['data'] as &$v) {
            if ($v['is_block'] == 0) {
                $v['is_block'] = '正常';
            } else {
                $v['is_block'] = '拉黑';
            }
            $v['nickname'] = '';
            $v['headimgurl'] = '';
            foreach ($memberInfo as $item) {
                if ($item['id'] == $v['member_id']) {
                    $v['nickname'] = $item['nickname'];
                    $v['headimgurl'] = $item['headimgurl'];
                }
            }
			$v['postCount'] = 0;
			isset($postCounts[$v['member_id']]) && $v['postCount'] = $postCounts[$v['member_id']]['num'];
        }
		$search['nickname'] = $request->has('nickname') ? $inputs['nickname'] : '';
        return view('merchants.microforum.users_list', [
            'title' => '用户管理列表',
            'leftNav' => 'marketing',
            'slidebar' => 'usersList',
            'list'       => $list['data'],
            'pageHtml'   => $pageHtml,
            'search' => $search,
        ]);
    }

	/**
	 * 用户拉黑
	 *
	 * @author mafanding
	**/
    public function usersBlocked(Request $request)
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
        $result = $forumService->blockedUser($inputs['id']);
        if ($result === false) {
            return error('拉黑失败');
        } else {
            return success('拉黑成功');
        }
    }

	/**
	 * 用户恢复
	 *
	 * @author mafanding
	**/
    public function usersUnblocked(Request $request)
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
        $result = $forumService->unblockedUser($inputs['id']);
        if ($result === false) {
            return error('恢复失败');
        } else {
            return success('恢复成功');
        }
    }

	/**
	 * 统计列表
	 *
	 * @author mafanding
	**/
    public function statisticsList(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'year' => 'numeric',
            'month' => 'numeric',
        ], [
            'year.numeric' => '年份必须是数字',
            'month.numeric' => '月份必须是数字',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }

        $forumService = $inputs['_forumService'];
        $forumInstance = $inputs['_forumInstance'];
		$year = $request->has('year') ? $inputs['year'] : date('Y');
		$month = $request->has('month') ? $inputs['month'] : date('m');
        $result = $forumService->getUserStatistics($forumInstance->id, $year, $month);
		success('', '', ['statisticsCounts' => $result]);
    }

    public function statisticsListView(Request $request)
	{
        return view('merchants.microforum.statistics_list', [
            'title' => '社区统计列表',
            'leftNav' => 'marketing',
            'slidebar' => 'statisticsList',
        ]);
	}
}
