<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingsResource\Pages;
use App\Filament\Resources\SiteSettingsResource\RelationManagers;
use App\Models\SiteSettings;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteSettingsResource extends Resource
{
    protected static ?string $model = SiteSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = "Control Panel";
    protected static ?int $navigationSort = 2;
    
    public $ngn = "₦";

    public static function canCreate(): bool
       {
       return SiteSettings::count() > 0? false : true;
         
       }

   
    public static function form(Form $form): Form
    {

        $ngn = "₦";
        return $form
            ->schema([
                //
               
                Section::make('Configure the APP')->schema([
                     TextInput::make('name')
                    ->default('billApp')
                    ->required(),
                    Section::make('Charges')->schema([
                        TextInput::make('agent_charges')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->prefix($ngn)
                            ->default(0),
                        TextInput::make('special_charges')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->prefix($ngn)
                            ->default(0),
                        TextInput::make('api_charges')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->prefix($ngn)
                            ->default(0),
                        // TextInput::make('portal_dev_charges')
                        //     ->label('VTU Portal Development Charges')
                        //     ->numeric()
                        //     ->inputMode('decimal')
                        //     ->required()
                        //     ->prefix($ngn)
                        //     ->default(0),
                    ])->columns(2),
                    Section::make('Charges B')->schema([
                        TextInput::make('refferal_commision')
                            ->label('Referral Commission')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->prefix($ngn)
                            ->default(0),
                        TextInput::make('wallet_to_charges')
                            ->label('Wallet to Wallet Charges')
                            ->numeric()
                            ->inputMode('decimal')
                            ->required()
                            ->prefix($ngn)
                            ->default(0),
                        Toggle::make('refferal_status')
                            ->default(false)
                            ->inline(false)

                    ])->columns(2),
                    
                    Section::make('Bonus Cap Amount')
                        ->description('At what amount can customers withdraw their bonus?')
                        ->schema([
                        TextInput::make('cashbak_cap_amount')
                        ->default('0')
                        ->numeric()
                            ->inputMode('decimal')
                            ->required()
                        ,
                        TextInput::make('referral_cap_amount')
                        ->default('0')
                        ->numeric()
                            ->inputMode('decimal')
                            ->required()
                        ,

                        

                    ])->columns(2),
                    Section::make('')->schema([
                        TextInput::make('whatsapp_number')
                        ->default('090')
                        ,

                        Select::make('default_theme')
                            ->options([
                                'light' => 'Light',
                                'dark' => 'Dark',
                            ])
                            ->label('Default Theme')
                            ->default('dark')

                    ])->columns(2)
                    
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Site Name'),

                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp Number'),

                TextColumn::make('default_theme')
                    ->label('Default Theme'),

                TextColumn::make('agent_charges')
                    ->money('NGN')
                    ->label('Agent Charges'),

                TextColumn::make('special_charges')
                    ->money('NGN')
                    ->label('Special Charges'),

                TextColumn::make('api_charges')
                    ->money('NGN')
                    ->label('API Charges'),

                // TextColumn::make('portal_dev_charges')
                //     ->money('NGN')
                //     ->label('Portal Dev Charges'),

                TextColumn::make('wallet_to_charges')
                    ->money('NGN')
                    ->label('Wallet 2 Wallet Charges'),

                

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSiteSettings::route('/'),
            //'create' => Pages\CreateSiteSettings::route('/create'),
            //'edit' => Pages\EditSiteSettings::route('/{record}/edit'),
        ];
    }
}
