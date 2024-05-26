<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIDSTVVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-tv';

    
    protected static ?string $navigationLabel = "DSTV Vending";
    protected static ?string $title = "DSTV Vending";
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-d-s-t-v-vending';
}
