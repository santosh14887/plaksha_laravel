<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		//Eloquent::unguard();
		//disable foreign key check for this connection before running seeders
		//DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        //$this->call(TagSeeder::class);
        $this->call(UsersTableSeeder::class);
		$this->call(RoleTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
		$this->call(PermissionTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(ServiceCategoryTableSeeder::class);
        $this->call(ServiceSubCategoryTableSeeder::class);
        $this->call(CredsDetailSeeder::class);
    }
}