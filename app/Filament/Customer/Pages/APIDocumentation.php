<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class APIDocumentation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static string $view = 'filament.customer.pages.a-p-i-documentation';

    protected static ?int $navigationSort = 30;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = "API Documentation";
}
