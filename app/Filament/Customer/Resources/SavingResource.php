<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Saving;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\SavingResource\Pages;
use App\Filament\Customer\Resources\SavingResource\RelationManagers;
use Filament\Forms\Components\Toggle;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static ?string $navigationGroup = "Cashflow";
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth()->id())->where('is_customer',true);
    }

    public static function form(Form $form): Form
    {
        $userId = Auth()->id();
        return $form
            ->schema([
               Section::make('')->schema([
                Section::make('Primary Info')->schema([
                    TextInput::make('title')
                        ->required()
                    ,
                   
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->inputMode('decimal')

                    ,
                    Hidden::make('is_customer')->default(true),
                    Hidden::make('is_custo')->default(true),
                    DatePicker::make('dated')
                        ->required()
                        ->placeholder('MM/DD/YYYY')
                        ->native(false)
                        //->date()
                    ,
                    
                    Hidden::make('user_id')->default($userId)
                    ])->columnSpan(1)
                ,
                    
                
                    Section::make('Note')->schema([
                    
                    RichEditor::make('note')
                    ->label('')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                    ,
                    
                ])->columnSpan(1)
               ])->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('note')
                    ->words(4)
                    ->markdown()
                    ->toggleable()
                    ,
                TextColumn::make('amount')
                    ->searchable()
                    ->sortable()
                    ->money('ngn')
                ,
                TextColumn::make('dated')
                    ->label('Savings Date')
                    ->date()
                    ->sortable()
                    ,
                TextColumn::make('user.name')
                    ->label('Added By')
                    ->toggleable()
                    ,
                TextColumn::make('created_at')
                    ->date()
                    ->label('Created On')
                    ->sortable()
                    

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSavings::route('/'),
            'create' => Pages\CreateSaving::route('/create'),
            'edit' => Pages\EditSaving::route('/{record}/edit'),
        ];
    }
}
