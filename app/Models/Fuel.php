<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Fuel extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'on_date','amount'
    ];
}
