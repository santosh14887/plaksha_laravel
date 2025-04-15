<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'user_type','user_id','quickbook_payable_bill_id','quickbook_payable_bill_res','quickbook_invoice_id','quickbook_invoice_res','customer_id','invoice_number','invoice_date','dispatch_ids','ticket_ids','invoice_pdf','subtotal','hst_per','hst_amount','total'
    ];
	public function getCustomer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
	public function getUser(){
        return $this->belongsTo(User::class,'user_id');
    }
	public function dispatches(){
        return $this->hasMany(Dispatch::class);
    }
	public function getDispatchesAttributes()
	{
		$dispatchId = $this->getOriginal('dispatch_ids');
		return Dispatch::whereIn('id', explode(',', $dispatchId))->get();
	}
	public function tickets(){
        return $this->hasMany(DispatchTicket::class);
    }
	public function getTicketsAttributes()
	{
		$ticketId = $this->getOriginal('ticket_ids');
		return DispatchTicket::whereIn('id', explode(',', $ticketId))->get();
	}
}
