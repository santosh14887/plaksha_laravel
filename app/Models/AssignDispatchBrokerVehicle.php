<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AssignDispatchBrokerVehicle extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'assign_dispatch_id','driver_name','vehicle_number','contact_number','created_by','updated_by'];
	
	public function getAssignDispatch(){
        return $this->belongsTo(AssignDispatch::class,'dispatch_id');
    }
}
