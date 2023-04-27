<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $timestamps = false;
    public $translatedAttributes = ['title'];
    protected $fillable = ['id', 'slug'];

    public function meals()
    {
        return $this->belongsTo('App\Models\Meal');
    }
}
