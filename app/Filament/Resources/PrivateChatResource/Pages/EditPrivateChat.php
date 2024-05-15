<?php

namespace App\Filament\Resources\PrivateChatResource\Pages;

use App\Filament\Resources\PrivateChatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrivateChat extends EditRecord
{
    protected static string $resource = PrivateChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
