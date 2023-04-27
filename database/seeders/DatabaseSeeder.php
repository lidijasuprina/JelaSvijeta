<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

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
        \App\Models\Ingredient::factory(10)->create();
        \App\Models\Tag::factory(10)->create();
        \App\Models\Category::factory(10)->create();
        \App\Models\Meal::factory(10)->create();
    }
}
