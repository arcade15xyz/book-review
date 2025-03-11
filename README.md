# Books and Review Project

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

```
$book = \App\Models\Book::find(1);

$review = new \App\Model\Review();

$review->review = 'This is a good book';

$review->rating = 5

$book->reviews()->save($review);

//How to get the reviews for the book
$book->reviews;
```

### Now to add a review for a book we are getting the data via form so we need to assign $fillables in the "Review Model".

```
$book = App\Models\Book::find(1);

//What is happening here is we are creating a review for the book using associtaion / relation
$review = $book->reviews()->create(['reviews' => 'A very Good book], 'rating' => 5);
```

### To find the book through a review

```
$review = App\Models\Review::find(1);

$review->book;
```

what this will do is it will provide us the book for which that review is made;

### If we need to change relation of review to other book

```
//initially the book_id = 1
$review = \App\Models\Review::find(1);

//get information on the book for review
$review->book;


$book2 = \App\Models\Book::find(2);

//save the $review form book_id=1 to book_id=2;
$book2->reviews()->save($review);

```

### How to search

```
\App\Models\Book::where('title', 'LIKE', '%delectus%')->get();
```



## Local Query Scope

What local Query scope is that we make methods that scopes queries with some action i.e. :apple:\ instead of using the query we just use these **Local Query Scope**. This local Query scope is declare/defined in the model;
The Query Scope is declared with 'scope' as prefix and when calling it this prefix is discarded;
**_example :arrow_heading_down:_**

```
//In Book Model

public function scopeTitle(Builder $query, string $title)
{
    return $query->where('title', 'LIKE', '%'.$title.'%');
}
```

**_How is it used_**

```
\App\Models\Book::title('qui')->get();
```

We can use some adder parameters here like below :arrow_heading_down:

```
\App\Models\Book::title('qui')->where('created_at', ">=", "2024-01-01")->get();
```

**_Misslinious_**
if we want to know the vanilla sql query in the terminal use :arrow_heading_down:

```
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

```
\App\Models\Book::withCount('reviews')->get();
```

:key: Here **withCount('reviews')** Add a new **reviews_count** attribute that holds the number of reviews each book has.

```
\App\Models\Book::withCount('reviews')->latest()->limit(3)->get();
```

It does the same thing as above and **latest()->limit(3)** gives us the latest 3 books along with **reviews_count**

### Using Avg (withAvg('reviews','rating'))

What this **Eloquent query** does is it retrieves all books from database along with **avg** of their related **reviews->rating**.

🔑 So what it does is for each **Book** to be get() it will add a attribute for **_withAvg('reviews','rating')_** as **reviews_avg_rating**;

**EXAMPLES**

```
\App\Models\Book::limit(5)->withAvg('reviews','rating')->orderBy('reviews_avg_rating')->get();
```
It show the details of 5 books by **reviews_avg_rating** ascending mode


```
\App\Models\Book:withCount('reviews')->withAvg('reviews','rating')->having('reviews_count','>=', 10)->orderBy('reviews_avg_rating','desc')->limit(10)->get();
```
It shows 10 book data according ordered by the *reviews_avg_rating* in desc which are having *reviews_count* greater than 10 and add *reviews_avg_rating* and *reviews_count*.

🔑 In case multiple Aggregation are done as in above example it gives us multiple attributes .

**_Misslinious_**
In case of query on Aggregation on relation we use *having* instead of *where* **query builder method**.
