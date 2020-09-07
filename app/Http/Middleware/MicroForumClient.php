<?php

namespace App\Http\Middleware;

use Closure;
use App\S\MicroForumService;

class MicroForumClient
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
		//判断论坛是否存在，同时判断该用户是否在论坛拉黒列表中
		$wid = session('wid');
		$mid = session('mid');
		$microForumService = new MicroForumService('forum');
        $forumInstance = $microForumService->getForumByWid($wid); 
        if (is_null($forumInstance)) {
            error('论坛不存在');
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
		/**
		 * mid信息
		 */
		$request['_mid'] = $mid;
        return $next($request);

	}
 
}
