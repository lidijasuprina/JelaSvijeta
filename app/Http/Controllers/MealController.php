<?php

namespace App\Http\Controllers;

use App\Http\Requests\MealRequest;
use App\Filters\MealFilter;
use App\Http\Resources\MealCollection;

class MealController extends Controller
{
    public function index(MealRequest $request)
    {
        // Filter meals based on diff_time, category and tags parameters
        $query = MealFilter::filter($request);
            
        // Paginate meals based on per_page and page parameter
        $meals = $query->paginate(intval($request->per_page) ?? null, ['*'], 'page', intval($request->page ?? null));
        
        // Return formatted data
        return new MealCollection($meals->appends($request->query()));
    }
}