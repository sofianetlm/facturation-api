<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label(__('app.number'))->searchable()->sortable(),
                TextColumn::make('client.name')->label(__('app.client_field'))->searchable(),
                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => __('app.draft'),
                        'sent' => __('app.sent'),
                        'paid' => __('app.paid'),
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'sent' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->label(__('app.total'))
                    ->state(fn ($record) => $record->total / 100)
                    ->money('EUR'),
                TextColumn::make('issued_at')->label(__('app.issued_on'))->date('d/m/Y')->sortable(),
                TextColumn::make('due_at')->label(__('app.due_at'))->date('d/m/Y'),
            ])
            ->defaultSort('issued_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}