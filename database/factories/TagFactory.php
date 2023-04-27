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
        $locales = config('translatable.locales');
        $title = $this->faker->sentence();

        return [
            'slug' => $this->faker->slug(3, false),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Tag $tag) {
            $languages = Language::all();

            foreach ($languages as $language) {
                $tag->translations()->create([
                    'locale' => $language->code,
                    'title' => $this->faker->sentence().' '.$language->code,
                ]);
            }
        });
    }
}
