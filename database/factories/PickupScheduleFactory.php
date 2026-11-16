<?php

namespace Database\Factories;

use App\Models\PickupSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PickupSchedule>
 */
class PickupScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pickup_date' => $this->faker->dateTimeBetween('tomorrow', '+3 months')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}
