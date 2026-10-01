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
                TextColumn::make('number')->label('Numéro')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Client')->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'sent' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn ($record) => $record->total / 100)
                    ->money('EUR'),
                TextColumn::make('issued_at')->label('Émise le')->date('d/m/Y')->sortable(),
                TextColumn::make('due_at')->label('Échéance')->date('d/m/Y'),
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