<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'name','slug','parent_category','comment','created_by','updated_by'
    ];
	public function subCategory()
	{
		return $this->hasMany(ServiceCategory::class, 'parent_category');
	}

	public function parentCategory()
	{
		return $this->belongsTo(ServiceCategory::class, 'parent_category');
	}
}
