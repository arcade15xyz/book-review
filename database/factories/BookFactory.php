<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
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
            'title'=>fake()->sentence(3),
            'author'=>fake()->name(),
            'created_at'=>$created_at,
            'updated_at'=>fake()->dateTimeBetween($created_at,'now')
        ];
    }
}
