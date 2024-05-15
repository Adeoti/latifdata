<?php

namespace App\Filament\Resources\ElectricityIntegrationResource\Pages;

use App\Filament\Resources\ElectricityIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElectricityIntegration extends EditRecord
{
    protected static string $resource = ElectricityIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
