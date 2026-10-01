<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        return ClientResource::collection(
            $request->user()->clients()->latest()->paginate(15)
        );
    }

    public function store(Request $request)
    {
        $client = $request->user()->clients()->create($this->validated($request));

        return (new ClientResource($client))->response()->setStatusCode(201);
    }

    public function show(Request $request, int $id)
    {
        return new ClientResource($request->user()->clients()->findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $client = $request->user()->clients()->findOrFail($id);
        $client->update($this->validated($request, partial: true));

        return new ClientResource($client);
    }

    public function destroy(Request $request, int $id)
    {
        $request->user()->clients()->findOrFail($id)->delete();

        return response()->noContent();
    }

    private function validated(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}