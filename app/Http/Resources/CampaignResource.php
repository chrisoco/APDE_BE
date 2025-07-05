<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

final class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Campaign $campaign */
        $campaign = $this->resource;

        return [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'description' => $campaign->description,
            $this->mergeWhen(Gate::allows('viewAny', Campaign::class), [
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'status' => $campaign->status->label(),
                'prospect_filter' => $campaign->prospect_filter ?? [],
            ]),
            'landingpage' => new LandingpageResource($this->whenLoaded('landingpage')),
        ];
    }
}
