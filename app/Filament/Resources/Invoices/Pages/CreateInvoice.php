<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['number'] = 'FAC-' . now()->year . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['currency'] = 'EUR';

        return $data;
    }
}