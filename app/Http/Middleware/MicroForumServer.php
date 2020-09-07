<?php

namespace App\Http\Middleware;

use App\S\MicroForumService;
use Closure;

class MicroForumServer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $wid = session('wid');
        if(!$wid){
            error('来源错误');
        }
        $microForumService = new MicroForumService('forum');
        $forumInstance = $microForumService->getForumByWid($wid);
        if (is_null($forumInstance)) {
            error('请先创建论坛');
        }
        /**
         * forum model实例
         */
        $request['_forumInstance'] = $forumInstance;
        /**
         * forum service实例
         */
        $request['_forumService'] = $microForumService;
        /**
         * wid信息
         */
        $request['_wid'] = $wid;
        return $next($request);
    }
}
