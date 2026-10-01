<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Demo',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        $clients = [
            ['name' => 'Agence Lumière', 'email' => 'contact@lumiere.test'],
            ['name' => 'Boutique Atlas', 'email' => 'info@atlas.test'],
        ];

        foreach ($clients as $i => $data) {
            $client = $user->clients()->create($data);

            $invoice = $client->invoices()->create([
                'number' => 'FAC-2026-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'issued_at' => now(),
                'due_at' => now()->addDays(30),
                'status' => 'sent',
            ]);

            $invoice->items()->createMany([
                ['description' => 'Développement API', 'quantity' => 10, 'unit_price' => 4500],
                ['description' => 'Maintenance mensuelle', 'quantity' => 1, 'unit_price' => 15000],
            ]);
        }
    }
}