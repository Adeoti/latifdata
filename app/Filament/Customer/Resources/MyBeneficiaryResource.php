<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Beneficiary;
use App\Models\MyBeneficiary;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\MyBeneficiaryResource\Pages;
use App\Filament\Customer\Resources\MyBeneficiaryResource\RelationManagers;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class MyBeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = "Settings";


    protected static ?string $title = "Beneficiary Settings";
    protected static ?string $navigationLabel = "Beneficiary Settings";

    protected static ?int $navigationSort = 45;


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Section::make('')->schema([

                    TextInput::make('name')
                        ->required()
                    ,
                    TextInput::make('number')
                        
                        ->required()
                    ,
                    Select::make('network')
                    ->required()
                    ->options([
                        'mtn'=>'MTN',
                        'airtel'=>'Airtel',
                        'glo'=>'GLO',
                        '9mobile'=>'9Mobile'
                    ]),
                    Hidden::make('user_id')->default(auth()->id()),

                ])->columns(3),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                ,
                TextColumn::make('number')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                ,
                TextColumn::make('network')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                ,
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->date("F d, Y h:i:s A")
                    ->label('Added On')
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
            'index' => Pages\ListMyBeneficiaries::route('/'),
            'create' => Pages\CreateMyBeneficiary::route('/create'),
            'edit' => Pages\EditMyBeneficiary::route('/{record}/edit'),
        ];
    }
}
