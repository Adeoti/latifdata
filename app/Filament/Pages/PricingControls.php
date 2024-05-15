<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PricingControls extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.pages.pricing-controls';
    protected static ?string $navigationGroup = "Control Panel";
    protected static ?int $navigationSort = 2;


    protected static ?string $title="Pricing Settings";
    protected ?string $heading = "Pricing Settings";
    protected ?string $subheading = "Set and Adjust Your Preferred Pricing.";
    protected static ?string $navigationLabel = "Pricing Settings";


     // public static function canAccess(): bool
    // {
    //    return auth()->user()->canManageSettings();
    // }
}
