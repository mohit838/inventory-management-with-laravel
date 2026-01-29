<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(10)->create()->each(function ($category) {
            Subcategory::factory(3)->create([
                'category_id' => $category->id,
            ]);
        });
    }
}
