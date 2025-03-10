<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);



        /**
         * Using a "factory" method for creating instance of the model then "create" collection of model and presist them to database then "each" for each of the factory instance for model Review with "count" and a custom state method "good()" which is "for" $book object for current instance and create it so both book and its reviews are created here
         */
        Book::factory(33)->create()->each(function($book){
            $numReviews = random_int(5,30);

            Review::factory()->count($numReviews)->good()->for($book)->create();

        });


        /**
         * Using a "factory" method for creating instance of the model then "create" collection of model and presist them to database then "each" for each of the factory instance for model Review with "count" and a custom state method "avg()" which is "for" $book object for current instance and create it so both book and its reviews are created here
         */
        Book::factory(33)->create()->each(function($book){
            $numReviews = random_int(5,30);

            Review::factory()->count($numReviews)->average()->for($book)->create();

        });

        /**
         * Using a "factory" method for creating instance of the model then "create" collection of model and presist them to database then "each" for each of the factory instance for model Review with "count" and a custom state method "bad()" which is "for" $book object for current instance and create it so both book and its reviews are created here
         */
        Book::factory(33)->create()->each(function($book){
            $numReviews = random_int(5,30);

            Review::factory()->count($numReviews)->bad()->for($book)->create();

        });
    }
}
