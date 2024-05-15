<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        
        //Model::unguard();

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                     ->label('Messaging')
                     ->icon('heroicon-s-chat-bubble-oval-left-ellipsis'),
                NavigationGroup::make()
                    ->label('User Mgt.')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Transactions')
                    ->icon('heroicon-o-chart-bar-square'),
                NavigationGroup::make()
                    ->label('Cashflow')
                    ->icon('heroicon-s-presentation-chart-line')
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label('API Settings')
                    ->icon('heroicon-s-cursor-arrow-ripple')
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label('Control Panel')
                    ->icon('heroicon-s-cog')
                    ->collapsed(true),
            ]);
        });


        // Model::unguard();
    }
}
