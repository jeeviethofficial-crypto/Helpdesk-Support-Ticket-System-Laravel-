<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(TicketStatus::cases())->value,
            'priority' => $this->faker->randomElement(TicketPriority::cases())->value,
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
