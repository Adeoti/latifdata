<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CreditNDebit extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-battery-50';

    protected static string $view = 'filament.pages.credit-n-debit';

    protected static ?string $navigationGroup = "User Mgt.";

    protected static ?int $navigationSort = 2;

    protected static ?string $title = "Credit or Debit ";
    protected static ?string $navigationLabel = "Credit or Debit ";
    protected ?string $heading = "Credit or Debit ";
    protected ?string $subheading = "Credit or Debit a user's wallet";

    
    
    // public static function canAccess(): bool
    // {
    //    return auth()->user()->canManageSettings();
    // }





}
