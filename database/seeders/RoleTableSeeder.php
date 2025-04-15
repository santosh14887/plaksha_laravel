<?php

namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateTime; 
class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();
        $data = array(
            array('name' => 'admin' ,'label' => 'admin','created_at' => new DateTime , 'updated_at' => new DateTime),
        );
        DB::table('roles')->insert($data);
    }
}
