<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(\App\Model\Order::class, 'Order', function (Faker\Generator $faker) {
    return [
        'oid'            => str_numeric_random(),
        'wid'            => 42,
        'mid'            => 3,
        'trade_id'       => 'N' . str_numeric_random(31),
        'pay_price'      => $faker->randomFloat(2, 0.01, 10000),
        'freight_price'  => $faker->randomFloat(2, 0.01, 50),
        'freight_id'     => mt_rand(-1,1),
        'express_type'   => mt_rand(0,3),
        'address_id'     => 1,
        'address_name'   => $faker->name,
        'address_phone'  => $faker->phoneNumber,
        'address_detail' => $faker->address,
        'type'           => mt_rand(1,4),
        'serial_id'      => str_numeric_random(),
        'pay_way'        => mt_rand(1,8),
        'star_level'     => mt_rand(0,5),
        'buy_remark'     => '',
        'seller_remark'  => '',
        'refund_status'  => mt_rand(0,4),
        'status'         => mt_rand(0,5),
        // 'created_at'     => $faker->dateTimeBetween('-1 days')->format('Y-m-d H:i:s'),
        // 'updated_at'     => $faker->dateTimeBetween('-1 days')->format('Y-m-d H:i:s'),
        // 'created_at'     => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        // 'updated_at'     => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        'deleted_at'     => null
    ];
});

$factory->defineAs(\App\Model\OrderDetail::class, 'OrderDetail', function (Faker\Generator $faker) {
    return [
        'product_id' => 1,
        'title'      => $faker->name . '的课程',
        'img'        => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1484757500161&di=1140f2319b55e218b3a9f9c76ab670a3&imgtype=0&src=http%3A%2F%2Fimg2.ph.126.net%2FC2_U1I10kUzpWJQoBb9ypA%3D%3D%2F1489565576852981657.jpg',
        'price'      => $faker->randomFloat(2, 0.01, 1000),
        'oprice'     => $faker->randomFloat(2, 0.01, 2000),
        'num'        => mt_rand(1, 10),
        'spec'       => mt_rand(12,36) . '节课时',
        // 'created_at' => $faker->dateTimeBetween('-1 days')->format('Y-m-d H:i:s'),
        // 'updated_at' => $faker->dateTimeBetween('-1 days')->format('Y-m-d H:i:s'),
        // 'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        // 'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        'deleted_at' => null
    ];
});

$factory->defineAs(\App\Model\OrderLog::class, 'OrderLog', function (Faker\Generator $faker) {
    return [
        'wid'        => 42,
        'mid'        => 3,
        'action'     => mt_rand(1,12),
        // 'created_at' => $faker->dateTimeBetween('-1 days')->format('Y-m-d H:i:s'),
        // 'updated_at' => $faker->dateTimeBetween('-1 days')->format('Y-m-d H:i:s'),
        // 'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        // 'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        'deleted_at' => null
    ];
});

$factory->defineAs(\App\Model\Member::class, 'Member', function (Faker\Generator $faker) {
    return [
        'wid'           => 42,
        'nickname'      => $faker->name,
        'mobile'        => $faker->phoneNumber,
        'sex'           => mt_rand(0, 2),
        'score'         => mt_rand(0, 1000),
        'source'        => mt_rand(0, 3),
        'buy_num'       => mt_rand(0,100),
        'province_id'   => 15,
        'city_id'       => 1213,
        'area_id'       => 2963,
        'created_at' => $faker->dateTimeBetween('-19 hours')->format('Y-m-d H:i:s'),
        'updated_at' => $faker->dateTimeBetween('-19 hours')->format('Y-m-d H:i:s'),
        // 'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        // 'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
        'deleted_at'    => null,
    ];
});

$factory->defineAs(\App\Model\Notification::class, 'Notification', function (Faker\Generator $faker) {
	return [
		'notification_type' => mt_rand(1, 4),
		'relate_order_id' => mt_rand(1, 1000),
		'recv_id' => 42,
		'recv_id_type' => 1,
		'send_id' => mt_rand(1, 1000),
		'send_id_type' => mt_rand(0, 1),
		'is_read' => mt_rand(0, 1),
		'notification_content' => $faker->sentence,
        'created_at' => $faker->dateTimeBetween('-19 hours')->format('Y-m-d H:i:s'),
        'updated_at' => $faker->dateTimeBetween('-19 hours')->format('Y-m-d H:i:s'),
        'deleted_at' => null,
	];
});

$factory->defineAs(\App\Model\MicroForumLog::class, 'MicroForumLog', function (Faker\Generator $faker) {
	return [
		'forum_id' => 1,
		'discussions_id' => mt_rand(1, 10),
		'operate_code' => mt_rand(0, 2),
		'id_type' => mt_rand(0, 1),
		'user_id' => mt_rand(0, 1000),
		'operate_describe' => $faker->sentence,
        'created_at' => $faker->dateTimeBetween('-30 days')->format('Y-m-d H:i:s'),
        'updated_at' => $faker->dateTimeBetween('-30 days')->format('Y-m-d H:i:s'),
	];
});
