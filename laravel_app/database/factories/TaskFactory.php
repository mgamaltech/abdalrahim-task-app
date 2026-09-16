<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'done' => false,
            'user_id' => fake()->boolean(70) ? User::factory() : null,
            'completed_at' => null,
        ];
    }

    /**
     * Indicate that the task is done, which requires a completion time.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'done' => true,
            'completed_at' => fake()->dateTimeBetween('-1 month'),
        ]);
    }
}
