<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('number')
                    ->required(),
                DatePicker::make('issued_at')
                    ->required(),
                DatePicker::make('due_at'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
            ]);
    }
}
