<?php

namespace App\Filament\Customer\Pages;

use App\Filament\Customer\Widgets\AutomatedFundingWidget;
use App\Filament\Customer\Widgets\KYCWidget;
use App\Filament\Customer\Widgets\ManualFundingWidget;
use Filament\Pages\Page;
use App\Models\PaymentIntegration;

class FundWallet extends Page 
{
  

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static string $view = 'filament.customer.pages.fund-wallet';

    protected static ?string $navigationLabel = "Fund Wallet";
    protected ?string $subheading = "Use the various options below to fund your wallet.";
    protected static ?int $navigationSort = 7;



    protected function getFooterWidgets(): array
    {
       if(auth()->user()->has_accounts == true){
        return [
            AutomatedFundingWidget::class,
            ManualFundingWidget::class,
        ];
       }else{
        return [
            AutomatedFundingWidget::class,
            KYCWidget::class,
            ManualFundingWidget::class,
        ];
       }
    }



}
