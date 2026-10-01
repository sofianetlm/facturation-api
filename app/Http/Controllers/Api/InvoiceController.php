<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = $this->ownedBy($request)
            ->with('client')
            ->when($request->query('status'), fn (Builder $q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        return InvoiceResource::collection($invoices);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('user_id', $request->user()->id),
            ],
            'issued_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
        ]);

        $invoice = DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'client_id' => $data['client_id'],
                'number' => 'FAC-' . now()->year . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT),
                'issued_at' => $data['issued_at'],
                'due_at' => $data['due_at'] ?? null,
                'currency' => $data['currency'] ?? 'EUR',
                'status' => 'draft',
            ]);

            $invoice->items()->createMany($data['items']);

            return $invoice;
        });

        return (new InvoiceResource($invoice->load('client', 'items')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, int $id)
    {
        $invoice = $this->ownedBy($request)->with('client', 'items')->findOrFail($id);

        return new InvoiceResource($invoice);
    }

    public function update(Request $request, int $id)
    {
        $invoice = $this->ownedBy($request)->findOrFail($id);

        $invoice->update($request->validate([
            'status' => ['sometimes', Rule::in(['draft', 'sent', 'paid'])],
            'due_at' => ['sometimes', 'nullable', 'date'],
        ]));

        return new InvoiceResource($invoice->load('client', 'items'));
    }

    public function destroy(Request $request, int $id)
    {
        $this->ownedBy($request)->findOrFail($id)->delete();

        return response()->noContent();
    }

    // Uniquement les factures des clients de l'utilisateur connecté
    private function ownedBy(Request $request): Builder
    {
        return Invoice::whereHas(
            'client',
            fn (Builder $q) => $q->where('user_id', $request->user()->id)
        );
    }
}