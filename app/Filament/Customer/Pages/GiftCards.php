<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class GiftCards extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-gift-top';

    protected static string $view = 'filament.customer.pages.gift-cards';

    protected static ?string $navigationLabel = "Gift Cards";
    protected ?string $subheading = "Currently Not Available ...";
    
    protected static ?int $navigationSort = 6;
    protected static bool $shouldRegisterNavigation = false;

}
