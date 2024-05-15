<?php

namespace App\Filament\Customer\Resources\ExpenseResource\Pages;

use App\Filament\Customer\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
}
