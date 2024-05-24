<?php

namespace App\Filament\Customer\Resources\MyBeneficiaryResource\Pages;

use App\Filament\Customer\Resources\MyBeneficiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyBeneficiaries extends ListRecords
{
    protected static string $resource = MyBeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
