<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class APIControls extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    protected static string $view = 'filament.pages.a-p-i-controls';
    protected static ?string $navigationGroup = "Control Panel";
    protected static ?int $navigationSort = 1;

    protected static ?string $title="API Control Panel";
    protected static bool $isDiscovered = false;
    protected ?string $heading = "API Settings";
    protected ?string $subheading = "Control Your Endpoints to Dispense From.";
    protected static ?string $navigationLabel = "API Settings";


     // public static function canAccess(): bool
    // {
    //    return auth()->user()->canManageSettings();
    // }





}
