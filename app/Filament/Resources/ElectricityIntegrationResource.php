<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Models\ElectricityIntegration;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ElectricityIntegrationResource\Pages;
use App\Filament\Resources\ElectricityIntegrationResource\RelationManagers;
use App\Filament\Resources\ElectricityIntegrationResource\Pages\EditElectricityIntegration;
use App\Filament\Resources\ElectricityIntegrationResource\Pages\ListElectricityIntegrations;
use App\Filament\Resources\ElectricityIntegrationResource\Pages\CreateElectricityIntegration;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class ElectricityIntegrationResource extends Resource
{
    protected static ?string $model = ElectricityIntegration::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationGroup = "API Settings";

    public static function form(Form $form): Form
    {
        $ngn = "₦";
        return $form
            ->schema([
                //
                Section::make('')->schema([
                  
                    

                    Select::make('vendor_name')
                        ->options([
                            'twins10' => 'twins10',
                            'datalight' => 'datalight',
                            'vtpass' => 'vtpass',
                            'flutterwave' => 'flutterwave',
                            'epins' => 'epins',

                        ])->searchable()
                        ->required(),
                        
                    Toggle::make('active_status')
                        ->inline(false)
                        ->default(true),
                    

                ])->columns(2),


                Section::make('Charges')
                        ->collapsible()
                        ->description('Set the chrages for each package')
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

                        Hidden::make('user_id')->default(auth()->id())
                            
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('vendor_name'),
                
                ToggleColumn::make('active_status'),

                TextColumn::make('primary_charges')
                    ->money('NGN'),

                TextColumn::make('agent_charges')
                    ->money('NGN'),
                
                TextColumn::make('special_charges')
                    ->money('NGN'),
                
                TextColumn::make('api_charges')
                    ->money('NGN')
                
                


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
            'index' => Pages\ListElectricityIntegrations::route('/'),
            'create' => Pages\CreateElectricityIntegration::route('/create'),
            'edit' => Pages\EditElectricityIntegration::route('/{record}/edit'),
        ];
    }
}
