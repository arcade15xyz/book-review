<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

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
    public function scopeTitle(Builder $query, string $title): Builder|QueryBuilder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopeWithReviewsCount(Builder $query, $from = null, $to= null){
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to= null){
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating');

    }


    /**
     * This Query scope function add an attribute in table reviews i.e. 'review_count' and on the date specified
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $from
     * @param mixed $to
     * @return Builder
     */
    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withReviewsCount()
        ->orderBy('reviews_count', 'desc');
    }


    /**
     * scopeHighestRated : what it does is according to attribute 'reviews_avg_rating' created by withAvg
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $from
     * @param mixed $to
     * @return Builder
     */
    public function scopeHighestRated(Builder $query,  $from = null, $to = null): Builder| QueryBuilder
    {
        return $query->withAvgRating()
            ->orderBy('reviews_avg_rating', 'desc');
    }

    /**
     * This is a private method which we use to find the Books in in a specific time period according to the params given
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $from
     * @param mixed $to
     * @return void
     */
    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    /**
     * This another Query scope method that gives us the builder for books with minimum reviews upto $minReviews
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minReviews
     * @return Builder
     */
    public function scopeMinReviews(Builder $query, int $minReviews)
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    /**
     * Summary of scopePopularLastMonth: So it shows the popular books in last month according to the highest rated with 2 minimum views
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopePopularLastMonth(Builder $query){
        return $query->popular(now()->subMonth(), now())
        ->highestRated(now()->subMonth(), now())
        ->minReviews(2);
    }

    /**
     * Summary of scopePopularLast6Months: So it shows the popular books in last 6 month according to the highest rated with 5 minimum views
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopePopularLast6Months(Builder $query){
        return $query->popular(now()->subMonths(6), now())
        ->highestRated(now()->subMonths(6), now())
        ->minReviews(5);
    }

    /**
     * Summary of scopeHighestRatedLastMonth: So it shows the highest rated books in last month according the popular books with 2 minimum views
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeHighestRatedLastMonth(Builder $query){
        return $query->highestRated(now()->subMonth(), now())
        ->Popular(now()->subMonth(), now())
        ->minReviews(2);
    }

    /**
     * Summary of scopeHighestRatedLast6Months: So it shows the highest rated books in last 6 month according the popular books with 5 minimum views
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeHighestRatedLast6Months(Builder $query){
        return $query->highestRated(now()->subMonths(6), now())
        ->Popular(now()->subMonths(6), now())
        ->minReviews(5);
    }

    protected static function booted()
    {
        static::updated(fn (Book $book) => cache()->forget('book:'. $book->id));
        static::deleted(fn(Book $book)=> cache()->forget('book:' . $book->id));
    }
}
