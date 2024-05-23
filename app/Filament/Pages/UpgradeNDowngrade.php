<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class UpgradeNDowngrade extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    protected static string $view = 'filament.pages.upgrade-n-downgrade';
    protected static ?string $navigationGroup = "User Mgt.";

    protected static ?int $navigationSort = 3;

    protected static ?string $title = "Upgrade or Downgrade";

    protected static ?string $navigationLabel = "Upgrade or Downgrade ";
    protected ?string $heading = "Upgrade or Downgrade ";
    protected ?string $subheading = "Upgrade or Downgrade a customer to another package.";

   
   
     
    public static function canAccess(): bool
    {
       return auth()->user()->can_upgrade_customer;
    }

    

}
