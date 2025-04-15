<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DispatchTicket extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'employee_invoice_generate_status','invoice_id','dispatch_id','assign_dispatch_id','shift_type','user_type','user_id','broker_vehicle_id','driver_name','unit_vehicle_number','contact_number','starting_km','ending_km','total_km','fuel_qty','fuel_card_number','fuel_receipt','def_qty','def_receipt','gas_station_location','ticket_number','ticket_img','hour_or_load','total_load','hour_or_load_integer','created_by','updated_by','income','expense','emp_brok_hour_rate','emp_brok_load_rate','profit','status','default_ticket_number','emploee_hour_over_load','emploee_hour_over_load_amount','emploee_hourly_rate_over_load','expense_without_emploee_hour_over_load','fuel_amount_paid'];
	
	public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }
	public function getDispatch(){
        return $this->belongsTo(Dispatch::class,'dispatch_id');
    }
    public function getEmpVehicle(){
        return $this->belongsTo(Vehicle::class,'unit_vehicle_number','vehicle_number');
    }
	public function getAssignDispatch(){
        return $this->belongsTo(AssignDispatch::class,'assign_dispatch_id');
    }
	public function getBrokerVehicle(){
        return $this->belongsTo(AssignDispatchBrokerVehicle::class,'assign_dispatch_id');
    }
}
