<?php

namespace App\Filament\APIDocumentationPanel\Pages;

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

class DataPlans extends Page implements HasTable
{

    use InteractsWithTable;
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.data-plans';
    protected static ?int $navigationSort = 30;



    public function table(Table $table): Table
    {

        $user_package = auth()->user()->package;
        $cashback = "";
        $price = "";

        switch ($user_package) {
            case 'primary':
                $price = 'primary_price';
                $cashback = 'primary_cashback';
                break;

            case 'agent':
                $price = 'agent_price';
                $cashback = 'agent_cashback';
                break;

            case 'special':
                $price = 'special_price';
                $cashback = 'special_cashback';
                break;

            case 'api':
                $price = 'api_price';
                $cashback = 'api_cashback';
                break;
        }


        return $table
        ->query(
            // ...
            MobileData::where('active_status',true)->orderBy('id', 'asc')->limit(8)
        )
        ->columns([
            //
            TextColumn::make('id')
                ->label('Data_ID')
                ->searchable()
                ->toggleable()
                ->copyable()
                ->copyMessage('Copied')
                ,

            TextColumn::make('network')
                ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
                ->searchable()
                ->toggleable()
                ->sortable()
                ,
            
            TextColumn::make('plan_size')
                ->searchable()
                ->toggleable()
                ->sortable()
                ,
            
            TextColumn::make('plan_type')
                ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
                ->searchable()
                ->sortable()
                ->toggleable()
                ,

            TextColumn::make('validity')
                ->searchable()
                ->toggleable()
                ->sortable()
                ,

            TextColumn::make($price)
                ->searchable()
                ->toggleable()
                ->default('00')
                ->money('NGN')
                ->sortable()
                ,
            TextColumn::make($cashback)
                ->searchable()
                ->label('Cashback')
                ->default('00')
                ->toggleable()
                ->money('NGN')
                ->sortable()
                ,
          

                


            
            ]) ->filters([
           
            ]);
    }
}
