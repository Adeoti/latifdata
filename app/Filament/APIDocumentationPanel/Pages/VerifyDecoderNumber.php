<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Filament\Pages\Page;

class VerifyDecoderNumber extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-viewfinder-circle';

    protected static ?string $navigationLabel = "Verify Decoder";
    protected static ?string $title = "Verify Decoder";
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.verify-decoder-number';
}
