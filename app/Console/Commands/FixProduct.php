<?php

namespace App\Console\Commands;

use App\Lib\Redis\Product as ProductRedis;
use App\Lib\Redis\ProductSku as SkuRedis;
use App\Model\Product;
use App\Model\ProductSku;
use App\S\Product\ProductService;
use DB;
use Illuminate\Console\Command;


class FixProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FixProduct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复商品数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
          DB::table('product')->select('id','content')
          ->where('wid' ,922)
          ->where('status', '>', -1)
          ->chunk(100, function($products) {
                foreach ($products as $product) {
                    $id         = $product->id;
                    $content    = json_decode($product->content, true);

                    $fixContent = "";
                    $updateContent = false;
                    if(empty($content)) {
                          continue;
                    }
                    foreach ($content as $k => $v) {
                        if ($v['type'] == 'store' && !empty($v['content'])) {
                            $fixContent = $v['content'];
                            unset($content[$k]['content']);
                            $updateContent = true;
                        }
                        if ($fixContent && $v['type'] == 'shop_detail') {
                            $content[$k]['content'] = $fixContent;
                            $updateContent = true;
                        }
                    }
                    if ($updateContent === true) {
                        $content = array_values($content);
                        echo $id."============\r\n";
                        //echo $product->content."\r\n";
                        //echo json_encode($content)."\r\n";
                        (new ProductService())->update($id, ['content' => json_encode($content)]);
                    }
                }
          });
    }
}
