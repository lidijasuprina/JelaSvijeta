<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Language;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Category::class;

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
        return $this->afterCreating(function (Category $category) {
            $languages = Language::all();

            foreach ($languages as $language) {
                $category->translations()->create([
                    'locale' => $language->code,
                    'title' => $this->faker->sentence().' '.$language->code,
                ]);
            }
        });
    }
}
