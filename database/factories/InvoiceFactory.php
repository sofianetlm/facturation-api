<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'number' => 'FAC-' . fake()->unique()->numerify('####-####'),
            'issued_at' => now(),
            'due_at' => now()->addDays(30),
            'status' => 'draft',
            'currency' => 'EUR',
        ];
    }
}