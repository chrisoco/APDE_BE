<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\Prospect;
use App\Services\CampaignTrackingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

final class CampaignEmailController extends Controller
{
    public function __construct(
        private readonly CampaignTrackingService $trackingService
    ) {}

    public function send(Request $request, Campaign $campaign): JsonResponse
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

        if ($campaign->status !== CampaignStatus::ACTIVE) {
            return response()->json([
                'message' => 'Campaign must be active to send emails.',
            ], 400);
        }

        $force = $request->boolean('force', false);

        // Filter prospects based on campaign filters
        $filteredProspects = Prospect::applyFilters($campaign->prospect_filter);
        $totalProspects = $filteredProspects->count();

        if ($force) {
            // Force mode: send to all prospects matching the filter
            $prospects = $filteredProspects->get();
        } else {
            // Get IDs of already-associated prospects
            $existingProspectIds = $campaign->campaignProspects()->pluck('prospect_id')->unique()->toArray();

            // Only get prospects matching the filter that are NOT already associated
            $prospects = $filteredProspects->whereNotIn('id', $existingProspectIds)->get();
        }

        if ($prospects->isEmpty()) {
            return response()->json([
                'message' => 'No prospects match the campaign filters or all prospects have already been contacted.',
            ], 400);
        }

        $emailsSent = 0;

        foreach ($prospects as $prospect) {
            try {
                $trackingUrl = $this->trackingService->generateCampaignEmailUrl($campaign, $prospect);

                // Create association record to track this email send
                $campaign->campaignProspects()->create([
                    'prospect_id' => $prospect->id,
                ]);

                Mail::to($prospect->email)->send(
                    new CampaignEmail($campaign, $prospect, $trackingUrl)
                );

                $emailsSent++;

                if (! $force && app()->isLocal()) {
                    break;
                }

                if ($force && app()->isLocal() && $emailsSent >= 3) {
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
            'total_emails_sent' => $campaign->campaignProspects()->count(),
            'notified_prospects' => $campaign->campaignProspects()->pluck('prospect_id')->unique()->count(),
            'available_prospects' => $totalProspects - $campaign->campaignProspects()->count(),
            'total_prospects' => $totalProspects,
        ]);
    }
}
