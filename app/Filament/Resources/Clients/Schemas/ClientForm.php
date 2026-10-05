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
            TextInput::make('name')->label(__('app.name'))->required()->maxLength(255),
            TextInput::make('email')->label(__('app.email'))->email()->maxLength(255),
            TextInput::make('phone')->label(__('app.phone'))->tel()->maxLength(50),
            Textarea::make('address')->label(__('app.address'))->columnSpanFull(),
        ]);
    }
}