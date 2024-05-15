<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIStarTimeVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    
    protected static ?string $navigationLabel = "StarTime Vending";
    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-star-time-vending';
}
