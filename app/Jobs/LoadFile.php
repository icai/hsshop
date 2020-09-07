<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;

class LoadFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;
    protected $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        //
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @update 何书哲 2019年06月20日 file_get_contents取消ssl验证
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }

        try {
            $cdnImg = config('app.cdn_img_url');
            // update 何书哲 2019年06月20日 file_get_contents取消ssl验证
            $arrContextOptions = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $bytes = Storage::put(
                $this->path,
                file_get_contents($cdnImg . trim($this->path, '/'), false, stream_context_create($arrContextOptions))
            );
            if (!$bytes) {
                \Log::info('存储文件失败' . $this->path);
                return true;
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return true;
        }
    }
}
