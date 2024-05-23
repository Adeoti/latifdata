<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class ChangeUserPassword extends Page
{
   



    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static string $view = 'filament.pages.change-user-password';


    
    protected static ?string $navigationGroup = "User Mgt.";

    protected static ?int $navigationSort = 3;

    protected static ?string $title = "Update User's Password ";
    protected static ?string $navigationLabel = "Update User's Password ";
    protected ?string $heading = "Update User's Password ";
    protected ?string $subheading = "Update User's Password to a new one (adivisably a more secured one)";





    public static function canAccess(): bool
    {
       return auth()->user()->can_reset_password;
    }

    
}
