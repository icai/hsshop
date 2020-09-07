<?php

namespace App\Jobs;

use App\Model\Product;
use App\Model\ProductImg;
use App\S\Product\ProductPropsToValuesService;
use App\Lib\Redis\ProductPropsToValues;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Lib\Redis\Product as ProductRedis;

class ProcessImportPictureUrl implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    
    public $timeout = 600;
    
    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
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

        if (!empty($this->params) && !empty($this->params['imgUrl'])) {
            //获取图片数组 Herry
            $imgs = [];
            if (empty($this->params['type'])) {
                $imgs = explode(';', $this->params['imgUrl']);
            }  elseif ($this->params['type'] == 'PROP') {
                $imgs = $this->params['imgUrl'];
            }

            $productRedis = new ProductRedis();
            foreach ($imgs as $k => $img) {
                if (false === filter_var($img, \FILTER_VALIDATE_URL)) {
                    exit(1);
                }
                $realPath = $this->params['targetDir'] . DIRECTORY_SEPARATOR . md5($img) . '.jpg';
                $relativePath = str_replace(public_path(), '', $realPath);

                try {
                    if ($fileContents = file_get_contents($img)) {
                        if (!file_exists($realPath)) {
                            $saveFlag = Storage::put($relativePath, $fileContents);
                            $relativePath = $saveFlag ? str_replace('\\', '/', ltrim($relativePath, '\\/')) : '';
                        } else {
                            $relativePath = str_replace('\\', '/', ltrim($relativePath, '\\/'));
                        }
                    } else {
                        //图片不存在
                        $relativePath = '';
                    }
                } catch (\Exception $e) {
                    //图片不存在
                    $relativePath = '';
                }

                if (empty($this->params['type'])) {
                    //默认处理商品图片
                    //第一张图插入商品表
                    if ($k == 0) {
                        $res = Product::where('id', $this->params['productId'])->update(['img' => $relativePath]);
                        $res && $productRedis->updateRow(['id' => $this->params['productId'], 'img' => $relativePath]);
                    }

                    //每张图片插入商品图片表
                    if ($relativePath) {
                        ProductImg::insertGetId([
                            'wid' => $this->params['wid'],
                            'product_id' => $this->params['productId'],
                            'img' => $relativePath,
                        ]);
                    }
                } elseif ($this->params['type'] == 'PROP') {
                    //处理规格图片
                    $propValueService = new ProductPropsToValuesService();
                    $propValueRow = $propValueService->model->select('id')
                        ->where('wid', $this->params['wid'])
                        ->where('pid', $this->params['productId'])
                        ->where('prop_id', $this->params['propID'])
                        ->where('value_id', $k)
                        ->first();
                    if (!empty($propValueRow->id) && !empty($relativePath)) {
                        //更新数据库
                        $updateData = ['prop_is_img' => 1, 'value_img' => $relativePath];
                        $propValueService->model->where('id', $propValueRow->id)->update($updateData);
                        //更新redis
                        $updateData['id'] = $propValueRow->id;
                        (new ProductPropsToValues())->updateRow($updateData);
                    }
                }
            }
        }
    }
}
