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
            $this->mergeWhen($request->routeIs('prospects.show'), [
                'firstName' => $prospect->first_name,
                'lastName' => $prospect->last_name,
                'email' => $prospect->email,
                'phone' => $prospect->email,
            ]),
            'gender' => $prospect->gender,
            'age' => $prospect->age,
            'birthDate' => $prospect->birth_date,
            'image' => $prospect->image,
            'bloodGroup' => $prospect->blood_group,
            'height' => $prospect->height,
            'weight' => $prospect->weight,
            'eyeColor' => $prospect->eye_color,
            'hairColor' => $prospect->hair_color,
            'hairType' => $prospect->hair_type,
            'address' => $prospect->address,
            $this->mergeWhen($request->routeIs('prospects.show'), [
                'source' => $prospect->source->label(),
            ]),
        ];
    }
}
