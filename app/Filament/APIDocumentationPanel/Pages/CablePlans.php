<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use App\Models\CableSubscription;
use App\Models\MobileData;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class CablePlans extends Page implements HasTable
{

    use InteractsWithTable;


    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.cable-plans';
    protected static ?int $navigationSort = 31;
    protected static ?string $navigationGroup = "Assets";



    public function table(Table $table): Table
    {

        $user_package = auth()->user()->package;
        $cable_charges = "";

        switch ($user_package) {
            case 'primary':
                $cable_charges = 'primary_charges';
                break;

            case 'agent':
                $cable_charges = 'agent_charges';
                break;

            case 'special':
                $cable_charges = 'special_charges';
                break;

            case 'api':
                $cable_charges = 'api_charges';
                break;
        }


        return $table
        ->query(
            // ...
            CableSubscription::where('active_status',true)->orderBy('id', 'asc')->limit(8)
        )
        ->columns([
            //
            TextColumn::make('id')
                ->label('Cable_ID')
                ->searchable()
                ->toggleable()
                ->copyable()
                ->copyMessage('Copied')
                ,
                
        TextColumn::make('service_id')
                        ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
                        ->searchable()
                        ->toggleable()
                        ->sortable()
                        ,
            TextColumn::make('name')
                ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
                ->searchable()
                ->toggleable()
                ->sortable()
                ,
           
            
            TextColumn::make('price')
                ->searchable()
                ->toggleable()
                ->money('NGN')
                ->sortable()
                ,
            
          

            TextColumn::make($cable_charges)
                ->searchable()
                ->toggleable()
                ->default('00')
                ->money('NGN')
                ->sortable()
                ,              


            
            ]) ->filters([
           
            ]);
    }
}
