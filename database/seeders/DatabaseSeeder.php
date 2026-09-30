<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Comment;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create specific users for testing
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin->value,
        ]);

        $agent = User::factory()->create([
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'role' => UserRole::Agent->value,
        ]);

        $customer = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'role' => UserRole::Customer->value,
        ]);

        // 2. Create standard categories
        $categories = collect(['Billing', 'Technical Support', 'Sales', 'General Inquiry'])->map(function ($name) {
            return Category::factory()->create(['name' => $name, 'slug' => str()->slug($name)]);
        });

        // 3. Generate random tickets for the customer
        Ticket::factory(10)->create([
            'user_id' => $customer->id,
            'category_id' => $categories->random()->id,
            'assigned_to' => $agent->id,
        ])->each(function ($ticket) use ($agent, $customer) {
            // Add some comments to each ticket
            Comment::factory(3)->create([
                'ticket_id' => $ticket->id,
                'user_id' => rand(0, 1) ? $customer->id : $agent->id,
            ]);
        });
    }
}
