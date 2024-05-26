<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIAirtimeVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-up-right';

    
    protected static ?string $navigationLabel = "Airtime Vending";
    protected static ?string $title = "Airtime Vending";
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-airtime-vending';
}
