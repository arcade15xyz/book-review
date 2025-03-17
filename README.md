# BOOKS AND REVIEW PROJECT

So whats happening here is that we are using relation one to many and using these relationship for making a **Book and Review**

## Qurerying and Associting Realted Models

### To get the book data for a specific book this is lazy loading

`$book = \App\Models\Book::find(1);`

### To get the reviews for the specific book this is lazy loading

`$reviews = $book->reviews;`
the reviews is the method provided in the Book model

### To get the book data as well as reviews for the book the reviews is the method in book model this is also lazy loading

`\App\Models\Book::with("reviews")->find(1);`

### To get the multiple data for books with now this is example of eager loading

`$books = App\Models\Book::with('reviews')->take(3)->get();`

### using "load" we can load multiple relationships for a $book

```
//this gives us the data for a book
$book = App\Models\Book::find(2);
//this load the relationships for the given $book
$book->load('reviews');
```

### How to add a new review for the $book

```php
$book = \App\Models\Book::find(1);

$review = new \App\Model\Review();

$review->review = 'This is a good book';

$review->rating = 5

$book->reviews()->save($review);

//How to get the reviews for the book
$book->reviews;
```

### Now to add a review for a book we are getting the data via form so we need to assign $fillables in the "Review Model".

```php
$book = App\Models\Book::find(1);

//What is happening here is we are creating a review for the book using associtaion / relation
$review = $book->reviews()->create(['reviews' => 'A very Good book'], 'rating' => 5);
```

### To find the book through a review

```php
$review = App\Models\Review::find(1);

$review->book;
```

what this will do is it will provide us the book for which that review is made;

### If we need to change relation of review to other book

```php
//initially the book_id = 1
$review = \App\Models\Review::find(1);

//get information on the book for review
$review->book;


$book2 = \App\Models\Book::find(2);

//save the $review form book_id=1 to book_id=2;
$book2->reviews()->save($review);

```

### How to search

```php
\App\Models\Book::where('title', 'LIKE', '%delectus%')->get();
```

## Local Query Scope

What local Query scope is that we make methods that scopes queries with some action i.e. :apple:\ instead of using the query we just use these **Local Query Scope**. This local Query scope is declare/defined in the model;
The Query Scope is declared with 'scope' as prefix and when calling it this prefix is discarded;
**_example :arrow_heading_down:_**

```php
//In Book Model

public function scopeTitle(Builder $query, string $title)
{
    return $query->where('title', 'LIKE', '%'.$title.'%');
}
```

**_How is it used_**

```php
\App\Models\Book::title('qui')->get();
```

We can use some adder parameters here like below :arrow_heading_down:

```php
\App\Models\Book::title('qui')->where('created_at', ">=", "2024-01-01")->get();
```

**_Misslinious_**
if we want to know the vanilla sql query in the terminal use :arrow_heading_down:

```php
\App\Models\Book::title('delecturs')->where('created_at', '>', '2024-01-01')->toSql();
```

## Aggregation on Relations

### Definition

Aggregation functions( like count(), sum(), avg(), min() and max() )allows us to preform mathematical calculations on related models(e.g. counting reviews for a book , averaging ratings,etc).

so It's main feature is to provide add a new category for all books that we will get with name like
:white_check_mark: withCount('reviews') :point_right: **\*review_count**
:white_check_mark: withAvg('reviews', 'rating') :point_right: **\*review_avg_rating**

### Using Count (withCount());

What this **Eloquent query** does is it retrieves all books from database along with a **count** of their related reviews.

```php
\App\Models\Book::withCount('reviews')->get();
```

:key: Here **withCount('reviews')** Add a new **reviews_count** attribute that holds the number of reviews each book has.

```php
\App\Models\Book::withCount('reviews')->latest()->limit(3)->get();
```

It does the same thing as above and **latest()->limit(3)** gives us the latest 3 books along with **reviews_count**

### Using Avg (withAvg('reviews','rating'))

What this **Eloquent query** does is it retrieves all books from database along with **avg** of their related **reviews->rating**.

🔑 So what it does is for each **Book** to be get() it will add a attribute for **_withAvg('reviews','rating')_** as **reviews_avg_rating**;

**EXAMPLES**

```php
\App\Models\Book::limit(5)->withAvg('reviews','rating')->orderBy('reviews_avg_rating')->get();
```

It show the details of 5 books by **reviews_avg_rating** ascending mode

```php
\App\Models\Book:withCount('reviews')->withAvg('reviews','rating')->having('reviews_count','>=', 10)->orderBy('reviews_avg_rating','desc')->limit(10)->get();
```

It shows 10 book data according ordered by the _reviews_avg_rating_ in desc which are having _reviews_count_ greater than 10 and add _reviews_avg_rating_ and _reviews_count_.

🔑 In case multiple Aggregation are done as in above example it gives us multiple attributes .

**_Misslinious_**
In case of query on Aggregation on relation we use _having_ instead of _where_ **query builder method**.

## Highest Rated and Popular Books

### 🔑 Comparing $this vs $query in Context

**$this (instance)**

🐤 Works on : A **single** model instance

🐤 Used in : Instance methods

