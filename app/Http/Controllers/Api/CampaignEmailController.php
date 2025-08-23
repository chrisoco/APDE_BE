<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\CampaignEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class CampaignEmailController extends Controller
{
    public function __construct(
        private readonly CampaignEmailService $campaignEmailService
    ) {}

    public function send(Request $request, Campaign $campaign): JsonResponse
    {
        Gate::authorize('sendEmails', $campaign);

        $validationError = $this->campaignEmailService->validateCampaignForSending($campaign);
        if ($validationError !== null && $validationError !== []) {
            return response()->json($validationError, 400);
        }

        $force = $request->boolean('force', false);
        $prospects = $this->campaignEmailService->getProspectsToEmail($campaign, $force);

        if ($prospects->isEmpty()) {
            return response()->json([
                'message' => 'No prospects match the campaign filters or all prospects have already been contacted.',
            ], 400);
        }

        $results = $this->campaignEmailService->sendEmailsToProspects($campaign, $prospects, $force);
        $totalProspects = $this->campaignEmailService->getTotalProspectsCount($campaign);

        return response()->json([
            'message' => "Campaign emails queued successfully. {$results['emails_sent']} emails sent to prospects.",
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
            ],
            'emails_sent' => $results['emails_sent'],
            'total_emails_sent' => $results['total_emails_sent'],
            'notified_prospects' => $results['notified_prospects'],
            'available_prospects' => $totalProspects - $results['notified_prospects'],
            'total_prospects' => $totalProspects,
        ]);
    }
}
