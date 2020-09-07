<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BiPageview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        for ($i=0; $i < 365; $i++) { 
            $tableTime = time() + ($i * 24 * 3600);
            Schema::connection('mysql_bi')->create('pageview_'.date('Ymd',$tableTime), function (Blueprint $table) {
                $table->engine = 'Myisam';
                $table->increments('id');
                $table->tinyInteger('appid');           //1 微商城
                $table->integer('uid')->unsigned();     //账号id
                $table->integer('wid')->unsigned();     //店铺ID
                $table->string('bikey', 64);            //uv判断标准
                $table->tinyInteger('type');            //页面类型  1、微页面  2、商品页
                $table->integer('p1');                  //type 1时，微页面ID； 2 商品ID
                $table->integer('p2');                  //type 1时，是否主页； 2 商品ID
                $table->string('source', 128);          //访问来源
                $table->bigInteger('createtime');       //创建时间

                $table->index('wid');
                $table->index('bikey');
            });
        }

        Schema::connection('mysql_bi')->create('page_pageview', function (Blueprint $table) {
            $table->engine = 'Myisam';
            $table->integer('wid')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->integer('type');
            $table->integer('viewpv');
            $table->integer('viewuv');
            $table->bigInteger('counttime');

            $table->unique(['wid','type_id','type','counttime']);

        });

        Schema::connection('mysql_bi')->create('wid_pageview', function (Blueprint $table) {
            $table->engine = 'Myisam';
            $table->integer('wid')->unsigned();
            $table->integer('viewpv');
            $table->integer('viewuv');
            $table->integer('pagepv');
            $table->integer('pageuv');
            $table->integer('productpv');
            $table->integer('productuv');
            $table->bigInteger('counttime');

            $table->unique(['wid','counttime']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
