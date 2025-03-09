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
            'rating'=> fake()->numberBetween(1,5),
            'created_at' => $created_at,
            'updated_at' => fake()->dateTimeBetween($created_at,'now')
        ];
    }

    public function good(){
        return $this->state(function (array $attributes) {
            return[
                'rating' => fake()->numberBetween(4,5)
            ];
    });
    }

    public function average(){
        return $this->state(function(array $attributes){
            return[
                'rating'=> fake()->numberBetween(2,5)
            ];
        });
    }

    public function bad(){
        return $this->stae(function (array $attributes){
            return[
                'rating'=>fake()->numberBetween(1,3)
            ];
        });
    }

}
