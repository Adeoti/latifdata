<?php

namespace App\Filament\Resources\DataAPIIntegrationResource\Pages;

use App\Filament\Resources\DataAPIIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataAPIIntegrations extends ListRecords
{
    protected static string $resource = DataAPIIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
