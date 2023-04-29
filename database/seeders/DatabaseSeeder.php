<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Ingredient;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Meal;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if(is_null(Language::first())) {
            $this->call([
                LanguageSeeder::class
            ]);
        }
        Ingredient::factory(10)->create();
        Tag::factory(10)->create();
        Category::factory(10)->create();
        Meal::factory(10)->create();
        Meal::first()->delete();
    }
}
