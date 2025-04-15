<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vehicle extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'vehicle_number','licence_plate','vin_number','annual_safty_renewal','licence_plate_sticker','service_due_every_km','air_filter_after_days','total_km','last_air_filter_date','due_air_filter_date','vehicle_desc'
    ];
    public function getOwner(){
        return $this->hasOne(User::class,'vehicle_id','id');
    }
	public function getIssue(){
        return $this->hasMany(UserIssue::class,'vehicle_id','id');
    }
	public function getMeterHistory(){
        return $this->hasMany(MeterHistory::class,'vehicle_id','id')->orderBy('on_date','desc');
    }
	public function getFuelHistory(){
        return $this->hasMany(FuelHistory::class,'vehicle_id','id')->orderBy('on_date','desc');
    }
	public function getServiceHistory(){
        return $this->hasMany(VehicleService::class,'vehicle_id','id')->orderBy('on_date','desc');
    }
	public function getVehicleAssignmentHistory(){
        return $this->hasMany(VehicleAssignmentHistory::class,'vehicle_id','id');
    }
}