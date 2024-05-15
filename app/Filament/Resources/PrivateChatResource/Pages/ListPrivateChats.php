<?php

namespace App\Filament\Resources\PrivateChatResource\Pages;

use App\Filament\Resources\PrivateChatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrivateChats extends ListRecords
{
    protected static string $resource = PrivateChatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
