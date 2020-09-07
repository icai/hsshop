<?php

namespace App\Jobs;

use App\S\Product\ProductService;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Upyun\Config;
use Upyun\Upyun;

class ProductCdn implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected  $content;
    protected  $imgUrl;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($content,$imgUrl = [])
    {
        //
        $this->content      = $content;
        $this->imgUrl       = $imgUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        try{
            $temp = json_decode($this->content->content);
            $re = $temp[0]->content;
            foreach ($this->imgUrl as $url) {
                //判断是否是外链，如果是外联则先下载保存，并上传至cdn 再更新数据库字段
                $path = $url;
                if ($this->isExternalLinks($url)) {
                    $path = "hsshop/productDetail/{$this->content->id}-".basename($url);
                    if (!$this->uploadFromExternalLinks($url,$path)) {
                        #下载失败
                        \Log::info($url.'外链下载失败'.$this->content->id);
                        continue ;
                    }
                }
                if ($cdnPath = $this->upCdn($path)) {
                    #上传成功
                    $handle = str_replace('/','\/',$url);
                    $pattern = "/".$handle."/";
                    $re = preg_replace($pattern, \config('app.cdn_img_url').$cdnPath, $re);
                }
                throw new \Exception($url.'CDN上传失败'.$this->content->id);
                continue ;
            }
            $temp[0]->content = $re;
            $this->updateDB(json_encode($temp));
        }catch (\Exception $exception){
            \Log::info('商品图片CDN处理异常:'.$exception->getMessage());
        }

    }


    /**
     * 判断是否为外链
     * @author: 梅杰 20180724
     */
    private function isExternalLinks($url)
    {
        return strstr($url,'http') && !strstr($url,'huisou.cn');
    }

    /**
     * 下载外链图片
     * @param $ExternalLinks 外链
     * @param $fileName 保存的文件名
     * @return bool 是否下载成功
     * @author: 梅杰 20180724
     */
    private function uploadFromExternalLinks($ExternalLinks,$fileName)
    {
        $client = new Client(['verify' => false]);
        $response = $client->get($ExternalLinks, ['save_to' => public_path($fileName)]);
        return $response->getStatusCode() == 200;
    }



    /**
     * 上传图片至CDN
     * @param $filePath 待上传的文件路径
     * @author: 梅杰 20180727
     */
    private function upCdn($filePath)
    {
        $bucket = \config('app.cdn_bucket');
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $client = new Upyun($config);
        $path = config('filesystems.file_path').'/image/'.date('Y/m/d').'/'.basename($filePath);
        if ($client->has($path)) {
            $path =  config('filesystems.file_path').'/image/'.date('Y/m/d').'/'.basename($filePath);
        }
        $img = file_get_contents(public_path($filePath));
        $re = $client->write($path, $img);
        return $re ? $path : false;
    }

    /**
     * 修改数据库字段
     * @author: 梅杰 20180725
     */
    private function updateDB($content)
    {
        $productService = new ProductService();
        $data = $productService->getRowById($this->content->id);
        $data['content'] = $content;
        $data['deleted_at'] = null;
        return $productService->update($this->content->id,$data);
    }
}
