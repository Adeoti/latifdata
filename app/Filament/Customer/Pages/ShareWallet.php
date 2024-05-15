<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class ShareWallet extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static string $view = 'filament.customer.pages.share-wallet';

    protected static ?string $navigationLabel = "Share Wallet";
    protected static ?int $navigationSort = 8;


    public static function getNavigationBadge(): ?string
    {
        return 'new';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }




}
