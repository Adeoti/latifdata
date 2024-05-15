<?php

namespace App\Filament\Resources\AirtimeIntegrationResource\Pages;

use App\Filament\Resources\AirtimeIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAirtimeIntegrations extends ListRecords
{
    protected static string $resource = AirtimeIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
