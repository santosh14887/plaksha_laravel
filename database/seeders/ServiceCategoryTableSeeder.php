<?php

namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateTime; 
class ServiceCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_categories')->delete();
        $data = array(
            array('name' => 'Service' ,'slug' => 'service','parent_category' => '0','created_at' => new DateTime , 'updated_at' => new DateTime),
			array('name' => 'Regular Service' ,'slug' => 'regular_service','parent_category' => '1','created_at' => new DateTime , 'updated_at' => new DateTime),
			array('name' => 'Air Filter Change' ,'slug' => 'air_filter_change','parent_category' => '1','created_at' => new DateTime , 'updated_at' => new DateTime),
			array('name' => 'Annual Safty' ,'slug' => 'annual_safty','parent_category' => '1','created_at' => new DateTime , 'updated_at' => new DateTime),
			array('name' => 'License Plate Ticker' ,'slug' => 'license_plate_ticker','parent_category' => '1','created_at' => new DateTime , 'updated_at' => new DateTime),
        );
        DB::table('service_categories')->insert($data);
    }
}
