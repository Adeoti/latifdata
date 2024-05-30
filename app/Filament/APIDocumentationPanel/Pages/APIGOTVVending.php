<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIGOTVVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-tv';

    
    protected static ?string $navigationLabel = "GOTV Vending";
    protected static ?string $title = "GOTV Vending";
    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-g-o-t-v-vending';
    protected static bool $shouldRegisterNavigation = false;
}
