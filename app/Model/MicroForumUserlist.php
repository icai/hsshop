<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroForumUserlist extends Model
{
    use SoftDeletes, RedisKeyTrait;

    protected $table = 'microforum_userlist';

    protected $redisKey = 'userlist';

	protected static $unguarded = true;

	public function createRelation($forumId, $memberId)
	{
		if (!$this->initModel(['forum_id' => $forumId, 'member_id' => $memberId])->count()) {
			return (new static)->insertGetId(['forum_id' => $forumId, 'member_id' => $memberId]);
		}
	}

    protected function initModel(array $where = [], array $orderBy = [])
    {
        $model = new static;
        if (!empty($where)) {
            $model = $model->where($where);
        }
        if (!empty($orderBy)) {
            foreach ($orderBy as $item) {
                list($column, $sort) = $item;
                $model = $model->orderBy($column, $sort);
            }
        }
        return $model;
    }
}
