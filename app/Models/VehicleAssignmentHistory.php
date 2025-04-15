<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VehicleAssignmentHistory extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'start_time','end_time','user_id','user_type','user_name','user_email','user_phone','vehicle_id','vehicle_number','licence_plate','vin_number','comment','deleted_at','created_by','updated_by'
    ];
}
