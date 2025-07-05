<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignTracking;
use App\Models\Landingpage;
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
        $campaign = Campaign::factory()->create();
        $landingpage = Landingpage::factory()->create(['campaign_id' => $campaign->id]);

        $utmSources = ['facebook', 'google', 'twitter', 'linkedin', 'email', 'direct', 'organic'];
        $utmMediums = ['cpc', 'social', 'email', 'banner', 'affiliate', 'organic'];

        return [
            'campaign_id' => $campaign->id,
            'landingpage_id' => $landingpage->id,
            'prospect_id' => $this->faker->optional()->randomElement([Prospect::factory()->create()->id]),
            'session_id' => $this->faker->uuid(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'referrer' => $this->faker->optional()->url(),
            'utm_source' => $this->faker->randomElement($utmSources),
            'utm_medium' => $this->faker->randomElement($utmMediums),
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
            'first_visit_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'last_visit_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'visit_count' => $this->faker->numberBetween(1, 10),
            'converted' => $this->faker->boolean(20), // 20% conversion rate
            'converted_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
        ];
    }

    /**
     * Indicate that the tracking record is converted.
     */
    public function converted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'converted' => true,
            'converted_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Indicate that the tracking record is from a specific UTM source.
     */
    public function fromSource(string $source): static
    {
        return $this->state(fn (array $attributes): array => [
            'utm_source' => $source,
        ]);
    }

    /**
     * Indicate that the tracking record is from a specific campaign.
     */
    public function forCampaign(Campaign $campaign): static
    {
        return $this->state(fn (array $attributes): array => [
            'campaign_id' => $campaign->id,
        ]);
    }
}
