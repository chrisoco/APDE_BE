<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\LandingpagePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Relations\BelongsTo;

/**
 * @property string $id
 * @property string|null $campaign_id
 * @property string $title
 * @property string $slug
 * @property string $headline
 * @property string|null $subline
 * @property array<int, array<string, mixed>> $sections
 * @property array<string, mixed>|null $form_fields
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
#[UsePolicy(LandingpagePolicy::class)]
final class Landingpage extends Model
{
    /** @use HasFactory<\Database\Factories\LandingpageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'campaign_id', 'title', 'slug', 'headline', 'subline', 'sections', 'form_fields', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the campaign that owns the landingpage.
     *
     * @return BelongsTo<Campaign, Landingpage>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
