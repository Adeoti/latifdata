<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MobileAirtime;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AirtimeIntegrationResource\Pages;
use App\Filament\Resources\AirtimeIntegrationResource\RelationManagers;
use App\Filament\Resources\AirtimeIntegrationResource\Pages\EditAirtimeIntegration;
use App\Filament\Resources\AirtimeIntegrationResource\Pages\ListAirtimeIntegrations;
use App\Filament\Resources\AirtimeIntegrationResource\Pages\CreateAirtimeIntegration;

class AirtimeIntegrationResource extends Resource
{
    protected static ?string $model = MobileAirtime::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-up-right';

    protected static ?string $navigationLabel = "Mobile Airtime";
    protected static ?string $navigationGroup = "API Settings";
    protected static ?string $modelLabel = "Mobile Airtime";
    protected static ?string $pluralModelLabel = "Mobile Airtime Controls";



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
                                ->prefix("%")
                                ->numeric(),

                            TextInput::make('agent_cashback')
                                ->default(0)
                                ->prefix("%")
                                ->numeric(),

                            TextInput::make('special_cashback')
                                ->default(0)
                                ->prefix("%")
                                ->numeric(),

                            



                        ])->columns(3),

                        Section::make('Restrictions')
                            ->schema([
                                TextInput::make('minimum_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix($ngn),

                                TextInput::make('maximum_amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix($ngn),

                            ])->columns(2),

                        Section::make('API Data')
                            ->schema([
                                
                                TextInput::make('api_code')
                                    ->label('Network (1,2,3,4...) ')
                                    ->required(),

                              
  
                                   
                                Hidden::make('user_id')->default(auth()->id())
                            ]),

                    
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

              
                TextColumn::make('minimum_amount')
                    ->searchable()
                    ->toggleable()
                    ->money("NGN")
                    ->sortable()
                    ,

                TextColumn::make('maximum_amount')
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

                TextColumn::make('service_id')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ,

                TextColumn::make('endpoint')
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
            'index' => Pages\ListAirtimeIntegrations::route('/'),
            'create' => Pages\CreateAirtimeIntegration::route('/create'),
            'edit' => Pages\EditAirtimeIntegration::route('/{record}/edit'),
        ];
    }
}
