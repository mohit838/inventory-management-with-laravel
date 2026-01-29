<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have categories and subcategories first
        $subcategories = Subcategory::all();

        if ($subcategories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $subcategories = Subcategory::all();
        }

        Product::factory(50)->make()->each(function ($product) use ($subcategories) {
            $subcategory = $subcategories->random();
            $product->category_id = $subcategory->category_id;
            $product->subcategory_id = $subcategory->id;
            $product->save();
        });
    }
}
