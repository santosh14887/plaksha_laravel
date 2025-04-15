<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Transaction extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'default_transaction_number','trans_genrate_type','user_type','type','invoice_id','dispatch_id','assign_dispatch_id','user_id','amount','total_amount','message','dispatch_ticket_id'
    ];
	public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }
	public function getCustomer(){
        return $this->belongsTo(Customer::class,'user_id');
    }
	public function dispatches(){
        return $this->belongsTo(Dispatch::class);
    }
	public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
}
