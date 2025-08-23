<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignTracking;
use App\Models\Prospect;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignTracking>
 */
final class CampaignTrackingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<CampaignTracking>
     */
    protected $model = CampaignTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $campaign = Campaign::all()->random();

        return [
            'campaign_id' => $campaign->id,
            'landingpage_id' => $campaign->landingpage_id,
            'prospect_id' => $this->faker->optional()->randomElement(Prospect::pluck('id')),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'referrer' => $this->faker->optional()->url(),
            'utm_source' => $this->faker->randomElement(['facebook', 'google', 'twitter', 'linkedin', 'email', 'direct', 'organic']),
            'utm_medium' => $this->faker->randomElement(['social', 'email', 'banner', 'affiliate', 'organic']),
            'utm_campaign' => $this->faker->slug(),
            'utm_content' => $this->faker->optional()->word(),
            'utm_term' => $this->faker->optional()->word(),
            'gclid' => $this->faker->optional()->regexify('[A-Za-z0-9]{20}'),
            'fbclid' => $this->faker->optional()->regexify('[A-Za-z0-9]{20}'),
            'tracking_data' => [
                'language' => $this->faker->randomElement(['en', 'de', 'fr', 'it']),
                'device_type' => $this->faker->randomElement(['desktop', 'mobile', 'tablet']),
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'os' => $this->faker->randomElement(['Windows', 'macOS', 'Linux', 'Android', 'iOS']),
                'screen_resolution' => $this->faker->randomElement(['1920x1080', '1366x768', '375x667', '768x1024']),
            ],
        ];
    }
}
