<?php

namespace App\Jobs;

use App\Lib\Redis\RedisClient;
use App\Module\ProductModule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Upyun\Config;
use Upyun\Upyun;

class CreateShareCard implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;

    public $id;
    public $mid;
    public $wid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $mid, $wid)
    {
        $this->mid = $mid;
        $this->wid = $wid;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @update 何书哲 2019年06月20日 file_get_contents取消ssl验证
     */
    public function handle(ProductModule $productModule)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }

        $redisClient = (new RedisClient())->getRedisClient();
        try {
            $cardData = $productModule->getShareCode($this->id, $this->mid, $this->wid);
            if (!$cardData) {
                return false;
            }
        } catch (\Exception $e) {
            \Log::info('队列生成卡片错误' . $e->getMessage());
            return true;
        }

        $path = 'hsshop/image/product/sharecard/' . md5($cardData['url']) . '.png';
        $bucket = config('app.cdn_bucket');
        $config = new Config($bucket, 'phpteam', 'phpteam123456');
        $client = new Upyun($config);
        $res = $client->has($path);
        $key = $productModule->getProductShareCardKey($this->id, $this->mid, $cardData['product']);
        if ($res) {
            $redisClient->set($key, $path);
        } else {
            try {
                // update 何书哲 2019年06月20日 file_get_contents取消ssl验证
                $arrContextOptions = [
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ],
                ];
                $stream = file_get_contents($cardData['url'], false, stream_context_create($arrContextOptions));
                $client->write($path, $stream);
                $redisClient->set($key, $path);
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
                return true;
            }
        }

    }
}
