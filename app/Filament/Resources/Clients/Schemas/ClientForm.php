<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom')->required()->maxLength(255),
            TextInput::make('email')->label('E-mail')->email()->maxLength(255),
            TextInput::make('phone')->label('Téléphone')->tel()->maxLength(50),
            Textarea::make('address')->label('Adresse')->columnSpanFull(),
        ]);
    }
}