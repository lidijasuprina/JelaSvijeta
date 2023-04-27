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
        $locales = config('translatable.locales');
        $title = $this->faker->sentence();

        return [
            'slug' => $this->faker->slug(3, false),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Ingredient $ingredient) {
            $languages = Language::all();

            foreach ($languages as $language) {
                $ingredient->translations()->create([
                    'locale' => $language->code,
                    'title' => $this->faker->sentence().' '.$language->code,
                ]);
            }
        });
    }
}
