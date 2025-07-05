<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CampaignTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\CampaignTracking $tracking */
        $tracking = $this->resource;

        return [
            'id' => $tracking->id,
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),
            'landingpage' => new LandingpageResource($this->whenLoaded('landingpage')),
            'prospect' => new ProspectResource($this->whenLoaded('prospect')),
            'session_id' => $tracking->session_id,
            'ip_address' => $tracking->ip_address,
            'user_agent' => $tracking->user_agent,
            'referrer' => $tracking->referrer,
            'utm_parameters' => $tracking->getUtmParameters(),
            'gclid' => $tracking->gclid,
            'fbclid' => $tracking->fbclid,
            'tracking_data' => $tracking->tracking_data,
            'first_visit_at' => $tracking->first_visit_at,
            'last_visit_at' => $tracking->last_visit_at,
            'visit_count' => $tracking->visit_count,
            'converted' => $tracking->converted,
            'converted_at' => $tracking->converted_at,
            'created_at' => $tracking->created_at,
            'updated_at' => $tracking->updated_at,
        ];
    }
}
