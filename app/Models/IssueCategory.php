<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class IssueCategory extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'title','description','deleted_at'
    ];
	public function getUserIssue(){
        return $this->hasMany(UserIssue::class,'issue_category');
    }
}
