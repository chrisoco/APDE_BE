<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CampaignSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Campaign $campaign */
        $campaign = $this->resource;

        return [
            'id' => $campaign->id,
            'status' => $campaign->status->label(),
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
        ];
    }
}
