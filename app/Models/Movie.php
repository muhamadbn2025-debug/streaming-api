<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'rating',
        'release_year',
        'category_id',
        'thumbnail'
    ];

    public function category()
    {
        return $this->belongsTo(MovieCategory::class, 'category_id');
    }
}
