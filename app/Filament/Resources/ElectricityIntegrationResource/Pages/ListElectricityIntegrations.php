<?php

namespace App\Filament\Resources\ElectricityIntegrationResource\Pages;

use App\Filament\Resources\ElectricityIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElectricityIntegrations extends ListRecords
{
    protected static string $resource = ElectricityIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
