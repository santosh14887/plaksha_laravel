<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Book extends Model
{
    use HasFactory, SoftDeletes;
	protected $fillable = [
        'name','author_id'
    ];
	public function getAuthor(){
        return $this->belongsTo(Author::class,'author_id');
    }
	public function tags()
    {
		return $this->belongsToMany(Tag::class);
    }
	public function reviews()
    {
        return $this->hasMany(Review::class, 'book_id');
    }
}
