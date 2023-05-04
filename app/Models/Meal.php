<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes;

    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['status'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($meal) {
            $meal->status = 'deleted';
            $meal->save();
        });
    }

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

    public function scopeFilterByCategory($query, $category)
    {
        if (strtolower($category) == 'null') {
            $query->whereNull('category_id');
        } else if (strtolower($category) == '!null') {
            $query->whereNotNull('category_id');
        } else {
            $query->where('category_id', $category);
        }
        return $query;
    }

    public function scopeFilterByTags($query, $tags)
    {
        $tags = explode(',', $tags);
        foreach ($tags as $tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('tag_id', $tag);
            });
        }
        return $query;
    }

    public function scopefilterByDiffTime($query, $diffTime)
    {
        $query->where(function($q) use ($diffTime) {
            $q->where('created_at', '>', date('Y-m-d H:i:s', $diffTime))
            ->orWhere('updated_at', '>', date('Y-m-d H:i:s', $diffTime))
            ->orWhere('deleted_at', '>', date('Y-m-d H:i:s', $diffTime));
        });
    }

    public function scopefilterByWith($query, $with)
    {
        $with = str_replace('category', 'categories', $with);
        $with = explode(',', $with);
        $query->with($with);
    }       
}
