<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Http\Requests\MealRequest;

class MealController extends Controller
{
    public function index(MealRequest $request)
    {
        $lang = $request->lang;
        // Determine the query type
        if ($request->has('diff_time')) {
            $query = Meal::withTrashed()->filterByDiffTime($request->diff_time);
        } else {
            $query = Meal::query();
        }

        // Filter meals based on diff_time, category and tags parameters
        $query
            ->when($request->has('with'), function($query) use ($request) {
                $query->filterByWith($request->with);
            })
            ->when($request->has('category'), function($query) use ($request) {
                $query->filterByCategory($request->category);
            })
            ->when($request->has('tags'), function($query) use ($request) {
                $query->filterByTags($request->tags);
            });
            
        // Paginate based on per_page and page parameter
        $meals = $query->paginate(intval($request->per_page) ?? null, ['*'], 'page', intval($request->page ?? 1));
        $page = $meals->currentPage();
        $total = $meals->total();
        $perPage = $request->per_page ? $meals->perPage() : $total;
        $totalPages = $meals->lastPage();

        // Format meals data based on with parameter
        $with = $request->with;
        $keywords = explode(',', $with);
        
        $meals = $meals->map(function ($meal) use ($lang, $keywords) {
            $mealData = [
                'id' => $meal->id,
                'title' => $meal->translate($lang)->title,
                'description' => $meal->translate($lang)->description,
                'status' => $meal->status
            ];

            // Show additional data - category
            if (in_array('category', $keywords)) {
                $mealData['category'] = $meal->categories ? [
                    'id' => $meal->categories->id,
                    'title' => $meal->categories->translate($lang)->title,
                    'slug' => $meal->categories->slug,
                ] : null;
            }

            // Show additional data - tags
            if (in_array('tags', $keywords)) {
                $mealData['tags'] = $meal->tags->map(function($tag) use ($lang) {
                    return [
                        'id' => $tag->id,
                        'title' => $tag->translate($lang)->title,
                        'slug' => $tag->slug
                    ];
                });
            }

            // Show additional data - ingredients
            if (in_array('ingredients', $keywords)) {
                $mealData['ingredients'] = $meal->ingredients->map(function($ingredient) use ($lang) {
                    return [
                        'id' => $ingredient->id,
                        'title' => $ingredient->{"title:$lang"},
                        'slug' => $ingredient->slug
                    ];
                });
            }

            return $mealData;
        });
        
        // Format the full response
        $response = [
            'meta' => [
                'currentPage' => $page,
                'totalItems' => $total,
                'itemsPerPage' => $perPage,
                'totalPages' => $totalPages,
            ],
            'data' => $meals,
            'links' => [
                'prev' => $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null,
                'next' => $totalPages > $page ? $request->fullUrlWithQuery(['page' => $page + 1]) : null,
                'self' => url()->full(),
            ],
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }
}