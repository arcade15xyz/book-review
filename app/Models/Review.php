<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    /**
     *using book we are telling laravel that there is also a relation ship from Review to book which is inverse of one-to-one or many relationship so belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Book, Review>
     */
    public function book()
    {
        return $this->belongsTo(Book::class,"book_id","id");
    }
}
