<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->translate($request->lang)->title,
            'description' => $this->translate($request->lang)->description,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('categories')),
            'tags' => new TagCollection($this->whenLoaded('tags')),
            'ingredients' => new IngredientCollection($this->whenLoaded('ingredients'))
        ];
    }
}
