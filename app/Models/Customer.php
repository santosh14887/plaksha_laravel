<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'street_line','city','country','coutry_devision_code','postal_code','quickbook_id','quickbook_res','company_name','address','customer_hst','hourly_rate','rate_per_load','deleted_at'
    ];
}
