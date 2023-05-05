<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MealCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function withResponse($request, $response)
    {
        // Get current meta data
        $data = $response->getData(true);
        $currentPage = $data['meta']['current_page'];
        $totalItems = $data['meta']['total'];
        $itemsPerPage = $data['meta']['per_page'];
        $totalPages = $data['meta']['last_page'];
        
        // Format links' urls to have all parameters
        $prev = $data['links']['prev'];
        $next = $data['links']['next'];
        $self = $request->fullUrl();

        // Override default meta and links
        $data['meta'] = compact('currentPage', 'totalItems', 'itemsPerPage', 'totalPages');
        $data['links'] = compact('prev', 'next', 'self');

        $response->setData($data);
    }
}