<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')
                ->label('Client')
                ->relationship(
                    name: 'client',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->where('user_id', auth()->id()),
                )
                ->searchable()
                ->preload()
                ->required(),

            Select::make('status')
                ->label('Statut')
                ->options([
                    'draft' => 'Brouillon',
                    'sent' => 'Envoyée',
                    'paid' => 'Payée',
                ])
                ->default('draft')
                ->required(),

            DatePicker::make('issued_at')->label('Date d\'émission')->default(now())->required(),
            DatePicker::make('due_at')->label('Échéance'),

            Repeater::make('items')
                ->label('Lignes de facture')
                ->relationship()
                ->schema([
                    TextInput::make('description')->label('Description')->required()->columnSpan(2),
                    TextInput::make('quantity')->label('Quantité')->numeric()->integer()->minValue(1)->default(1)->required(),
                    TextInput::make('unit_price')
                        ->label('Prix unitaire (€)')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->required()
                        // la base stocke des centimes, l'écran affiche des euros
                        ->formatStateUsing(fn ($state) => $state === null ? null : $state / 100)
                        ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                ])
                ->columns(4)
                ->minItems(1)
                ->defaultItems(1)
                ->addActionLabel('Ajouter une ligne')
                ->columnSpanFull(),
        ]);
    }
}