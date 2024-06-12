<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use App\Models\CableSubscription;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CableIntegrationResource\Pages;
use App\Filament\Resources\CableIntegrationResource\RelationManagers;
use App\Filament\Resources\CableIntegrationResource\Pages\EditCableIntegration;
use App\Filament\Resources\CableIntegrationResource\Pages\ListCableIntegrations;
use App\Filament\Resources\CableIntegrationResource\Pages\CreateCableIntegration;

class CableIntegrationResource extends Resource
{
    protected static ?string $model = CableSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationGroup = "API Settings";

    public static function form(Form $form): Form
    {
        $ngn = "₦";
        return $form
            ->schema([
                //
                Section::make('')->schema([
                    Select::make('service_id')
                        ->required()

                        ->options([
                            'dstv'=>'DSTV',
                            'gotv'=>'GOTV',
                            'startimes'=>'StarTimes',

                        ]),
                    TextInput::make('plan_type')
                        ->label('Subscription Type')
                        ,

                    TextInput::make('name')
                        ->label('Plan Label')
                        ->required(),


                    TextInput::make('country_code')
                        ->required()
                        ->default('NG'),
                    Toggle::make('active_status')
                        ->inline(false)
                        ->default(true)
                    

                ])->columns(3),

                Section::make('Pricing')
                        ->schema([
                            
                            TextInput::make('price')
                                ->required()
                                ->prefix("$ngn")
                                ->numeric(),




                        ]),

                Section::make('Charges')
                        ->collapsible()
                        ->description('Set the charges for each package')
                        ->schema([
                            
                            TextInput::make('primary_charges')
                                ->required()
                                ->default(200)
                                ->prefix("$ngn")
                                ->numeric(),

                            TextInput::make('agent_charges')
                            ->required()
                            ->default(60)
                                ->prefix("$ngn")
                                ->numeric(),

                            TextInput::make('special_charges')
                                ->required()
                                ->prefix("$ngn")
                                ->default(50)
                                ->numeric(),

                            TextInput::make('api_charges')
                            ->required()
                                ->prefix("$ngn")
                                ->default(50)
                                ->numeric(),



                        ])->columns(4),

                        Section::make('SweetBill API Data')
                            ->schema([
                                
                                TextInput::make('api_code')
                                    ->label('Cable ID')
                                    ->required(),

                                
                                
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

                TextColumn::make('name')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
                    ->sortable()
                
                    ,

                ToggleColumn::make('active_status'),

                TextColumn::make('price')
                    ->searchable()
                    ->toggleable()
                    ->money("NGN")
                    ->sortable()
                    ,

                TextColumn::make('primary_charges')
                    ->searchable()
                    ->toggleable()
                    ->money("NGN")
                    ->sortable()
                    ,

                TextColumn::make('agent_charges')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->money("NGN")
                    ,

                TextColumn::make('special_charges')
                    ->searchable()
                    ->toggleable()
                    ->money("NGN")
                    ->sortable()
                    ,

                TextColumn::make('api_charges')
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
                    Action::make('Activate selected')
                        ->color('success')
                        ->icon('heroicon-m-power'),


                    Action::make('Deactivate selected')
                        ->color('primary')
                        ->icon('heroicon-m-power')
                        //->accessSelectedRecords()
                        ->requiresConfirmation()
                       
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
            'index' => Pages\ListCableIntegrations::route('/'),
            'create' => Pages\CreateCableIntegration::route('/create'),
            'edit' => Pages\EditCableIntegration::route('/{record}/edit'),
        ];
    }
}
