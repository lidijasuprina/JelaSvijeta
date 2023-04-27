<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Tag extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $timestamps = false;
    public $translatedAttributes = ['title'];
    protected $fillable = ['slug'];

    public function tags()
    {
        return $this->belongsToMany(Meal::class, 'meals_tags', 'tag_id', 'meal_id');
    }
}
