<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieCategory extends Model
{
    protected $table = 'movie_category';

    protected $fillable = ['name', 'description'];

    public function movies()
    {
        return $this->hasMany(Movie::class, 'category_id');
    }
}
