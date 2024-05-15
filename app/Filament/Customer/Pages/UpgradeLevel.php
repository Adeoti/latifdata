<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class UpgradeLevel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static string $view = 'filament.customer.pages.upgrade-level';

    
    protected ?string $subheading = "Choose your preferred package, and click the proceed button to upgrade automatically! ";

    protected static ?int $navigationSort = 28;
}
