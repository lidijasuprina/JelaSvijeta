<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ingredient;
use App\Models\Language;

class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Ingredient::class;

    public function definition()
    {
        return [
            'slug' => $this->faker->slug(3, false),
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
                ]);
            }
        });
    }
}
