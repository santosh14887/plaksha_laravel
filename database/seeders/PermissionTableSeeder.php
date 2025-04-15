<?php

namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DateTime; 
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();
        $data = array(
           // array('name' => 'viewMenu:All' ,'label'=>'All','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Dashboard' ,'label'=>'Dashboard','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionDashboard' ,'label'=>'Dashboard','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Permission' ,'label'=>'Permission','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionPermission' ,'label'=>'Permission','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Roles' ,'label'=>'Roles','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionRoles' ,'label'=>'Roles','created_at' => new DateTime , 'updated_at' => new DateTime),
           array('name' => 'viewMenu:Vehicle' ,'label'=>'Vehicle','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionVehicle' ,'label'=>'Vehicle','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:VehicleService' ,'label'=>'VehicleService','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionVehicleService' ,'label'=>'VehicleService','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:IssueCategory' ,'label'=>'IssueCategory','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionIssueCategory' ,'label'=>'IssueCategory','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:UserIssue' ,'label'=>'UserIssue','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionUserIssue' ,'label'=>'UserIssue','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Employee' ,'label'=>'Employee','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionEmployee' ,'label'=>'Employee','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Customer' ,'label'=>'Customer','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionCustomer' ,'label'=>'Customer','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Broker' ,'label'=>'Broker','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionBroker' ,'label'=>'Broker','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:User' ,'label'=>'User','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionUser' ,'label'=>'User','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Transaction' ,'label'=>'Transaction','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionTransaction' ,'label'=>'Transaction','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Dispatch' ,'label'=>'Dispatch','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionDispatch' ,'label'=>'Dispatch','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:DispatchTicket' ,'label'=>'DispatchTicket','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionDispatchTicket' ,'label'=>'DispatchTicket','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Invoice' ,'label'=>'Invoice','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionInvoice' ,'label'=>'Invoice','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Report' ,'label'=>'Report','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionReport' ,'label'=>'Report','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:Fuel' ,'label'=>'Fuel','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionFuel' ,'label'=>'Fuel','created_at' => new DateTime , 'updated_at' => new DateTime),
			array('name' => 'viewMenu:ExpenseCategory' ,'label'=>'ExpenseCategory','created_at' => new DateTime , 'updated_at' => new DateTime),
            array('name' => 'viewMenu:ActionExpenseCategory' ,'label'=>'ExpenseCategory','created_at' => new DateTime , 'updated_at' => new DateTime),
			
        );
        DB::table('permissions')->insert($data);
    }
}