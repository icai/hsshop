<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory('App\Model\Member', 'Member', 111)->create();
        // $this->call(UsersTableSeeder::class);
        // factory('App\Model\Order', 'Order', 51)->create()->each(function($u) {
        //     for ($i=0; $i < mt_rand(1,3); $i++) {
        //         $u->orderDetail()->save(factory('App\Model\OrderDetail', 'OrderDetail', 1)->make());
        //     }
        //     for ($k=1; $k < mt_rand(2,6); $k++) { 
        //         $u->orderLog()->save(factory('App\Model\OrderLog', 'OrderLog', 1)->make([
        //             'action' => $k,
        //         ]));
        //     }
        // });
        //factory(App\Model\Notification::class, 'Notification', 100)->create();
        //factory(App\Model\MicroForumLog::class, 'MicroForumLog', 1000)->create();
    }
}
