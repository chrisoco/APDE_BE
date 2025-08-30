<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Mail\CampaignEmail;
use App\Models\Campaign;
use App\Models\Prospect;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

final readonly class CampaignEmailService
{
    public function __construct(
        private CampaignTrackingService $trackingService
    ) {}

    /**
     * Validate if a campaign can send emails.
     *
     * @return array<string, string>|null Returns error message array if invalid, null if valid
     */
    public function validateCampaignForSending(Campaign $campaign): ?array
    {
        if (! $campaign->landingpage) {
            return ['message' => 'Campaign must have an associated landing page to send emails.'];
        }

        if (! $campaign->prospect_filter) {
            return ['message' => 'Campaign must have prospect filters defined to send emails.'];
        }

        if ($campaign->status !== CampaignStatus::ACTIVE) {
            return ['message' => 'Campaign must be active to send emails.'];
        }

        return null;
    }

    /**
     * Get prospects that should receive emails for a campaign.
     *
     * @return Collection<int, Prospect>
     */
    public function getProspectsToEmail(Campaign $campaign, bool $force): Collection
    {
        $filteredProspects = Prospect::applyFilters($campaign->prospect_filter ?? []);

        if ($force) {
            return $filteredProspects->get();
        }

        $existingProspectIds = $campaign->campaignProspects()->pluck('prospect_id')->unique()->toArray();

        return $filteredProspects->whereNotIn('id', $existingProspectIds)->get();
    }

    /**
     * Send emails to prospects for a campaign.
     *
     * @param  Collection<int, Prospect>  $prospects
     * @return array{emails_sent: int, total_emails_sent: int, notified_prospects: int}
     */
    public function sendEmailsToProspects(Campaign $campaign, Collection $prospects, bool $force): array
    {
        $emailsSent = 0;

        foreach ($prospects as $prospect) {
            try {
                $trackingUrl = $this->trackingService->generateCampaignEmailUrl($campaign, $prospect);

                $campaign->campaignProspects()->create([
                    'prospect_id' => $prospect->id,
                ]);

                Mail::to($prospect->email)->send(
                    new CampaignEmail($campaign, $prospect, $trackingUrl)
                );

                $emailsSent++;

                if ($this->shouldStopSending($force, $emailsSent)) {
                    break;
                }

            } catch (Exception $e) {
                logger()->error('Failed to send campaign email', [
                    'campaign_id' => $campaign->id,
                    'prospect_id' => $prospect->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'emails_sent' => $emailsSent,
            'total_emails_sent' => $campaign->campaignProspects()->count(),
            'notified_prospects' => $campaign->campaignProspects()->pluck('prospect_id')->unique()->count(),
        ];
    }

    /**
     * Get total prospects count for a campaign.
     */
    public function getTotalProspectsCount(Campaign $campaign): int
    {
        if (! $campaign->prospect_filter) {
            return 0;
        }

        return Prospect::applyFilters($campaign->prospect_filter)->count();
    }

    /**
     * Determine if we should stop sending emails based on environment and force flag.
     */
    private function shouldStopSending(bool $force, int $emailsSent): bool
    {
        if (! app()->isLocal()) {
            return false;
        }

        if (! $force && $emailsSent >= 1) {
            return true;
        }

        return $force && $emailsSent >= 3;
    }
}
