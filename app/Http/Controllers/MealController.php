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

        $lang = $request->lang;
        $with = $request->with;
        $keyword = explode(',', $with);
        $meta = [];
        $links = [];
        $data = [];

        // Add diff_time filter
        if ($request->has('diff_time')) {
            $query = Meal::withTrashed();
            $diffTime = $request->diff_time;
            $query->where(function($q) use ($diffTime) {
                $q->where('created_at', '>', date('Y-m-d H:i:s', $diffTime))
                ->orWhere('updated_at', '>', date('Y-m-d H:i:s', $diffTime))
                ->orWhere('deleted_at', '>', date('Y-m-d H:i:s', $diffTime));
            });
        } else {
            $query = Meal::query();
        }

        // Filter by category
        if (isset($request->category)) {
            $category = $request->category;
            if (strtolower($category) == 'null') {
                $query->whereNull('category_id');
            } else if (strtolower($category) == '!null') {
                $query->whereNotNull('category_id');
            } else {
                $query->whereHas('categories', function($q) use ($category) {
                    $q->where('id', $category);
                });
            }
        }

        // Filter by tags
        if (isset($request->tags)) {
            $tags = $request->tags;
            $tags = explode(',', $tags);
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('tag_id', $tag);
                });
            }
        }

        $meals = $query->get();

        // Format meals data
        $meals = $meals->map(function ($meal) use ($lang, $keyword) {
            $mealData = [
                'id' => $meal->id,
                'title' => $meal->{"title:$lang"},
                'description' => $meal->{"description:$lang"},
                'status' => $meal->status
            ];

            // Show additional data - category
            if (in_array('category', $keyword)) {
                $mealData['category'] = $meal->categories ? [
                    'id' => $meal->categories->id,
                    'title' => $meal->categories->{"title:$lang"},
                    'slug' => $meal->categories->slug,
                ] : null;
            }

            // Show additional data - tags
            if (in_array('tags', $keyword)) {
                $mealData['tags'] = $meal->tags->map(function($tag) use ($lang) {
                    return [
                        'id' => $tag->id,
                        'title' => $tag->{"title:$lang"},
                        'slug' => $tag->slug
                    ];
                });
            }

            // Show additional data - ingredients
            if (in_array('ingredients', $keyword)) {
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

        // Get the meals and return them as a JSON response
        $perPage = isset($request->per_page) ? $request->per_page : null;
        $page = isset($request->page) ? $request->page : null;

        // Pagination
        if (!is_null($page) && !is_null($perPage)) {
            // && $totalPages > $page
            $query->paginate(intval($perPage), ['*'], 'page', intval($page));
        }
        $total = $meals->count();
        $totalPages = isset($request->per_page) ? ceil($total / $perPage) : null;

        $meta = [
            'currentPage' => $page ?? 1,
            'totalItems' => $total,
            'itemsPerPage' => $perPage ?? $total,
            'totalPages' => $totalPages ?? 1,
        ];
    
        $links = [
            'prev' => $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null,
            'next' => $totalPages > $page ? $request->fullUrlWithQuery(['page' => $page + 1]) : null,
            'self' => url()->full(),
        ];
    
        $response = [
            'meta' => $meta,
            'data' => $meals,
            'links' => $links,
        ];

        
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }
}
