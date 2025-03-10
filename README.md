# Books and Review Project

So whats happening here is that we are using relation one to many and using these relationship for making a **Book and Review**

# Qurerying and Associting Realted Models
 
 ## To get the book data for a specific book this is lazy loading
 ```$book = \App\Models\Book::find(1);```
 
 ## To get the reviews for the specific book this is lazy loading
 ```$reviews = $book->reviews;```
 the reviews is the method provided in the Book model

 ## To get the book data as well as reviews for the book the reviews is the method in book model this is also lazy loading
 ```\App\Models\Book::with("reviews")->find(1);```
 
 ## To get the multiple data for books with now this is example of eager loading
 ```$books = App\Models\Book::with('reviews')->take(3)->get();```

 ## using "load" we can load multiple relationships for a $book
 ```
 //this gives us the data for a book
 $book = App\Models\Book::find(2);
 //this load the relationships for the given $book
 $book->load('reviews');
 ```
 ## How to add a new review for the $book
 ```
 $book = \App\Models\Book::find(1);

 $review = new \App\Model\Review();

 $review->review = 'This is a good book';

 $review->rating = 5

 $book->reviews()->save($review); 

 //How to get the reviews for the book
 $book->reviews;
 ```

 ## Now to add a review for a book we are getting the data via form so we need to assign $fillables in the "Review Model".
 ```
 $book = App\Models\Book::find(1);

 //What is happening here is we are creating a review for the book using associtaion / relation
 $review = $book->reviews()->create(['reviews' => 'A very Good book], 'rating' => 5); 
 ```
 
 ## To find the book through a review
 ```
 $review = App\Models\Review::find(1);

 $review->book;
 ```
 what this will do is it will provide us the book for which that review is made;


## If we need to change relation of review to other book 
```
//initially the book_id = 1
$review = \App\Models\Review::find(1);

//get information on the book for review
$review->book;


$book2 = \App\Models\Book::find(2);

//save the $review form book_id=1 to book_id=2;
$book2->reviews()->save($review);

```
