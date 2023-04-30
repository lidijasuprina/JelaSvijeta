<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Language;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealController extends Controller
{
    public function index(Request $request)
    {
        // Validate lang - required
        if (!$request->has('lang')) {
            return response()->json(['error' => "'lang' parameter is required"], 400);
        } else if (!Language::where('code', $request->lang)->exists()) {
            return response()->json(['error' => "'$request->lang' is not a language for this app"], 400);
        }
        
        // Validate per_page - optional
        if ($request->has('per_page') && !is_numeric($request->per_page)) {
            return response()->json(['error' => "'per_page' parameter must be a number"], 400);
        }

        // Validate page - optional
        if ($request->has('page') && !is_numeric($request->page)) {
            return response()->json(['error' => "'page' parameter must be a number"], 400);
        }

        // Validate category - optional
        if ($request->has('category') && !in_array(strtolower($request->category), ['null', '!null']) && !is_numeric($request->category)) {
            return response()->json(['error' => "'category' parameter must be a number, 'null', or '!null'"], 400);
        }

        // Validate tags - optional
        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            foreach ($tags as $tag) {
                if (!is_numeric($tag)) {
                    return response()->json(['error' => "'tags' parameter must be a comma-separated list of numbers"], 400);
                }
            }
        }

        // Validate with - optional
        if ($request->has('with')) {
            $keywords = ['category', 'tags', 'ingredients'];
            $with = explode(',', $request->with);
            foreach ($with as $keyword) {
                if (!in_array($keyword, $keywords)) {
                    return response()->json(['error' => "'with' parameter must be a comma-separated list of 'category', 'tags', or 'ingredients'"], 400);
                }
            }
        }

        // Validate diff_time - optional
        if ($request->has('diff_time')) {
            if (!is_numeric($request->diff_time) || $request->diff_time <= 0) {
                return response()->json(['error' => "'diff_time' parameter must be a positive number"], 400);
            }
            $query = Meal::withTrashed();
        } else {
            $query = Meal::query();
        }

        // Filter by diff_time, category, tags
        $query
            ->when($request->has('diff_time'), function($query) use ($request) {
                $query->filterByDiffTime($request->diff_time);
            })
            ->when($request->has('category'), function($query) use ($request) {
                $query->filterByCategory($request->category);
            })
            ->when($request->has('tags'), function($query) use ($request) {
                $tags = explode(',', $request->tags);
                $query->filterByTags($tags);
            });
            
        // Pagination based on per_page and page parameter
        $total = $query->get()->count();
        $perPage = $request->per_page ?? $total;
        $page = $request->page ?? 1;
        $totalPages = ceil($total / $perPage) ?? 1;

        $meals = $query->paginate(intval($perPage), ['*'], 'page', intval($page));

        // Format meals data based on with parameter
        $lang = $request->lang;
        $with = $request->with;
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
                'prev' => $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null,
                'next' => $totalPages > $page ? $request->fullUrlWithQuery(['page' => $page + 1]) : null,
                'self' => url()->full(),
            ],
        ];

        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }
}