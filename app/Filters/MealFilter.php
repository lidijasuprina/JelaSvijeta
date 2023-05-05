<?php

namespace App\Filters;

use App\Http\Requests\MealRequest;
use App\Models\Meal;

class MealFilter
{
    public function filter(MealRequest $request)
    {
        // Determine the query type
        if ($request->has('diff_time')) {
            $query = Meal::withTrashed()->filterByDiffTime($request->diff_time);
        } else {
            $query = Meal::query();
        }

        // Filter meals based on diff_time, category and tags parameters
        $query->with(['translations'])
            ->when($request->has('with'), function($query) use ($request) {
                $query->filterByWith($request->with);
            })
            ->when($request->has('category'), function($query) use ($request) {
                $query->filterByCategory($request->category);
            })
            ->when($request->has('tags'), function($query) use ($request) {
                $query->filterByTags($request->tags);
            });

        return $query;
    }
}
