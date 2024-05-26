<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class VerifyMeter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt-slash';

    protected static ?string $navigationLabel = "Verify Meter";
    protected static ?string $title = "Verify Meter";
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.verify-meter';
}
