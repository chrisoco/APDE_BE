<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Landingpage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Landingpage>
 */
final class LandingpageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Landingpage>
     */
    protected $model = Landingpage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(3),
            'headline' => $this->faker->sentence(6),
            'subline' => $this->faker->sentence(10),
            'sections' => array_map(fn (): array => [
                'text' => $this->faker->paragraphs(3, true),
                'image_url' => $this->faker->imageUrl(800, 400, 'business', true, 'Landingpage'),
                'cta_text' => $this->faker->randomElement(['Sign Up', 'Learn More', 'Get Started', 'Contact Us']),
                'cta_url' => $this->faker->url(),
            ], range(1, $this->faker->numberBetween(0, 3))),
            // 'form_fields' => [],
        ];
    }
}
