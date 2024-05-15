<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.customer.pages.settings';

    protected static ?int $navigationSort = 28;
    protected static ?string $navigationLabel = "Core Settings";
}
