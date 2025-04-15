<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class MeterHistory extends Model
{
    use HasFactory;
	protected $fillable = [
        'vehicle_id','vehicle_number','dispatch_ticket_id','on_date','starting_km','ending_km','total_km','resource','comment','created_by','updated_by'
    ];
	public function getVehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }
	public function getTicket(){
        return $this->belongsTo(DispatchTicket::class,'dispatch_ticket_id');
    }
}
