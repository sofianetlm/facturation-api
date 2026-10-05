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
                ->label(__('app.client_field'))
                ->relationship(
                    name: 'client',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->where('user_id', auth()->id()),
                )
                ->searchable()
                ->preload()
                ->required(),

            Select::make('status')
                ->label(__('app.status'))
                ->options([
                    'draft' => __('app.draft'),
                    'sent' => __('app.sent'),
                    'paid' => __('app.paid'),
                ])
                ->default('draft')
                ->required(),

            DatePicker::make('issued_at')->label(__('app.issued_at'))->default(now())->required(),
            DatePicker::make('due_at')->label(__('app.due_at')),

            Repeater::make('items')
                ->label(__('app.items'))
                ->relationship()
                ->schema([
                    TextInput::make('description')->label(__('app.description'))->required()->columnSpan(2),
                    TextInput::make('quantity')->label(__('app.quantity'))->numeric()->integer()->minValue(1)->default(1)->required(),
                    TextInput::make('unit_price')
                        ->label(__('app.unit_price'))
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
                ->addActionLabel(__('app.add_item'))
                ->columnSpanFull(),
        ]);
    }
}