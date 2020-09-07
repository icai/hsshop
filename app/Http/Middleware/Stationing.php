<?php
/**
 * @author wuxiaoping <2018.04.02>
 * @desc   用于布点数据统计
 */
namespace App\Http\Middleware;

use Closure;
use App\S\Foundation\Bi;
use Route;
use App\Jobs\DataStatistics;


class Stationing 
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
        /*用于访问统计 add by wuxiaoping 2018.03.20 start*/
        try {
            if (config('app.is_open_send_log')) { //是否开启发送布点数据
                $pageId = 0; //定义访问各页面的路由参数
                if (Route::input('id')) {
                    $pageId = Route::input('id');
                }else if (Route::input('groups_id')) {
                    $pageId = Route::input('groups_id');
                }else if (Route::input('gid')) {
                    $pageId = Route::input('gid');
                }else if (Route::input('pid')) {
                    $pageId = Route::input('pid');
                } else if ($request->input('activityId')) {//何书哲 2018年8月7日 获取享立减活动id
                    $pageId = $request->input('activityId');
                }
                $biKey = (new Bi())->getUniqueKey();
                //何书哲 2018年8月7日 添加小程序获取店铺、用户id
                $wid = session('wid') ?? $request->input('wid');
                $mid = session('mid') ?? $request->input('mid');
                $job = new DataStatistics($request->url(),$pageId,$wid,$mid,$biKey);
                dispatch($job->onQueue('DataStatistics')->onConnection('dc'));
            }
        } catch (\Exception $exception) {
            \Log::info('日志处理异常'.$exception->getMessage());
        }
        return $next($request);
    }
}
