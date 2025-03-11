<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * defines or tell the laravel that there is some relationship(specifically hasMany aka one-to-many) there between Review and books
     * @return\Illuminate\Database\Eloquent\Relations\HasMany<Review, Book>
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'book_id', 'id');
    }

    /**
     * This is a Local Query Scope the method name should start with the prefix "scope";
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $title
     * @return Builder
     */
    public function scopeTitle(Builder $query, string $title)
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    /**
     * This Local Query Scope  return Builder with books with most 'review_count' in desending order.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopePopular(Builder $query)
    {
        return $query->withCount('reviews')
            ->orderBy('reviews_count', 'desc');
    }

    /**
     * Return builder according to the 'reviews_avg_rating' in descending order for books
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeHighestRated(Builder $query)
    {
        return $query->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc');
    }
}
