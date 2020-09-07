<?php
namespace App\Jobs;

use App\S\Product\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 处理会搜云新零售系统的店铺商品导入到会搜云
 * @package App\Jobs
 * @author：吴晓平 2019年09月29日 09:24:42
 */
class importCardProduct implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 定义队列出错重试数
     * @var int
     */
    public $tries = 3;

    /**
     * 定义队列运行的超过60秒未完成表示失败
     * @var int
     */
    public $timeout = 60;

    /**
     * @var 要导入的商品数组数据
     */
    protected $productData;

    /**
     * @var 要导入商品数据的对应规格json格式，默认为空
     */
    protected $sku;


    /**
     * 队列构造函数定义队列的名称
     * @param $productData 要导入的商品数组数据
     * @param $sku         对应导入商品的规格
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年10月09日 08:26:45
     */
    public function __construct($productData, $sku)
    {
        // 队列监控时的队列名称
        $this->queue = 'import_card_product';
        $this->productData = $productData;
        $this->sku = $sku;
    }

    /**
     * @description：处理执行商品导入   
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年09月29日 09:15:53
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }
        app(ProductService::class)->insertProductRecord($this->productData, $this->sku);
    }
}
