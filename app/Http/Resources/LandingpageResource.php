<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LandingpageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Landingpage $landingpage */
        $landingpage = $this->resource;

        return [
            'id' => $landingpage->id,
            'campaigns' => CampaignResource::collection($this->whenLoaded('campaigns')),
            'title' => $landingpage->title,
            'headline' => $landingpage->headline,
            'subline' => $landingpage->subline,
            'sections' => $landingpage->sections,
            // 'form_fields' => $landingpage->form_fields,
        ];
    }
}
