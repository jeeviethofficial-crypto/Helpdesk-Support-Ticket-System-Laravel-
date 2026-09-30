<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'ticket_id' => Ticket::factory(),
        ];
    }
}
