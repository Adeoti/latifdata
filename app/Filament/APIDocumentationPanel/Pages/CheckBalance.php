<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class CheckBalance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';


    protected static ?string $navigationLabel = "Check Balance";
    protected static ?string $title = "Check Balance";
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.a-p-i-documentation-panel.pages.check-balance';
}