🐤 Example : `$this->title`

🐤 When to use : Access model properties, call relations

**$query (Query Builder)**

🐤 Works on : A **query on multiple** model

🐤 Used in : Scope methods (`scopeXyz()`)

🐤 Example : `$query->having('title','laravel');`

🐤 When to use : Modify queries, apply filters

|  Category   | $this (instance)                        | $query (Query Builder)               |
| :---------: | --------------------------------------- | ------------------------------------ |
|  Works on   | A **single** model instance             | A **query on multiple** model        |
|   Used in   | Instance methods                        | Scope methods (`scopeXyz()`)         |
|   Example   | `$this->title`                          | `$query->having('title','laravel');` |
| When to use | Access model properties, call relations | Modify queries, apply filters        |

🚀 `where()` is used for direct column filtering before aggregation.

🚀 `having()` is used for filtering aggregated results (like COUNT(), AVG()).

## Getting Books with recent reviews

### Introduction

**What did we learn**

🔑 **Builder $query**

<ol>
<li>It represents the current query being built
<ul>
<li>$query modifies the query dynamically</li>
<li>This allows chaning methods like (where(), orderby(), withCount()) without overriding the original query</li>
</ul>
</li>
<li>It makes query scopers reusables</li>
<ul>
<li>We can apply these query scopes in any query</li>
</ul>
</ol>

```php
    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])
            ->orderBy('reviews_count', 'desc');
    }
```

This is a Query scope method in Book model for finding the most popular books in specific time periods. To find books in specific timeperiod we use dateRangeFilter which gives us all the books in that time period.

**_Kindly check the Book model to see the methods used_**x

## Controllers and Resource Controllers

So instead of using all our request handling logic as closure in our route we will organize this behaviour using **controller** class.  
Controller can group related requests handling logic in a single class.  
**Example :arrow_heading_down:**  
UserController class can manage all the request handling logic related to _users_, including _showing_, _creating_, _updating_ and _deleting_ the user.

**_How to build a controller_**

```
php artisan make:controller UserController
```

what is does is it make a controller in app\Http\Controllers\UserController which will be used in routes like following ⤵️

```php
    use App\Http\Controllers\UserController;
    Route::get('user/{id}', [controller: UserController::class, action: 'show'])
```

