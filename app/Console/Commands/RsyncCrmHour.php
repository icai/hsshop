<?php

namespace App\Console\Commands;

use App\S\Foundation\RegionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RsyncCrmHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RsyncCrmHour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $regions = (new RegionService())->getAll();
        foreach ($regions as $key => $item) {
            if ($item['status'] == -1) {
                unset($regions[$key]);
            }
        }
        $regions = array_column($regions, null, 'id');

        $shops = DB::table('weixin as w')
            ->leftJoin('weixin_role as wr', 'w.id', '=', 'wr.wid')
            ->leftJoin('user as u', 'w.uid', '=', 'u.id')
            ->leftJoin('saleachieve_count as s', 'w.uid', '=', 's.uid')
            ->whereNull('w.deleted_at')
            ->select('w.id', 'w.shop_name', 'w.created_at', 'w.shop_expire_at', 'wr.admin_role_id', 'u.name as username',
                'u.mphone as userphone', 'w.uid', 'w.company_name', 'u.email as user_email', 'u.created_at as user_created_at',
                's.salesman', 's.achievement', 's.trade', 'w.province_id', 'w.city_id', 'w.area_id', 'w.address', 'w.shop_expire_at')
            ->orderBy('id', 'desc')
            ->limit(100)->get();

        foreach ($shops as $shop) {
            $sql = "SELECT id FROM crm_cust_shop_item_temp  WHERE shop_item_id = " . $shop->id . "  AND shop_item_type = 7";
            $res = \DB::connection('crm')->select($sql);
            if (!empty($res)) {
                continue;
            }
            $province = $regions[$shop->province_id]['title'] ?? '';
            $city = $regions[$shop->city_id]['title'] ?? '';
            $area = $regions[$shop->area_id]['title'] ?? '';
            $shop_address = $shop->address ?? '';
            $address = $province . $city . $area . $shop_address;

            $insert = [
                'shop_item_id' => $shop->id,
                'shop_item_name' => $shop->shop_name,
                'shop_begin_time' => $shop->created_at,
                'shop_end_time' => $shop->shop_expire_at,
                'shop_item_remark' => $shop->achievement . ";" . $shop->trade,
                'shop_item_type' => 7,
                'shop_version' => $shop->admin_role_id ?? 1,
                'shop_item_create_at' => $shop->created_at,
                'user_id' => $shop->uid,
                'user_phone' => $shop->userphone,
                'user_name' => $shop->username,
                'xls_isagent' => 0,
                'xls_agentgrade' => 0,
                'company_name' => $shop->company_name,
                'company_address' => $address,
                'weixin' => '',
                'email' => $shop->user_email,
                'idcard' => '',
                'user_create_at' => $shop->user_created_at,
                'sala_name' => $shop->salesman ?? '',
                'sale_phone' => '',
            ];
            \DB::connection('crm')->table('crm_cust_shop_item_temp')->insert($insert);
        }
    }
}
