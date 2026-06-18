<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id',
        'title',
        'year',
        'genre',
        'director',
        'actors',
        'plot',
        'poster_url',
        'imdb_rating',
        'runtime',
        'country',
    ];

    public function watchlistItems()
    {
        return $this->hasMany(WatchlistItem::class);
    }
}
