<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataAPIIntegrationResource\Pages;
use App\Filament\Resources\DataAPIIntegrationResource\RelationManagers;
use App\Models\MobileData;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataAPIIntegrationResource extends Resource
{
    protected static ?string $model = MobileData::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = "Mobile Data";
    protected static ?string $navigationGroup = "API Settings";
    protected static ?string $modelLabel = "Mobile Data";
    protected static ?string $pluralModelLabel = "Mobile Data Controls";

    public static function form(Form $form): Form
    {
        $ngn = "₦";
        return $form
            ->schema([
                //
                Section::make('')->schema([
                    Select::make('network')
                        ->required()

                        ->options([
                            'mtn'=>'MTN',
                            'glo'=>'GLO',
                            'airtel'=>'Airtel',
                            '9mobile'=>'9Mobile'

                        ]),
                    Select::make('plan_type')
                        ->required()
                        ->options([
                            'SME'=>'SME',
                            'SME2'=>'SME2',
                            'CG'=>'CG',
                            'CDG'=>'CDG',
                            'Daily'=>'Daily',
                            'Weekly'=>'Weekly',
                            'Monthly'=>'Monthly',
                            'BI-Monthly'=>'BI-Monthly',
                            'Weekend'=>'Weekend',
                            'Social'=>'Social',
                            'Hourly'=>'Hourly',
                            'Special'=>'Special',
                            '2-Months'=>'2-Months',
                            'Other'=>'Other',
                            'Direct'=>'Direct',
                            'Gifting'=>'Gifting',

                        ]),

                    TextInput::make('plan_size')
                        ->required(),

                    TextInput::make('validity')
                        ->required(),

                    TextInput::make('country_code')
                        ->required()
                        ->default('NG'),
                    Toggle::make('active_status')
                        ->inline(false)
                    

                ])->columns(3),

                Section::make('Pricing')
                        ->schema([
                            
                            TextInput::make('primary_price')
                                ->required()
                                ->prefix("$ngn")
                                ->numeric(),

                            TextInput::make('agent_price')
                                ->required()
                                ->prefix("$ngn")
                                ->numeric(),

                            TextInput::make('special_price')
                                ->required()
                                ->prefix("$ngn")
                                ->numeric(),

                           



                        ])->columns(3),

                Section::make('Cashback')
                        ->collapsible()
                        ->collapsed(false)
                        ->description('Set the cashback for each package')
                        ->schema([
                            
                            TextInput::make('primary_cashback')
                                ->default(0)
                                ->prefix("$ngn")
                                ->numeric(),

                            TextInput::make('agent_cashback')
                                ->default(0)
                                ->prefix("$ngn")
                                ->numeric(),

                            TextInput::make('special_cashback')
                                ->default(0)
                                ->prefix("$ngn")
                                ->numeric(),

                           



                        ])->columns(3),

                        Section::make('API Data')
                            ->schema([
                                
                                TextInput::make('api_code')
                                    ->required()
                                    ->label('Data ID')
                                    ,

                                Hidden::make('user_id')->default(auth()->id())
                            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $ngn = "₦";
        return $table
            ->columns([
                //

                TextColumn::make('network')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
                    ->sortable()
                
                    ,

                ToggleColumn::make('active_status'),

                TextColumn::make('plan_size')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ,

                TextColumn::make('plan_type')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable()
                    ,

                TextColumn::make('validity')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ,

                TextColumn::make('primary_price')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->money("NGN")
                    ,

                TextColumn::make('agent_price')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->money("NGN")
                    ,

                TextColumn::make('special_price')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->money("NGN")
                    ,

               

                TextColumn::make('primary_cashback')
                    ->searchable()
                    ->toggleable()
                    ->money("NGN")
                    ->sortable()
                    ,

                TextColumn::make('agent_cashback')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->money("NGN")
                    ,

                TextColumn::make('special_cashback')
                    ->searchable()
                    ->toggleable()
                    ->money("NGN")
                    ->sortable()
                    ,

               

                TextColumn::make('api_code')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ,

               
               



            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataAPIIntegrations::route('/'),
            'create' => Pages\CreateDataAPIIntegration::route('/create'),
            'edit' => Pages\EditDataAPIIntegration::route('/{record}/edit'),
        ];
    }
}
