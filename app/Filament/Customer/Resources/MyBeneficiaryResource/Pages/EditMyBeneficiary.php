<?php

namespace App\Filament\Customer\Resources\MyBeneficiaryResource\Pages;

use App\Filament\Customer\Resources\MyBeneficiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyBeneficiary extends EditRecord
{
    protected static string $resource = MyBeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
