<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Notification extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id','push_notification_message_id','message'
    ];
	public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }
}
