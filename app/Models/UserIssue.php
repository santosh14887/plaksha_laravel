<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserIssue extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'issue_category','user_id','user_type','user_name','user_email','user_phone','vehicle_id','vehicle_number','licence_plate','vin_number','title','description','deleted_at','status','start_time','status_no'
    ];
	public function getIssueCatgory(){
        return $this->belongsTo(IssueCategory::class,'issue_category');
    }
	public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }
}
