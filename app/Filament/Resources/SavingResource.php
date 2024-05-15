<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SavingResource\Pages;
use App\Filament\Resources\SavingResource\RelationManagers;
use App\Models\Saving;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-down';
    protected static ?string $navigationGroup = "Cashflow";
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_customer',false);
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
                   Hidden::make('is_customer')->default(true),
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->inputMode('decimal')
                    ,
                    
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
