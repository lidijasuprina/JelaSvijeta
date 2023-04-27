<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locales = config('translatable.locales');

        $languages = collect($locales)->flatMap(function ($locales, $language) {
            return collect($locales)->map(function ($locale) use ($language) {
                $localeParts = explode('_', $locale);
                $code = isset($language) && is_string($language) ? $language.'_'.$localeParts[0] : $localeParts[0];
                $countryCode = isset($language) && is_string($language) ? $localeParts[0] : null;

                return [
                    'code' => $code,
                    'language' => isset($language) && is_string($language) ? $language : $locale,
                    'locale' => $countryCode,
                ];
            });
        });

        DB::table('languages')->insert($languages->toArray());

        // DB::table('languages')->insert($languages->toArray());

        // var_dump($locales);
        // $languages = collect($locales)
        //     ->map(function ($locale) {
        //         $locale = is_array($locale) ? $locale[0] : $locale;
        //         $parts = explode('_', $locale);

        //         return [
        //             'code' => $locale,
        //             'language' => ucfirst($parts[0]),
        //             'locale' => ucfirst($parts[0]),
        //         ];
        //     })
        //     ->groupBy('language');

        // foreach ($languages as $language) {
        //     if (DB::table('languages')->where('code', $language)->doesntExist()) {
        //         DB::table('languages')->insert([
        //             'code' => $language['code'],
        //             'language' => $language['language'],
        //             'locale' => $language['locale'],
        //         ]);
        //     }
        // }

        // foreach ($languages as $name => $locales) {

        //     foreach ($locales as $locale) {
        //         DB::table('languages')->insert([
        //             'code' => $locale['code'],
        //             'language' => $locale['language'],
        //             'locale' => $locale['locale'],
        //         ]);
        //     }
        // }
    }
}
