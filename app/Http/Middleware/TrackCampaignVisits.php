<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Landingpage;
use App\Services\CampaignTrackingService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final readonly class TrackCampaignVisits
{
    public function __construct(
        private CampaignTrackingService $trackingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track if this is a landing page route
        if ($this->isLandingPageRoute($request)) {
            $landingpageSlug = $this->extractLandingPageSlug($request);

            if ($landingpageSlug !== null && $landingpageSlug !== '' && $landingpageSlug !== '0') {
                try {
                    $landingpage = Landingpage::where('slug', $landingpageSlug)->first();

                    if ($landingpage) {
                        $this->trackingService->trackVisit(
                            $request,
                            $landingpage->campaign_id,
                            $landingpage->id
                        );
                    }
                } catch (Exception $e) {
                    // Log the error but don't break the request
                    Log::error('Campaign tracking failed: '.$e->getMessage());
                }
            }
        }

        return $response;
    }

    /**
     * Check if the current request is for a landing page.
     */
    private function isLandingPageRoute(Request $request): bool
    {
        $path = $request->path();

        // Check if the path matches landing page patterns
        return preg_match('/^landing\//', $path) ||
               preg_match('/^lp\//', $path) ||
               preg_match('/^campaign\//', $path);
    }

    /**
     * Extract the landing page slug from the request path.
     */
    private function extractLandingPageSlug(Request $request): ?string
    {
        $path = $request->path();

        // Extract slug from various possible patterns
        if (preg_match('/^landing\/(.+)$/', $path, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^lp\/(.+)$/', $path, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^campaign\/(.+)$/', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
