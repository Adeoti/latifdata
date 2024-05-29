<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIDataVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    
    protected static ?string $navigationLabel = "Data Vending";
    protected static ?string $title = "";
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-data-vending';
}
