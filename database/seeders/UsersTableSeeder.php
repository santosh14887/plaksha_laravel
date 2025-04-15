<?php

namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateTime; 
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $data = array(
            array('name' => 'admin' ,'email' =>'admin@gmail.com' , 'password' => '$2y$10$x3XMC8YLgD93GAN8su6yeOe4KYYC0GiVO1HOsWjV15dAHKXI589XC' ,'password_string' => 'test@321','type'=>'admin','created_at' => new DateTime , 'updated_at' => new DateTime),

        );
        DB::table('users')->insert($data);
    }
}
