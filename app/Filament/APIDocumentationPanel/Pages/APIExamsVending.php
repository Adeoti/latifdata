<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class APIExamsVending extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    
    protected static ?string $navigationLabel = "Exam Pins Vending";
    protected static ?string $title = "Exam Pins Vending";
    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-exams-vending';
}
