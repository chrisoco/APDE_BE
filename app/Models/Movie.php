<?php

declare(strict_types=1);

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

final class Movie extends Model
{
    protected $fillable = ['title', 'year', 'runtime', 'imdb', 'plot', 'actors'];

    protected $casts = [
        'created_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * @return array<int, array<string, int|string>>
     */
    public function generateActors(): array
    {
        return [['name' => 'John Doe', 'age' => 30], ['name' => 'Jane Doe', 'age' => 25], ['name' => 'John Smith', 'age' => 35], ['name' => 'Jane Smith', 'age' => 30]];
    }
}
