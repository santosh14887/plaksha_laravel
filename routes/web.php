<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return 'DONE'; //Return anything
});
Auth::routes();
Route::middleware(['auth','admin'])->group(function () {
    Route::resource('authors', App\Http\Controllers\AuthorsController::class);
	Route::resource('customers', App\Http\Controllers\CustomerController::class);
	Route::resource('employees', App\Http\Controllers\EmployeeController::class);
	Route::resource('service_categories', App\Http\Controllers\ServiceCategoryController::class);
	Route::post('admin_change_password', [App\Http\Controllers\EmployeeController::class, 'admin_change_password'])->name('admin_change_password');
	Route::get('transaction/employee/{id}', [App\Http\Controllers\TransactionController::class, 'all_transaction'])->name('all_transaction');
	Route::get('transaction/customer/{id}', [App\Http\Controllers\TransactionController::class, 'all_customer_transaction'])->name('all_customer_transaction');
	Route::get('reports/customer_revenue', [App\Http\Controllers\ReportController::class, 'customer_revenue'])->name('customer_revenue');
	Route::get('reports/dispatch_report', [App\Http\Controllers\ReportController::class, 'dispatch_report'])->name('dispatch_report');
//Route::get('reports/generate_pdf/{id}', [App\Http\Controllers\ReportController::class, 'generate_pdf'])->name('generate_pdf');
Route::get('reports/all_invoices', [App\Http\Controllers\ReportController::class, 'all_invoices'])->name('all_invoices');
Route::post('reports/generate_multi_row_pdf', [App\Http\Controllers\ReportController::class, 'generate_multi_row_pdf'])->name('generate_multi_row_pdf');
	Route::get('reports/invoice', [App\Http\Controllers\ReportController::class, 'invoice'])->name('invoice');
	Route::post('reports/get_user_current_amount', [App\Http\Controllers\ReportController::class, 'get_user_current_amount'])->name('get_user_current_amount');
	Route::post('reports/add_user_transaction', [App\Http\Controllers\ReportController::class, 'add_user_transaction'])->name('add_user_transaction');
	Route::post('reports/update_status', [App\Http\Controllers\ReportController::class, 'update_status'])->name('update_status');
	Route::post('reports/invoice_sent', [App\Http\Controllers\ReportController::class, 'invoice_sent'])->name('invoice_sent');
	Route::post('reports/quickbook_download_pdf', [App\Http\Controllers\ReportController::class, 'quickbook_download_pdf'])->name('quickbook_download_pdf');
	Route::get('reports/employee_payment_earning', [App\Http\Controllers\ReportController::class, 'employee_payment_earning'])->name('employee_payment_earning');
	Route::resource('roles', App\Http\Controllers\RolesController::class);
	Route::resource('permissions', App\Http\Controllers\PermissionsController::class);
	Route::resource('users', App\Http\Controllers\UsersController::class);
	Route::resource('brokers', App\Http\Controllers\BrokerController::class);
	Route::resource('vehicles', App\Http\Controllers\VehicleController::class);
	Route::resource('meter_histories', App\Http\Controllers\MeterHistoryController::class);
	Route::resource('fuel_histories', App\Http\Controllers\FuelHistoryController::class);
	Route::resource('vehicle_assignment_histories', App\Http\Controllers\VehicleAssignmentHistoryController::class);
	Route::resource('fuels', App\Http\Controllers\FuelController::class);
	Route::resource('dispatches', App\Http\Controllers\DispatchController::class);
	Route::get('dispatch/vehicle_assignment', [App\Http\Controllers\DispatchController::class, 'vehicle_assignment'])->name('vehicle_assignment');
	Route::get('dispatch/update_prev_cusomer_on_dispatch', [App\Http\Controllers\DispatchController::class, 'update_prev_cusomer_on_dispatch'])->name('update_prev_cusomer_on_dispatch');
	Route::get('dispatch/update_prev_user_on_assign_dispatch', [App\Http\Controllers\DispatchController::class, 'update_prev_user_on_assign_dispatch'])->name('update_prev_user_on_assign_dispatch');
	Route::get('dispatch/update_prev_emp_brok_on_ticket', [App\Http\Controllers\DispatchController::class, 'update_prev_emp_brok_on_ticket'])->name('update_prev_emp_brok_on_ticket');
	/******need to update script on live 1-3-23 */
	Route::get('dispatch/update_prev_emp_vehicle_assignment', [App\Http\Controllers\DispatchController::class, 'update_prev_emp_vehicle_assignment'])->name('update_prev_emp_vehicle_assignment');
	Route::get('dispatch/update_prev_vehicle_number_on_assign_vehicle', [App\Http\Controllers\DispatchController::class, 'update_prev_vehicle_number_on_assign_vehicle'])->name('update_prev_vehicle_number_on_assign_vehicle');
	Route::get('dispatch/update_prev_vehicle_airfilter_service_date', [App\Http\Controllers\DispatchController::class, 'update_prev_vehicle_airfilter_service_date'])->name('update_prev_vehicle_airfilter_service_date');
	Route::get('dispatch/update_prev_vehicle_fuel_update', [App\Http\Controllers\DispatchTicketController::class, 'update_prev_vehicle_fuel_update'])->name('update_prev_vehicle_fuel_update');
	/******end need to update script on live 1-3-23 */
	Route::get('dispatch/update_driver_from_assign_to_ticket', [App\Http\Controllers\DispatchController::class, 'update_driver_from_assign_to_ticket'])->name('update_driver_from_assign_to_ticket');
	
Route::resource('assign_dispatches', App\Http\Controllers\AssignDispatchController::class);
Route::resource('issue_categories', App\Http\Controllers\IssueCategoryController::class);
Route::resource('user_issues', App\Http\Controllers\UserIssueController::class);
Route::get('cat_user_issues/{id}', [App\Http\Controllers\IssueCategoryController::class, 'user_issues'])->name('user_issues');
Route::resource('dispatch_tickets', App\Http\Controllers\DispatchTicketController::class);
Route::resource('vehicle_services', App\Http\Controllers\VehicleServiceController::class);
Route::get('specific_vehicle_service/{id}', [App\Http\Controllers\VehicleServiceController::class, 'specific_vehicle_service'])->name('specific_vehicle_service');
Route::get('specific_service_sub_category/{id}', [App\Http\Controllers\ServiceCategoryController::class, 'specific_service_sub_category'])->name('specific_service_sub_category');
Route::resource('assign_dispatch_broker_vehicles', App\Http\Controllers\AssignDispatchBrokerVehicleController::class);
Route::get('assign_dispatch_broker_vehicles/create/{id}', [App\Http\Controllers\AssignDispatchBrokerVehicleController::class, 'create'])->name('create');
Route::get('vehicle_services/create/{id}', [App\Http\Controllers\VehicleServiceController::class, 'create'])->name('create');
Route::get('add_more_dispach_assignment', [App\Http\Controllers\AssignDispatchController::class, 'add_more_dispach_assignment'])->name('add_more_dispach_assignment');
Route::post('get_users', [App\Http\Controllers\AssignDispatchController::class, 'get_users'])->name('get_users');
Route::post('dispatch_tickets/update_status', [App\Http\Controllers\DispatchTicketController::class, 'update_status'])->name('update_status');
Route::post('dispatch_tickets/load_over_hours_amount', [App\Http\Controllers\DispatchTicketController::class, 'load_over_hours_amount'])->name('load_over_hours_amount');
Route::post('dispatch_tickets/verify_fuel_amount', [App\Http\Controllers\DispatchTicketController::class, 'verify_fuel_amount'])->name('verify_fuel_amount');
Route::get('get_user_vehicle', [App\Http\Controllers\AssignDispatchController::class, 'get_user_vehicle'])->name('get_user_vehicle');
Route::get('get_assign_user_vehicle', [App\Http\Controllers\DispatchTicketController::class, 'get_assign_user_vehicle'])->name('get_assign_user_vehicle');
Route::get('get_hour_or_load', [App\Http\Controllers\DispatchTicketController::class, 'get_hour_or_load'])->name('get_hour_or_load');
Route::get('get_dispatch_users', [App\Http\Controllers\DispatchTicketController::class, 'get_dispatch_users'])->name('get_dispatch_users');
Route::get('get_service_subcategory', [App\Http\Controllers\VehicleServiceController::class, 'get_service_subcategory'])->name('get_service_subcategory');
Route::get('get_broker_users', [App\Http\Controllers\DispatchTicketController::class, 'get_broker_users'])->name('get_broker_users');
Route::get('assign_dispatches/create/{id}', [App\Http\Controllers\AssignDispatchController::class, 'create'])->name('create');
Route::get('assigned_dispatche/{id}', [App\Http\Controllers\AssignDispatchController::class, 'specific_assigned_dispatch'])->name('specific_assigned_dispatch');
Route::post('cancel_dispatch_from_users', [App\Http\Controllers\AssignDispatchController::class, 'cancel_dispatch_from_users'])->name('cancel_dispatch_from_users');
Route::post('add_ticket_remark', [App\Http\Controllers\DispatchTicketController::class, 'add_ticket_remark'])->name('add_ticket_remark');

/*** assign broker vehicle to dispatch list***/
Route::get('assigned_dispatche_broker_vehicles/{id}', [App\Http\Controllers\AssignDispatchBrokerVehicleController::class, 'assigned_dispatche_broker_vehicles'])->name('assigned_dispatche_broker_vehicles');
/***end assign broker vehicle to dispatch list***/
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/update_sidebar_help', [App\Http\Controllers\HomeController::class, 'update_sidebar_help'])->name('update_sidebar_help');
Route::get('/noaccess', [App\Http\Controllers\HomeController::class, 'noaccess'])->name('noaccess');
Route::get('/cron_notification/order_pending_notification', [App\Http\Controllers\CronController::class, 'order_pending_notification'])->name('order_pending_notification');
Route::get('/cron_notification/cron_normal_service', [App\Http\Controllers\CronController::class, 'cron_normal_service'])->name('cron_normal_service');
Route::get('/cron_notification/cron_air_filter', [App\Http\Controllers\CronController::class, 'cron_air_filter'])->name('cron_air_filter');
Route::get('/notification_test/{id}', [App\Http\Controllers\HomeController::class, 'notification_test'])->name('notification_test');
Route::get('/email_test/{id}', [App\Http\Controllers\HomeController::class, 'email_test'])->name('email_test');
Route::get('/twillio_test/{id}', [App\Http\Controllers\HomeController::class, 'twillio_test'])->name('twillio_test');
Route::get('/quickbook_test', [App\Http\Controllers\HomeController::class, 'quickbook_test'])->name('quickbook_test');
Route::get('/vehicle_api/{id}', [App\Http\Controllers\HomeController::class, 'vehicle_api'])->name('vehicle_api');
Route::get('/gas_api', [App\Http\Controllers\HomeController::class, 'gas_api'])->name('gas_api');
Route::get('reports/employee_bill', [App\Http\Controllers\ReportController::class, 'employee_bill'])->name('employee_bill');
Route::post('reports/generate_employee_invoice', [App\Http\Controllers\ReportController::class, 'generate_employee_invoice'])->name('generate_employee_invoice');
Route::get('reports/employee_all_bills', [App\Http\Controllers\ReportController::class, 'employee_all_bills'])->name('employee_all_bills');
Route::post('reports/quickbook_employee_bill_download_pdf', [App\Http\Controllers\ReportController::class, 'quickbook_employee_bill_download_pdf'])->name('quickbook_employee_bill_download_pdf');
Route::post('reports/update_employee_payroll_bill_status', [App\Http\Controllers\ReportController::class, 'update_employee_payroll_bill_status'])->name('update_employee_payroll_bill_status');
