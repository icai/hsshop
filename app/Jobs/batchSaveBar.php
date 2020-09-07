<?php

namespace App\Jobs;

use App\Module\XCXModule;
use App\S\WXXCX\WXXCXConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class batchSaveBar implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $userInfo;
    protected $barData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($xcxConfigId,$userInfo = [],$barData = [])
    {
        //
        $this->id     = $xcxConfigId;
        $this->userInfo = $userInfo;
        $this->barData  = $barData;
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

        $userInfo=$this->userInfo;
        $input = $this->barData;
        $xcxConfigId = $this->id;
        $operatorId=$userInfo['id'] ?? 0;
        $operator=$userInfo['name'] ?? '';
        $data = [];
        if ($input) {
            foreach ($input as $key => $value) {
                $iconPathArr = explode('mctsource/',$value['iconPath']);
                $selectedPathArr = explode('mctsource/',$value['selectedIconPath']);
                $data[$key]['text'] = $value['text'];
                $data[$key]['pagePath'] = $value['pagePath'];
                $data[$key]['iconPath'] = !empty($iconPathArr) ? $iconPathArr[1] : '';
                $data[$key]['selectedIconPath'] = !empty($selectedPathArr) ? $selectedPathArr[1] : '';
            }
        }
        $barList['selectedColor'] = '#b1292d';
        $barList['list'] = $data;
        $barList['backgroundColor'] = '#fff';
        $barList['borderStyle'] = 'black';
        $xcxConfigService = new WXXCXConfigService();
        $row = $xcxConfigService->getRowById($xcxConfigId);
        $row['errCode'] != 0 && error('小程序不存在');
        $pagesList = json_decode($row['data']['page_list'],true);
        if ($row['data']) {
            $templateId = $row['data']['template_id'] ?? 0;
            $xcxModule = new XCXModule();
            $categoryList = json_decode($row['data']['category_list'],true);
            if (!$categoryList) {
                $categoryList = $xcxModule->getCategory($row,true,$operatorId,$operator);
                //error('提交审核项的一个列表不能为空（至少填写1项，至多填写5项）');
            }
            $itemList = [];
            foreach ($categoryList as $key => $value) {
                $value['title'] = '首页';
                $value['tag']   = '';
                $value['address'] = !empty($pagesList) ? $pagesList[0] : 'pages/index/index';
                $itemList[$key] = $value;
            }

            //add by jonzhang 模板id为最新模板id
            //begin
            $userVersion=$row['data']['version'];
            $userDesc=$row['data']['version_desc'];
            $xcxOnlineData=$xcxModule->getXCXOnLine();
            if($xcxOnlineData['errCode']==0&&!empty($xcxOnlineData['data']))
            {
                $templateId=$xcxOnlineData['data']['template_id']??0;
                $userVersion=$xcxOnlineData['data']['user_version']??'';
                $userDesc=$xcxOnlineData['data']['user_desc']??'';
            }

            if(empty($templateId))
            {
                \Log::info('模板Id不能够为0');
            }
            //end

            $xcxModule->commit($xcxConfigId, $templateId,$userVersion,$userDesc,$barList,true,$itemList,$operatorId,$operator);
        }
    }
}
