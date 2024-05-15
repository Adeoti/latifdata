<?php

namespace App\Filament\Resources\CableIntegrationResource\Pages;

use App\Filament\Resources\CableIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCableIntegration extends EditRecord
{
    protected static string $resource = CableIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
