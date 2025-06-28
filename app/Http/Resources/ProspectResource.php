<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Prospect
 */
final class ProspectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Prospect $prospect */
        $prospect = $this->resource;

        return [
            'id' => $prospect->id,
            // Maybe not expose due tue security concerns / regulations.
            $this->mergeWhen($request->routeIs('prospects.show'), [
                'firstName' => $prospect->first_name,
                'lastName' => $prospect->last_name,
            ]),
            'age' => $prospect->age,
            'gender' => $prospect->gender,
            'birthDate' => $prospect->birth_date,
            'bloodGroup' => $prospect->blood_group,
            'height' => $prospect->height,
            'weight' => $prospect->weight,
            'eyeColor' => $prospect->eye_color,
            'hairColor' => $prospect->hair_color,
            'hairType' => $prospect->hair_type,
            'university' => $prospect->university,
            'role' => $prospect->role,
            $this->mergeWhen($request->routeIs('prospects.show'), [
                'source' => $prospect->source?->label(),
            ]),
        ];
    }
}
