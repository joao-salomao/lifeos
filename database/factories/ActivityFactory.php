<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\CheckIn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 month', 'now');
        $endedAt = fake()->dateTimeBetween($startedAt, '+2 hours');

        return [
            'check_in_id' => CheckIn::factory(),
            'type' => fake()->randomElement(ActivityType::cases()),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'distance' => fake()->boolean(60) ? fake()->randomFloat(2, 0.5, 42.2) : null,
            'calories_burned' => fake()->boolean(80) ? fake()->randomFloat(2, 50, 1000) : null,
            'steps' => fake()->boolean(70) ? fake()->numberBetween(100, 20000) : null,
        ];
    }
}
