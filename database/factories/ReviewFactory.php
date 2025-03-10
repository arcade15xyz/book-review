<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $created_at = $this->faker->dateTimeBetween("-2 years","now");
        return [
            'book_id' => null,
            'review' => fake()->paragraph,
            //if we use this numberBetween then almost all the entries will be average
            'rating'=> fake()->numberBetween(1,5),
            'created_at' => $created_at,
            'updated_at' => fake()->dateTimeBetween($created_at,'now')
        ];
    }
    
    /**
     * This is a custom state method provides the entries(ratings) with 4 or 5 (values)
     * @return ReviewFactory
     */
    public function good(){
        return $this->state(function (array $attributes) {
            return[
                'rating' => fake()->numberBetween(4,5)
            ];
    });
    }

    /**
     * This is a custom state method  which provides the entries to the rating with average value(2-5)
     * @return ReviewFactory
     */
    public function average(){
        return $this->state(function(array $attributes){
            return[
                'rating'=> fake()->numberBetween(2,5)
            ];
        });
    }

    /**
     * This is a custom state method which provides the entries to the rating with bad value(1-3)
     */
    public function bad(){
        return $this->stae(function (array $attributes){
            return[
                'rating'=>fake()->numberBetween(1,3)
            ];
        });
    }

}
