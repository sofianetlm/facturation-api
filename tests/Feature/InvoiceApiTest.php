<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_an_invoice_with_items(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $this->postJson('/api/invoices', [
            'client_id' => $client->id,
            'issued_at' => '2026-10-01',
            'items' => [
                ['description' => 'Audit du code', 'quantity' => 2, 'unit_price' => 30000],
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.total', 60000);

        $this->assertDatabaseCount('invoice_items', 1);
    }

    public function test_invoice_requires_at_least_one_item(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $this->postJson('/api/invoices', [
            'client_id' => $client->id,
            'issued_at' => '2026-10-01',
            'items' => [],
        ])->assertUnprocessable()->assertJsonValidationErrors('items');
    }

    public function test_user_cannot_create_invoice_for_another_users_client(): void
    {
        $foreignClient = Client::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/invoices', [
            'client_id' => $foreignClient->id,
            'issued_at' => '2026-10-01',
            'items' => [
                ['description' => 'Test', 'quantity' => 1, 'unit_price' => 1000],
            ],
        ])->assertUnprocessable()->assertJsonValidationErrors('client_id');
    }

    public function test_user_cannot_view_another_users_invoice(): void
    {
        $foreignInvoice = Invoice::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/invoices/{$foreignInvoice->id}")->assertNotFound();
    }

    public function test_invoice_total_is_computed_in_cents(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 3,
            'unit_price' => 1000,
        ]);
        Sanctum::actingAs($user);

        $this->getJson("/api/invoices/{$invoice->id}")
            ->assertOk()
            ->assertJsonPath('data.total', 3000);
    }

    public function test_user_can_mark_invoice_as_paid(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        Sanctum::actingAs($user);

        $this->patchJson("/api/invoices/{$invoice->id}", ['status' => 'paid'])
            ->assertOk()
            ->assertJsonPath('data.status', 'paid');
    }
}