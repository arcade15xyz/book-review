<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * defines or tell the laravel that there is some relationship(specifically hasMany aka one-to-many) there between Review and books
     * @return\Illuminate\Database\Eloquent\Relations\HasMany<Review, Book>
     */
    public function reviews(){
        return $this->hasMany(Review::class,'book_id','id');
    }
}
