<?php

namespace App\S\Solve;
use App\S\S;

class ProblemSolvingService extends S{

	public function __construct()
	{
		parent::__construct('ProblemSolvingStatistics');
	}

	//添加数据
	public function add($data)
	{
		return $this->model->insertGetId($data);
	}
}