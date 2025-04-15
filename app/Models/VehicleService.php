<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VehicleService extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'vehicle_id','on_date','on_km','expense_amount','service_cat_id','parent_service_type','service_subcat_id','service_type','comment','created_by','updated_by'
    ];
	public function getVehicle(){
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }
}
