<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subcategory>
 */
class SubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(), // Defaults to creating a parent category
            'name' => ucfirst($this->faker->word()) . ' ' . rand(100, 99999),
            'description' => $this->faker->sentence(),
            'slug' => $this->faker->slug() . '-' . uniqid(),
            'active' => true,
        ];
    }
}
