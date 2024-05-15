<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIElectricityVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    
    protected static ?string $navigationLabel = "Electricity Vending";
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-electricity-vending';
}
