<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroForumForum extends Model
{
    use SoftDeletes, RedisKeyTrait;

    protected $table = 'microforum_forum';

    protected $redisKey = 'forum';

	protected static $unguarded = true;

    public function createForum($wid, array $input)
    {
        if (!isset($input['title']) || empty($input['title']) || !isset($input['introduction']) || empty($input['introduction']) || !isset($input['imgid']) || empty($input['imgid'])) {
            return false;
        }
        if (is_null($this->getForumByWid($wid))) {
            $data['wid'] = $wid;
            $data['title'] = $input['title'];
            $data['introduction'] = $input['introduction'];
            $data['imgid'] = $input['imgid'];
            return (new static)->insertGetId($data);
        }
        return false;
    }

    public function updateForum(array $where, array $data)
    {
        return (new static)->where($where)->update($data);
    }

    public function incrementViewCount($wid, $step = 1)
    {
        return (new static)->where('wid', $wid)->increment('view_count', $step);
    }
}
