<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Meal;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Ingredient;
use App\Models\Language;

class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Meal::class;

    public function definition()
    {
        return [
            'status' => 'created',
            'category_id' => $this->faker->optional()->randomElement(Category::pluck('id')),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($model) {
            $languages = Language::all();

            foreach ($languages as $language) {
                $model->translations()->create([
                    'locale' => $language->code,
                    'title' => $this->faker->words(3, true).' '.$language->code,
                    'description' => $this->faker->sentence().' '.$language->code,
                ]);
            }

            $tag = Tag::inRandomOrder()->first();
            $model->tags()->attach($tag);

            $ingredient = Ingredient::inRandomOrder()->first();
            $model->ingredients()->attach($ingredient);
        });
    }
}
