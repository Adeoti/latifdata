<?php

namespace App\Filament\Resources\AirtimeIntegrationResource\Pages;

use App\Filament\Resources\AirtimeIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAirtimeIntegration extends EditRecord
{
    protected static string $resource = AirtimeIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
