<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Arr::collapse(array_map(function ($value, $key) {
            if (is_array($value)) {
                return [$key];
            }
            return [$value];
        }, config('translatable.locales'), array_keys(config('translatable.locales'))));
        
        $languages = array_unique($languages);
        
        $languages = collect($languages)->map(function ($language) {
            return [
                'code' => $language,
                'name' => $language,
            ];
        });
        
        DB::table('languages')->insert($languages->toArray());
    }
}
