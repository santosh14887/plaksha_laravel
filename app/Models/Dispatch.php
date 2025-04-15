<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Dispatch extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'customer_id','start_time','start_location','dump_location','job_type','supervisor_name','supervisor_contact','required_unit','comment','job_rate','status','completed_date','default_dispatch_number','invoice_id','invoice_date','invoice_sent','invoice_sent_date','employee_rate','customer_company_name','customer_address','customer_customer_hst'
    ];
	public function getCustomer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
	public function getInvoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
	public function getDispatchAssign(){
        return $this->hasMany(AssignDispatche::class,'dispatch_id');
    }
	public function getDispatchTicket(){
        return $this->hasMany(DispatchTicket::class,'dispatch_id');
    }
	public function getDispatchCompleteTicket(){
        return $this->hasMany(DispatchTicket::class,'dispatch_id')->where('status','=','completed');
    }
	public function getAssignDispatchAssign(){
        return $this->hasMany(AssignDispatch::class,'dispatch_id')->where('status','=','accepted');
    }
}