[For more knowledge on topic **CONTROLLER** click here](https://laravel.com/docs/12.x/controllers#introduction)

**Resource Controller**  
This is used in common used case i.e The Controller handles **CRUD**(Create, Read, Update, Delete) requests so we use resource controller to make actions like index, create, show, store, edit, update and destroy.

**_How to create a Resource Controller_**

```
php artisan make:controller BookController --resource
```

What this does is it creates a Contoller with following functions(actions) automatically:

1. index()
1. create()
1. store()
1. show()
1. edit()
1. update()
1. destroy()

How to use this in _route_ ⤵️

```php
use App\Http\Controllers\BookController;
Route::resource('books', BookController::class);
```

This automatically registers all seven routes for tasks using the TaskController.  
So 'books' is the base url. And for any type of reqest(get, post, put, delete) it automatically does contorller actions, _The route names will also be automatically defined with this template {{baseurl . controller_method}}_

This is The table giving information on how the routes are choosen ⤵️

| HTTP Verb |        URL         | Controller Method | Purpose                             |  Route Name   |
| :-------: | :----------------: | :---------------: | ----------------------------------- | :-----------: |
|    GET    |       /books       |      index()      | Show a list of all tasks            |  books.index  |
|    GET    |   /books/create    |     create()      | Show a form to create a new task    | books.create  |
|   POST    |       /books       |      store()      | Store a newly created task          |  books.store  |
|    GET    |   /books/{book}    |      show()       | Show details of a specific task     |  books.show   |
|    GET    | /books/{book}/edit |      edit()       | Show a form to edit a specific task |  books.edit   |
| PUT/PATCH |   /books/{book}    |     update()      | Update a specific task              | books.update  |
|  DELETE   |   /books/{book}    |     destroy()     | Delete a specific task              | books.destroy |

[To know more about **Resource Controller** click here](https://laravel.com/docs/12.x/controllers#resource-controllers)

**In books.index**  
We are using _Str::plural('review',$book->reviews_count)_
what it does is it generated plural or singular of value based on value and count respectively.  
i.e here if $book->reviews_count is 1 then 'review' is sent if it is more than 1 then 'reviews' renders.

```php
<div class="book-review-count">
out of  {{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }}
</div>
```

## Filter Books by Title - Adding the Form

so what happens here is simple in _index.blade.php_ we simply based on the searched value of **title** we find all the books with similar titles.

## Popular or Highest rated - View and Logic

### VIEW

so first well make a view in which on clicking the options style changes.

```php
<form method="GET" action="{{ route('books.index') }}" class="mb-4 flex items-center space-x-2">
    <input type="text" name="title" placeholder="Search by title" value="{{ request('title') }}" class="input h-10"/>
    <input type="hidden" name="filter" value="{{ request('filter') }}"/>
    <button type="submit" class="btn h-10">Search</button>
    <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>
</form>

<div class="filter-container mb-4 flex">
    @php
        $filters = [
            '' => 'Latest' ,
            'popular_last_month' => 'Popular Last Month',
            'popular_last_6month' => 'popular Last 6 Months',
            'highest_rated_last_month' => 'Highest Rated Last Month',
            'highest_rated_last_6month' => 'Highest Rated last 6 Months'
        ];
    @endphp

    @foreach ($filters as $key => $label)
    <a href="{{ route('books.index',[ ...request()->query(), 'filter'=> $key]) }}" class="{{ request('filter') === $key || (request('filter')=== null && $key === '') ? 'filter-item-active' : 'filter-item' }}">
        {{ $label }}
    </a>
    @endforeach

</div>
```

So whats happening here let's first talk about filters these are the filters for the data namely _latest, popular last month, popular last 6 months, Highest Rated Last Months, Highest Rated Last 6 months_ these are with keys and labels.  
In for each in _anchor tag_ we provide route name, [all request Query, the key to the filter] this is so that the view for these filters is enabled whenever we view page see class too as that works for starting rendering.  
Now, lets talk about **form** we added a **hidden input** type with _name = filter_ and _value = {{ request('filter') }}_ this is done because when ever the form is submitted the view gets to default and don't show previously choosen filter now this will work. And Hidden input is not shown too just submitted as request.

### LOGIC

So this logic part is the most important part here what we do here is ⤵️  
🚀. In the Book model we add **Local Query Scope** functions namely _scopePopularLastMonth, scopePopularLast6Months, scopeHighestRatedLastMonth, scopeHighestRatedLast6Months_ these are made with previous made Query scopes we can check them in the Book model.  
🚀. In **_BookController_** in **index** we can see along with _title_ we fetch _filter or ''_ and use

```php
$books = match($filter) {
    'popular_last_month' =>$books->popularLastMonth(),
    'popular_last_6month' =>$books->popularLast6Months(),
    'highest_rated_last_month' =>$books->highestRatedLastMonth(),
    'Highest_Rated_last_6month' =>$books->highestRatedLast6Months(),
    default => $books->latest()
};
```

what _match_ does is it kinda behaves as switch case.
so based on _$filter_ we decide query to be used

## ONE BOOK PAGE (show)

We made another book view namely **show.blade.php** which shows us the reviews and details of the books.  
In the **Book controller** in function _store_ we use eager loading using _load_ and a additional query for filtering.

```php
    public function show(Book $book)
    {

        return view('books.show',[
            'book'=> $book->load([
                'reviews' => fn($query) => $query->latest()
            ])
        ]);
    }
```

## Cache and Caching Quries

Some data retrival or processing tasks are CPU intensive so it takes time to retrive data to solve this, it is common to cache the retrived data for a period of time so that the retrival is fast on subsequent request.  
The cache configuration file is situated in **config > cache.php**.

### How to use cache

lets take an example in **_BookController's index_** we add following ⤵️

```php
    $books = Cache::remember('books', 3600, fn () => $books->get());
```

OR

```php
$cacheKey = 'books:' . $filter . ':' . $title;
$books = cache()->remember($cacheKey,3600,fn()=>$books->get);
```

**remember(key,ttl,callback)** ➡️ remember only stores data if the key is not present only. _key_ is like the key for the given data. _ttl_ it means timeperiod it is in seconds. _callback_ is a function to get the data.  
[To learn more about **_Cache_** click here](https://laravel.com/docs/12.x/cache#introduction)

## Invalidating Cache

What it means is that if there is some updation, deletion etc in the database the cache cannot get the changed data until the timeperiod is over, after that it automatically again get for the data using _remember_.
**But we can make sure on any updation or deletion etc on the database the _cache_ updates itself.** ⤵️  
Lets take an example form this code itself there might be some change in the _reviews_ like _reviews->rating_ etc. so for that we need to go to the **review model** in it ⤵️

```php
    protected static function booted()
    {
        static::updated(fn (Review $review) => cache()->forget('book:'. $review->book_id));
        static::deleted(fn(Review $review)=> cache()->forget('book:' . $review->book_id));
    }
```

so form now on whenever there occurs an event the cache will be deleted so that it can use _remember_ and update itself.  
**_Mass assignment_**  
For this to work the fillable needs to be assigned.Here model is loaded.
` $review->updated(['rating' => 1]);`  
**_In some cases it don't work_**  
`\App\Models\Review::where('id',944)->update(['rating' => 2])`  
now this is modified in database but not in page so that event is not triggered as this has _just run a query but not loaded the model_ so the **booted** don't work

[To know more about **events** click here](https://laravel.com/docs/12.x/eloquent#events)



## Rating and Review Count on All Pages   
We are using **with** here instead of **load** as load is used when the model is already loaded and we don't have model loaded as we are not using **route model binding**(show(Book $book)). **load** is a instance method i.e method on a created object and we need static method of a class instead i.e(_with_). 

```php
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
```

So lets see what all is happening here in **Book model** we make 2 Query Scope Methods in which works as sub part of the other Query Scope Methods and can also be called from outside namely *scopeWithReviewsCount* and *scopeWithAvgRating*
