<?php
/**
 * Created by PhpStorm.
 * User: johnson
 * Date: 8/2/17
 * Time: 4:47 PM
 */

namespace App\S;

use App\Lib\Redis\MicroForumRedis;
use App\Model\Member;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Model\FileInfo;
use DB;

class MicroForumService extends S{
	 public $perPage = 10;
    /**
     * @var array
     */
    protected $models = [
        'forum' => \App\Model\MicroForumForum::class,
        'favor' => \App\Model\MicroForumFavor::class,
        'userlist' => \App\Model\MicroForumUserlist::class,
        'post' => \App\Model\MicroForumPost::class,
        'reply' => \App\Model\MicroForumReply::class,
        'discussion' => \App\Model\MicroForumDiscussion::class,
        'notification' => \App\Model\MicroForumNotification::class,
        'log' => \App\Model\MicroForumLog::class,
    ];

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var \Illuminate\Http\Request|null
     */
    protected $request = null;

    /**
     * @var null|\Illuminate\Database\Eloquent\Model
     */
    public $model = null;

    public function __construct($model = null)
    {
        $this->request = app('request');
        if (! is_null($model)) {
            if (!array_key_exists($model, $this->models)) {
                throw new ModelNotFoundException("Illegal model name {$model}");
            }
            $this->model = new $this->models[$model];
            $this->instances[$model] = $this->model;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel($model)
    {
        if (!array_key_exists($model, $this->models)) {
            throw new \RuntimeException("Undefined model '{$model}'");
        }
        if (!isset($this->instances[$model])) {
            $this->instances[$model]= new $this->models[$model];
        }
        $this->model = $this->instances[$model];
        return $this;
    }

    /**
     * @param string $model
     * @return \Illuminate\Database\Eloquent\Model|null
     */
	public function getInstance($model)
	{
		return $this->setModel($model)->getModel();
	}

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        list($model, $method) = $this->split($name);
        if (empty($method)) {
            throw new \RuntimeException("Method could't be empty");
        }
        if (empty($model) && !is_null($this->model) && method_exists($this->model, $method)) {
            /**
             * $name为全小写的情况
             */
            return call_user_func_array([$this->model, $method], $arguments);
        }
        if (array_key_exists($model, $this->models)) {
            /**
             * $model存在对应的model
             */
            if (!isset($this->instances[$model])) {
                $this->model = new $this->models[$model];
                $this->instances[$model] = $this->model;
            }
            if (method_exists($this->instances[$model], $method)) {
                return call_user_func_array([$this->instances[$model], $method], $arguments);
            }
        }
        if (!is_null($this->model) && method_exists($this->model, $name)) {
            /**
             * $model不存在对应的model
             */
            return call_user_func_array([$this->model, $name], $arguments);
        }
        throw new \RuntimeException("Undefined model '{$model}' or undefined method '{$method}'");
    }

    /**
     * @param string $inaccessibleMethod
     * @return array
     */
    protected function split($inaccessibleMethod)
    {
        $lowerCase = strtolower($inaccessibleMethod);
        $count = strlen($lowerCase);
        for ($i = 0; $i < $count; $i ++) {
            if ($lowerCase{$i} !== $inaccessibleMethod{$i}) {
                break;
            }
        }
        return [substr($inaccessibleMethod, 0, $i), lcfirst(substr($inaccessibleMethod ,$i))];
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function count($where = [])
    {
        if (is_null($this->model)) {
            throw new \RuntimeException("model is null");
        }
        return parent::count($where);
    }

    /**
     * @param array $where
     * @param string $skip
     * @param string $perPage
     * @param string $orderBy
     * @param string $order
     * @return mixed
     */
    protected function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        if (is_null($this->model)) {
            throw new \RuntimeException("model is null");
        }
        return parent::getList($where, $skip, $perPage, $orderBy, $order);
    }

    /**
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @param int $pageSize
     * @return array
     */
    public function getListWithPage($where = [], $orderBy = '', $order = '', $pageSize = 0)
    {
        if (is_null($this->model)) {
            throw new \RuntimeException("model is null");
        }
        return parent::getListWithPage($where, $orderBy, $order, $pageSize);
    }

    protected function getListById(array $idArr)
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new MicroForumRedis($this->model->getRedisKey());
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

	/**
	 * Get Methods
	 */
	/**
	 * @param array $ids
	 * @return \Illuminate\Support\Collection
	 **/
    public function getMemberByIds(array $ids)
    {
        return Member::whereIn('id', $ids)->get(['id', 'nickname', 'headimgurl']);
    }

	/**
	 * @param string $nickname
	 * @return \Illuminate\Support\Collection
	 **/
	public function getMemberIdByNickname($nickname)
	{
		return Member::where('nickname', $nickname)->get(['id']);
	}

	/**
	 * @param array $ids
	 * @return \Illuminate\Support\Collection
	 **/
	public function getFileInfoByIds(array $ids)
	{
		return FileInfo::whereIn('id', $ids)->get();
	}
	
	public function getForumByWid($wid)
	{
        $forumInfo =  $this->getInstance('forum')->where('wid', $wid)->first(['id', 'title', 'introduction', 'view_count', 'imgid']);
		if (!is_null($forumInfo)) {
			$imgInfo = $this->getFileInfoByIds([$forumInfo->imgid])->toArray();
			$forumInfo->img_path = empty($imgInfo) ? '' : $imgInfo[0]['path'];
		}
		return $forumInfo;
	}

	public function getDiscussionById($id)
	{
		return $this->getInstance('discussion')->find($id);
	}
	
	/**
	 * @param int $fid
	 * @return \Illuminate\Support\Collection
	 **/
	public function getDiscussionByFid($fid)
	{
		return $this->getInstance('discussion')->where('forum_id', $fid)->orderBy('sort', 'desc')->get();
	}

	public function getPostById($id)
	{
		$postInfo = $this->getInstance('post')->find($id);
		!is_null($postInfo) && $postInfo->img_paths = [];
		if (!is_null($postInfo) && !empty($postInfo->imgids)) {
			$imgInfos = $this->getFileInfoByIds(explode(',', $postInfo->imgids));
			foreach ($imgInfos as &$imgInfo) {
				if (empty($imgInfo['img_size'])) {
					$imgInfo['img_size'] = '720x1280';
				} else {
					$tempSize = explode('x', $imgInfo['img_size']);
					$tempSize[0] = $tempSize[0] * config('filesystems.image.l')/100;
					$tempSize[1] = $tempSize[1] * config('filesystems.image.l')/100;
					$imgInfo['img_size'] = implode('x', $tempSize);
				}
			}
			$postInfo->img_paths = $imgInfos;
		}
		return $postInfo;
	}

	public function getReplyById($id, $withTrashed = false)
	{
		if ($withTrashed) {
			return $this->getInstance('reply')->withTrashed()->find($id);
		}
		return $this->getInstance('reply')->find($id);
	}

	public function getFavorById($id)
	{
		return $this->getInstance('favor')->find($id);
	}

	public function getRepliesByPid($pid)
	{
		$replyInfos = $this->getInstance('reply')->where('posts_id', $pid)->get()->toArray();
		$uids = [];
		array_walk($replyInfos , function ($value) use (&$uids){
			if ($value['id_type'] == 0) {
				$uids[] = $value['user_id'];
			}	
		});
		if (!empty($uids)) {
			$userInfos = $this->getMemberByIds($uids)->toArray();
		}
		foreach ($replyInfos as &$replyInfo) {
			$replyInfo['parent_user_name'] = $replyInfo['nickname'] = $replyInfo['headimgurl'] = '';
			if ($replyInfo['id_type'] == 0) {
				foreach ($userInfos as $v) {
					if ($replyInfo['user_id'] == $v['id']) {
						$replyInfo['nickname'] = $v['nickname'];
						$replyInfo['headimgurl'] = $v['headimgurl'];
					}
				}
			} else {
				$replyInfo['nickname'] = '坛主';
			}
			$replyInfo['created_at'] = substr($replyInfo['created_at'], 0, -3);
			if ($replyInfo['parent_id'] > 0) {
				$parentReplyInfo = $this->getReplyById($replyInfo['parent_id'], true);
				if ($parentReplyInfo['id_type'] == 1){
					$replyInfo['parent_user_name'] = '坛主';
				} else {
					$memberInfos = $this->getMemberByIds([$parentReplyInfo['user_id']])->toArray();
					$replyInfo['parent_user_name'] = isset($memberInfos[0]) ? $memberInfos[0]['nickname'] : '';
				}
			}
		}
		return $replyInfos;
	}

	public function getFaovrByIds(array $ids)
	{
		$favorInfos = $this->getInstance('favor')->whereIn('id', $ids)->get()->toArray();
		foreach ($favorInfos as &$favorInfo) {
			$favorInfo['nickname'] = $favorInfos['headimgurl'] = '';	
			if ($favorInfo['id_type'] == 0) {
				$userInfo = $this->getMemberByIds([$favorInfo['user_id']])->toArray();
				$favorInfo['nickname'] = $userInfo['nickname'];
				$favorInfo['headimgurl'] = $userInfo['headimgurl'];
			} else {
				$favorInfo['nickname'] = '坛主';
			}
			$favorInfo['content'] = '赞了你的帖子';
			$postInfo = $this->getPostById($favorInfo['posts_id']);
			$favorInfo['title'] = $postInfo['title'];
		}
		return $favorInfos;
	}
	
	protected function basicFind($model, $primaryKey)
	{
		return $this->getInstance($model)->find($primaryKey);
	}

	protected function basicGet($model, array $where)
	{
		return $this->getInstance($model)->wheres($where)->get();
	}

	public function discussionCount($where)
	{
		return $this->basicCount('discussion', $where);
	}

	public function notificationCount($where)
	{
		return $this->basicCount('notification', $where);
	}

	public function favorCount($where)
	{
		return $this->basicCount('favor', $where);
	}

	protected function basicCount($model, $where)
	{
		return $this->getInstance($model)->wheres($where)->count();
	}

	public function replyMultCounts($groupKey, $where)
	{
		return $this->basicMultCounts('reply', $groupKey, $where);
	}

	public function favorMultCounts($groupKey, $where)
	{
		return $this->basicMultCounts('favor', $groupKey, $where);
	}

	public function postMultCounts($groupKey, $where)
	{
		return $this->basicMultCounts('post', $groupKey, $where);
	}

	protected function basicMultCounts($model, $groupKey, $where)
	{
		return $this->getInstance($model)->select($groupKey, DB::raw('count(*) as num'))->wheres($where)->groupBy($groupKey)->get();
	}

	/**
	 * Delete Methods
	 */
	public function postDelete(array $where)
	{
		$postInfos = $this->getInstance('post')->wheres($where)->get()->toArray();
		$postDeleteResult = $this->getInstance('post')->wheres($where)->delete();
		if ($postDeleteResult === false) {
			return false;
		}

		$pids = array_column($postInfos, 'id');
		if (empty($pids)) {
			return true;
		}
		$replyDeleteResult = $this->replyDelete(['posts_id' => ['in', $pids]]);
		if (false === $replyDeleteResult) {
			return false;
		}
		$favorDeleteResult = $this->favorDelete(['posts_id' => ['in', $pids]]);
		if (false === $favorDeleteResult) {
			return false;
		}
		return true;
	}

	public function replyDelete(array $where)
	{
		$replyInfos = $this->getInstance('reply')->wheres($where)->get();
		$replyDeleteResult = $this->getInstance('reply')->wheres($where)->delete();
		if ($replyDeleteResult === false) {
			return false;
		}
		foreach ($replyInfos as $replyInfo) {
			$nWhere['event_id'] = $replyInfo->id;
			$nWhere['from_type'] = $replyInfo->id_type;
			$nWhere['from_id'] = $replyInfo->user_id;
			$nWhere['event_type'] = $replyInfo->parent_id > 0 ? 1 : 2;
			$notificationDeleteResult = $this->notificationDelete($nWhere);
			if ($notificationDeleteResult === false) {
				return false;
			}
		}
		return true;
	}

	public function userlistDelete(array $where)
	{
		return $this->getInstance('userlist')->wheres($where)->delete();
	}

	public function favorDelete(array $where)
	{
		$favorInfos = $this->getInstance('favor')->wheres($where)->get();
		$favorDeleteResult = $this->getInstance('favor')->wheres($where)->delete();
		if ($favorDeleteResult === false) {
			return false;
		}

		foreach ($favorInfos as $favorInfo) {
			$nWhere['event_id'] = $favorInfo->id;
			$nWhere['event_type'] = 0;
			$nWhere['from_type'] = $favorInfo->id_type;
			$nWhere['from_id'] = $favorInfo->user_id;
			$notificationDeleteResult = $this->notificationDelete($nWhere);
			if ($notificationDeleteResult === false) {
				return false;
			}
		}
		return true;
	}

	public function discussionDelete(array $where)
	{
		return $this->getInstance('discussion')->wheres($where)->delete();
	}
	
	public function notificationDelete(array $where)
	{
		return $this->getInstance('notification')->wheres($where)->delete();
	}

	/**
	 * Create Methods
	 */
	public function replyCreate(array $createData)
	{
		return $this->getInstance('reply')->create($createData);
	}

	public function postCreate(array $createData)
	{
		return $this->getInstance('post')->create($createData);
	}

	public function userlistCreate(array $createData)
	{
		return $this->getInstance('userlist')->create($createData);
	}

	public function forumCreate(array $createData)
	{
		return $this->getInstance('forum')->create($createData);
	}

	public function notificationCreate(array $createData)
	{
		return $this->getInstance('notification')->create($createData);
	}

	public function favorCreate(array $createData)
	{
		return $this->getInstance('favor')->create($createData);
	}

	public function discussionCreate(array $createData)
	{
        if ($this->discussionCount(['forum_id' => $createData['forum_id']]) < 5) {
			return $this->getInstance('discussion')->create($createData);
		}
		return null;
	}

	public function logCreate(array $createData)
	{
		return $this->getInstance('log')->create($createData);
	}

	/**
	 * Update Methods
	 */
	public function postUpdate(array $where, array $updateData)
	{
		return $this->basicUpdate('post', $where, $updateData);
	}

	public function discussionUpdate(array $where, array $updateData)
	{
		return $this->basicUpdate('discussion', $where, $updateData);
	}

	public function userlistUpdate(array $where, array $updateData)
	{
		return $this->basicUpdate('userlist', $where, $updateData);
	}

	protected function basicUpdate($model, array $where, array $updateData)
	{
		$updateResult = $this->getInstance($model)->wheres($where)->update($updateData);
		if (false === $updateResult) {
			return false;
		}
        $redis = new MicroForumRedis($this->model->getRedisKey());
		if (isset($updateData['id'])) {
			$redis->updateRow($updateData);
		} else {
			$ids = $this->getInstance($model)->select('id')->wheres($where)->get();
			foreach ($ids as $id) {
				$groupUpdateData = $updateData + ['id' => $id['id']];
				$redis->updateRow($groupUpdateData);
			}
		}
		return true;
	}

	/**
	 * Other Methods
	 */
	public function checkIfFavored($pid, $uid, $idType)
	{
		$count = $this->getInstance('favor')->where(['posts_id' => $pid, 'user_id' => $uid, 'id_type' => $idType])->count();
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function checkIfBlocked($forumId, $mid)
	{
		$count = $this->getInstance('userlist')->where(['forum_id' => $forumId, 'member_id' => $mid, 'is_block' => 1])->count();
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

    public function untoppedPost($postsId)
    {
		return $this->postUpdate(['id' => $postsId], ['is_top' => 0]);
    }

    public function toppedPost($forumId, $postsId)
    {
        $hasTopped = $this->getInstance('post')->where(['forum_id' => $forumId, 'is_top' => 1])->get(['id'])->toArray();
        if (!empty($hasTopped)) {
			$untoppedResult = $this->untoppedPost($hasTopped[0]['id']);
			if ($untoppedResult === false) {
				return false;
			}
        }
		return $this->postUpdate(['id' => $postsId], ['is_top' => 1]);
    }

	public function blockedUser($uid)
	{
		return $this->userlistUpdate(['id' => $uid], ['is_block' => 1]);
	}

	public function unblockedUser($uid)
	{
		return $this->userlistUpdate(['id' => $uid], ['is_block' => 0]);
	}

	public function recordReleasePostLog($forumId, $discussionsId, $userId, $idType = 0)
	{
		$createData['operate_code'] = 1;
		$createData['operate_describe'] = sprintf("发帖操作");
		$createData['id_type'] = $idType;
		$createData['user_id'] = $userId;
		$createData['forum_id'] = $forumId;
		$createData['discussions_id'] = $discussionsId;
		return $this->logCreate($createData);
	}

	public function recordDeletePostLog($forumId, $discussionsId, $userId, $idType = 0)
	{
		$createData['operate_code'] = 2;
		$createData['operate_describe'] = sprintf("删帖操作");
		$createData['id_type'] = $idType;
		$createData['user_id'] = $userId;
		$createData['forum_id'] = $forumId;
		$createData['discussions_id'] = $discussionsId;
		return $this->logCreate($createData);
	}

	public function recordLoginLog($forumId, $discussionsId, $userId, $idType = 0)
	{
		$createData['operate_code'] = 0;
		$createData['operate_describe'] = sprintf("登入操作");
		$createData['id_type'] = $idType;
		$createData['user_id'] = $userId;
		$createData['forum_id'] = $forumId;
		$createData['discussions_id'] = $discussionsId;
		return $this->logCreate($createData);
	}

	public function getUserStatistics($forumId, $year = 2017, $month = 10)
	{
		$splice = ['date' => [], 'active' => [], 'release' => [], 'delete' => []];
		$length = date('j', strtotime($year . '-'. ($month + 1) . '-1 00:00:00') - 1);
		$year == date('Y') && $month == date('m') && $length = date('j');
		$splice['date'] = range(1, $length, 1);
		foreach ($splice['date'] as $key => $val) {
			$splice['date'][$key] = $month . '-' . sprintf("%02d", $val);
		}

		$splice['delete'] = array_pad($splice['delete'], $length, 0);
		$splice['release'] = array_pad($splice['release'], $length, 0);
		$splice['active'] = array_pad($splice['active'], $length, 0);
		$raw = $this->getInstance('log')->select(DB::raw('DATE_FORMAT(created_at, \'%e\') as days'), 'operate_code', DB::raw('count(*) as num'))->wheres(['forum_id' => $forumId, 'created_at' => ['between', [$year . '-' . $month . '-01', $year . '-' . $month . '-31']]])->groupBy(DB::raw('DATE_FORMAT(created_at, \'%Y-%c-%d\')'))->groupBy('operate_code')->get()->toArray();
		foreach ($raw as $key => $val) {
			if ($val['operate_code'] == 0) {
				$splice['active'][$val['days'] - 1] = $val['num'];
			} elseif ($val['operate_code'] == 1) {
				$splice['release'][$val['days'] - 1] = $val['num'];
			} else {
				$splice['delete'][$val['days'] - 1] = $val['num'];
			}
		}
		return $splice;
	}

	public function favoredPost($forumId, $postId, $userId, $idType = 0)
	{
		if ($this->checkIfFavored($postId, $userId, $idType)) {
			return true;
		}
		$favorCreateResult = $this->favorCreate(['posts_id' => $postId, 'id_type' => $idType, 'user_id' => $userId]);
		if (is_null($favorCreateResult)) {
			return false;
		}
		$postInfo = $this->getInstance('post')->find($postId);
		$datas['event_type'] = 0;
		$datas['event_id'] = $favorCreateResult->id;
		$datas['from_type'] = $idType;
		$datas['from_id'] = $userId;
		$datas['to_type'] = $postInfo->id_type;
		$datas['to_id'] = $postInfo->user_id;
		$datas['forum_id'] = $forumId;
		$notificationCreateReuslt = $this->notificationCreate($datas);
		if (is_null($notificationCreateReuslt)) {
			return false;
		}
		return true;
	}

	public function unfavoredPost($postId, $userId, $idType = 0)
	{
		if (!$this->checkIfFavored($postId, $userId, $idType)) {
			return true;
		}
		$favorInfo = $this->getInstance('favor')->where(['posts_id' => $postId, 'id_type' => $idType, 'user_id' => $userId])->get()->toArray();
		if (empty($favorInfo)) {
			return true;
		}
		$favorDeleteResult = $this->favorDelete(['posts_id' => $postId, 'id_type' => $idType, 'user_id' => $userId]);
		if (false === $favorDeleteResult) {
			return false;
		}
		$notificationDeleteResult = $this->notificationDelete(['event_type' => 0, 'event_id' => $favorInfo[0]['id'], 'from_type' => $idType, 'from_id' => $userId]);
		if (false === $notificationDeleteResult) {
			return false;
		}
		return true;
	}

	public function buildAssociativeArray(array $source, $hashKey = 'id')
	{
		$destination = [];
		array_walk($source, function ($value) use (&$destination, $hashKey) {
			$destination[$value[$hashKey]] = $value;
		});
		return $destination;
	}

	public function groupPostExtraInfos(array $postInfos)
	{
		$ids = array_column($postInfos, 'id');
		if (!empty($ids)) {
			$repliesCount = $this->buildAssociativeArray($this->replyMultCounts('posts_id', ['posts_id' => ['in', $ids]])->toArray(), 'posts_id');	
			$favorsCount = $this->buildAssociativeArray($this->favorMultCounts('posts_id', ['posts_id' => ['in', $ids]])->toArray(), 'posts_id');
		}
		$imgInfos = $userInfos = $imgids = $uids = [];
		array_walk($postInfos, function ($value) use (&$uids, &$imgids){
			if ($value['id_type'] == 0) {
				$uids[] = $value['user_id'];
			}	
			if (!empty($value['imgids'])) {
				$imgids += explode(',', $value['imgids']);
			}
		});
		if (!empty($uids)) {
			$userInfos = $this->buildAssociativeArray($this->getMemberByIds($uids)->toArray());
		}
		if (!empty($imgids)) {
			$imgInfos = $this->buildAssociativeArray($this->getFileInfoByIds($imgids)->toArray());
		}
		foreach ($postInfos as &$postInfo) {
			$postInfo['isFavors'] = $postInfo['favorsCount'] = $postInfo['repliesCount'] = 0;
			$postInfo['nickname'] = $postInfo['headimgurl'] = '';
			$postInfo['img_paths'] = [];
			isset($repliesCount[$postInfo['id']]) && $postInfo['repliesCount'] = $repliesCount[$postInfo['id']]['num'];
			isset($favorsCount[$postInfo['id']]) && $postInfo['favorsCount'] = $favorsCount[$postInfo['id']]['num'];
			$postInfo['id_type'] == 0 && isset($userInfos[$postInfo['user_id']]) && $postInfo['nickname'] = $userInfos[$postInfo['user_id']]['nickname'];
			$postInfo['id_type'] == 0 && isset($userInfos[$postInfo['user_id']]) && $postInfo['headimgurl'] = $userInfos[$postInfo['user_id']]['headimgurl'];
			$postInfo['id_type'] == 1 && $postInfo['nickname'] = '坛主';
			if (!empty($postInfo['imgids'])) {
				$iids = explode(',', $postInfo['imgids']);
				foreach ($iids as $iid) {
					if (isset($imgInfos[$iid])) {
						if (empty($imgInfos[$iid]['img_size'])) {
							$imgInfos[$iid]['img_size'] = '720x1280';
						} else {
							$tempSize = explode('x', $imgInfos[$iid]['img_size']);
							$tempSize[0] = $tempSize[0] * config('filesystems.image.l')/100;
							$tempSize[1] = $tempSize[1] * config('filesystems.image.l')/100;
							$imgInfos[$iid]['img_size'] = implode('x', $tempSize);
						}
						$postInfo['img_paths'][] = $imgInfos[$iid];
					}
				}
			}
		}
		return $postInfos;
	}
}
