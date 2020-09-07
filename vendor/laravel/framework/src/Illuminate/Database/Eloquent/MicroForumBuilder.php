<?php
/**
 * Created by PhpStorm.
 * User: johnson
 * Date: 8/4/17
 * Time: 3:49 PM
 */

namespace Illuminate\Database\Eloquent;


class MicroForumBuilder extends Builder
{
    public function get($columns = ['*'], $useRedis = false)
    {
        /**
         * we query from redis for data at first.
         * if it can get data, we don't need to query from mysql.
         * else we need to query data from mysql and put the result into redis also.
         */
        if ($useRedis === true) {
            $pkIds = parent::get([$this->getModel()->getQualifiedKeyName()]);
        } else {
            return parent::get($columns);
        }
    }
}