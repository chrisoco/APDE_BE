<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
final class CampaignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Campaign>
     */
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(CampaignStatus::values()),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
            'prospect_filter' => array_filter([
                'min_age' => $this->faker->boolean ? $this->faker->numberBetween(18, 50) : null,
                'max_age' => $this->faker->boolean ? $this->faker->numberBetween(50, 100) : null,
                'gender' => $this->faker->boolean ? $this->faker->randomElement(['female', 'male']) : null,
                'source' => $this->faker->boolean ? $this->faker->randomElement(['erp', 'kueba']) : null,
            ], fn ($v): bool => ! is_null($v)),
        ];
    }
}
