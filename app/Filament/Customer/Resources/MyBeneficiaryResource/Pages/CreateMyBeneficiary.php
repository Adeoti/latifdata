<?php

namespace App\Filament\Customer\Resources\MyBeneficiaryResource\Pages;

use App\Filament\Customer\Resources\MyBeneficiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyBeneficiary extends CreateRecord
{
    protected static string $resource = MyBeneficiaryResource::class;
}
