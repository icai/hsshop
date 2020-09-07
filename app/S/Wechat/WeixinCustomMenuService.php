<?php
namespace App\S\Wechat;
use App\Lib\Redis\WeixinMenu;
use App\S\S;

class WeixinCustomMenuService extends S{

	private $wechatRedis;

	//
	public function __construct()
	{
		parent::__construct('WeixinCustomMenu');
	}

    /**
     * [获取某个店铺下的全部微信菜单数据，可以进行分页]
     * @param  [int]    $wid   [店铺id]
     * @param  [array]  $whereData [条件关联数组]
     * @param  $is_page 是否进行分页
     * @author WuXiaoPing
     * @date 2017-07-26
     * @return [array]  $list  [微信图文数据]
     */
    public function getAllList($wid,$whereData = [],$is_page = true)
    {
        $where = [];
        $where['wid'] = $wid;
        if($whereData){
            foreach ($whereData as $key => $item) {
                switch($key){
                    case 'parent_id':$where['parent_id'] = $item;
                        break;

                    default:
                        break;
                }
            }
        }
        //是否分页
        $list = [];
        if($is_page){
            $list = $this->getListWithPage($where);
        }else{
            $list = $this->getList($where,'','','id','ASC');
        }

        return $list;

    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-07-26
     */
    public function getListById($idArr = [])
    {
        $weixinMenuRedis = new WeixinMenu();
        $redisData = $mysqlData = [];
        $redisId = [];

        $result = $weixinMenuRedis->getArr($idArr);

        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }

        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $weixinMenuRedis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * [根据主键id，获取单条微信图文数据]
     * @param  [int]   $id      [主键id]
     * @return [type]  $data    [微信图文数据]
     */
    public function getRowById($id)
    {
        $data = [];
        $obj = $this->model->wheres(['id' => $id])->first();
        if($obj){
            $data = $obj->toArray();
        }

        return $data;
    }

	/**
	 * [处理显示菜单列表的数据 >> 主要用于显示后台页面，不用来生成公众号的自定义菜单]
	 * @param  [int] $wid [店铺id]
	 * @return [array] 需要返回的菜单信息
	 */
	public function getMenuList( $wid )
	{
		$customMenus = $this->getWeixinMenuData($wid,false);

		//处理显示内容的格式
		foreach($customMenus['button'] as &$menu){
            if(!empty($menu['sub_button']) && is_array($menu['sub_button'])){
                foreach($menu['sub_button'] as &$child){
                    if(json_decode(urldecode($child['content']))){
                        $child['content'] = json_decode(urldecode($child['content']),true);
                    }else{
                        $child['content'] = urldecode($child['content']);
                    }
                }
            }else{
                if(json_decode(urldecode($menu['content']))){
                    $menu['content'] = json_decode(urldecode($menu['content']),true);
                }else{
                    $menu['content'] = urldecode($menu['content']);
                }

            }
        }
        return $customMenus;
	}

	/**
     * [处理菜单数据，返回要生成的菜单合法格式 >>主要用于生成微信公众号的自定义菜单]
     * [boolen] $is_menu 表示是否为菜单数据
     * @return [array] [菜单数据的数组形式]
     */
	public function getWeixinMenuData( $wid,$is_menu=true )
	{
		//全部菜单列表
        $customMenus = $this->getAllList($wid,[],false);
        //定义返回的数据
        $last = [];
        $resultData = [];
        $result = [];

        $parentIds = [];
        //分别取出一级菜单与二级菜单数据
        foreach($customMenus as $val){
            if($val['parent_id'] == 0){
                $arr['name']    = $val['name'];
                $arr['id']      = $val['id'];
                $arr['type']    = $val['type'];
                $arr['content'] = $val['content'];
                $arr['value']   = $val['value'];
                //add MayJay
                array_push($parentIds,$val['id']);
                array_push($last, $arr);
                //$last[]['sub_button'] = $result[$val['id']];
            }else{
                //add MayJay
                if(empty($val['content']) && $is_menu ){
                    error('存在二级菜单回复内容为空');
                }
                if(in_array($val['parent_id'],$parentIds)){
                    unset($parentIds[array_search($val['parent_id'],$parentIds)]);
                }
                $result[$val['parent_id']][] = $val;
            }
        }


        //add MayJay
        $parentData = array_column($last, 'content', 'id');
        if($parentIds && $is_menu){
            foreach ($parentIds as $id){
                if(empty($parentData[$id])){
                    error('存在一级菜单回复内容为空');
                }

            }
        }
        // end


        //对各级菜单数据进行整合处理
        $menuData = [];
        foreach($last as $lk=>$la){
            $menuData[$lk]['name'] = $la['name'];
            if(!$is_menu)
            {
                $menuData[$lk]['id'] = $la['id'];
                $menuData[$lk]['content'] = urlencode($la['content']);
            }
            if(!empty($result) && !empty($result[$la['id']])){
                foreach($result[$la['id']] as $rk=>$res){
                    $menuData[$lk]['sub_button'][$rk]['name'] = $res['name'];
                    if(!$is_menu){
                        $menuData[$lk]['sub_button'][$rk]['id'] = $res['id'];
                        $menuData[$lk]['sub_button'][$rk]['content'] = urlencode($res['content']);
                    }
                    switch ($res['type']){
                        case 1:
                            $menuData[$lk]['sub_button'][$rk]['type'] = 'click';
                            if(empty($res['value'])){
                                $menuData[$lk]['sub_button'][$rk]['key'] = 'key'.($lk+1);
                            }else{
                                $menuData[$lk]['sub_button'][$rk]['key'] = $res['value'];
                            }
                            break;
                        case 2:
                            $menuData[$lk]['sub_button'][$rk]['type'] = 'view';
                            $menuData[$lk]['sub_button'][$rk]['url'] = $res['value'];
                            break;
                        case 3:
                            $config = json_decode($res['content'],1);
                            $menuData[$lk]['sub_button'][$rk]['type'] = 'miniprogram';
                            $menuData[$lk]['sub_button'][$rk]['url'] = config('app.url');
                            $menuData[$lk]['sub_button'][$rk]['appid'] = $config['appid'];
                            $menuData[$lk]['sub_button'][$rk]['pagepath'] = $config['pagepath'];
                            break;
                    }
                }

            }else{
                switch ($la['type']){
                    case 1:
                        $menuData[$lk]['type'] = 'click';
                        if(empty($la['value'])){
                            $menuData[$lk]['key'] = 'key'.($lk+1);
                        }else{
                            $menuData[$lk]['key'] = $la['value'];
                        }
                        break;
                    case 2:
                        $menuData[$lk]['type'] = 'view';
                        $menuData[$lk]['url']  = $la['value'];
                        break;
                    case 3:
                        $config = json_decode($la['content'],1);
                        $menuData[$lk]['type'] = 'miniprogram';
                        $menuData[$lk]['url'] = config('app.url');
                        $menuData[$lk]['appid'] = $config['appid'] ;
                        $menuData[$lk]['pagepath'] = $config['pagepath'] ;
                        break;
                }

            }
        }
        $resultData['button'] = $menuData;
        return $resultData;
	}

    /**
     * [自定义菜单的 添加]
     * @param [array] $data [关联数组--要添加的数据]
     */
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * [自定义菜单数据更新]
     * @param  [int]   $id   [对应的主键id]
     * @param  [array] $data [要更新的关联数组数据]
     * @return [array]       [对应返回的状态与提示信息]
     */
    public function update($id,$data)
    {
        $returnData = ['errcode' => 0, 'errmsg' => ''];
        if(empty($id)){
            $returnData['errcode'] = -1;
            $returnData['errmsg']  = 'id不能为空';
            return $returnData;
        }

        if(empty($data)){
            $returnData['errcode'] = -2;
            $returnData['errMsg']  = '更新的数据为空';
            return $returnData;
        }

        $row = $this->model->wheres(['id' => $id])->update($data);
        $weixinMenuRedis = new WeixinMenu();

        //redis永远不更新的bug fixed by Herry
        /*if(!$row){
        	$result = $this->getRowById($id);
        	$weixinMenuRedis->updateHashRow($id, $result);
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }*/

        $result = $this->getRowById($id);
        $weixinMenuRedis->updateHashRow($id, $result);

        return $returnData;
    }

    /**
     * [del description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function del($id)
    {
        $ids = [];
        //获取二级菜单的所以id
        $obj = $this->model->select('id')->wheres(['parent_id' => $id])->get();
        if($obj){
            $arr = $obj->toArray();
            foreach($arr as $val){
                $ids[] = $val['id'];
            }
        }
        $ids[] = $id;
        $result = $this->model->where(['id'=>$id])->orWhere(['parent_id'=>$id])->delete();
        if($result){
            //删除redis数据
            $weixinMenuRedis = new WeixinMenu();
            foreach($ids as $id){
                $weixinMenuRedis->del($id);
            }
            return true;
        }

        return false;
    }

	/**
	 * 自定义菜单的 添加，编辑，删除
	 * 当表单传值input['id']有值时，表示编辑或删除
	 * input['type'] = 'del'表示删除
	 * 				 = 'update'表示编辑
	 * @param  [int] $wid   [店铺id]
	 * @param  [array] $input [表单传递的数据]
	 * @return [type]        [description]
	 */
	public function save( $wid,$input )
	{
		$id  = $input['id'] ?? 0;
        $saveData = []; //定义保存的数据
        $saveData['wid'] = $wid;
        $saveData['parent_id'] = $input['parent_id'] ?? 0;

        if($id){ //表示进行删除或编辑操作
        	if($input['type'] == 'del'){
                $result = $this->del($id);
            }else{
            	if(isset($input['name']) && $input['name']){
                    $saveData['name'] = $input['name'];
                }

                if(isset($input['content']) && $input['content']){
                    $saveData['content'] = $input['content'];
                }
                /**
                 * 如果传递过来的是一个数组数据，则说明不是一般的信息，图文或者有链接跳转地址
                 */
                if(!$input['content']){
                    $rs = $this->getRowById($id);
                    $content = json_decode($rs['content'],1);
                    if ($content['type'] == 18) {
                        $content['url'] = config('app.url').'shop/kf/index?wid='.$wid;
                    }
                    if($content){
                        if(isset($content['url'])){
                            $input['value'] = $content['url'];
                        }else{
                            $input['value'] = $content['value'];
                        }
                        $saveData['content'] = $content;
                    }else{
                        $saveData['content'] = $rs['content'];
                    }
                }
                if(isset($saveData['content']) && is_array($saveData['content'])){
                	//当type为7或8,14时，表示图文信息与营销活动，event为click,其他为view
                    //add by wuxiaoping 当type=17时，表示微预约图文信息
                    if($saveData['content']['type'] == 7 || $saveData['content']['type'] == 8 || $saveData['content']['type'] == 14 || $saveData['content']['type'] == 17){
                        $saveData['type'] = 1; //表示是click类型
                        $saveData['value'] = 'V1001_IMGTXT'.'_'.$saveData['content']['content_id']; //自定义key
                    }elseif ($saveData['content']['type'] == 12){
                        if(!$saveData['content']['appid']){
                            error('请设置跳转的小程序appid');
                        }
                        $url = $input['value'];
                        $saveData['content']['url'] = config('app.url');
                        $saveData['content']['pagepath'] = trim($url) ?? 'pages/index/index';
                        $saveData['type'] = 3; //表示是miniprogram类型
                        $saveData['value'] = $input['value'];
                    }elseif ($saveData['content']['type'] == 18){
                        //微信客服
//                        $saveData['type'] = 1; //表示是click类型
//                        $saveData['value'] = 'V1001_IMGTXT'.'_'.'wxkf'; //自定义key
                        //改为新版客服
                        $saveData['type'] = 2; //表示是view类型
                        $saveData['value'] = config('app.url').'shop/kf/index?wid='.$wid;

                    }
                    else{
                        $saveData['type'] = 2; //表示是view类型
                        $saveData['value'] = $input['value'];
                    }
                    /**
                     * 1-商品，2-商品分组，3-微页面
                     * 4-微页面分类，5-店铺主页，6-会员主页
                     * 7、8-微信图文
                     */
                    if($input['activityType']){
                        $saveData['content']['activityType'] = $input['activityType'];
                    }
                    $saveData['content']['url'] = $input['value'];
                    $saveData['content'] = json_encode($saveData['content'],JSON_UNESCAPED_UNICODE);
                }else{
                    $saveData['type'] = 1; //表示是view类型
                    $saveData['value'] = 'V1001_GENERAL'.'_'.$id;
                }
                $returnData = $this->update($id,$saveData);
                if($returnData['errcode'] == 0){
                    $result = true;
                }else{
                    $result = false;
                }
            }
        }else{
        	//根据条件查询菜单列表
        	$customMenus = $this->getAllList($wid,['parent_id' => $saveData['parent_id']],false);

            //统计相应一级和二级菜单数据
            $menuLevelOne = [];
            $menuLevelTwo = [];
            foreach($customMenus as $items){
                if($items['parent_id'] == 0){
                    $menuLevelOne[] = $items;
                }else{
                    $menuLevelTwo[$items['parent_id']][] = $items;
                }
            }
            $saveData['name']  = '标题';
            $saveData['value'] = 'V1001_GENERAL'.'_'.date('s',time()).rand(10,99);

            if($saveData['parent_id']){
                if(isset($menuLevelTwo[$saveData['parent_id']]) && count($menuLevelTwo[$saveData['parent_id']]) >= 5){
                    error('二级菜单最多只能添加5个');
                }
            }else{
                if(count($menuLevelOne) >= 3){
                    error('一级菜单最多只能添加3个');
                }
            }
            $result = $this->add($saveData);
        }

        if ( $result ) {
            success();
        }

        error();

	}

}



?>