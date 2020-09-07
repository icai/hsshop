<?php
namespace App\S;
use Illuminate\Pagination\LengthAwarePaginator;

class S
{
    public $model;
	public $perPage = 15;
    public $primaryKey = 'id';
    public function __construct($modelName = null)
    {
         $this->request = app('request');
         if (!empty($modelName)) {
            $m = '\\App\\Model\\' . $modelName;
            $this->model = new $m();
         }
    }

    protected function getListWithPage($where = [], $orderBy = '', $order = '',$pageSize=0 )
    {
        $input = $this->request->input();
        $page  = $input['page'] ?? 1;
        $page = intval($page);
        if(!empty($pageSize))
        {
            $this->perPage = $pageSize;
        }
        $skip = ($page - 1) * $this->perPage;
        //分页
        $paginator = new LengthAwarePaginator([], $this->count($where), $this->perPage, null, ['path' => $this->request->url()]);
        //添加参数到分页
        $list = $paginator->appends($input);
        //分页HTML
        $pageHtml = $list->links();
        //分页信息
        $list = $list->toArray();
        //from第几条数据to第几条 不是下标
        $list['from'] = ($page - 1) * $this->perPage + 1;
        $list['to'] = $page * $this->perPage;
        //分页列表数据
        $list['data'] = $this->getList($where, $skip, $this->perPage, $orderBy, $order);
        return [$list, $pageHtml];
    }

    protected function count($where = [])
    {
        return $this->model->wheres($where)->count();
    }
    
    protected function setParameter($primaryKey = 'id')
    {
        $this->primaryKey = $primaryKey;
    }   

    protected function getList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        $primaryKey = $this->primaryKey;
        if ($primaryKey == 'all') {
            $select = $this->model->wheres($where); 
        } else {
            $select = $this->model->select($primaryKey)->wheres($where);
        }

        //排序
        if (is_array($orderBy)) {
            $select->order($orderBy[0] . ' ' . $order);
            $select->order($orderBy[1] . ' ' . $order);
            if(isset($orderBy[2])) $select->order($orderBy[2] . ' ' . $order);
        }else if ($orderBy) {
            $select->order($orderBy . ' ' . $order);
        } else {
            $select->order('id desc');
        }
        //分页
        if (empty($skip) && empty($perPage)) {
            if ($primaryKey == 'all') {
                return $select->get()->toArray();
            } else {
                $ids = $select->pluck($primaryKey)->toArray();
            }
            
        } else {
            if ($primaryKey == 'all') {
                return $select->skip($skip)->take($perPage)->get()->toArray();
            } else {
                $ids = $select->skip($skip)->take($perPage)->pluck($primaryKey)->toArray();
            }
        }
        return $this->getListById($ids);
    }
}