<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;

class MealController extends Controller
{
    public function index(Request $request)
    {
        $query = Meal::query();
        
        $perPage = isset($request->per_page) ? $request->per_page : null;
        $page = isset($request->page) ? $request->page : null;

        
        // Pagination
        if (!is_null($page) && !is_null($perPage)) {
            // && $totalPages > $page
            $query->paginate($perPage, ['*'], 'page', $page);
        }
        
        // Filter by category
        if (isset($request->category)) {
            $category = $request->category;
            if ($category == 'null') {
                $query->whereNull('category_id');
            } else if ($category == '!null') {
                $query->whereNotNull('category_id');
            } else {
                $query->where('category_id', $category);
            }
        }

        // Get the meals and return them as a JSON response
        $meals = $query->get();
        $total = $query->get()->count();
        $totalPages = isset($request->per_page) ? ceil($total / $perPage) : null;

        $meta = [
            'currentPage' => $page,
            'totalItems' => $total,
            'itemsPerPage' => $perPage,
            'totalPages' => $totalPages,
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
