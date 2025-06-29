<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

final class Landingpage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'campaign_id', 'title', 'slug', 'headline', 'subline', 'sections', 'form_fields', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the campaign that owns the landingpage.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
