<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Http\Requests\MealRequest;

class MealController extends Controller
{
    public function index(MealRequest $request)
    {
        // Determine the query type
        if ($this->request->has('diff_time')) {
            $query = Meal::withTrashed();
        } else {
            $query = Meal::query();
        }

        // Filter meals based on diff_time, category and tags parameters
        $query
            ->when($this->request->has('diff_time'), function($query) {
                $query->filterByDiffTime($this->request->diff_time);
            })
            ->when($this->request->has('category'), function($query) {
                $query->filterByCategory($this->request->category);
            })
            ->when($this->request->has('tags'), function($query) {
                $tags = explode(',', $this->request->tags);
                $query->filterByTags($tags);
            });
            
        // Paginate based on per_page and page parameter
        $total = $query->get()->count();
        $perPage = $this->request->per_page ?? $total;
        $page = $this->request->page ?? 1;
        $totalPages = ceil($total / $perPage) ?? 1;

        $meals = $query->paginate(intval($perPage), ['*'], 'page', intval($page));

        // Format meals data based on with parameter
        $lang = $this->request->lang;
        $with = $this->request->with;
        $keywords = explode(',', $with);
        
        $meals = $meals->map(function ($meal) use ($lang, $keywords) {
            $mealData = [
                'id' => $meal->id,
                'title' => $meal->{"title:$lang"},
                'description' => $meal->{"description:$lang"},
                'status' => $meal->status
            ];

            // Show additional data - category
            if (in_array('category', $keywords)) {
                $mealData['category'] = $meal->categories ? [
                    'id' => $meal->categories->id,
                    'title' => $meal->categories->{"title:$lang"},
                    'slug' => $meal->categories->slug,
                ] : null;
            }

            // Show additional data - tags
            if (in_array('tags', $keywords)) {
                $mealData['tags'] = $meal->tags->map(function($tag) use ($lang) {
                    return [
                        'id' => $tag->id,
                        'title' => $tag->{"title:$lang"},
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
                'prev' => $page > 1 ? $this->request->fullUrlWithQuery(['page' => $page - 1]) : null,
                'next' => $totalPages > $page ? $this->request->fullUrlWithQuery(['page' => $page + 1]) : null,
                'self' => url()->full(),
            ],
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }
}