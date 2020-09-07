<?php
/**
 * Notification Service
 *
 * @author mafanding
 */

namespace App\S;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationService extends S{

    /**
     * @var array
     */
    protected $models = [
        'subscribe' => \App\Model\NotificationSubscribe::class,
		'' => \App\Model\Notification::class,
    ];

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var \Illuminate\Http\Request|null
     */
    protected $request = null;

    /**
     * @var null|\Illuminate\Database\Eloquent\Model
     */
    public $model = null;

    public function __construct($model = null)
    {
        $this->request = app('request');
        if (! is_null($model)) {
            if (!array_key_exists($model, $this->models)) {
                throw new ModelNotFoundException("Illegal model name {$model}");
            }
            $this->model = new $this->models[$model];
            $this->instances[$model] = $this->model;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel($model = '')
    {
        if (!array_key_exists($model, $this->models)) {
            throw new \RuntimeException("Undefined model '{$model}'");
        }
        if (!isset($this->instances[$model])) {
            $this->instances[$model]= new $this->models[$model];
        }
        $this->model = $this->instances[$model];
        return $this;
    }

    /**
     * @param string $model
     * @return \Illuminate\Database\Eloquent\Model|null
     */
	public function getInstance($model = '')
	{
		return $this->setModel($model)->getModel();
	}

	/**
	 * Create methods
	 */
	public function createNotificationSubscribeModel($createData)
	{
		return $this->getInstance('subscribe')->create($createData);
	}

	public function createNotificationModel($createData)
	{
		return $this->getInstance('')->create($createData);
	}

	/**
	 * Delete methods
	 */
	public function deleteNotificationSubscribeByConditions($wheres)
	{
		$ids = $this->getInstance('subscribe')->wheres($wheres)->get(['id'])->toArray();
		if (!empty($ids)) {
			$this->getInstance('subscribe')->whereIn('id', $ids)->delete();
		}
		return $ids;
	}

	public function deleteNotificationSubscribeByPrimaryKeys($pks)
	{
		if (!is_array($pks)) {
			$pks = [$pks];
		}
		return $this->getInstance('subscribe')->whereIn('id', $pks)->delete();
	}

	public function deleteNotificationByConditions($wheres)
	{
		$ids = $this->getInstance('')->wheres($wheres)->get(['id'])->toArray();
		if (!empty($ids)) {
			$this->getInstance('')->whereIn('id', $ids)->delete();
		}
		return $ids;
	}

	public function deleteNotificationByPrimaryKeys($pks)
	{
		if (!is_array($pks)) {
			$pks = [$pks];
		}
		return $this->getInstance('')->whereIn('id', $pks)->delete();
	}

	/**
	 * Other methods
	 */
	/**
	 * @param int $notificationType
	 * @param int $subscribeId
	 * @param int $subscribeIdType
	 * @return bool
	 */
	public function checkIfSubscribed($notificationType, $subscriberId, $subscriberIdType = 1)
	{
		$count = $this->getInstance('subscribe')->subscriberId($subscriberId)->subscriberIdType($subscriberIdType)->notificationType($notificationType)->count();
		if ($count > 0) {
			return true;
		}
		return false;
	}

	public function getCollectionByConditions($model, array $columns = ['*'], array $wheres = [], $orderStr = "id desc")
	{
		return $this->getInstance($model)->wheres($wheres)->order($orderStr)->get($columns);
	}

	public function getCollectionByConditionsWithPage($model, array $columns = ['*'], array $wheres = [], $orderStr = "id desc", $page = 1, $size = 15)
	{
		$returnData = ['pageSize' => $size, 'currentPage' => $page];
		$returnData['count'] = $this->getCountByConditions($model, $wheres);
		$returnData['data'] = $this->getInstance($model)->wheres($wheres)->skip($size * ($page - 1))->take($size)->order($orderStr)->get($columns);
		return $returnData;
	}

	public function getCountByConditions($model, array $wheres = [])
	{
		return $this->getInstance($model)->wheres($wheres)->count();
	}

	public function getCollectionByPrimaryKeys($model, $pks)
	{
		if (!is_array($pks)) {
			$pks = [$pks];
		}
		return $this->getInstance($model)->whereIn('id', $pks)->get();
	}

	public function getModelByPrimaryKey($model, $pk)
	{
		return $this->getInstance($model)->find($pk);
	}

	public function getTopicItems()
	{
		return $this->getInstance('subscribe')->getTopicItems();
	}

    /**
     * 更新消息提醒
     * @author hsz
     * @param $model
     * @param $pk
     * @param array $update
     * @return mixed
     */
	public function updateNotificactionByPrimaryKey ($model, $pk, $update = array()) {
        return $this->getInstance($model)->where('id', $pk)->update($update);
    }

}
