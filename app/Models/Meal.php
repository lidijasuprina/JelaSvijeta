<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Meal extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['status'];

    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id', 'id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'meals_ingredients', 'meal_id', 'ingredient_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'meals_tags', 'meal_id', 'tag_id');
    }
}
