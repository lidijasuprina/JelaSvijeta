<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tag;
use App\Models\Language;

class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Tag::class;

    public function definition()
    {
        return [
            'slug' => $this->faker->slug(3, false),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Tag $model) {
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
