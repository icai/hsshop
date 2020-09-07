<?php

namespace App\Model\Bi;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $connection = 'mysql_bi';

    protected $table = 'pageview';

    public function __construct()
    {
        $this->table = $this->table."_".date('Ymd');
    }
}
