<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     *
     * function when : when the value is true or not null the callback function will run else not.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');


        $books = Book::when($title, fn($query, $title) => $query->title($title));

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6month' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()->withAvgRating()->withReviewsCount()
        };

        $books = $books->paginate(10);

        // $books = Cache::remember('books', 3600, fn () => $books->get()); Or
        $cacheKey = 'books:' . $filter . ':' . $title;
        //$books =
        // cache()->remember(
            // $cacheKey,
            // 3600,
            // function () use ($books) {
           // $books->get();
        // });

        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Summary of show: Display the specified resource. And we are using the eager load relation on model with some additional query or filtering.
     * @param \App\Models\Book $book
     * @return \Illuminate\Contracts\View\View
     */
    public function show(int $id)
    {
        $cacheKey = 'book:' . $id;
        $book = cache()->remember(
            $cacheKey,
            3600,
            fn() => Book::with([
                'reviews' => fn($query) => $query->latest()
            ])->withAvgRating()->withReviewsCount()->findOrFail($id)
        );
        return view('books.show', [
            'book' => $book
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
