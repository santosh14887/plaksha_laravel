<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FuelHistory extends Model
{
    use HasFactory;
	protected $fillable = [
        'vehicle_id','vehicle_number','dispatch_ticket_id','on_date','fuel_card_number','fuel_qty','fuel_id','per_liter_amount','fuel_tbl_amount_date','total_km','fuel_economy','resource','comment','created_by','updated_by'
    ];
	public function getVehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }
	public function getTicket(){
        return $this->belongsTo(DispatchTicket::class,'dispatch_ticket_id');
    }
}
