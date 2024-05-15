<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BulkMessage;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BulkMessageResource\Pages;
use App\Filament\Resources\BulkMessageResource\RelationManagers;

class BulkMessageResource extends Resource
{
    protected static ?string $model = BulkMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = "Messaging";
    protected static ?string $modelLabel = "Notifications";
     

 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Message')->schema([
                    TextInput::make('title')->required()
                        ,
                    RichEditor::make('message')
                        ->label('Body')
                        ->required(),
                ])->columnSpan(1),
                Section::make('Ingredients')->schema([
                    FileUpload::make('graphics')
                        ->label('Notification Icon')
                        ->disk('public')
                        ->directory('notificationGrahics')
                        ->imageEditor()
                        ->image(),
                    Select::make('target')->options([
                        'all' => "All",
                        'staff' => "Staff",
                        'nonstaff' => "Non-Staff"
                    ])->required(),
                    Hidden::make('user_id')->default(Auth()->id()),
                    Select::make('style')->options([
                        'native' => "Native",
                        'classic' => "Classic",
                        'optin' => "Optin"
                    ])
                        ->required()
                        ->default('native')

                ])->columnSpan(1)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('message')
                    ->markdown()
                    ->words(4)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->date()
                    ->label('Sent On')
                    ,
                TextColumn::make('user.name')
                    ->label('Sent By')
                    ->toggleable()
                    ->searchable()
                    ->sortable()
                    
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBulkMessages::route('/'),
            'create' => Pages\CreateBulkMessage::route('/create'),
            'edit' => Pages\EditBulkMessage::route('/{record}/edit'),
        ];
    }
}
