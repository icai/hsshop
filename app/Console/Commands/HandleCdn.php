<?php

namespace App\Console\Commands;

use App\Jobs\ProductCdn;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HandleCdn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productImgCdnHandle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        #todo 正则匹配非cdn的商品详情图片
        DB::table('product')->select('id','content')->whereNull('deleted_at')->where('content','<>',"")
            ->where(function ($query) {
                $query->whereRaw("content REGEXP '<img'");
            })->chunk(10,function ($content){
                foreach ( $content as $v) {
                    $temp = json_decode($v->content);
                    //
                    $handle = str_replace('/','\/',config('app.cdn_img_url'));
                    preg_match_all('/<img [^>]*src=[\'"](?!'.$handle.'|\/hsshop\/ueditor|\/ueditor)([^\'"]+)[^>]*>/', $temp[0]->content, $img_array);
                    if ($img_array  && $img_array[1]) {
                        //队列中处理,1、如果是外链先下载 再上传CDN，如果不是则直接上传CDN
                        $arr = array_flip($img_array[1]);
                        $arr = array_flip($arr);
                        $job = new ProductCdn($v,$arr);
                        dispatch($job->onQueue('productImgCdn'));
                    }
                }
            });

    }
}
