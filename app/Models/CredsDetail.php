<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CredsDetail extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'quickbook_bill_payable_bank_id','quickbook_invoice_hour_id','quickbook_invoice_load_id','quickbook_emp_bill_hour_id','quickbook_emp_bill_load_id','quickbook_emp_invoice_tax_code_ref','quickbook_emp_invoice_tax_rate_ref','quickbook_invoice_tax_code_ref','quickbook_invoice_tax_rate_ref','quickbook_sale_term_ref','use_quickbook_api','use_own_system_opposite_quickbook','creds_for','auth_mode','client_id','client_secret','redirect_uri','refresh_token','realm_id','type','deleted_at'
    ];
}
