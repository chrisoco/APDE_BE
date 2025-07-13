<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\Prospect;
use App\Services\CampaignTrackingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

final class CampaignEmailController extends Controller
{
    public function __construct(
        private readonly CampaignTrackingService $trackingService
    ) {}

    public function send(Campaign $campaign): JsonResponse
    {
        Gate::authorize('sendEmails', $campaign);

        if (! $campaign->landingpage) {
            return response()->json([
                'message' => 'Campaign must have an associated landing page to send emails.',
            ], 400);
        }

        if (! $campaign->prospect_filter) {
            return response()->json([
                'message' => 'Campaign must have prospect filters defined to send emails.',
            ], 400);
        }

        // Get filtered prospects based on campaign filters
        $prospects = Prospect::applyFilters($campaign->prospect_filter)->get();

        if ($prospects->isEmpty()) {
            return response()->json([
                'message' => 'No prospects match the campaign filters.',
            ], 400);
        }

        $emailsSent = 0;

        foreach ($prospects as $prospect) {
            try {
                $trackingUrl = $this->trackingService->generateCampaignEmailUrl($campaign, $prospect);

                Mail::to($prospect->email)->send(
                    new CampaignEmail($campaign, $prospect, $trackingUrl)
                );

                $emailsSent++;

                if (app()->isLocal()) {
                    break;
                }

            } catch (Exception $e) {
                // Log the error but continue with other prospects
                logger()->error('Failed to send campaign email', [
                    'campaign_id' => $campaign->id,
                    'prospect_id' => $prospect->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => "Campaign emails queued successfully. {$emailsSent} emails sent to prospects.",
            'emails_sent' => $emailsSent,
            'total_prospects' => $prospects->count(),
        ]);
    }
}
